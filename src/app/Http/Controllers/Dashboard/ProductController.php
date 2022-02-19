<?php

namespace App\Http\Controllers\Dashboard;

use App\Product;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{

    public function index(Request $request) {
        $sort_query = [];
        $sorted     = "";

        if ($request->sort !== null) {
            $slices                 = explode(' ', $request->sort);
            $sort_query[$slices[0]] = $slices[1];
            $sorted                 = $request->sort;
        }

        if ($request->keyword !== null) {
            $keyword     = rtrim($request->keyword);
            $total_count = Product::where('name', 'like', "%{$keyword}%")->orwhere('id', "{$keyword}")->count();
            $products    = Product::where('name', 'like', "%{$keyword}%")->orwhere('id', "{$keyword}")->sortable($sort_query)->paginate(15);
        } else {
            $keyword     = "";
            $total_count = Product::count();
            $products    = Product::sortable($sort_query)->paginate(15);
        }

        $sort = [
            '価格の安い順'  => 'price asc',
            '価格の高い順'  => 'price desc',
            '出品の古い順'  => 'updated_at asc',
            '出品の新しい順' => 'updated_at desc'
        ];

        return view('dashboard.products.index', compact('products', 'sort', 'sorted', 'total_count', 'keyword'));
    }

    public function create() {
        $categories = Category::all();

        return view('dashboard.products.create', compact('categories'));
    }

    public function store(Request $request) {
        $request->validate(
            [
                'name'        => 'required',
                'price'       => 'required',
                'description' => 'required',
            ],
            [
                'name.required'        => '商品名は必須です。',
                'price.required'       => '価格は必須です。',
                'description.required' => '商品説明は必須です。',
            ]
        );

        $product              = new Product();
        $product->name        = $request->input('name');
        $product->description = $request->input('description');
        $product->price       = $request->input('price');
        $product->category_id = $request->input('category_id');

        // 64_おすすめフラグをONにする
        if ($request->input('recommend') == 'on') {
            $product->recommend_flag = true;
        } else {
            $product->recommend_flag = false;
        }

        // todo: 68_アップロードされたファイルをローカルに保存する
        if ($request->file('image') !== null) { // POSTにimageのファイルが存在すれば（$request->file()でアップロードファイルを取得）
            $image          = $request->file('image')->store('public/products'); // storeメソッドでファイルシステムで設定したルートディレクトリからの相対位置で、どこに保存するかを指定する
            $product->image = basename($image); // basename：パスの最後の部分を返す basename('/foo/bar/baz') => 'baz'
        } else {
            $product->image = '';
        }

        // 67_送料フラグをONにする
        if ($request->input('carriage') == 'on') {
            $product->carriage_flag = true;
        } else {
            $product->carriage_flag = false;
        }
        $product->save();

        return redirect()->route('dashboard.products.index');
    }

    public function edit(Product $product) {
        $categories = Category::all();

        return view('dashboard.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product) {
        $request->validate(
            [
                'name'        => 'required',
                'price'       => 'required',
                'description' => 'required',
            ],
            [
                'name.required'        => '商品名は必須です。',
                'price.required'       => '価格は必須です。',
                'description.required' => '商品説明は必須です。',
            ]
        );

        $product->name        = $request->input('name');
        $product->description = $request->input('description');
        $product->price       = $request->input('price');
        $product->category_id = $request->input('category_id');

        if ($request->input('recommend') == 'on') {
            $product->recommend_flag = true;
        } else {
            $product->recommend_flag = false;
        }

        //
        if ($request->hasFile('image')) { // リクエストにファイルが存在していれば
            $image          = $request->file('image')->store('public/products'); //file()でファイルを取得し、store()でディレクトリに画像ファイルを保存
            $product->image = basename($image); // basename()パスの最後にある名前の部分を取得
        } elseif (isset($product->image)) { // 商品画像が存在してれば
            // do nothing
        } else {
            $product->image = '';
        }

        // 67_送料フラグをONにする
        if ($request->input('carriage') == 'on') {
            $product->carriage_flag = true;
        } else {
            $product->carriage_flag = false;
        }
        $product->update();

        return redirect()->route('dashboard.products.index');
    }

    public function destroy(Product $product) {
        $product->delete();

        return redirect()->route('dashboard.products.index');
    }

    public function import(Product $product) {
        return view('dashboard.products.import');
    }

    public function import_csv(Request $request) {
        if ($request->hasFile('csv')) {
            Excel::import(new ProductsImport, $request->file('csv'));
            return redirect()->route('dashboard.products.import_csv')->with('flash_message', 'CSVでの一括登録が成功しました!');
        }
        return redirect()->route('dashboard.products.import_csv')->with('flash_message', 'CSVが追加されていません。CSVを追加してください。');
    }
}
