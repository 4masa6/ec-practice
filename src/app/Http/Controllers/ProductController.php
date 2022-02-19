<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index(Request $request) {

        // todo: 14_ソートを行う処理を記述
        $sort_query = [];
        $sorted     = "";

        if ($request->direction !== null) {
            $sort_query = $request->direction;
            $sorted     = $request->sort;
        } else if ($request->sort !== null) {
            $slices                 = explode(' ', $request->sort);
            $sort_query[$slices[0]] = $slices[1];
            $sorted                 = $request->sort;
        }

        // todo: 13_カテゴリーの絞り込みを行う
        if ($request->category !== null) { // 13_リクエストにカテゴリーが含まれていたら ⇒ つまり、viewファイル側からカテゴリーidのデータが送られてきたら。
            $products    = Product::where('category_id', $request->category)->sortable($sort_query)->paginate(15); // todo: 14_ソートのクエリを追加
            $total_count = Product::where('category_id', $request->category)->count();   // 全体の件数も取得しておく（表示用）
            $category    = Category::find($request->category);
        } else {                           // 13_リクエストにカテゴリーが含まれていなければ ⇒ つまり、何もカテゴリーが絞り込まれていなければ。
            $products    = Product::sortable($sort_query)->paginate(15); // todo: 14_ソートのクエリを追加
            $total_count = "";
            $category    = null;
        }

        // todo: 14_ソートの選択肢の配列を用意（view側で使用）
        $sort = [
            '並び替え'    => '',
            '価格の安い順'  => 'price asc',
            '価格の高い順'  => 'price desc',
            '出品の古い順'  => 'updated_at asc',
            '出品の新しい順' => 'updated_at desc'
        ];

        $categories = Category::all(); // 13_カテゴリーのデータをすべて取得してビューに送る

        $major_category_names = Category::pluck('major_category_name')->unique(); // 13_全カテゴリのデータからmajor_category_nameのカラムのみを取得し、重複を削除

        return view('products.index', compact('products', 'category', 'categories', 'major_category_names', 'total_count', 'sort', 'sorted'));
    }

    public function favorite(Product $product) {
        $user = Auth::user(); // ログインユーザーを取得

        if ($user->hasFavorited($product)) { // ユーザーが商品をお気に入りに登録していたら
            $user->unfavorite($product);     // お気に入りをはずす
        } else {                             // ユーザーが商品をお気に入りに登録していなければ
            $user->favorite($product);       // お気に入りをする
        }

        return redirect()->route('products.show', $product);
    }

    public function show(Product $product) {
        $reviews = $product->reviews()->get(); // todo: $productからリレーションで紐付いているreviewsを取得する

        return view('products.show', compact('product', 'reviews')); // todo: viewファイルに$productと$reviewsを渡している
    }
}
