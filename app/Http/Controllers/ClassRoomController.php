<?php

namespace App\Http\Controllers;


use App\Services\ClassRoomService;
use Illuminate\Http\Request;

class ClassRoomController extends Controller
{

    protected $classRoomService;


    public function __construct(ClassRoomService $classRoomService)
    {
        $this->classRoomService = $classRoomService;
    }

    public function fetchAllClassRoom(Request $request)
    {
        return $this->classRoomService->fetchAllClassRoom($request);
    }
    public function fetchClasses()
    {
        return $this->classRoomService->fetchClasses();
    }

    public function fetchActiveClasses(Request $request)
    {
        return $this->classRoomService->fetchActiveClasses($request);
    }

    public function fetchSingleClasse($id)
    {
        return $this->classRoomService->fetchSingleClasse($id);
    }

    public function fetchTeacherClasse($teacher_id)
    {
        return $this->classRoomService->fetchTeacherClasse($teacher_id);
    }

       public function getAllClassesByGrades($gradeId)
    {
        return $this->classRoomService->getAllClassesByGrades($gradeId);
    }

    public function deactivateClassActive($id)
    {
        return $this->classRoomService->deactivateClassActive($id);
    }
    public function deactivateClassOngoing($id)
    {
        return $this->classRoomService->deactivateClassOngoing($id);
    }

    public function reactivateClassActive($id)
    {
        return $this->classRoomService->reactivateClassActive($id);
    }

    public function reactivateClassOngoing($id)
    {
        return $this->classRoomService->reactivateClassOngoing($id);
    }

    public function update(Request $request, $id)
    {
        return $this->classRoomService->update($request, $id);
    }
    public function store(Request $request)
    {
        return $this->classRoomService->store($request);
    }

    // ========================
    // List all Class Rooms (Web)
    // ========================
    public function index()
    {
        return view('class_rooms.index');
    }

    // ========================
    // Show create form
    // ========================
    public function create()
    {
        return view('class_rooms.create');
    }

    // ========================
    // Show single Class Room
    // ========================
    public function show($id)
    {
        return view('class_rooms.show', compact('id'));
    }

    public function edit($id)
    {
        return view('class_rooms.edit', compact('id'));
    }


    public function schedule()
    {
        return view('class_rooms.schedule');
    }
    public function classCategoryAdd($id)
    {
        return view('class_rooms.add_class_category', compact('id'));
    }
}
