<?php

namespace App\Http\Controllers;

use App\Imports\StudentsImport;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{

    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function fetchStudents()
    {
        return $this->studentService->fetchStudents();
    }
    public function fetchAllStudentCustomIDs()
    {
        return $this->studentService->fetchAllStudentCustomIDs();
    }
    public function fetchActiveStudents()
    {
        return $this->studentService->fetchActiveStudents();
    }

    public function fetchTempQrCode()
    {
        return $this->studentService->fetchTempQrCode();
    }

    public function fetchNotPaidAdmissionStudent()
    {
        return $this->studentService->fetchNotPaidAdmissionStudent();
    }
    public function fetchStudent($id)
    {
        return $this->studentService->fetchStudent($id);
    }

    public function filterByCreatedDate(Request $request)
    {
        return $this->studentService->filterByCreatedDate($request);
    }

    public function fetchStudentCustomId(Request $request)
    {
        return $this->studentService->fetchStudentByQRCode($request);
    }
    public function updateStudentImage(Request $request, $custom_id)
    {
        return $this->studentService->updateStudentImage($request, $custom_id);
    }
    public function analytics($student_id)
    {
        return $this->studentService->studentAnalytics($student_id);
    }

    public function destroy($id)
    {
        return $this->studentService->destroy($id);
    }

    public function reactivate($id)
    {
        return $this->studentService->reactivate($id);
    }

    public function update(Request $request, $id)
    {
        return $this->studentService->update($request, $id);
    }

    public function store(Request $request)
    {
        return $this->studentService->store($request);
    }

    public function generateCustomIdAPI(Request $request)
    {
        return $this->studentService->generateCustomIdAPI($request);
    }


    public function publicStudentRegister(Request $request)
    {
        return $this->studentService->publicStudentRegister($request);
    }

    // web page route
    public function index()
    {
        $students = Student::all(); // Fixed variable name
        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }


    public function editPage($student_id)
    {
        return view('students.edit', compact('student_id'));
    }

    public function show($student_id)
    {
        return view('students.show', compact('student_id'));
    }

    public function studentImages()
    {
        return view('students.student_images');
    }

    public function allImages(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 12);
            $search = $request->input('search', '');

            $query = Student::with(['grade:id,grade_name'])
                ->where('is_active', 1)
                ->orderBy('created_at', 'desc');

            // Search
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('custom_id', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%")
                        ->orWhere('initial_name', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(full_name,' ',initial_name) LIKE ?", ["%{$search}%"]);
                });
            }

            $students = $query->paginate($perPage);

            // Transform image URLs
            foreach ($students as $student) {
                if (
                    !empty($student->img_url) &&
                    !Str::startsWith($student->img_url, ['http://', 'https://'])
                ) {
                    $student->img_url = Str::startsWith($student->img_url, 'uploads/')
                        ? asset($student->img_url)
                        : asset('uploads/' . $student->img_url);
                }
            }

            // Get all active students for dropdown
            $allStudents = Student::where('is_active', 1)
                ->orderBy('custom_id')
                ->get(['custom_id', 'full_name', 'initial_name', 'img_url']);

            return view('students.images', compact('students', 'allStudents'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to load student images: ' . $e->getMessage());
        }
    }

    // Handle CSV import
    public function import(Request $request)
    {
        // Validate uploaded file
        $request->validate([
            'file' => 'required|mimes:csv,xlsx'
        ]);

        $import = new StudentsImport;

        try {
            Excel::import($import, $request->file('file'));

            $errors = $import->errors;

            if (count($errors) > 0) {
                return back()->with('errors', $errors)
                    ->with('success', 'Some students imported successfully.');
            }

            return back()->with('success', 'All students imported successfully!');
        } catch (\Exception $e) {
            return back()->with('errors', ['Import failed: ' . $e->getMessage()]);
        }
    }



    public function addStudentToClass($class_id)
    {
        return view('students.add_student_to_class', compact('class_id'));
    }

    public function addStudentToSingleClass($student_id)
    {
        return view('students.add_student_to_single_class', compact('student_id'));
    }

    public function studentAnalytic($student_id)
    {
        return view('students.student_analytic', compact('student_id'));
    }

    public function showImportForm()
    {
        return view('students.import');
    }

    public function examResults($classCategoryHasStudentClassId, $student_id)
    {
        return view('students.exam_results', compact('classCategoryHasStudentClassId', 'student_id'));
    }
}
