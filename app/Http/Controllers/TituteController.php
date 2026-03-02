<?php

namespace App\Http\Controllers;

use App\Services\TituteService;
use Illuminate\Http\Request;

class TituteController extends Controller
{
    protected $tituteService;

    public function __construct(TituteService $tituteService)
    {
        $this->tituteService = $tituteService;
    }

    public function checkTute(Request $request, int $studentId, int $classCategoryStudentClassId)
    {
        return $this->tituteService->checkTute($request, $studentId, $classCategoryStudentClassId);
    }

    public function getStudentWithAllTutes(int $studentId, int $classCategoryStudentClassId)
    {
        return $this->tituteService->getStudentWithAllTutes($studentId, $classCategoryStudentClassId);
    }

    public function createTitute(Request $request)
    {
        return $this->tituteService->store($request);
    }

    public function toggleStatus(int $id)
    {
        return $this->tituteService->toggleStatus($id);
    }

    public function readClassWiseTute($customId)
    {
        return $this->tituteService->readClassWiseTute($customId);
    }
}
