<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();
        $course = Course::where('id', $user['course_id'])->first();

        return view(
            'groups.create', //sera que teria que trazer a lista e nao o cadastro? 
            compact(
                'user',
                'course'
        ));
    }
}
