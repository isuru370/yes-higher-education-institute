<?php

namespace App\Http\Controllers;


use App\Services\StudentStudentStudentClassService;
use Illuminate\Http\Request;


class StudentStudentStudentClassController extends Controller
{
    protected $studentStudentStudentClassService;

    public function __construct(StudentStudentStudentClassService $studentStudentStudentClassService)
    {
        $this->studentStudentStudentClassService = $studentStudentStudentClassService;
    }

    public function readStudentClass(Request $request)
    {
        return $this->studentStudentStudentClassService->readStudentClass($request);
    }

    public function getStudentsByClassAndCategory($classId, $categoryId)
    {
        return $this->studentStudentStudentClassService->getStudentsByClassAndCategory($classId, $categoryId);
    }

    public function allDetailsGetStudentsByClassAndCategory($classId, $categoryId)
    {
        return $this->studentStudentStudentClassService->allDetailsGetStudentsByClassAndCategory($classId, $categoryId);
    }
    public function getStudentClassessDetails($student_id)
    {
        return $this->studentStudentStudentClassService->getStudentClassessDetails($student_id);
    }
    public function getStudentClassessFilterDetails($student_id)
    {
        return $this->studentStudentStudentClassService->getStudentClassessFilterDetails($student_id);
    }

    public function activateStudentClass($id)
    {
        return $this->studentStudentStudentClassService->activateStudentClass($id);
    }

    public function deactivateStudentClass($id)
    {
        return $this->studentStudentStudentClassService->deactivateStudentClass($id);
    }

    public function bulkDeactivateStudentClasses(Request $request)
    {
        return $this->studentStudentStudentClassService->bulkDeactivateStudentClasses($request);
    }

    public function toggleStudentClassStatus($id)
    {
        return $this->studentStudentStudentClassService->toggleStudentClassStatus($id);
    }

    public function bulkStore(Request $request)
    {
        return $this->studentStudentStudentClassService->bulkStore($request);
    }
    public function storeSingleStudentClass(Request $request)
    {
        return $this->studentStudentStudentClassService->storeSingleStudentClass($request);
    }
}
