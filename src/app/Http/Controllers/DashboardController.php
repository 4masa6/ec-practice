<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\ShoppingCart;

class DashboardController extends Controller
{
    public function index(Request $request) {
        $page     = $request->page != null ? $request->page : 1;
        $sort     = $request->sort;
        $billings = []; // 売上の情報を格納する配列
        if ($request->sort == 'month') { // todo: 65_sortが'月'ならば、ShoppingCartモデルのgetMonthlyBillingsメソッドを使う
            $billings = ShoppingCart::getMonthlyBillings();
        } else { // todo: 65_sortが'月'ならば、ShoppingCartモデルのgetDailyBillingsメソッドを使う
            $billings = ShoppingCart::getDailyBllings();
        }
//        dd($billings);
        $total     = count($billings);
        $paginator = new LengthAwarePaginator(array_slice($billings, ($page - 1), 15), $total, 15, $page, ['path' => 'dashboard']);

        return view('dashboard.index', compact('billings', 'total', 'paginator', 'sort'));
    }
}
