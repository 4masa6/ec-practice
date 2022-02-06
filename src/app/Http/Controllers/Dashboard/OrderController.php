<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use App\ShoppingCart;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->page != null ? $request->page : 1; // リクエストにページが含まれていれば使用し、無ければ1
        $code = $request->code != null ? $request->code : ""; // リクエストにコードが含まれていれば使用し、無ければ空
        $sort = $request->sort;
        $orders = ShoppingCart::getOrders($code); // 注文番号で購入履歴を取得する
        $total = count($orders);
        $orders = new LengthAwarePaginator(array_slice($orders, ($page - 1) * 15, 15), $total, 15, $page, array('path' => $request->url()));

        return view('dashboard.orders.index', compact('orders', 'total', 'sort', 'code'));
    }
}
