<?php

namespace App\Services;

use App\Models\ClassRoom;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Enums\ClassType;
use Illuminate\Validation\Rule;

class ClassRoomService
{
    public function fetchAllClassRoom(Request $request)
    {
        try {
            $query = ClassRoom::with(['teacher', 'subject', 'grade']);

            // Apply status filter
            if ($request->has('status')) {
                if ($request->status === 'active') {
                    $query->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    $query->where('is_active', false);
                }
            }

            // Apply ongoing filter
            if ($request->has('ongoing')) {
                if ($request->ongoing === 'ongoing') {
                    $query->where('is_ongoing', true);
                } elseif ($request->ongoing === 'not_ongoing') {
                    $query->where('is_ongoing', false);
                }
            }

            // Apply grade filter
            if ($request->has('grade_id') && $request->grade_id) {
                $query->where('grade_id', $request->grade_id);
            }

            // Apply teacher filter
            if ($request->has('teacher_id') && $request->teacher_id) {
                $query->where('teacher_id', $request->teacher_id);
            }

            // Apply subject filter
            if ($request->has('subject_id') && $request->subject_id) {
                $query->where('subject_id', $request->subject_id);
            }

            // Apply search
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('class_name', 'like', "%{$search}%")
                        ->orWhereHas('teacher', function ($q) use ($search) {
                            $q->where('fname', 'like', "%{$search}%")
                                ->orWhere('lname', 'like', "%{$search}%");
                        })
                        ->orWhereHas('subject', function ($q) use ($search) {
                            $q->where('subject_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('grade', function ($q) use ($search) {
                            $q->where('grade_name', 'like', "%{$search}%");
                        });
                });
            }

            $classRooms = $query->orderBy('created_at', 'desc')
                ->paginate(15);

            return response()->json([
                'status' => 'success',
                'data'   => $classRooms->items(),
                'meta'   => [
                    'current_page' => $classRooms->currentPage(),
                    'last_page'    => $classRooms->lastPage(),
                    'per_page'     => $classRooms->perPage(),
                    'total'        => $classRooms->total(),
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'An unexpected error occurred',
                'error'   => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }


    // ========================
    // Store new Class Room
    // ========================
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'class_name'          => 'required|string|max:255',
                'medium'              => 'required|string|max:255',
                'class_type'          => ['required', Rule::in(ClassType::all())],

                'teacher_percentage'  => 'required|numeric|min:0|max:100',

                'is_active'           => 'required|boolean',
                'is_ongoing'          => 'required|boolean',

                'teacher_id'          => 'required|exists:teachers,id',
                'subject_id'          => 'required|exists:subjects,id',
                'grade_id'            => 'required|exists:grades,id',
            ]);

            $classRoom = ClassRoom::create($validated);

            return response()->json([
                'status'  => 'success',
                'message' => 'Class created successfully',
                'data'    => $classRoom
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to create class',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'class_name'         => 'required|string|max:255',

                'class_type'         => ['nullable', Rule::in(ClassType::all())],

                'teacher_percentage' => 'required|numeric|min:0|max:100',

                'is_active'          => 'nullable|boolean',
                'is_ongoing'         => 'nullable|boolean',

                'teacher_id'         => 'required|exists:teachers,id',
                'subject_id'         => 'required|exists:subjects,id',
                'grade_id'           => 'required|exists:grades,id',
            ]);

            $classRoom = ClassRoom::findOrFail($id);
            $classRoom->update($validated);

            return response()->json([
                'status'  => 'success',
                'message' => 'Class room updated successfully',
                'data'    => $classRoom
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update class room',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    // ========================
    // Optional: Fetch Class Rooms with relationships (API)
    // ========================
    public function fetchClasses()
    {
        $class_rooms = ClassRoom::with([
            'teacher' => function ($query) {
                $query->select('id', 'custom_id', 'fname', 'lname', 'email');
            },
            'subject' => function ($query) {
                $query->select('id', 'subject_name');
            },
            'grade' => function ($query) {
                $query->select('id', 'grade_name');
            },
        ])->get();

        return response()->json([
            'status' => 'success',
            'data' => $class_rooms
        ]);
    }

    public function fetchActiveClasses(Request $request)
    {
        try {
            $query = ClassRoom::with([
                'teacher' => function ($query) {
                    $query->select('id', 'custom_id', 'fname', 'lname', 'email');
                },
                'subject' => function ($query) {
                    $query->select('id', 'subject_name');
                },
                'grade' => function ($query) {
                    $query->select('id', 'grade_name');
                },
            ])->where('is_active', 1);

            // Apply class type filter (Online/Offline)
            if ($request->has('class_type') && $request->class_type !== '') {
                if ($request->class_type === 'online') {
                    $query->where('class_type', 'online');
                } elseif ($request->class_type === 'offline') {
                    $query->where('class_type', 'offline');
                }
            }

            // Apply search filter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('class_name', 'like', "%{$search}%")
                        ->orWhereHas('teacher', function ($q) use ($search) {
                            $q->where('fname', 'like', "%{$search}%")
                                ->orWhere('lname', 'like', "%{$search}%")
                                ->orWhere('custom_id', 'like', "%{$search}%");
                        })
                        ->orWhereHas('subject', function ($q) use ($search) {
                            $q->where('subject_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('grade', function ($q) use ($search) {
                            $q->where('grade_name', 'like', "%{$search}%");
                        });
                });
            }

            // Apply ordering
            $query->orderBy('created_at', 'desc');

            // Get paginated results (10 records per page)
            $class_rooms = $query->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $class_rooms->items(),
                'meta' => [
                    'current_page' => $class_rooms->currentPage(),
                    'last_page' => $class_rooms->lastPage(),
                    'per_page' => $class_rooms->perPage(),
                    'total' => $class_rooms->total(),
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch active classes',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function fetchSingleClasse($id)
    {
        try {
            // Fetch the class with subject and grade relationships
            $class_room = ClassRoom::with([
                'subject:id,subject_name',
                'grade:id,grade_name',
                'teacher:id,custom_id,fname,lname'
            ])->find($id);

            if (!$class_room) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Class not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $class_room
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch class',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function fetchTeacherClasse($teacher_id)
    {
        try {
            // Fetch classes related to the teacher with subject and grade relationships
            $classes = ClassRoom::with([
                'subject:id,subject_name',
                'grade:id,grade_name',
                'teacher:id,custom_id,fname,lname'
            ])->where('teacher_id', $teacher_id)->get(); // get() instead of find()

            if ($classes->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No classes found for this teacher'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $classes
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch classes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deactivateClassActive($id)
    {
        DB::beginTransaction();
        try {
            $class = ClassRoom::find($id);

            if (!$class) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Class not found'
                ], 404);
            }

            $class->update(['is_active' => 0]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Class marked as inactive successfully',
                'data' => [
                    'id' => $class->id,
                    'is_active' => $class->is_active
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to deactivate class',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deactivateClassOngoing($id)
    {
        DB::beginTransaction();
        try {
            $class = ClassRoom::find($id);

            if (!$class) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Class not found'
                ], 404);
            }

            $class->update(['is_ongoing' => 0]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Class marked as not ongoing successfully',
                'data' => [
                    'id' => $class->id,
                    'is_ongoing' => $class->is_ongoing
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to deactivate class',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reactivateClassActive($id)
    {
        DB::beginTransaction();
        try {
            $class = ClassRoom::find($id);

            if (!$class) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Class not found'
                ], 404);
            }

            $class->update(['is_active' => 1]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Class reactivated successfully',
                'data' => [
                    'id' => $class->id,
                    'is_active' => $class->is_active
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reactivate class',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reactivateClassOngoing($id)
    {
        DB::beginTransaction();
        try {
            $class = ClassRoom::find($id);

            if (!$class) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Class not found'
                ], 404);
            }

            $class->update(['is_ongoing' => 1]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Class marked as ongoing successfully',
                'data' => [
                    'id' => $class->id,
                    'is_ongoing' => $class->is_ongoing
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to mark class as ongoing',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
