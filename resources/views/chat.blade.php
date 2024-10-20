<html>
    <meta charset="UTF-8">
    <meta name="api-token" content="{{ $apiToken }}">
    <body>
        <h1>Chat</h1>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
        <div>
            <div>
                <input type="text" id="message" placeholder="Type your message">
                <button onclick="handleSendMessage()">Send</button>
            </div>
        </div>
        <script src={{ asset('js/api.js') }}></script>
        <script src={{ asset('js/chat.js') }}></script>
    </body>
</html>