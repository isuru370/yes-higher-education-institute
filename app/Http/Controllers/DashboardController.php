<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemUser;
use App\Models\Teacher;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Get authenticated user
        $user = Auth::user();
        
        // Get user statistics
        $totalUsers = User::count();
        $activeSystemUsers = SystemUser::where('is_active', 1)->count();
        $totalStudents = Student::count();
        $totalActiveTeachers = Teacher::where('is_active', 1)->count();
        $totalOnGoinClasses = ClassRoom::where('is_ongoing',1)->count(); // Placeholder for ongoing classes count

        return view('dashboard.index', [
            'user' => $user,
            'totalUsers' => $totalUsers,
            'activeSystemUsers' => $activeSystemUsers,
            'totalStudents' => $totalStudents,
            'totalActiveTeachers' => $totalActiveTeachers,
            'totalOnGoinClasses' => $totalOnGoinClasses
        ]);
    }
}