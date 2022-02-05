<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class WebController extends Controller
{
    public function index()
    {
        $categories = Category::all()->sortBy('major_category_name');

        $major_category_names = Category::pluck('major_category_name')->unique();

        return view('web.index', compact('major_category_names', 'categories'));
    }
}
