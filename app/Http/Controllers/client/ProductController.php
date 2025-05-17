<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\DiscountCode;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $product = Product::orderBy('created_at', 'DESC')
            ->with('product_images')
            ->where('status', 1)
            ->get();

        return response()->json([
            'data'=>$product,
            'status'=>200,
            'message'=>"Get all products successfully"
        ]);
    }

    public function featuredProduct()
    {
        $product = Product::orderBy('created_at', 'DESC')
            ->with('product_images')
            ->where('status', 1)
            ->where('is_featured', 'yes')
            ->limit(5)
            ->get();

        return response()->json([
            'data'=>$product,
            'status'=>200,
            'message'=>"Get featured products successfully"
        ]);
    }

    public function categoryProduct( $id)
{
    $product = Product::orderBy('created_at', 'DESC')
        ->with('product_images')
        ->where('status', 1)
        ->where('category_id', $id)
        ->get();

    return response()->json([
        'data'=>$product,
        'status'=>200,
        'message'=>"Get products by category successfully"
    ]);
}

    public function brandProduct( $id)
    {
        $product = Product::orderBy('created_at', 'DESC')
            ->with('product_images')
            ->where('status', 1)
            ->where('brand_id', $id)
            ->get();

        return response()->json([
            'data'=>$product,
            'status'=>200,
            'message'=>"Get products by brand successfully"
        ]);
    }

    public function showDetail($id)
    {
        $product = Product::with('product_images')->find($id);

        if( $product == null){
            return response()->json([
                'message'=>'Product not found',
                'data'=>[],
                'status'=>404,
            ],  404);
        }

        return response()->json([
            'data'=>$product,
            'message'=>'Get a product successfully',
            'status'=>200,
        ], status: 200);
    }

    public function searchByName(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255'
        ]);

        $name = trim($request->input('name'));

        if (!$name) {
            return response()->json([
                'data' => [],
                'status' => 400,
                'message' => 'Thiếu từ khóa tìm kiếm'
            ]);
        }

        $products = Product::with('product_images')
            ->where('status', 1)
            ->where('name', 'like', "%$name%")
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json([
            'data' => $products,
            'status' => 200,
            'message' => 'Kết quả tìm kiếm'
        ]);
    }

    public function categories()
    {
        $cate = Category::orderBy('created_at', 'DESC')->get();

        return response()->json([
            'data'=>$cate,
            'status'=>200,
            'message'=>"Get all cate successfully"
        ]);
    }

    public function getCategory($id)
    {
        $cate = Category::find($id);

        return response()->json([
            'data'=>$cate,
            'status'=>200,
            'message'=>"Get cate successfully"
        ]);
    }

    public function brands()
    {
        $brands = Brand::orderBy('created_at', 'DESC')->get();

        return response()->json([
            'data'=>$brands,
            'status'=>200,
            'message'=>"Get all brands successfully"
        ]);
    }

    public function getBrand($id)
    {
        $cate = Brand::find($id);

        return response()->json([
            'data'=>$cate,
            'status'=>200,
            'message'=>"Get brand successfully"
        ]);
    }

    public function getDiscountCode()
    {
        $data = DiscountCode::orderBy('created_at', 'DESC')->get();
        return response()->json([
            'data' => $data,
            'status' => 200,
            'message' => "Get all discount code successfully"
        ]);
    }
}
