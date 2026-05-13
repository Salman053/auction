<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of all top-level categories.
     */
    public function index(): View
    {
        $categories = Category::where('depth', 0)
            ->orderBy('name')
            ->get();

        return view('user.categories.index', compact('categories'));
    }

    /**
     * Display the specified category and its children.
     */
    public function show(Category $category): View
    {
        $category->load(['children', 'parent']);

        return view('user.categories.show', compact('category'));
    }
}
