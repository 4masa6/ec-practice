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
        $cart = Cart::instance(Auth::user()->id)->content(); // todo: 19_ユーザーIDからこれまで追加したカートの中身を取得

        $total = 0;

        foreach ($cart as $c) {
            $total += $c->qty * $c->price; // qty => 購入数 トータル金額を計算
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
        $user_shoppingcarts = DB::table('shoppingcart')->where('instance', Auth::user()->id)->get(); // todo: 19_現在までのユーザーが注文したカートを取得

        $count = $user_shoppingcarts->count(); // todo: 19_現在までのユーザーが注文したカートの数を取得
        $count += 1; // todo: 19_新しく追加するカートのIDを作成

        Cart::instance(Auth::user()->id)->store($count); // todo: 19_カートをDBに保存

        DB::table('shoppingcart')->where('instance', Auth::user()->id)->where('number', null)->update(['number' => $count, 'buy_flag' => true]); // todo: 19_購入処理

        Cart::instance(Auth::user()->id)->destroy();

        return redirect()->route('carts.index');
    }
}
