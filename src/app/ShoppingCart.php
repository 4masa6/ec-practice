<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\User;

class ShoppingCart extends Model
{
    public static function getDailyBllings()
    {

        $count = DB::table('shoppingcart')->count();
        if ($count == 0) { // 決済の数が0なら終了
            return [];
        }

        $recent_date = DB::table('shoppingcart')->latest('created_at')->first()->created_at; // 最新のカートの作成日時
        $recent_date = new Carbon($recent_date);
        $recent_date->addDays(1); // 最新のカートの作成日時 に１日追加

        $latest_date = DB::table('shoppingcart')->first()->created_at; // 一番古いカートの作成日時
        $latest_date = new Carbon($latest_date);

        $billings = [];

        // todo: 65_古いカートの日付から最新の日付までループ => 日数分繰り返す処理
        while ($recent_date->format('Y-m-d') != $latest_date->format('Y-m-d')) {
            $date = $latest_date->format('Y-m-d');
            $query = DB::table('shoppingcart')->whereDate('created_at', '=', $date);

            // 売上の情報を配列$billingsに格納する
            $billings[] = [
                'created_at' => $date,
                'total' => $query->sum('price_total'),
                'count' => $query->count(),
                'avg' => round($query->avg('price_total'), 1)
            ];
            $latest_date->addDays(1);
        }

        return $billings;
    }

    public static function getMonthlyBillings()
    {
        $recent_date = DB::table('shoppingcart')->latest('created_at')->first()->created_at;
        $recent_date = new Carbon($recent_date);
        $recent_date->addMonths(1);

        $latest_date = DB::table('shoppingcart')->first()->created_at;
        $latest_date = new Carbon($latest_date);

        $billings = [];

        while ($recent_date->format('Y-m') != $latest_date->format('Y-m')) {
            $date = $latest_date->format('Y-m');
            $query = DB::table('shoppingcart')->whereYear('created_at', '=', $latest_date->year)->whereMonth('created_at', '=', $latest_date->month);

            $billings[] = [
                'created_at' => $date,
                'total' => $query->sum('price_total'),
                'count' => $query->count(),
                'avg' => round($query->avg('price_total'), 1)
            ];
            $latest_date->addMonths(1);
        }

        return $billings;
    }

    /*
     * 注文番号で購入履歴を取得する
     */
    public static function getOrders($code)
    {
        $shoppingcarts = DB::table('shoppingcart')->where("code", 'like', "%{$code}%")->get();

        $orders = [];

        foreach($shoppingcarts as $order) {
            $orders[] = [
                'created_at' => $order->created_at,
                'total' => $order->price_total,
                'user_name' => User::find($order->instance)->name,
                'code' => $order->code
            ];
        }

        return $orders;
    }

    /*
     * 現在のユーザーの購入履歴を取得する
     */
    public static function getCurrentUserOrders($user_id)
    {
        $shoppingcarts = DB::table('shoppingcart')->where("instance", "{$user_id}")->get();

        $orders = [];

        foreach($shoppingcarts as $order) {
            $orders[] = [
                'id' => $order->number,
                'created_at' => $order->updated_at,
                'total' => $order->price_total,
                'user_name' => User::find($order->instance)->name,
                'code' => $order->code
            ];
        }

        return $orders;
    }
}
