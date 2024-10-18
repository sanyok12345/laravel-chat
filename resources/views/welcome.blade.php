<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>LaravelChat</title>
    <style>
        .full-height {
            height: 100vh;
        }
        .btn-custom {
            width: 150px;
        }
        /* Light theme */
        body {
            background-color: #ffffff;
            color: #000000;
        }
        /* Dark theme */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #121212;
                color: #ffffff;
            }
            .btn-primary {
                background-color: #1a73e8;
                border-color: #1a73e8;
            }
            .btn-secondary {
                background-color: #333333;
                border-color: #333333;
            }
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center full-height">
        <div class="text-center">
            <img src="https://cdn-icons-png.flaticon.com/512/5962/5962463.png" alt="ChatApp Logo" class="mb-4" style="width: 96px; height: 96px;">
            <h1>Welcome to LaravelChat</h1>
            <p class="lead">
                A simple chat application built with Laravel and Blade.
            </p>
            <div class="mt-4">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg btn-custom mr-2">Login</a>
                <a href="{{ route('register') }}" class="btn btn-secondary btn-lg btn-custom">Register</a>
            </div>
        </div>
    </div>
</body>
</html>