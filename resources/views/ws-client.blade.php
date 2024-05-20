<html>
<head>
    <title>WS Client</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<textarea id="msg"></textarea>
<button id="btn-base">Base</button>
<button id="btn-broadcast">Broadcast</button>
<ul id="output"></ul>

<script type="text/javascript">
    // page elements
    const msg = document.getElementById('msg')
    const btnBase = document.getElementById('btn-base')
    const btnBroadcast = document.getElementById('btn-broadcast')
    const output = document.getElementById('output')
    let counter = 0;

    const connect = (token) => {
        let conveyor = new window.Conveyor({
            protocol: '{{ $protocol }}',
            uri: '{{ $uri }}',
            port: {{ $wsPort }},
            channel: '{{ $channel }}',
            query: '?token=' + token,
            onMessage: (e) => {
                const newEl = document.createElement('li');
                newEl.innerHTML = e + ' Counter: ' + counter;
                output.appendChild(newEl);
                counter++;
            },
            onReady: () => {
                btnBase.addEventListener('click', () => conveyor.send(msg.value))
                btnBroadcast.addEventListener('click', () => conveyor.send(msg.value, 'broadcast-action'))
            },
        });
    };

    const  getAuth = (callback) => {
        fetch('/broadcasting/auth?channel_name={{ $channel }}', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        })
            .then(response => response.json())
            .then(data => callback(data.auth))
            .catch(error => console.error(error));
    }

    document.addEventListener("DOMContentLoaded", () => getAuth(connect));
</script>
</body>
</html>
