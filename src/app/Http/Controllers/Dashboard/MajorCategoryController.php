<?php

namespace App\Http\Controllers\Dashboard;

use App\MajorCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MajorCategoryController extends Controller
{

    public function index()
    {
        $major_categories = MajorCategory::paginate(15);

        return view('dashboard.major_categories.index', compact('major_categories'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'name'        => 'required|unique:major_categories',
                'description' => 'required',
            ],
            [
                'name.required'        => '親カテゴリ名は必須です。',
                'name.unique'          => '親カテゴリ名「' . $request->input('name') . '」は登録済みです。',
                'description.required' => '親カテゴリの説明は必須です。',
            ]
        );

        $major_category = new MajorCategory();
        $major_category->name = $request->input('name');
        $major_category->description = $request->input('description');
        $major_category->save();

        return redirect("/dashboard/major_categories");

    }

    public function show(MajorCategory $majorCategory)
    {
        //
    }

    public function edit(MajorCategory $major_category)
    {
        return view('dashboard.major_categories.edit', compact('major_category'));
    }

    public function update(Request $request, MajorCategory $major_category)
    {
        $request->validate(
            [
                'name'        => 'required|unique:major_categories',
                'description' => 'required',
            ],
            [
                'name.required'        => '親カテゴリ名は必須です。',
                'name.unique'          => '親カテゴリ名「' . $request->input('name') . '」は登録済みです。',
                'description.required' => '親カテゴリの説明は必須です。',
            ]
        );

        $major_category->name = $request->input('name');
        $major_category->description = $request->input('description');
        $major_category->update();

        return redirect("/dashboard/major_categories");
    }

    public function destroy(MajorCategory $major_category)
    {
        $major_category->delete();

        return redirect("/dashboard/major_categories");
    }
}
