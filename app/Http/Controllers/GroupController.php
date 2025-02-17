<?php

namespace App\Http\Controllers;

use App\Domain\Group\GroupDomain;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::all();
        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        $courses = GroupDomain::buildCourseSelect();
        return view('groups.create', compact('courses'));
    }

    public function show(Request $request)
    {
        return redirect()->route('groups.index');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['owner_id'] = auth()->user()->id;

        $group = Group::create($data);
        return redirect()->route('groups.index');
    }
}
