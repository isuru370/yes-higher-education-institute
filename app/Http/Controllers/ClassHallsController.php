<?php

namespace App\Http\Controllers;


use App\Services\ClassHallsService;
use Illuminate\Http\Request;

class ClassHallsController extends Controller
{

    protected $classHallService;

    public function __construct(ClassHallsService $classHallsService)
    {
        $this->classHallService = $classHallsService;
    }
    public function fetchDropdownHalls()
    {
        return $this->classHallService->fetchDropdownHalls();
    }
    public function fetchClassHall($id)
    {
        return $this->classHallService->fetchClassHall($id);
    }

    public function fetchClassHalls()
    {
        return $this->classHallService->fetchClassHalls();
    }

    public function updateClassHall(Request $request, $id)
    {
        return $this->classHallService->updateClassHall($request, $id);
    }
    public function storeClassHall(Request $request)
    {
        return $this->classHallService->storeClassHall($request);
    }

    public function indexPage()
    {
        return view('class_halls.index');
    }
}
