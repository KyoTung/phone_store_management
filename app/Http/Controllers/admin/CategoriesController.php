<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    public function index()
    {
     $category = Category::orderBy('created_at', 'DESC')->get();
     return response()->json([
         'data'=>$category,
         'status'=>200,
         'message'=>"Get all categories successfully"
     ]);
    }

    public function store(Request $request)
    {
      $validator = Validator::make($request->all(),[
          'name'=>'required|'
      ]);

        if($validator->fails()){
            return response()->json([
                'status'=>403,
                'errors'=>$validator->errors(),
            ], status: 403);
        }

        $category = new Category();
        $category->name = $request->name;
        $category->save();

        return response()->json([
            'data'=>$category,
            'message'=>'Category added successfully',
            'status'=>200,
        ], status: 200);
    }
    public function show($id)
    {
        $category = Category::find($id);

        if($category == null){
            return response()->json([
                'message'=>'Category not found',
                'data'=>[],
                'status'=>404,
            ],  404);
        }

        return response()->json([
            'data'=>$category,
            'status'=>200,
            'message'=>"Get category successfully"
        ]);
    }
    public function update($id, Request $request)
    {
        $category = Category::find($id);

        if($category == null){
            return response()->json([
                'message'=>'Category not found',
                'data'=>[],
                'status'=>404,
            ],  404);
        }

        $validator = Validator::make($request->all(),[
            'name'=>'required|'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=>403,
                'errors'=>$validator->errors(),
            ], status: 403);
        }

        $category->name = $request->name;
        $category->save();

        return response()->json([
            'data'=>$category,
            'message'=>'Category updated successfully',
            'status'=>200,
        ], status: 200);
    }

    // delete a cate
    public function destroy($id)
    {
        $category = Category::find($id);

        if($category == null){
            return response()->json([
                'message'=>'Category not found',
                'data'=>[],
                'status'=>404,
            ],  404);
        }
        $category->delete();

        return response()->json([
        'message'=>'Category deleted successfully',
        'status'=>200,
          ], status: 200);
    }
}
