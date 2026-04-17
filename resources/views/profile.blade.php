@php($title = 'Profile - Job Connect')
@extends('layout')

@section('content')
    <section>
        <h2>Profile</h2>
        <p><a href="javascript:history.back()">Kembali</a></p>
        <p><a href="/worker/applications">Lihat Lamaran Saya</a></p>
        <button type="button" id="logoutBtn">Logout</button>
        <div id="profileBox"></div>
    </section>

    <script>
        const profileBox = document.getElementById('profileBox');
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

        function renderProfile(data) {
            if (!data || !data.user) {
                profileBox.textContent = 'Profile not available.';
                return;
            }

            const user = data.user;
            const profile = user.profile || {};
            profileBox.innerHTML = '';
            const list = document.createElement('ul');
            const items = [
                `Name: ${user.name || '-'}`,
                `Email: ${user.email || '-'}`,
                `Role: ${user.role || '-'}`,
                `Phone: ${user.phone_number || '-'}`,
                `Avatar: ${user.avatar || '-'}`,
                `Bio: ${profile.bio || '-'}`,
                `Address: ${profile.address || '-'}`,
            ];
            items.forEach((text) => {
                const li = document.createElement('li');
                li.textContent = text;
                list.appendChild(li);
            });
            profileBox.appendChild(list);
        }

        (async function init() {
            const data = await request('/auth/me', { method: 'GET' });
            renderProfile(data);
        })();

        document.getElementById('logoutBtn').addEventListener('click', async () => {
            await request('/auth/logout', { method: 'POST' });
            localStorage.removeItem('apiToken');
            window.location.href = '/login';
        });
    </script>
@endsection
