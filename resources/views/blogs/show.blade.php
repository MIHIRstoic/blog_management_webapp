<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $blog->title }} | JobYaari</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f5f7fb; color:#17324d; }
        .container { width:min(1100px, calc(100% - 2rem)); margin: 0 auto; }
        .hero { background: linear-gradient(135deg,#0f172a,#2563eb); color:#fff; padding:1.5rem 0; }
        .card { background:#fff; border-radius:18px; padding:1rem; box-shadow:0 10px 18px rgba(15,23,42,.08); margin:1rem 0 2rem; }
        img { width:100%; max-height:420px; object-fit:cover; border-radius:14px; }
        .chip { display:inline-block; background:#eff6ff; color:#1e3a8a; padding:0.25rem 0.5rem; border-radius:999px; font-size:0.9rem; }
        .btn { display:inline-block; background:#2563eb; color:white; text-decoration:none; border-radius:10px; padding:0.65rem 1rem; }
    </style>
</head>
<body>
<div class="hero">
  <div class="container">
    <a class="btn" href="{{ route('blogs.index') }}" style="background:#e5eefc; color:#17324d;">← Back to blogs</a>
    <h1 style="margin-bottom:0.25rem;">{{ $blog->title }}</h1>
    <p style="color:#dbeafe;">{{ $blog->category }} • {{ $blog->published_at?->format('M d, Y') }}</p>
  </div>
</div>
<div class="container">
  <article class="card">
    <img src="{{ $blog->image ?: 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $blog->title }}">
    <p class="chip" style="margin-top:1rem;">{{ $blog->category }}</p>
    <p style="color:#5b6b85;">{{ $blog->short_description }}</p>
    <div style="line-height:1.7;">{!! nl2br(e($blog->content)) !!}</div>
  </article>
</div>
</body>
</html>
