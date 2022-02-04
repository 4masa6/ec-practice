<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        // todo: 13_カテゴリーの絞り込みを行う
        if ($request->category !== null) { // todo: 13_リクエストにカテゴリーが含まれていたら ⇒ つまり、viewファイル側からカテゴリーidのデータが送られてきたら。
            $products = Product::where('category_id', $request->category)->paginate(15); // カテゴリーIDをwhere文に組み込む
            $total_count = Product::where('category_id', $request->category)->count();   // 全体の件数も取得しておく（表示用）
            $category = Category::find($request->category);
        } else {                           // todo: 13_リクエストにカテゴリーが含まれていなければ ⇒ つまり、何もカテゴリーが絞り込まれていなければ。
            $products = Product::paginate(15);
            $total_count = "";
            $category = null;
        }

        $categories = Category::all(); // todo: 13_カテゴリーのデータをすべて取得してビューに送る

        $major_category_names = Category::pluck('major_category_name')->unique(); // todo: 13_全カテゴリのデータからmajor_category_nameのカラムのみを取得し、重複を削除

        return view('products.index', compact('products', 'category', 'categories', 'major_category_names', 'total_count'));
    }

    public function favorite(Product $product)
    {
        $user = Auth::user(); // todo: ログインユーザーを取得

        if ($user->hasFavorited($product)) { // ユーザーが商品をお気に入りに登録していたら
            $user->unfavorite($product);     // お気に入りをはずす
        } else {                             // ユーザーが商品をお気に入りに登録していなければ
            $user->favorite($product);       // お気に入りをする
        }

        return redirect()->route('products.show', $product);
    }

    public function create()
    {
        $categories = Category::all();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $product = new Product();
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->category_id = $request->input('category_id');
        $product->save();

        return redirect()->route('products.show', ['id' => $product->id]);
    }

    public function show(Product $product)
    {
        $reviews = $product->reviews()->get(); // todo: $productからリレーションで紐付いているreviewsを取得する

        return view('products.show', compact('product', 'reviews')); // todo: viewファイルに$productと$reviewsを渡している
    }

    public function edit(Product $product)
    {
        $categories = Category::all();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->category_id = $request->input('category_id');
        $product->update();

        return redirect()->route('products.show', ['id' => $product->id]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index');
    }
}
