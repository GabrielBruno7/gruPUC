<?php

namespace App\Http\Controllers;

use App\Domain\Group\GroupDomain;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $courses = GroupDomain::buildCourseSelect();
        return view('groups.create', compact('courses'));
    }
}
