<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //show all order
    public function index(Request $request)
    {
      $orders = Order::orderBy('created_at', 'DESC')->get();

      return response()->json([
        'data'=>$orders,
        "message"=>"Get all orders succesfully",
        "status"=>200
        ], 200);
    }

   //show detail one order
    public function show(Request $request, $id)
    {
        $order = Order::with('items', 'items.product')->find($id);

        if($order == null){
            return response()->json([
                'data'=>[],
                "message"=>"Order not found",
                "status"=>404
            ], 404);

        }

        return response()->json([
            'data'=>$order,
            "message"=>"Get all orders succesfully",
            "status"=>200
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $order = Order::with('items', 'items.product')->find($id);

        if ($order == null) {
            return response()->json([
                'data' => [],
                "message" => "Order not found",
                "status" => 404
            ], 404);
        }

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Nếu yêu cầu chuyển sang cancelled, kiểm tra trạng thái hợp lệ
        if ($newStatus === 'cancelled') {
            if (!in_array($oldStatus, ['pending', 'processing'])) {
                return response()->json([
                    'data' => [],
                    "message" => "Cannot cancel order in current status: $oldStatus",
                    "status" => 400
                ], 400);
            }
            // Hoàn lại tồn kho
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->quantity += $item->qty;
                    $product->status = ($product->quantity > 0) ? 1 : 0;
                    $product->save();
                }
            }
        }

        // Nếu chuyển sang trạng thái refunded và trạng thái cũ KHÁC refunded thì hoàn lại tồn kho
        if ($newStatus === 'refunded' && $oldStatus !== 'refunded') {
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->quantity += $item->qty;
                    if ($product->quantity > 0) {
                        $product->status = 1; // Còn hàng
                    }
                    $product->save();
                }
            }
        }

        $order->status = $newStatus;
        $order->payment_status = $request->payment_status;
        $order->save();

        return response()->json([
            'data' => $order,
            "message" => "Update orders successfully",
            "status" => 200
        ], 200);
    }
}
