<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Job Connect' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        header h1 {
            margin: 0;
            font-weight: 700;
            font-size: 1.8rem;
        }
        header nav {
            margin-top: 10px;
        }
        header a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }
        header a:hover {
            text-decoration: underline;
        }
        main {
            padding: 40px 20px;
        }
        .container {
            max-width: 1200px;
        }
    </style>
    <script>
        const baseUrl = 'http://127.0.0.1:8000/api/v1';

        function formToJson(form) {
            const formData = new FormData(form);
            const json = {};
            formData.forEach((value, key) => {
                json[key] = value;
            });
            return json;
        }

        async function request(path, options = {}) {
            const headers = options.headers || {};
            const token = localStorage.getItem('apiToken');
            if (token) {
                headers['Authorization'] = 'Bearer ' + token;
            }

            const response = await fetch(baseUrl + path, {
                ...options,
                headers,
            });

            let data;
            try {
                data = await response.json();
            } catch (error) {
                data = { message: 'Invalid JSON response' };
            }

            return data;
        }

        function buildQuery(params) {
            const query = new URLSearchParams(params);
            return query.toString() ? '?' + query.toString() : '';
        }

        function showNotice(message, isError = false) {
            const noticeElement = document.getElementById('notice');
            if (noticeElement) {
                noticeElement.textContent = message;
                noticeElement.className = 'alert ' + (isError ? 'alert-danger' : 'alert-success');
                noticeElement.style.display = 'block';
            } else {
                alert(message);
            }
        }

        function goBackAndRefresh() {
            sessionStorage.setItem('refreshOnLoad', '1');
            window.history.back();
            return false;
        }
    </script>
</head>
<body>
    <header>
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; height: 60px;">
                <div style="display: flex; align-items: center; gap: 50px;">
                    <h1 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                        <i class="bi bi-briefcase-fill" style="font-size: 2rem;"></i> 
                        <span>Job Connect</span>
                    </h1>
                    <a href="/" style="color: white; text-decoration: none; font-weight: 500; font-size: 1rem;"><i class="bi bi-house"></i> Dashboard</a>
                </div>
                <nav style="display: flex; gap: 15px; align-items: center;">
                    <span id="authLinks" style="display: flex; gap: 15px; align-items: center;">
                        <a href="/login" class="btn btn-sm" style="background-color: rgba(255,255,255,0.2); color: white; text-decoration: none; font-weight: 500; padding: 8px 15px; border-radius: 5px; transition: background-color 0.3s;"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                        <a href="/register" class="btn btn-sm btn-light" style="color: #667eea; text-decoration: none; font-weight: 500; padding: 8px 15px; border-radius: 5px;"><i class="bi bi-person-plus"></i> Register</a>
                    </span>
                    <span id="profileLink" style="display: none; gap: 15px; align-items: center;">
                        <a href="/profile" style="color: white; text-decoration: none; font-weight: 500; font-size: 1rem;"><i class="bi bi-person-circle"></i> Profile</a>
                        <button id="logoutBtnHeader" style="background-color: rgba(255,255,255,0.2); color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: 500; transition: background-color 0.3s; font-size: 0.9rem;"><i class="bi bi-box-arrow-right"></i> Logout</button>
                    </span>
                </nav>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navigation toggling based on token
        (function toggleNav() {
            const token = localStorage.getItem('apiToken');
            const authLinks = document.getElementById('authLinks');
            const profileLink = document.getElementById('profileLink');

            if (token) {
                if (authLinks) authLinks.style.display = 'none';
                if (profileLink) {
                    profileLink.style.display = 'flex';
                }
            } else {
                if (authLinks) authLinks.style.display = 'flex';
                if (profileLink) profileLink.style.display = 'none';
            }
        })();

        // Refresh page when coming back from navigation
        (function refreshOnBack() {
            const refresh = sessionStorage.getItem('refreshOnLoad');
            if (refresh === '1') {
                sessionStorage.removeItem('refreshOnLoad');
                window.location.reload();
            }
        })();

        // Logout button in header
        document.addEventListener('DOMContentLoaded', function() {
            const logoutBtn = document.getElementById('logoutBtnHeader');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', async () => {
                    await request('/auth/logout', { method: 'POST' });
                    localStorage.removeItem('apiToken');
                    window.location.href = '/login';
                });
                logoutBtn.addEventListener('mouseover', function() {
                    this.style.backgroundColor = 'rgba(255,255,255,0.3)';
                });
                logoutBtn.addEventListener('mouseout', function() {
                    this.style.backgroundColor = 'rgba(255,255,255,0.2)';
                });
            }
        });
    </script>
</body>
</html>
