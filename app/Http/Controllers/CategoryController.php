<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = \App\Models\Category::latest()->get();
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" data-id="'.$row->id.'" class="edit btn btn-primary btn-sm editCategory">Edit</a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="'.$row->id.'" class="btn btn-danger btn-sm deleteCategory">Delete</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('categories.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required|max:255|unique:categories,slug,' . $request->category_id,
        ]);

        \App\Models\Category::updateOrCreate(
            ['id' => $request->category_id],
            [
                'name' => $request->name,
                'slug' => \Illuminate\Support\Str::slug($request->slug),
                'description' => $request->description
            ]
        );        

        return response()->json(['success'=>'Category saved successfully.']);
    }

    public function edit($id)
    {
        $category = \App\Models\Category::find($id);
        return response()->json($category);
    }

    public function destroy($id)
    {
        \App\Models\Category::find($id)->delete();
        return response()->json(['success'=>'Category deleted successfully.']);
    }
}
