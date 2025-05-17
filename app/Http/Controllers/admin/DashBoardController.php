<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

class DashBoardController extends Controller
{
    public function totalProducts()
    {
        // Giả sử cột status = 1 là còn hàng
        $totalQuantity = Product::where('status', 1)->sum('quantity');

        return response()->json([
            'total_quantity' => $totalQuantity,
            'message' => 'Lấy tổng số lượng sản phẩm còn hàng thành công',
            'status' => 200
        ], 200);
    }

    // Lấy tổng số tiền đã bán của các đơn hàng đã hoàn thành (completed)
    public function totalPaidOrders()
    {
        $totalRevenue = Order::where('status', 'completed')->sum('grand_total');

        return response()->json([
            'total_revenue' => $totalRevenue,
            'message' => 'Lấy tổng số tiền đã bán từ các đơn hàng hoàn thành thành công',
            'status' => 200
        ], 200);
    }
    // Lấy tổng số user
    public function totalUser()
    {
        $totalUser = User::count();

        return response()->json([
            'total_user' => $totalUser,
            'message' => 'Lấy tổng số user thành công',
            'status' => 200
        ], 200);
    }

    // Lấy tổng số lượng sản phẩm đã bán (chỉ tính các đơn hàng completed)
    public function totalSaleProduct()
    {
        // Lọc các order_items thuộc các đơn đã completed
        $totalSold = OrderItem::whereHas('order', function($query) {
            $query->where('status', 'completed');
        })->sum('qty');

        return response()->json([
            'total_sold_product_quantity' => $totalSold,
            'message' => 'Lấy tổng số lượng sản phẩm đã bán thành công',
            'status' => 200
        ], 200);
    }

    // Lấy 5 sản phẩm bán chạy nhất (theo tổng số lượng đã bán, chỉ tính các đơn hàng completed)
    public function top5SaleProducts()
    {
        // Lấy top 5 product_id có tổng qty lớn nhất từ các order completed
        $topProducts = OrderItem::select('product_id')
            ->selectRaw('SUM(qty) as total_sold')
            ->whereHas('order', function($query) {
                $query->where('status', 'completed');
            })
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product')
            ->take(5)
            ->get();

        // Trả về thông tin sản phẩm kèm tổng số lượng đã bán
        $data = $topProducts->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'name' => $item->product ? $item->product->name : null,
                'total_sold' => $item->total_sold,
                'product' => $item->product // Có thể trả về chi tiết sản phẩm nếu cần
            ];
        });

        return response()->json([
            'data' => $data,
            'message' => 'Lấy top 5 sản phẩm bán chạy nhất thành công',
            'status' => 200
        ], 200);
    }
}
