<?php

namespace App\Http\Controllers;

use App\User;
use App\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\ShoppingCart;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{
    public function mypage()
    {
        $user = Auth::user();

        return view('users.mypage', compact('user'));
    }

    public function show(User $user)
    {
        //
    }

    public function edit(User $user)
    {
        $user = Auth::user();

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $user = Auth::user();

        $user->name = $request->input('name') ? $request->input('name') : $user->name;
        $user->email = $request->input('email') ? $request->input('email') : $user->email;
        $user->postal_code = $request->input('postal_code') ? $request->input('postal_code') : $user->postal_code;
        $user->address = $request->input('address') ? $request->input('address') : $user->address;
        $user->phone = $request->input('phone') ? $request->input('phone') : $user->phone;
        $user->update();

        return redirect()->route('mypage');
    }

    public function edit_address()
    {
        $user = Auth::user();

        return view('users.edit_address', compact('user'));
    }

    public function edit_password()
    {
        return view('users.edit_password');
    }

    public function update_password(Request $request)
    {
        $user = Auth::user();

        if ($request->input('password') == $request->input('password_confirmation')) { // 16_POSTされてきたパスワードと確認パスワードが同じなら更新
            $user->password = bcrypt($request->input('password'));
            $user->update();
        } else { // 16_POSTされてきたパスワードと確認パスワードが異なればパスワード編集画面へリダイレクト
            return redirect()->route('mypage.edit_password');
        }

        return redirect()->route('mypage');
    }

    public function favorite()
    {
        $user = Auth::user();

        $favorites = $user->favorites(Product::class)->get(); // todo: 17_ユーザーがお気に入りした商品を取得（laravel-favoriteの機能）

        return view('users.favorite', compact('favorites'));
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();

        if ($user->deleted_flag) {
            $user->deleted_flag = false;
        } else {
            $user->deleted_flag = true;
        }

        $user->update();

        Auth::logout();

        return redirect('/');
    }

    /*
     * 購入履歴の一覧を表示
     */
    public function cart_history_index(Request $request)
    {
        /*
         *
         */

        $page = $request->page != null ? $request->page : 1;
        $user_id = Auth::user()->id;
        $billings = ShoppingCart::getCurrentUserOrders($user_id); // shoppingcartテーブルから現在のユーザーの購入情報を取得
        $total = count($billings);
        $billings = new LengthAwarePaginator(array_slice($billings, ($page - 1) * 15, 15), $total, 15, $page, array('path' => $request->url())); // 15件でページングするための処理
        // [表示するコレクション] = new LengthAwarePaginator([表示するコレクション], [コレクションの大きさ], [1ページ当たりの表示数], [現在のページ番号], [オプション(ここでは"ページの遷移先パス")]);

        return view('users.cart_history_index', compact('billings', 'total'));
    }

    /*
     * 購入履歴の詳細表示
     */
    public function cart_history_show(Request $request)
    {
        $num = $request->num; // 注文の番号

        $user_id = Auth::user()->id;

        $cart_info = DB::table('shoppingcart')->where('instance', $user_id)->where('number', $num)->get()->first();

        Cart::instance($user_id)->restore($num);

        $cart_contents = Cart::content();

        Cart::instance($user_id)->store($num);

        Cart::destroy();

        DB::table('shoppingcart')->where('instance', $user_id)
            ->where('number', null)
            ->update(
                [
                    'code' => $cart_info->code,
                    'number' => $num,
                    'price_total' => $cart_info->price_total,
                    'qty' => $cart_info->qty,
                    'buy_flag' => $cart_info->buy_flag,
                    'updated_at' => $cart_info->updated_at
                ]
            );

        return view('users.cart_history_show', compact('cart_contents', 'cart_info'));
    }

}
