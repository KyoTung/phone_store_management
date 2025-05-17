<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Container\Attributes\DB;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function saveOrder(Request $request)
    {
        try {
            if (empty($request->cart)) {
                return response()->json([
                    "message" => "Giỏ hàng trống",
                    "status" => 400
                ], 400);
            }

            \Illuminate\Support\Facades\DB::beginTransaction();

            // Kiểm tra tồn kho
            foreach ($request->cart as $item) {
                $product = \App\Models\Product::find($item['product_id']);
                if (!$product || $product->quantity < $item['qty']) {
                    throw new \Exception("Sản phẩm {$item['name']} không đủ số lượng tồn kho.");
                }
            }

            // Tính lại sub_total (đảm bảo client không gửi sai)
            $sub_total = 0;
            foreach ($request->cart as $item) {
                $sub_total += $item['qty'] * $item['price'];
            }


            $shipping = $request->shipping ?? 0;
            $discount = $request->discount ?? 0;


            $grand_total = $sub_total - ($sub_total * $discount) + $shipping;


            // Tạo order
            $order = new Order();
            $order->user_id = $request->user()->id;
            $order->sub_total = $sub_total;
            $order->grand_total = $grand_total;
            $order->shipping = $shipping;
            $order->discount = $discount;
            $order->payment_status = $request->payment_status;
            $order->status = $request->status;
            $order->name = $request->name;
            $order->email = $request->email;
            $order->phone = $request->phone;
            $order->address = $request->address;
            $order->city = $request->city;
            $order->district = $request->district;
            $order->commune = $request->commune;
            $order->payment_method = $request->payment_method;
            $order->save();

            // Lưu các order items và cập nhật tồn kho, trạng thái sản phẩm
            foreach ($request->cart as $item) {
                $orderItem = new OrderItem();
                $orderItem->price = $item['qty'] * $item['price'];
                $orderItem->unit_price = $item['price'];
                $orderItem->qty = $item['qty'];
                $orderItem->product_id = $item['product_id'];
                $orderItem->order_id = $order->id;
                $orderItem->name = $item['name'];
                $orderItem->ram = $item['ram'];
                $orderItem->storage_capacity = $item['storage_capacity'];
                $orderItem->save(); // Lưu từng item

                // Trừ số lượng và cập nhật trạng thái sản phẩm
                $product = \App\Models\Product::find($item['product_id']);
                $product->quantity -= $item['qty'];
                // Nếu số lượng về 0 thì đặt status hết hàng
                if ($product->quantity <= 0) {
                    $product->quantity = 0;
                    $product->status = 0; // 0: hết hàng, 1: còn hàng
                }
                $product->save();
            }

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                "message" => "Thanh toán thành công",
                "order_id" => $order->id,
                "status" => 200
            ], 200);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();

            return response()->json([
                "message" => "Lỗi thanh toán: " . $e->getMessage(),
                "status" => 500
            ], 500);
        }
    }

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
            "message"=>"Get orders succesfully",
            "status"=>200
        ], 200);
    }

    public function getOrderHistory($id)
    {
        try {
            // Sử dụng get() để thực sự lấy dữ liệu từ database
            $orders = Order::where('user_id', $id)
                ->with(['items.product']) // Thêm relation product nếu cần
                ->orderBy('created_at', 'DESC') // Sắp xếp theo thời gian tạo mới nhất
                ->get();

            // Kiểm tra nếu không có đơn hàng
            if ($orders->isEmpty()) {
                return response()->json([
                    'data' => [],
                    'message' => 'No orders found for this user',
                    'status' => 404
                ], 404);
            }

            return response()->json([
                'data' => $orders,
                'message' => 'Get all orders history successfully',
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'data' => [],
                'message' => 'Error retrieving order history: ' . $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function cancelOrder($id)
    {
        $order = Order::with('items', 'items.product')->find($id);

        if ($order == null) {
            return response()->json([
                'data' => [],
                "message" => "Order not found",
                "status" => 404
            ], 404);
        }

        // Nếu đơn hàng đã bị hủy rồi
        if ($order->status === 'cancelled') {
            return response()->json([
                'data' => $order,
                "message" => "Order already cancelled",
                "status" => 200
            ], 200);
        }

        // Chỉ cho hủy nếu trạng thái hiện tại là pending hoặc processing
        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'data' => $order,
                "message" => "Cannot cancel order in status: {$order->status}",
                "status" => 400
            ], 400);
        }

        // Hoàn lại số lượng sản phẩm
        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product) {
                $product->quantity += $item->qty;
                $product->save();
            }
        }

        $order->status = 'cancelled';
        $order->save();

        return response()->json([
            'data' => $order,
            "message" => "Cancelled order successfully",
            "status" => 200
        ], 200);
    }

    public function shippedOrder( $id)
    {
        $order = Order::with('items', 'items.product')->find($id);

        if($order == null){
            return response()->json([
                'data'=>[],
                "message"=>"Order not found",
                "status"=>404
            ], 404);
        }

        $order->status = 'completed';
        $order->save();

        return response()->json([
            'data'=>$order,
            "message"=>"Shipped orders succesfully",
            "status"=>200
        ], 200);
    }

    public function refundedOrder($id)
    {
        $order = Order::with('items', 'items.product')->find($id);

        if ($order == null) {
            return response()->json([
                'data' => [],
                "message" => "Order not found",
                "status" => 404
            ], 404);
        }

        // Chỉ cộng lại số lượng nếu trạng thái trước đó không phải là 'refunded'
        //Chỉ cộng lại số lượng khi trạng thái cũ KHÁC 'refunded', để đảm bảo mỗi đơn hàng chỉ
        // được hoàn kho đúng 1 lần duy nhất khi hoàn tiền.
        if ($order->status !== 'refunded') {
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->quantity += $item->qty;
                    // Nếu sản phẩm hết hàng trước đó thì cập nhật lại status là còn hàng (nếu quantity > 0)
                    if ($product->quantity > 0 && $product->status == 0) {
                        $product->status = 1;
                    }
                    $product->save();
                }
            }
        }

        $order->status = 'refunded';
        $order->save();

        return response()->json([
            'data' => $order,
            "message" => "Refunded orders successfully",
            "status" => 200
        ], 200);
    }
}
