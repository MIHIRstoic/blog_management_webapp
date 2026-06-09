<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public function loginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($credentials['email'] === 'admin@jobyaari.dev' && $credentials['password'] === 'admin12345') {
            Session::put('admin_logged_in', true);

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid admin credentials.']);
    }

    public function logout()
    {
        Session::forget('admin_logged_in');

        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        $blogs = Blog::latest('published_at')->get();

        return view('admin.dashboard', compact('blogs'));
    }

    public function create()
    {
        return view('admin.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'short_description' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|url',
            'published_at' => 'nullable|date',
        ]);

        Blog::create($data);

        return redirect()->route('admin.dashboard')->with('success', 'Blog added successfully.');
    }

    public function edit(Blog $blog)
    {
        return view('admin.form', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'short_description' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|url',
            'published_at' => 'nullable|date',
        ]);

        $blog->update($data);

        return redirect()->route('admin.dashboard')->with('success', 'Blog updated successfully.');
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Blog deleted successfully.');
    }
}
