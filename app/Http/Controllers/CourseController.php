<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = \App\Models\Course::with('category', 'teacher')->latest()->get();
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('category_name', function($row){
                    return $row->category ? $row->category->name : '';
                })
                ->addColumn('teacher_name', function($row){
                    return $row->teacher ? $row->teacher->name : '';
                })
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" data-id="'.$row->id.'" class="edit btn btn-primary btn-sm editCourse">Edit</a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="'.$row->id.'" class="btn btn-danger btn-sm deleteCourse">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        $categories = \App\Models\Category::all();
        $teachers = \App\Models\User::role('teacher')->get(); // Needs spatie permission
        
        return view('courses.index', compact('categories', 'teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'slug' => 'required|max:255|unique:courses,slug,' . $request->course_id,
            'category_id' => 'required',
            'teacher_id' => 'required',
        ]);

        \App\Models\Course::updateOrCreate(
            ['id' => $request->course_id],
            [
                'title' => $request->title,
                'slug' => \Illuminate\Support\Str::slug($request->slug),
                'description' => $request->description,
                'category_id' => $request->category_id,
                'teacher_id' => $request->teacher_id,
                'status' => $request->status ?? 'active',
            ]
        );        

        return response()->json(['success'=>'Course saved successfully.']);
    }

    public function edit($id)
    {
        $course = \App\Models\Course::find($id);
        return response()->json($course);
    }

    public function destroy($id)
    {
        \App\Models\Course::find($id)->delete();
        return response()->json(['success'=>'Course deleted successfully.']);
    }
}
