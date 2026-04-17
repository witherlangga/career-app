<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Job Connect' }}</title>
</head>
<body>
    <header>
        <h1>{{ $title ?? 'Job Connect' }}</h1>
        <nav>
            <a href="/">Dashboard</a> |
            <span id="authLinks">
                <a href="/login">Login</a> |
                <a href="/register">Register</a>
            </span>
            <span id="profileLink" style="display: none;">
                | <a href="/profile">Profile</a>
            </span>
        </nav>
        <hr />
    </header>

    <main>
        @yield('content')
    </main>

    <script>
        (function toggleNav() {
            const token = localStorage.getItem('apiToken');
            const authLinks = document.getElementById('authLinks');
            const profileLink = document.getElementById('profileLink');

            if (token) {
                if (authLinks) authLinks.style.display = 'none';
                if (profileLink) profileLink.style.display = 'inline';
            } else {
                if (authLinks) authLinks.style.display = 'inline';
                if (profileLink) profileLink.style.display = 'none';
            }
        })();

        (function refreshOnBack() {
            const refresh = sessionStorage.getItem('refreshOnLoad');
            if (refresh === '1') {
                sessionStorage.removeItem('refreshOnLoad');
                window.location.reload();
            }
        })();

        function goBackAndRefresh() {
            sessionStorage.setItem('refreshOnLoad', '1');
            window.history.back();
            return false;
        }
    </script>
</body>
</html>
