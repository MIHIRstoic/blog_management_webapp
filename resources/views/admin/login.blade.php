<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | JobYaari</title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; background:#0f172a; color:#eff6ff; }
        .wrap { min-height:100vh; display:grid; place-items:center; padding:1rem; }
        .card { width:min(420px, 100%); background:#111827; border-radius:18px; padding:1.25rem; box-shadow: 0 20px 30px rgba(0,0,0,.25); }
        input, button { width:100%; border-radius:10px; border:1px solid #334155; padding:0.75rem; margin-top:0.6rem; font-size:1rem; }
        button { background:#2563eb; color:white; border:0; cursor:pointer; }
        a { color:#bfdbfe; text-decoration:none; }
        .error { color:#fecaca; font-size:0.92rem; margin-top:0.5rem; }
    </style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <h2 style="margin-top:0;">Admin Login</h2>
    <p style="color:#bfdbfe;">Use admin@jobyaari.dev / admin12345.</p>
    <form method="POST" action="{{ route('admin.login.post', [], false) }}">
      @csrf
      <input name="email" type="email" placeholder="Email" required>
      <input name="password" type="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    @if($errors->any())
      <div class="error">{{ $errors->first() }}</div>
    @endif
    <p style="margin-top:1rem;"><a href="{{ route('blogs.index', [], false) }}">Back to public blog page</a></p>
  </div>
</div>
</body>
</html>
