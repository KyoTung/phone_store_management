<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index()
    {
        $brand = Brand::orderBy('created_at', 'DESC')->get();
        return response()->json([
            'data'=>$brand,
            'status'=>200,
            'message'=>"Get all brands successfully"
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

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->save();

        return response()->json([
            'data'=>$brand,
            'message'=>'Brand added successfully',
            'status'=>200,
        ], status: 200);
    }
    public function show($id)
    {
        $brand = Brand::find($id);

        if($brand == null){
            return response()->json([
                'message'=>'Brand not found',
                'data'=>[],
                'status'=>404,
            ],  404);
        }

        return response()->json([
            'data'=>$brand,
            'status'=>200,
            'message'=>"Get brand successfully"
        ]);
    }

    public function update($id, Request $request)
    {
        $brand = Brand::find($id);

        if($brand == null){
            return response()->json([
                'message'=>'Brand not found',
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


        $brand->name = $request->name;
        $brand->save();

        return response()->json([
            'data'=>$brand,
            'message'=>'Brand updated successfully',
            'status'=>200,
        ], status: 200);
    }

    // delete a cate
    public function destroy($id)
    {
        $brand = Brand::find($id);

        if($brand == null){
            return response()->json([
                'message'=>'Brand not found',
                'data'=>[],
                'status'=>404,
            ],  404);
        }
        $brand->delete();

        return response()->json([
            'message'=>'Brand deleted successfully',
            'status'=>200,
        ], status: 200);
    }
}
