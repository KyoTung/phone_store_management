<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscountCodeController extends Controller
{
    public function index()
    {
        $data = DiscountCode::orderBy('created_at', 'DESC')->get();
        return response()->json([
            'data' => $data,
            'status' => 200,
            'message' => "Get all discount code successfully"
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'value' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 403,
                'errors' => $validator->errors(),
            ], 403);
        }

        $discount = new DiscountCode();
        $discount->name = $request->name;
        $discount->value = $request->value;
        $discount->start_date = $request->start_date;
        $discount->end_date = $request->end_date;
        $discount->save();

        return response()->json([
            'data' => $discount,
            'message' => 'Discount code added successfully',
            'status' => 200,
        ], 200);
    }

    public function show($id)
    {
        $discount = DiscountCode::find($id);

        if ($discount == null) {
            return response()->json([
                'message' => 'Discount code not found',
                'data' => [],
                'status' => 404,
            ], 404);
        }

        return response()->json([
            'data' => $discount,
            'status' => 200,
            'message' => "Get discount code successfully"
        ]);
    }

    public function update($id, Request $request)
    {
        $discount = DiscountCode::find($id);

        if ($discount == null) {
            return response()->json([
                'message' => 'Discount code not found',
                'data' => [],
                'status' => 404,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'value' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 403,
                'errors' => $validator->errors(),
            ], 403);
        }

        $discount->name = $request->name;
        $discount->value = $request->value;
        $discount->start_date = $request->start_date;
        $discount->end_date = $request->end_date;
        $discount->save();

        return response()->json([
            'data' => $discount,
            'message' => 'Discount code updated successfully',
            'status' => 200,
        ], 200);
    }

    public function destroy($id)
    {
        $discount = DiscountCode::find($id);

        if ($discount == null) {
            return response()->json([
                'message' => 'Discount code not found',
                'data' => [],
                'status' => 404,
            ], 404);
        }
        $discount->delete();

        return response()->json([
            'message' => 'Discount code deleted successfully',
            'status' => 200,
        ], 200);
    }
}
