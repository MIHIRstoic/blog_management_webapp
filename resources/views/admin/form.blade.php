<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($blog) ? 'Edit Blog' : 'Add Blog' }} | JobYaari</title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; background:#f5f7fb; color:#17324d; }
        .container { width:min(900px, calc(100% - 2rem)); margin:0 auto; }
        .card { background:white; border-radius:18px; padding:1rem; box-shadow:0 10px 18px rgba(15,23,42,.08); margin:1rem 0 2rem; }
        label { display:block; font-weight:600; margin-top:0.75rem; }
        input, textarea, select { width:100%; padding:0.75rem; border-radius:10px; border:1px solid #cbd5e1; box-sizing:border-box; margin-top:0.25rem; }
        button { background:#2563eb; color:#fff; border:0; border-radius:10px; padding:0.7rem 1rem; cursor:pointer; margin-top:1rem; }
        .btn { display:inline-block; text-decoration:none; color:#1e3a8a; background:#e5eefc; border-radius:10px; padding:0.6rem 0.9rem; margin-top:1rem; }
        .errors { color:#b91c1c; background:#fef2f2; border-radius:10px; padding:0.75rem 1rem; }
    </style>
</head>
<body>
<div class="container">
  <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
    <h2 style="margin-bottom:0;">{{ isset($blog) ? 'Edit Blog' : 'Add New Blog' }}</h2>
    <a class="btn" href="{{ route('admin.dashboard', [], false) }}">← Back to dashboard</a>
  </div>
  <div class="card">
    @if($errors->any())
      <ul class="errors">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    @endif
    <form method="POST" action="{{ isset($blog) ? route('admin.blogs.update', $blog, false) : route('admin.blogs.store', [], false) }}">
      @csrf
      @if(isset($blog)) @method('PUT') @endif
      <label>Title</label>
      <input name="title" value="{{ old('title', $blog->title ?? '') }}" required>
      <label>Category</label>
      <input name="category" value="{{ old('category', $blog->category ?? '') }}" placeholder="Admit Card, Result" required>
      <label>Image URL</label>
      <input name="image" value="{{ old('image', $blog->image ?? '') }}" placeholder="https://example.com/image.jpg">
      <label>Published Date</label>
      <input type="date" name="published_at" value="{{ old('published_at', isset($blog) ? $blog->published_at?->format('Y-m-d') : '') }}">
      <label>Short Description</label>
      <textarea name="short_description" rows="3" required>{{ old('short_description', $blog->short_description ?? '') }}</textarea>
      <label>Content</label>
      <textarea name="content" rows="8" required>{{ old('content', $blog->content ?? '') }}</textarea>
      <button type="submit">{{ isset($blog) ? 'Update Blog' : 'Create Blog' }}</button>
    </form>
  </div>
</div>
</body>
</html>
