<?php

namespace App\Http\Controllers\Dashboard;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::paginate(15);

        return view('dashboard.categories.index', compact('categories'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $category = new Category();
        $category->name = $request->input('name');
        $category->description = $request->input('description');
        $category->major_category_name = $request->input('major_category_name');
        $category->save();

        return redirect("/dashboard/categories");
    }

    public function show($id)
    {
        //
    }

    public function edit(Category $category)
    {
        return view('dashboard.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $category->name = $request->input('name');
        $category->description = $request->input('description');
        $category->major_category_name = $request->input('major_category_name');
        $category->update();

        return redirect("/dashboard/categories");
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect("/dashboard/categories");
    }
}
