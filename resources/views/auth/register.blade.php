<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | NU Clark Asset Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{min-height:100vh;margin:0;font-family:Inter,Segoe UI,Arial,sans-serif;background:radial-gradient(circle at 20% 20%,rgba(255,255,255,.06),transparent 30%),linear-gradient(135deg,#15235d,#111e55 55%,#12204b);display:grid;place-items:center;padding:24px}
        .card-wrap{width:900px;max-width:96vw;background:rgba(249,250,251,.93);border:1px solid rgba(255,255,255,.5);border-radius:22px;box-shadow:0 20px 60px rgba(0,0,0,.28);overflow:hidden}
        .left-pane{background:linear-gradient(180deg,#183078,#102358);color:#fff;padding:28px;height:100%}
        .left-pane h1{font-size:28px;font-weight:800;margin-bottom:10px}
        .left-pane p{font-size:14px;opacity:.9}
        .step{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.14);border-radius:14px;padding:14px 16px;margin-top:14px}
        .step strong{display:block;font-size:14px;margin-bottom:4px}
        .right-pane{padding:26px}
        .box{border:1px solid #dbe1ef;border-radius:16px;padding:18px;margin-bottom:16px;background:#fff}
        .box h5{font-size:16px;font-weight:800;margin-bottom:12px}
        .form-label{font-size:12px;font-weight:700;color:#334155}
        .form-control{border-radius:10px;padding:10px 12px}
        .btn-main{background:#4c7ef3;color:#fff;border:none;border-radius:10px;padding:10px 14px;font-size:13px;font-weight:700}
        .btn-main:hover{background:#3e6ddd;color:#fff}
        .muted{font-size:12px;color:#64748b}
        .verified-badge{display:inline-block;background:#dcfce7;color:#166534;border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700}
    </style>
</head>
<body>
<div class="card-wrap">
    <div class="row g-0">
        <div class="col-lg-4">
            <div class="left-pane">
                <h1>Create your account</h1>
                <p>Bago makagawa ng account, kailangan munang ma-verify ang email gamit ang code na isi-send sa inbox.</p>
                <div class="step"><strong>Step 1</strong>Enter your email and send verification code.</div>
                <div class="step"><strong>Step 2</strong>Type the 6-digit code from your email.</div>
                <div class="step"><strong>Step 3</strong>Complete your name and password to finish registration.</div>
                <div class="mt-4"><a href="{{ route('login') }}" class="btn btn-light btn-sm">Back to login</a></div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="right-pane">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="box">
                    <h5>1. Send verification code</h5>
                    <form method="POST" action="{{ route('register.send-code') }}">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-8">
                                <label class="form-label">Email address</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', old('verified_email', $verifiedEmail ?? '')) }}" placeholder="Enter your email" required>
                            </div>
                            <div class="col-md-4">
                                <button class="btn-main w-100" type="submit">Send code</button>
                            </div>
                        </div>
                        <div class="muted mt-2">Code expires in 10 minutes.</div>
                    </form>
                </div>

                <div class="box">
                    <h5>2. Verify email code</h5>
                    @if($verifiedEmail)
                        <div class="mb-3"><span class="verified-badge">Verified: {{ $verifiedEmail }}</span></div>
                    @endif
                    <form method="POST" action="{{ route('register.verify-code') }}">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label">Verified email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', old('verified_email', $verifiedEmail ?? ($pendingVerification['email'] ?? ''))) }}" placeholder="Enter the same email" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">6-digit code</label>
                                <input type="text" name="code" class="form-control" maxlength="6" placeholder="123456" required>
                            </div>
                            <div class="col-md-3">
                                <button class="btn-main w-100" type="submit">Verify code</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="box mb-0">
                    <h5>3. Finish account creation</h5>
                    <form method="POST" action="{{ route('register.submit') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter your full name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('verified_email', $verifiedEmail ?? '') }}" placeholder="Verified email only" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Re-enter password" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <select name="department_id" class="form-control" required>
                                    <option value="">Select your department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="muted mb-2">Your account will stay pending until an admin reviews and approves it.</div>
                                <button class="btn-main" type="submit">Create account</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
