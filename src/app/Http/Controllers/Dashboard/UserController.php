<?php

namespace App\Http\Controllers\Dashboard;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->keyword !== null) { // 検索キーワードが設定されていれば
            $keyword = rtrim($request->keyword); // rtrim()：文字列の最後から空白を取り除く
            if (is_int($request->keyword)) { // キーワードが数字であれば
                $keyword = (string)$keyword; //文字列にキャスト
            }
            $users = User::where('name', 'like', "%{$keyword}%") // LIKE句で検索
                ->orwhere('email', 'like', "%{$keyword}%")
                ->orwhere('address', 'like', "%{$keyword}%")
                ->orwhere('postal_code', 'like', "%{$keyword}%")
                ->orwhere('phone', 'like', "%{$keyword}%")
                ->orwhere('id', "{$keyword}")->paginate(15);
        } else {
            $users = User::paginate(15);
            $keyword = "";
        }

        return view('dashboard.users.index', compact('users', 'keyword'));

    }

    public function destroy(User $user)
    {
        // todo: 61_ユーザーの削除フラグがONならOFFに、OFFならONに
        if ($user->deleted_flag) {
            $user->deleted_flag = false;
        } else {
            $user->deleted_flag = true;
        }

        $user->update();

        return redirect()->route('dashboard.users.index');

    }
}
