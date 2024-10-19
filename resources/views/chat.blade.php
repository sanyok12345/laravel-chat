<html>
    <meta charset="UTF-8">
    <meta name="api-token" content="{{ $apiToken }}">
    <body>
        <h1>Chat</h1>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
        <script src={{ asset('js/api.js') }}></script>
        <script>
            API.call('GET', '/api/long-poll/messages', null, (response) => {
                console.log(response);
            });
        </script>
    </body>
</html>