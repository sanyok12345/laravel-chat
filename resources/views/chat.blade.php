<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="api-token" content="{{ $apiToken }}">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, viewport-fit=cover" />        
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <title>{{ __('chat.chat') }}</title>
        <link href={{ asset('css/chat.css') }} rel="stylesheet">
    </head>
    <body>
        <div class="container full-height d-flex-center">
            <div class="chat-container">
                <div class="chat-header">
                    <h4>{{ __('chat.chat') }}</h4>
                    <div class="ml-auto">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a href="#" class="btn btn-link btn-flat" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('chat.exit') }}</a>
                    </div>
                </div>
                <div id="messages" class="chat-messages loading">
                    <img class="spinner" src="https://cdn-icons-png.flaticon.com/512/7794/7794282.png" alt="{{ __('chat.loading') }}">
                </div>
                <form id="form" class="chat-input">
                    <input id="text" type="text" class="form-control" placeholder="{{ __('chat.type_message') }}" required>
                    <button class="btn btn-primary" type="submit">{{ __('chat.send') }}</button>
                </form>
            </div>
        </div>
        <script src={{ asset('js/api.js') }}></script>
        <script src={{ asset('js/chat.js') }}></script>
    </body>
</html>