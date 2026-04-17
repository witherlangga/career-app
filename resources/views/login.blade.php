@php($title = 'Login - Job Connect')
@extends('layout')

@section('content')
    <section>
        <h2>Login</h2>
        <div id="notice" style="display: none;"></div>
        <form id="loginForm">
            <label>Email <input name="email" type="email" required /></label><br />
            <label>Password <input name="password" type="password" required /></label><br />
            <button type="submit">Login</button>
        </form>
        <button type="button" id="logoutBtn">Logout (current)</button>
    </section>

    <script>
        const baseUrl = 'http://127.0.0.1:8000/api/v1';

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

        function showNotice(message, isError = false) {
            const notice = document.getElementById('notice');
            notice.textContent = message;
            notice.style.display = 'block';
            notice.style.color = isError ? 'red' : 'green';
        }

        function formToJson(form) {
            const formData = new FormData(form);
            const payload = {};
            for (const [key, value] of formData.entries()) {
                if (value === '') {
                    continue;
                }
                payload[key] = value;
            }
            return payload;
        }

        document.getElementById('loginForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = formToJson(event.target);
            const result = await request('/auth/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
            if (result?.token) {
                localStorage.setItem('apiToken', result.token);
                showNotice('Login berhasil. Mengarahkan ke dashboard...');
                window.location.href = '/';
            } else {
                showNotice(result?.message || 'Login gagal.', true);
            }
        });

        document.getElementById('logoutBtn').addEventListener('click', async () => {
            await request('/auth/logout', { method: 'POST' });
            localStorage.removeItem('apiToken');
        });
    </script>
@endsection
