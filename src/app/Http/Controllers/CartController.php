<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{

    /*
     * 現在カートに入っている商品一覧とこれまで購入した商品履歴を表示する
     */
    public function index()
    {
        $cart = Cart::instance(Auth::user()->id)->content(); // 19_ユーザーIDからこれまで追加したカートの中身を取得

        $total = 0;

        foreach ($cart as $c) {
            if($c->options->carriage) { // todo: 67_送料フラグがONであれば
                $total += ($c->qty * ($c->price + env('CARRIAGE'))); // 送料をプラス
            } else {
                $total += $c->qty * $c->price;
            }
        }

        return view('carts.index', compact('cart', 'total'));
    }

    /**
     * カートに商品を追加する
     */
    public function store(Request $request)
    {
        Cart::instance(Auth::user()->id)->add(
            [
                'id' => $request->id,
                'name' => $request->name,
                'qty' => $request->qty,
                'price' => $request->price,
                'weight' => $request->weight,
                'options' => [
                    'carriage' => $request->carriage
                ]
            ]
        );

        return redirect()->route('products.show', $request->get('id'));
    }

    /*
     * 過去の商品履歴（カートの履歴）を表示できるようにする
     */
    public function show($id)
    {
        // shoppingcartテーブルのモデルを作成していないため、クエリビルダでデータを取得
        $cart = DB::table('shoppingcart')->where('instance', Auth::user()->id)->where('identifier', $count)->get();

        return view('carts.show', compact('cart'));
    }

    /*
     * カートの中身を更新する処理
     */
    public function update(Request $request)
    {
        if ($request->input('delete')) { // 削除のパラメーターなら削除する
            Cart::instance(Auth::user()->id)->remove($request->input('id')); // todo: 19_shoppingcartでは、Cart::remove()に削除したいカート内の商品IDを渡すことで、カートから削除することができる
        } else {
            Cart::instance(Auth::user()->id)->update($request->input('id'), $request->input('qty')); // todo: 19_商品の個数を$request->input('qty')の値へ更新
        }

        return redirect()->route('carts.index');
    }

    /*
     * カートの商品を購入する処理
     */
    public function destroy(Request $request)
    {
        $user_shoppingcarts = DB::table('shoppingcart')->get(); // todo: 65_すべてのユーザーが現在まで注文したカートを取得
        $number = DB::table('shoppingcart')->where('instance', Auth::user()->id)->count(); // todo: 65_ログインユーザーが現在まで注文したカートの数を取得

        $count = $user_shoppingcarts->count(); // todo: 65_すべてのユーザーが現在まで注文したカートの数

        $count += 1;
        $number += 1;
        $cart = Cart::instance(Auth::user()->id)->content(); // todo: 65_ログインユーザーの今回購入するカート

        $price_total = 0;
        $qty_total = 0;

        foreach ($cart as $c) {
            if ($c->options->carriage) { // todo: 65_オプションで送料があれば
                $price_total += ($c->qty * ($c->price + 800)); // 送料込みの値段
            } else {
                $price_total += $c->qty * $c->price;
            }
            $qty_total += $c->qty; // todo: 65_ qty：購入個数
        }

        Cart::instance(Auth::user()->id)->store($count); // todo: 65_カートをDBのshoppingcartテーブルに保存

        // todo: 65_購入処理（shoppingcartテーブルを更新）
        DB::table('shoppingcart')->where('instance', Auth::user()->id)
            ->where('number', null)
            ->update(
                [
                    'code' => substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 10),
                    'number' => $number,
                    'price_total' => $price_total,
                    'qty' => $qty_total,
                    'buy_flag' => true,
                    'updated_at' => date("Y/m/d H:i:s")
                ]
            );

        $pay_jp_secret = env('PAYJP_SECRET_KEY');
        \Payjp\Payjp::setApiKey($pay_jp_secret);

        $user = Auth::user();

        // todo: 73_決済処理
        $res = \Payjp\Charge::create(
            [
                "customer" => $user->token,
                "amount" => $price_total,
                "currency" => 'jpy'
            ]
        );

        Cart::instance(Auth::user()->id)->destroy();

        return redirect()->route('carts.index');
    }
}
