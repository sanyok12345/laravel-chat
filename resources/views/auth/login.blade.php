<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Login</title>
    <style>
        .full-height {
            height: 100vh;
        }
        .btn-custom {
            width: 150px;
        }
        .form-container {
            max-width: 400px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center full-height">
        <div class="form-container">
            <div class="text-center mb-4">
                <img src="https://cdn-icons-png.flaticon.com/512/5962/5962463.png" alt="ChatApp Logo" style="width: 96px; height: 96px;">
                <h2>Login to LaravelChat</h2>
            </div>
            @if (session('status'))
                <div class="alert alert-success mb-4">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">{{ __('Email') }}</label>
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                    @if ($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif
                </div>
                <div class="form-group mt-3">
                    <label for="password">{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password">
                    @if ($errors->has('password'))
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                    @endif
                </div>
                <div class="form-group form-check mt-3">
                    <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                    <label class="form-check-label" for="remember_me">{{ __('Remember me') }}</label>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="{{ route('register') }}" class="text-muted">{{ __('Don\'t have an account?') }}</a>
                    <button type="submit" class="btn btn-primary btn-custom">{{ __('Log in') }}</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
