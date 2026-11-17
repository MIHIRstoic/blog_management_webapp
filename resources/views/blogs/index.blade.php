<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobYaari Blog Hub</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f5f7fb; color: #17324d; }
        .container { width: min(1200px, calc(100% - 2rem)); margin: 0 auto; }
        .hero { background: linear-gradient(135deg, #0f172a, #2563eb); color: #fff; padding: 2rem 0; }
        .nav { display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; }
        .badge { display:inline-block; background:#eff6ff; color:#1d4ed8; padding:0.3rem 0.75rem; border-radius:999px; font-size:0.9rem; }
        .grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-top: 1rem; }
        .card { background:#fff; border-radius:18px; box-shadow:0 8px 18px rgba(15,23,42,.08); overflow:hidden; }
        .card img { width:100%; height:180px; object-fit:cover; }
        .card-body { padding: 1rem; }
        .filters { background:white; border-radius:18px; padding:1rem; box-shadow:0 8px 18px rgba(15,23,42,.08); display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:1rem; margin-top:1rem; }
        .btn { display:inline-block; background:#2563eb; color:white; border:0; border-radius:10px; padding:0.65rem 1rem; text-decoration:none; cursor:pointer; }
        .btn.secondary { background:#e5eefc; color:#1e3a8a; }
        .muted { color:#5b6b85; font-size:0.95rem; }
        .chip { display:inline-block; background:#eff6ff; color:#1e3a8a; padding:0.25rem 0.5rem; border-radius:999px; font-size:0.85rem; margin-bottom:0.5rem; }
        @media (max-width: 768px) { .hero { padding: 1rem 0;} }
    </style>
</head>
<body>
<div class="hero">
  <div class="container">
    <div class="nav">
      <div>
        <div class="badge">AJAX + Laravel Blog System</div>
        <h1 style="font-size:1.8rem; margin:0.35rem 0;">JobYaari Blog Hub</h1>
        <p class="muted" style="color: #dbeafe;">Responsive blog listing with live category/date filtering using AJAX and jQuery.</p>
      </div>
      <a class="btn secondary" href="{{ route('admin.login', [], false) }}">Admin Login</a>
    </div>
  </div>
</div>
<div class="container" style="padding: 1rem 0 2rem;">
  <div class="filters">
    <label>Category
      <select id="categoryFilter" class="btn secondary" style="width:100%; border:1px solid #dbe4f0; background:white; color:#17324d;">
        <option value="All">All</option>
        @foreach($categories as $category)
          <option value="{{ $category }}">{{ $category }}</option>
        @endforeach
      </select>
    </label>
    <label>Date
      <input id="dateFilter" type="date" class="btn secondary" style="width:100%; border:1px solid #dbe4f0; background:white; color:#17324d;">
    </label>
    <div style="display:flex; align-items:end; gap:0.5rem;">
      <button id="applyFilter" class="btn">Apply Filter</button>
      <button id="resetFilter" class="btn secondary">Reset</button>
    </div>
  </div>

  <div id="blogList" class="grid">
    @foreach($blogs as $blog)
      <article class="card">
        <img src="{{ $blog->image ?: 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $blog->title }}">
        <div class="card-body">
          <span class="chip">{{ $blog->category }}</span>
          <h3 style="margin:0.2rem 0 0.5rem;">{{ $blog->title }}</h3>
          <p class="muted" style="min-height: 48px;">{{ $blog->short_description }}</p>
          <p class="muted">Published {{ $blog->published_at?->format('M d, Y') }}</p>
          <a class="btn" href="{{ route('blogs.show', $blog, false) }}">Read More</a>
        </div>
      </article>
    @endforeach
  </div>
</div>
<script>
  $(function () {
    function renderBlogs(blogs) {
      if (!blogs.length) {
        $('#blogList').html('<div class="card" style="padding:1rem;">No blogs match the current filter.</div>');
        return;
      }

      const html = blogs.map((blog) => `
        <article class="card">
          <img src="${blog.image || 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=900&q=80'}" alt="${blog.title}">
          <div class="card-body">
            <span class="chip">${blog.category}</span>
            <h3 style="margin:0.2rem 0 0.5rem;">${blog.title}</h3>
            <p class="muted" style="min-height: 48px;">${blog.short_description}</p>
            <p class="muted">Published ${new Date(blog.published_at).toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'})}</p>
            <a class="btn" href="/blogs/${blog.id}">Read More</a>
          </div>
        </article>
      `).join('');
      $('#blogList').html(html);
    }

    $('#applyFilter').on('click', function () {
      $.getJSON('{{ route('blogs.filter', [], false) }}', {
        category: $('#categoryFilter').val(),
        date: $('#dateFilter').val()
      }, function (blogs) {
        renderBlogs(blogs);
      });
    });

    $('#resetFilter').on('click', function () {
      $('#categoryFilter').val('All');
      $('#dateFilter').val('');
      $.getJSON('{{ route('blogs.filter', [], false) }}', function (blogs) {
        renderBlogs(blogs);
      });
    });
  });
</script>
</body>
</html>
