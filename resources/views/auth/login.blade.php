<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU Clark Asset Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{min-height:100vh;margin:0;font-family:Inter,Segoe UI,Arial,sans-serif;background:radial-gradient(circle at 20% 20%,rgba(255,255,255,.06),transparent 30%),linear-gradient(135deg,#15235d,#111e55 55%,#12204b);display:grid;place-items:center;overflow:hidden}
        .wrap{position:relative;width:100%;min-height:100vh;display:grid;place-items:center;padding:28px}
        .backdrop-word{position:absolute;inset:0;display:grid;place-items:center;font-weight:900;font-size:76px;line-height:.9;color:rgba(255,255,255,.14);text-align:center;letter-spacing:.02em;pointer-events:none}
        .backdrop-sub{position:absolute;bottom:120px;color:rgba(255,255,255,.85);font-size:28px;font-style:italic;pointer-events:none}
        .login-card{position:relative;z-index:1;width:360px;max-width:92vw;background:rgba(249,250,251,.88);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.5);border-radius:18px;box-shadow:0 20px 60px rgba(0,0,0,.28);padding:28px 26px}
        .logo-qr{width:66px;height:66px;border-radius:50%;background:#f1b800;color:#16214f;display:grid;place-items:center;font-size:30px;margin:0 auto 10px;box-shadow:0 8px 25px rgba(241,184,0,.35)}
        .school{font-weight:800;text-align:center;line-height:1.1;margin-bottom:4px}
        .school small{display:block;font-size:11px;font-weight:500;color:#5f6572}
        .form-label{font-size:11px;font-weight:700;color:#333;margin-bottom:4px}.form-control{border-radius:8px;border-color:#c9cdd7;font-size:12px;padding:9px 12px}.btn-login{background:#4c7ef3;color:#fff;border:none;border-radius:8px;padding:10px 12px;font-size:12px;font-weight:700;width:100%}
        .tabline{margin:14px 0 10px;font-size:9px;color:#596070;display:flex;gap:10px;justify-content:center;flex-wrap:wrap}.sys-pill{display:inline-block;background:#4c7ef3;color:#fff;border-radius:5px;padding:4px 14px;font-size:10px}.forgot{display:block;text-align:center;margin-top:10px;font-size:11px;color:#6b7280;text-decoration:none}.demo{margin-top:12px;font-size:10px;color:#6b7280;text-align:center}
    </style>
</head>
<body>
<div class="wrap">
    <div class="backdrop-word">NATIONAL<br>UNIVERSITY</div>
    <div class="backdrop-sub">Education that works.</div>
    <div class="login-card">
        <div class="logo-qr">⌘</div>
        <div class="school">NU Clark<br>Asset Management<small>Inventory Management Platform</small></div>
        <div class="tiny text-center mb-2" style="font-size:11px;color:#555;">Sign in</div>
        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div class="mb-2">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ old('email','admin@nuclark.local') }}" placeholder="Enter your email" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" value="admin123" placeholder="Enter your password" required>
            </div>
            <button class="btn-login">Log in</button>
        </form>
        <div class="tabline"><span>Asset Office</span><span>Inventory Office</span><span>Dept User</span></div>
        <div class="text-center"><span class="sys-pill">Asset QR System</span></div>
        <a href="{{ route('register') }}" class="forgot">Create account</a>
        <div class="demo">Demo login: admin@nuclark.local / admin123</div>
    </div>
</div>
</body>
</html>
