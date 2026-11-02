<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password | CollegeMusic Distribution</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">CollegeMusic</div>
                <h2>Change your password</h2>
                <p class="auth-subtitle">For security, replace the temporary password before continuing.</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('admin.password.update') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="password">New password</label>
                    <input class="form-input" type="password" id="password" name="password" required autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm new password</label>
                    <input class="form-input" type="password" id="password_confirmation" name="password_confirmation" required>
                </div>
                <button class="btn btn-primary btn-block" type="submit">
                    <i class="fa-solid fa-key"></i> Change Password & Continue
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" style="margin-top: 1rem; text-align: center;">
                @csrf
                <button class="btn btn-secondary" type="submit"><i class="fa-solid fa-right-from-bracket"></i> Sign out</button>
            </form>
        </div>
    </div>
</body>
</html>