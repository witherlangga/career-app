@php($title = 'Profile - Job Connect')
@extends('layout')

@section('content')
    <section>
        <h2>Profile</h2>
        <p><a href="#" onclick="return goBackAndRefresh();">Kembali</a></p>
        <p><a id="workerAppsLink" href="/worker/applications" style="display: none;">Lihat Lamaran Saya</a></p>
        <p><a href="/profile/edit">Edit Profile</a></p>
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
            const avatarPath = user.avatar ? `/storage/${user.avatar}` : null;
            profileBox.innerHTML = '';
            const list = document.createElement('ul');
            const items = [
                `Nama: ${user.name || '-'}`,
                `Email: ${user.email || '-'}`,
                `Role: ${user.role || '-'}`,
                `Nomor HP: ${user.phone_number || '-'}`,
                `Avatar: ${user.avatar || '-'}`,
                `Bio: ${profile.bio || '-'}`,
                `Alamat: ${profile.address || '-'}`,
                `Skills: ${Array.isArray(profile.skills) ? profile.skills.join(', ') : '-'}`,
                `CV: ${profile.cv || '-'}`,
            ];
            items.forEach((text) => {
                const li = document.createElement('li');
                li.textContent = text;
                list.appendChild(li);
            });
            if (avatarPath) {
                const avatarItem = document.createElement('li');
                const avatarImg = document.createElement('img');
                avatarImg.src = avatarPath;
                avatarImg.alt = 'Avatar';
                avatarImg.style.maxWidth = '160px';
                avatarImg.style.display = 'block';
                avatarImg.style.marginTop = '8px';
                avatarItem.appendChild(avatarImg);
                list.appendChild(avatarItem);
            }
            profileBox.appendChild(list);
        }

        (async function init() {
            const data = await request('/auth/me', { method: 'GET' });
            renderProfile(data);
            const role = data?.user?.role || null;
            const workerAppsLink = document.getElementById('workerAppsLink');
            if (workerAppsLink) {
                workerAppsLink.style.display = role === 'worker' ? 'inline' : 'none';
            }
        })();

        document.getElementById('logoutBtn').addEventListener('click', async () => {
            await request('/auth/logout', { method: 'POST' });
            localStorage.removeItem('apiToken');
            window.location.href = '/login';
        });
    </script>
@endsection
