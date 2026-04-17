@php($title = 'Register - Job Connect')
@extends('layout')

@section('content')
    <section>
        <h2>Register</h2>
        <p><a href="javascript:history.back()">Kembali</a></p>
        <p><a href="/">Kembali ke Dashboard</a></p>
        <div id="notice" style="display: none;"></div>
        <form id="registerForm">
            <label>Name <input name="name" type="text" required /></label><br />
            <label>Email <input name="email" type="email" required /></label><br />
            <label>Password <input name="password" type="password" required /></label><br />
            <label>Confirm Password <input name="password_confirmation" type="password" required /></label><br />
            <label>Role
                <select name="role" required>
                    <option value="admin">admin</option>
                    <option value="employer" selected>employer</option>
                    <option value="worker">worker</option>
                </select>
            </label><br />
            <label>Phone <input name="phone_number" type="text" /></label><br />
            <label>Avatar URL <input name="avatar" type="text" /></label><br />
            <button type="submit">Register</button>
        </form>
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

        document.getElementById('registerForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = formToJson(event.target);
            const result = await request('/auth/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
            if (result?.token) {
                showNotice('Register berhasil. Silakan login.', false);
                setTimeout(() => {
                    window.location.href = '/login';
                }, 500);
            } else {
                showNotice(result?.message || 'Register gagal.', true);
            }
        });
    </script>
@endsection
