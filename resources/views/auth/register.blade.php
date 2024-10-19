<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href={{ asset('css/register.css') }} rel="stylesheet">
    <title>{{ __('register.register_to_laravelchat') }}</title>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center full-height">
        <div class="form-container">
            <div class="text-center mb-4">
                <img src="https://cdn-icons-png.flaticon.com/512/5962/5962463.png" alt="ChatApp Logo" style="width: 96px; height: 96px;">                <h2>{{ __('register.register_to_laravelchat') }}</h2>
            </div>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-group">
                    <label for="name">{{ __('register.name') }}</label>
                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                    @if ($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                </div>
                <div class="form-group mt-3">
                    <label for="email">{{ __('register.email') }}</label>
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                    @if ($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif
                </div>
                <div class="form-group mt-3">
                    <label for="password">{{ __('register.password') }}</label>
                    <input id="password" type="password" class="form-control" name="password" required>
                    @if ($errors->has('password'))
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                    @endif
                </div>
                <div class="form-group mt-3">
                    <label for="password_confirmation">{{ __('register.confirm_password') }}</label>
                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                    @if ($errors->has('password_confirmation'))
                        <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                    @endif
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="{{ route('login') }}" class="text-muted">{{ __('register.already_registered') }}</a>
                    <button type="submit" class="btn btn-primary btn-custom">{{ __('register.register') }}</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>