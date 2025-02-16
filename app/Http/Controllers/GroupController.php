<?php

namespace App\Http\Controllers;

use App\Domain\Group\GroupDomain;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        dd("Oi");
        $groups = Group::all();
        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        dd($this);
        $courses = GroupDomain::buildCourseSelect();
        return view('groups.create', compact('courses'));
    }

    public function show(Request $request)
    {
        $group = Group::create($request->all());
        return redirect()->route('groups.index');
    }

    public function store(Request $request)
    {
        $group = Group::create($request->all());
        return redirect()->route('groups.index');
    }
}
