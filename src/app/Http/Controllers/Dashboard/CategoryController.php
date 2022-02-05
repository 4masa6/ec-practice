<?php

namespace App\Http\Controllers\Dashboard;

use App\Category;
use App\MajorCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{

    public function index() {
        $categories = Category::paginate(15);

        $major_categories = MajorCategory::all();

        return view('dashboard.categories.index', compact('categories', 'major_categories'));
    }

    public function create() {
        //
    }

    public function store(Request $request) {
        $request->validate(
            [
                'name'        => 'required|unique:categories',
                'description' => 'required',
            ],
            [
                'name.required'        => 'カテゴリ名は必須です。',
                'name.unique'          => 'カテゴリ名「' . $request->input('name') . '」は登録済みです。',
                'description.required' => 'カテゴリの説明は必須です。',
            ]
        );
        $category                      = new Category();
        $category->name                = $request->input('name');
        $category->description         = $request->input('description');
        $category->major_category_id   = $request->input('major_category_id');
        $category->major_category_name = MajorCategory::find($request->input('major_category_id'))->name;
        $category->save();

        return redirect("/dashboard/categories");
    }

    public function show($id) {
        //
    }

    public function edit(Category $category) {
        $major_categories = MajorCategory::all();

        return view('dashboard.categories.edit', compact('category', 'major_categories'));
    }

    public function update(Request $request, Category $category) {
        $request->validate(
            [
                'name'        => 'required|unique:categories',
                'description' => 'required',
            ],
            [
                'name.required'        => 'カテゴリ名は必須です。',
                'name.unique'          => 'カテゴリ名「' . $request->input('name') . '」は登録済みです。',
                'description.required' => 'カテゴリの説明は必須です。',
            ]
        );

        $category->name                = $request->input('name');
        $category->description         = $request->input('description');
        $category->major_category_id   = $request->input('major_category_id');
        $category->major_category_name = MajorCategory::find($request->input('major_category_id'))->name;
        $category->update();

        return redirect("/dashboard/categories");
    }

    public function destroy(Category $category) {
        $category->delete();

        return redirect("/dashboard/categories");
    }
}
