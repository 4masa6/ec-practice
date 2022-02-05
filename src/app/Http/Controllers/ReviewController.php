<?php

namespace App\Http\Controllers;

use App\Review;
use App\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ReviewController extends Controller
{

    public function store(Product $product, Request $request)
    {
        $review = new Review(); // Reviewモデルのインスタンス作成
        $review->content = $request->input('content'); // $request->input('content')でPOSTしてきた値を取得できる
        $review->product_id = $product->id;
        $review->user_id = Auth::user()->id; // Auth::user() でログインしているユーザーの情報を取得できる
        $review->score = $request->input('score'); // todo: 53_POSTしてきた name="score" の値を取得
        $review->save();

        return redirect()->route('products.show', $product);
    }
}
