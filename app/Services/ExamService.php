<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\StudentStudentStudentClass;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Exception;

class ExamService
{

    public function studentClassMiniDetails($exam_id)
    {
        try {
            // Get the exam along with its class_category_has_student_class relationship
            $exam = Exam::with('studentResults')->findOrFail($exam_id);

            // Get all students in that class
            $students = StudentStudentStudentClass::with(['student' => function ($query) use ($exam_id) {
                // Eager load the student's result for this exam only
                $query->with(['studentResults' => function ($q) use ($exam_id) {
                    $q->where('exam_id', $exam_id);
                }]);
            }])
                ->where('class_category_has_student_class_id', $exam->class_category_has_student_class_id)
                ->get()
                ->map(function ($item) {
                    $student = $item->student;

                    // Get the student's result for this exam, if exists
                    $result = $student->studentResults->first();

                    return [
                        'id' => $student->id,
                        'custom_id' => $student->custom_id,
                        'full_name' => $student->full_name,
                        'initial_name' => $student->initial_name,
                        'mobile' => $student->mobile,
                        'guardian_mobile' => $student->guardian_mobile,
                        'marks' => $result ? $result->marks : null,
                        'reason' => $result ? $result->reason : null,
                        'is_updated' => $result ? $result->is_updated : false,
                        'user_name' => $result && $result->user ? $result->user->name : null,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'exam' => $exam->title,
                'students' => $students
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch students',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Create new exam
     */
    public function createExam(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'class_category_has_student_class_id' => 'required|exists:class_category_has_student_class,id',
            'class_hall_id' => 'required|exists:class_halls,id',
        ]);

        try {

            // Prevent hall time conflict
            if ($this->hasTimeConflict(
                $validated['date'],
                $validated['start_time'],
                $validated['end_time'],
                $validated['class_hall_id']
            )) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hall already booked for this time slot',
                ], 422);
            }

            // PHP 7.4 does NOT support ... spread here, so merge manually
            $data = $validated;
            $data['is_canceled'] = false;

            $exam = Exam::create($data);

            // Replace null safe operator ?-> with ternary
            $userId = $request->user() ? $request->user()->id : null;

            return response()->json([
                'status' => 'success',
                'message' => 'Exam created successfully',
                'data' => $exam,
            ], 201);
        } catch (Exception $e) {

            $userId = $request->user() ? $request->user()->id : null;


            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create exam',
            ], 500);
        }
    }

    /**
     * Fetch all exams
     */
    public function fetchExam()
    {
        try {

            $exams = Exam::with('hall')
                ->orderBy('date')
                ->get()
                ->map(function ($exam) {
                    return [
                        'id'        => $exam->id,
                        'title'     => $exam->title,
                        'date'      => $exam->date,
                        'start'     => $exam->start_time ? $exam->start_time->format('H:i') : null,
                        'end'       => $exam->end_time ? $exam->end_time->format('H:i') : null,
                        'hall_name' => $exam->hall ? $exam->hall->hall_name : 'N/A',
                        'status'    => $exam->is_canceled ? 'Canceled' : 'Active',
                        'duration'  => $exam->duration,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data'   => $exams,
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to fetch exams',
            ], 500);
        }
    }

    /**
     * Update exam
     */
    public function updateExam(Request $request, $exam_id)
    {
        try {

            $exam = Exam::findOrFail($exam_id);

            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'date' => 'sometimes|date',
                'start_time' => 'sometimes|date_format:H:i',
                'end_time' => 'sometimes|date_format:H:i|after:start_time',
                'class_category_has_student_class_id' => 'sometimes|exists:class_category_has_student_class,id',
                'class_hall_id' => 'sometimes|exists:class_halls,id',
            ]);

            // Safely resolve new values
            $newDate = isset($validated['date']) ? $validated['date'] : $exam->date;
            $newStart = isset($validated['start_time']) ? $validated['start_time'] : ($exam->start_time ? $exam->start_time->format('H:i') : null);
            $newEnd = isset($validated['end_time']) ? $validated['end_time'] : ($exam->end_time ? $exam->end_time->format('H:i') : null);
            $newHall = isset($validated['class_hall_id']) ? $validated['class_hall_id'] : $exam->class_hall_id;

            // Convert date safely to string
            if ($newDate instanceof Carbon) {
                $newDate = $newDate->toDateString();
            }

            // Prevent hall time conflict (exclude current exam)
            if ($this->hasTimeConflict($newDate, $newStart, $newEnd, $newHall, $exam->id)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hall already booked for this time slot',
                ], 422);
            }

            $exam->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Exam updated successfully',
                'data' => $exam,
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update exam',
            ], 500);
        }
    }

    /**
     * Cancel exam
     */
    public function cancelExam($exam_id)
    {
        try {

            $exam = Exam::findOrFail($exam_id);

            // Convert safely to Carbon
            $examDate = $exam->date instanceof Carbon ? $exam->date : Carbon::parse($exam->date);

            if ($examDate->lt(Carbon::today())) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Exam date has already expired. Cannot cancel.',
                ], 422);
            }

            $exam->update(['is_canceled' => true]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Exam canceled successfully',
                'data'    => $exam,
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to cancel exam',
            ], 500);
        }
    }

    /**
     * Check hall time conflict
     */
    private function hasTimeConflict($date, $start, $end, $hallId, $excludeId = null)
    {
        return Exam::where('class_hall_id', $hallId)
            ->where('date', $date)
            ->where('is_canceled', false)
            ->when($excludeId, function ($query) use ($excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_time', [$start, $end])
                    ->orWhereBetween('end_time', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('start_time', '<=', $start)
                            ->where('end_time', '>=', $end);
                    });
            })
            ->exists();
    }
}
