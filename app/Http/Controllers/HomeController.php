<?php

namespace App\Http\Controllers;

use App\Models\GroupInvitation;
use App\Models\Course;
use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $course = Course::where('id', $user['course_id'])->first();

        return view(
            'groups.create',
            compact(
                'user',
                'course'
        ));
    }
}
