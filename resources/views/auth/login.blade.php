<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, viewport-fit=cover" />
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link href={{ asset('css/login.css') }} rel="stylesheet">
        <title>{{ __('login.login_to_laravelchat') }}</title>
    </head>
    <body>
        <div class="container d-flex justify-content-center align-items-center full-height">
            <div class="form-container">
                <div class="text-center mb-4">
                    <img src="https://cdn-icons-png.flaticon.com/512/5962/5962463.png" alt="ChatApp Logo" style="width: 96px; height: 96px;">
                    <h2>{{ __('login.login_to_laravelchat') }}</h2>
                </div>
                @if (session('status'))
                    <div class="alert alert-success mb-4">
                        {{ session('status') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email">{{ __('login.email') }}</label>
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                        @if ($errors->has('email'))
                            <span class="text-danger">{{ $errors->first('email') }}</span>
                        @endif
                    </div>
                    <div class="form-group mt-3">
                        <label for="password">{{ __('login.password') }}</label>
                        <input id="password" type="password" class="form-control" name="password" required>
                        @if ($errors->has('password'))
                            <span class="text-danger">{{ $errors->first('password') }}</span>
                        @endif
                    </div>
                    <div class="form-group form-check mt-3">
                        <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                        <label class="form-check-label" for="remember_me">{{ __('login.remember_me') }}</label>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('register') }}" class="text-muted">{{ __('login.dont_have_account') }}</a>
                        <button type="submit" class="btn btn-primary btn-custom">{{ __('login.log_in') }}</button>
                    </div>
                </form>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>