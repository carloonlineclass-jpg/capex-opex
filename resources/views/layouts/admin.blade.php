<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'NU Clark Asset Management' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --sidebar:#101a32;
            --sidebar-2:#0c152b;
            --ink:#14181f;
            --muted:#6b7280;
            --panel:#ffffff;
            --bg:#eef2f7;
            --line:#d9dde7;
            --accent:#4f46e5;
            --warning:#f3c969;
            --success:#27c96f;
            --danger:#ff4d4f;
            --violet:#7c6bff;
            --cyan:#57d2eb;
        }
        *{box-sizing:border-box}
        body{margin:0;background:var(--bg);color:var(--ink);font-family:Inter,Segoe UI,Arial,sans-serif}
        .app-shell{display:flex;min-height:100vh}
        .sidebar{width:112px;background:linear-gradient(180deg,var(--sidebar),var(--sidebar-2));color:#fff;position:sticky;top:0;height:100vh;border-right:1px solid rgba(255,255,255,.06);z-index:20}
        .brand-wrap{padding:20px 12px 18px;border-bottom:1px solid rgba(255,255,255,.12)}
        .brand-box{display:flex;align-items:center;gap:10px}
        .brand-mark{width:30px;height:30px;border-radius:8px;background:#ffc107;color:#101a32;display:grid;place-items:center;font-size:15px;font-weight:700;box-shadow:0 8px 18px rgba(255,193,7,.25)}
        .brand-title{font-weight:700;font-size:12px;line-height:1.2}
        .brand-sub{font-size:10px;color:#c6d1ea}
        .nav-list{padding:14px 8px;display:grid;gap:8px}
        .nav-linkx{display:flex;align-items:center;gap:12px;padding:11px 12px;border-radius:12px;text-decoration:none;color:#d8deee;font-size:12px}
        .nav-linkx i{font-size:16px;min-width:16px}
        .nav-linkx:hover,.nav-linkx.active{background:rgba(255,255,255,.08);color:#fff}
        .main{flex:1;min-width:0}
        .topbar{height:70px;background:rgba(255,255,255,.92);border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;padding:0 26px;position:sticky;top:0;z-index:10;backdrop-filter:blur(8px)}
        .page-title{font-size:14px;font-weight:800;margin:0}.page-subtitle{font-size:11px;color:var(--muted)}
        .top-actions{display:flex;align-items:center;gap:16px}
        .top-icon{font-size:22px;color:#111}.notif-link{position:relative;color:#111;text-decoration:none;display:inline-flex;align-items:center}.notif-badge{position:absolute;top:-5px;right:-8px;background:#ef4444;color:#fff;border-radius:999px;min-width:18px;height:18px;padding:0 5px;display:grid;place-items:center;font-size:10px;font-weight:700}.user-chip{display:flex;align-items:center;gap:10px}
        .avatar{width:34px;height:34px;border-radius:50%;background:#7e57c2;color:#fff;display:grid;place-items:center;font-size:20px}
        .user-meta{line-height:1.1}.user-name{font-weight:700;font-size:12px}.user-role{font-size:11px;color:var(--muted)}
        .logout-btn{color:#ef4444;text-decoration:none;font-size:12px;background:none;border:none;padding:0}
        .content{padding:26px}
        .surface{background:#efefef;border:1px solid #cfd3dc;border-radius:16px;box-shadow:0 6px 24px rgba(15,23,42,.07)}
        .module-head{display:flex;align-items:flex-end;justify-content:space-between;gap:12px;margin-bottom:12px;flex-wrap:wrap}
        .module-title{font-size:16px;font-weight:800;margin:0}.module-note{font-size:12px;color:var(--muted)}
        .btn-primaryx{background:#3b2af4;color:#fff;border:none;border-radius:9px;padding:9px 14px;font-weight:600;font-size:12px;text-decoration:none;display:inline-flex;align-items:center;gap:8px}
        .btn-primaryx:hover{opacity:.92;color:#fff}
        .search-strip{display:flex;align-items:center;gap:10px;padding:10px 12px;border:1px solid #b8beca;background:#f6f6f7;border-radius:10px;box-shadow:0 3px 8px rgba(0,0,0,.05) inset;margin-bottom:14px}
        .search-input{border:none;background:transparent;outline:none;width:100%;font-size:12px;color:#555}
        .filter-box{min-width:110px;border-left:1px solid #c9ced8;padding-left:10px;display:flex;align-items:center;gap:8px}.filter-box select{border:none;background:transparent;width:100%;font-size:12px;outline:none;color:#4b5563}
        .stat-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:20px;margin-bottom:14px}
        .stat-card{background:#e7e7e7;border:1px solid #d2d6de;border-radius:18px;padding:16px 16px 12px;position:relative;min-height:110px}
        .stat-icon{width:38px;height:38px;border-radius:14px;display:grid;place-items:center;color:#fff;font-size:18px;margin-bottom:14px}
        .stat-mini{position:absolute;top:14px;right:18px;font-size:11px;font-weight:700}.stat-label{font-size:13px;color:#222;margin-bottom:4px}.stat-value{font-size:40px;font-weight:800;line-height:1}
        .icon-cyan{background:#1dd7ef}.icon-green{background:#10da59}.icon-amber{background:#f4cf7a}.icon-red{background:#ff1515}.mini-green{color:#2dbb58}.mini-red{color:#ff1515}
        .panel-grid-2,.report-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
        .chart-card,.report-box{padding:0;border-radius:16px;overflow:hidden;background:#d8d8d8;border:1px solid #bfc4cf}
        .chart-head{height:36px;display:flex;align-items:center;gap:8px;padding:0 12px;border-bottom:1px solid #c0c5cf;font-weight:700;font-size:13px;background:#d0d0d0}
        .chart-body{padding:16px;background:#e6e6e7}
        .chart-wrap{height:280px;position:relative}
        .data-panel{padding:14px;background:#d8d8d8;border:1px solid #bfc4cf;border-radius:16px}
        .data-table{width:100%;border-collapse:collapse;font-size:12px}.data-table thead th{padding:10px 12px;background:#e0e0e0;border-bottom:1px solid #a9afbc;color:#555;text-transform:uppercase;font-size:11px;letter-spacing:.03em}.data-table tbody td{padding:10px 12px;border-top:1px solid #bfc4cf;vertical-align:middle}
        .asset-card,.request-card,.issue-card,.supplier-card{background:#d8d8d8;border:1px solid #bfc4cf;border-radius:16px;padding:12px;margin-bottom:12px}
        .grid-cards{display:grid;grid-template-columns:1fr 1fr;gap:18px}.supplier-card{padding:14px 18px;min-height:230px;background:#dedede}
        .supplier-meta{color:#787878;font-size:11px;margin-bottom:18px}.supplier-avatar{width:24px;height:24px;border-radius:50%;display:grid;place-items:center;background:#8eb8ff;color:#24477d;font-size:12px;margin-bottom:10px}
        .muted-line{color:#7b7b7b;font-size:12px;margin:8px 0;display:flex;gap:10px;align-items:flex-start}.tag{display:inline-block;padding:2px 8px;font-size:10px;border-radius:6px;background:#f4f4f4;border:1px solid #cfd3dc;color:#646464;margin-right:6px}
        .status{display:inline-flex;align-items:center;gap:6px;border-radius:14px;padding:2px 8px;font-size:10px;font-weight:700}.status.available{background:#d9f8de;color:#17984d}.status.in-use{background:#dfeeff;color:#2c6dd8}.status.maintenance{background:#fff6c9;color:#af9000}.status.pending{background:#f6ffcb;color:#96a000}.status.approved{background:#ddfbe7;color:#23a955}.status.low{background:#ffe1e1;color:#ef4444}
        .tiny{font-size:11px;color:#666}.tiny-2{font-size:10px;color:#888}.code-badge{display:inline-block;padding:2px 6px;border-radius:10px;font-size:9px;background:#dcebff;color:#3570d6;font-weight:700}.pill-opex{background:#ffef9c;color:#957600;border-radius:12px;padding:1px 7px;font-size:10px;font-weight:700}
        .stock-bar{height:5px;border-radius:999px;background:#d6d9df;overflow:hidden;width:90px;margin-top:3px}.stock-fill{height:100%;background:#24d14d}.stock-fill.low{background:#ef4444}
        .request-actions{display:flex;gap:12px;justify-content:flex-end;flex-wrap:wrap}.btn-approve,.btn-reject,.btn-soft{border:none;border-radius:8px;padding:8px 18px;font-size:11px;font-weight:700;color:#fff;text-decoration:none;display:inline-flex;gap:6px;align-items:center}
        .btn-approve{background:#27c43d}.btn-reject{background:#e54848}.btn-soft{background:#23344d}.empty-state{padding:28px;text-align:center;color:#666;font-size:13px}
        .form-shell{background:#dedede;border:1px solid #c5c9d3;border-radius:16px;padding:20px}.form-control,.form-select,.form-check-input{border-color:#bfc5d0;border-radius:10px}.form-control:focus,.form-select:focus{box-shadow:none;border-color:#8ca4ff}.form-label{font-size:12px;font-weight:700;color:#3a3f47}.small-btn{padding:7px 12px;border-radius:9px;font-size:12px;font-weight:700}
        .page-tabs{display:flex;gap:18px;margin:10px 0 14px;font-size:12px;font-weight:700;flex-wrap:wrap}.page-tabs .active{color:#ff9900}
        .qr-card{background:#d8d8d8;border:1px solid #bfc4cf;border-radius:16px;padding:16px}.qr-grid{display:grid;grid-template-columns:1.1fr .9fr;gap:16px}.qr-tiles{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.qr-tile{background:#ececec;border:1px solid #ccd2dc;border-radius:16px;padding:12px;text-align:center}
        .scanner-shell{background:#ececec;border:1px solid #ccd2dc;border-radius:16px;padding:14px}.scanner-box{background:#0f172a;border-radius:16px;padding:12px;min-height:340px;color:#fff}.scanner-result{background:#ffffff;border:1px solid #ccd2dc;border-radius:14px;padding:14px}
        .report-stat{background:#e7e7e7;border:1px solid #d2d6de;border-radius:16px;padding:14px}
        .mobile-menu{display:none}
        @media (max-width: 991px){
            .sidebar{position:fixed;left:-120px;transition:.25s ease;box-shadow:0 20px 40px rgba(0,0,0,.18)}
            body.sidebar-open .sidebar{left:0}
            .mobile-menu{display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border:none;border-radius:12px;background:#eef2ff}
            .topbar{padding:0 14px}
            .content{padding:16px}
            .stat-grid,.panel-grid-2,.grid-cards,.report-grid,.qr-grid,.qr-tiles{grid-template-columns:1fr}
            .user-meta{display:none}
            .request-actions{justify-content:flex-start}
        }
        @media (max-width: 576px){
            .data-table{font-size:11px}
            .data-table thead{display:none}
            .data-table tbody tr{display:block;padding:10px 0;border-top:1px solid #bfc4cf}
            .data-table tbody td{display:flex;justify-content:space-between;gap:16px;padding:8px 10px;border-top:none}
            .data-table tbody td::before{content:attr(data-label);font-weight:700;color:#4b5563;text-transform:uppercase;font-size:10px}
            .top-actions{gap:10px}
            .page-title{font-size:13px}
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand-wrap">
            <div class="brand-box">
                <div class="brand-mark"><i class="bi bi-upc-scan"></i></div>
                <div>
                    <div class="brand-title">NU Clark</div>
                    <div class="brand-sub">Asset Management</div>
                </div>
            </div>
        </div>
        <nav class="nav-list">
            <a class="nav-linkx {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-grid"></i><span>Dashboard</span></a>
            <a class="nav-linkx {{ request()->routeIs('items.*') && request('type', 'CAPEX') === 'CAPEX' ? 'active' : '' }}" href="{{ route('items.index', ['type' => 'CAPEX']) }}"><i class="bi bi-pc-display"></i><span>Capex</span></a>
            <a class="nav-linkx {{ request()->routeIs('items.*') && request('type') === 'OPEX' ? 'active' : '' }}" href="{{ route('items.index', ['type' => 'OPEX']) }}"><i class="bi bi-layers"></i><span>Opex</span></a>
            <a class="nav-linkx {{ request()->routeIs('requisitions.*') ? 'active' : '' }}" href="{{ route('requisitions.index') }}"><i class="bi bi-file-earmark-text"></i><span>Requisitions</span></a>
            @if(auth()->user()->canManageInventory())
            <a class="nav-linkx {{ request()->routeIs('issuances.*') ? 'active' : '' }}" href="{{ route('issuances.index') }}"><i class="bi bi-arrow-repeat"></i><span>Issuance & Returns</span></a>
            <a class="nav-linkx {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}"><i class="bi bi-truck"></i><span>Suppliers</span></a>
            <a class="nav-linkx {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}"><i class="bi bi-graph-up"></i><span>Reports</span></a>
            <a class="nav-linkx {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="bi bi-people"></i><span>Users</span></a>
            <a class="nav-linkx {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}"><i class="bi bi-gear"></i><span>Settings</span></a>
            @endif
        </nav>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="mobile-menu" type="button" onclick="document.body.classList.toggle('sidebar-open')"><i class="bi bi-list"></i></button>
                <div>
                    <p class="page-title">{{ $title ?? 'Dashboard' }}</p>
                    <div class="page-subtitle">Welcome back, {{ auth()->user()->name ?? 'Admin' }} · Assets Office</div>
                </div>
            </div>
            <div class="top-actions">
                <a href="{{ route('notifications.index') }}" class="notif-link" title="Notifications"><i class="bi bi-bell top-icon"></i>@if(auth()->user()->unreadNotifications->count())<span class="notif-badge">{{ auth()->user()->unreadNotifications->count() }}</span>@endif</a>
                <div class="user-chip">
                    <div class="avatar"><i class="bi bi-person-circle"></i></div>
                    <div class="user-meta">
                        <div class="user-name">{{ auth()->user()->name ?? 'Christian Bundalian' }}</div>
                        <div class="user-role">{{ ucfirst(auth()->user()->role ?? 'admin') }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</button></form>
            </div>
        </div>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success rounded-4 border-0 shadow-sm">{{ session('success') }}</div>
            @endif
            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
