<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | JobYaari</title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; background:#f5f7fb; color:#17324d; }
        .container { width:min(1200px, calc(100% - 2rem)); margin:0 auto; }
        .top { background:#0f172a; color:white; padding:1rem 0; }
        .card { background:white; border-radius:18px; padding:1rem; box-shadow:0 10px 18px rgba(15,23,42,.08); margin:1rem 0; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:0.75rem; border-bottom:1px solid #e5e7eb; text-align:left; vertical-align:top; }
        .btn { display:inline-block; text-decoration:none; background:#2563eb; color:white; border-radius:10px; padding:0.55rem 0.8rem; margin-right:0.35rem; }
        .btn.secondary { background:#e5eefc; color:#1e3a8a; }
        .btn.danger { background:#ef4444; color:white; }
    </style>
</head>
<body>
<div class="top">
  <div class="container" style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
    <div>
      <h2 style="margin:0;">Admin Dashboard</h2>
      <p style="margin:0.15rem 0 0; color:#bfdbfe;">Manage blogs, add new content, and update categories.</p>
    </div>
    <div>
      <a class="btn secondary" href="{{ route('admin.blogs.create', [], false) }}">Add Blog</a>
      <form method="POST" action="{{ route('admin.logout', [], false) }}" style="display:inline;">
        @csrf
        <button class="btn secondary" type="submit">Logout</button>
      </form>
    </div>
  </div>
</div>
<div class="container" style="padding-bottom:2rem;">
  @if(session('success'))
    <div class="card" style="background:#ecfdf5; color:#065f46;">{{ session('success') }}</div>
  @endif
  <div class="card">
    <h3 style="margin-top:0;">All Blogs</h3>
    <table>
      <thead><tr><th>Title</th><th>Category</th><th>Date</th><th>Actions</th></tr></thead>
      <tbody>
      @foreach($blogs as $blog)
        <tr>
          <td>{{ $blog->title }}</td>
          <td>{{ $blog->category }}</td>
          <td>{{ $blog->published_at?->format('M d, Y') }}</td>
          <td>
            <a class="btn secondary" href="{{ route('admin.blogs.edit', $blog, false) }}">Edit</a>
            <form action="{{ route('admin.blogs.destroy', $blog, false) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this blog?');">
              @csrf
              @method('DELETE')
              <button class="btn danger" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
