<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::query()->latest('published_at');

        if ($request->filled('category') && $request->category !== 'All') {
            $query->where('category', $request->category);
        }

        if ($request->filled('date')) {
            $query->whereDate('published_at', $request->date);
        }

        $blogs = $query->get();
        $categories = Blog::query()->select('category')->distinct()->pluck('category');

        return view('blogs.index', compact('blogs', 'categories'));
    }

    public function filter(Request $request)
    {
        $query = Blog::query()->latest('published_at');

        if ($request->filled('category') && $request->category !== 'All') {
            $query->where('category', $request->category);
        }

        if ($request->filled('date')) {
            $query->whereDate('published_at', $request->date);
        }

        return response()->json($query->get());
    }

    public function show(Blog $blog)
    {
        return view('blogs.show', compact('blog'));
    }
}
