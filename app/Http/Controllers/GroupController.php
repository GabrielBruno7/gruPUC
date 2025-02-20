<?php

namespace App\Http\Controllers;

use App\Domain\Group\GroupDomain;
use App\Models\Course;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::with(['course', 'owner'])->get(); // Carregando os relacionamentos

        return view('groups.index', compact('groups'));
    }
    
    

    public function create()
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

    public function show(Request $request)
    {
        return redirect()->route('groups.index');
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'course_id' => 'required|uuid|exists:courses,id',
            'period' => 'required|integer',
        ]);

        $validatedData['owner_id'] = Auth::id();
    
        $group = Group::create($validatedData);

        return redirect()->route('groups.index');
    }
}
