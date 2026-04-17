@php($title = 'Edit Profile - Job Connect')
@extends('layout')

@section('content')
    <section>
        <h2>Edit Profile</h2>
        <p><a href="#" onclick="return goBackAndRefresh();">Kembali</a></p>
        <p id="notice"></p>
        <form id="profileForm">
            <label>Nama <input name="name" type="text" /></label><br />
            <label>Email <input name="email" type="email" /></label><br />
            <label>Nomor HP <input name="phone_number" type="text" /></label><br />
            <label>Bio <textarea name="bio"></textarea></label><br />
            <label>Alamat <textarea name="address"></textarea></label><br />
            <label>Skills (pisahkan dengan koma) <input name="skills" type="text" /></label><br />
            <button type="submit">Simpan</button>
        </form>
        <div id="avatarPreview" style="margin: 8px 0;"></div>
        <form id="avatarForm" enctype="multipart/form-data">
            <label>Upload Avatar (JPG/JPEG) <input name="avatar" type="file" accept="image/jpeg" /></label><br />
            <button type="submit">Upload Avatar</button>
        </form>
        <form id="cvForm" enctype="multipart/form-data">
            <label>Upload CV (PDF/DOC/DOCX) <input name="cv" type="file" /></label><br />
            <button type="submit">Upload CV</button>
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

            try {
                return await response.json();
            } catch (error) {
                return { message: 'Invalid JSON response' };
            }
        }

        function fillProfileForm(user) {
            const profile = user.profile || {};
            const form = document.getElementById('profileForm');
            if (!form) return;
            form.name.value = user.name || '';
            form.email.value = user.email || '';
            form.phone_number.value = user.phone_number || '';
            form.bio.value = profile.bio || '';
            form.address.value = profile.address || '';
            if (Array.isArray(profile.skills)) {
                form.skills.value = profile.skills.join(', ');
            } else {
                form.skills.value = '';
            }

            const avatarPreview = document.getElementById('avatarPreview');
            avatarPreview.innerHTML = '';
            if (user.avatar) {
                const avatarImg = document.createElement('img');
                avatarImg.src = `/storage/${user.avatar}`;
                avatarImg.alt = 'Avatar';
                avatarImg.style.maxWidth = '160px';
                avatarImg.style.display = 'block';
                avatarPreview.appendChild(avatarImg);
            }
        }

        function buildSkills(value) {
            return value
                .split(',')
                .map((skill) => skill.trim())
                .filter((skill) => skill.length > 0);
        }

        (async function init() {
            const data = await request('/auth/me', { method: 'GET' });
            if (data?.user) {
                fillProfileForm(data.user);
            }
        })();

        document.getElementById('profileForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.target);
            const payload = {
                name: formData.get('name'),
                email: formData.get('email'),
                phone_number: formData.get('phone_number'),
                bio: formData.get('bio'),
                address: formData.get('address'),
                skills: buildSkills(formData.get('skills') || ''),
            };
            const token = localStorage.getItem('apiToken');
            const response = await fetch(baseUrl + '/profile', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    ...(token ? { Authorization: 'Bearer ' + token } : {}),
                },
                body: JSON.stringify(payload),
            });
            const notice = document.getElementById('notice');
            let data = null;
            try {
                data = await response.json();
            } catch (error) {
                data = { message: 'Invalid JSON response' };
            }
            if (!response.ok) {
                notice.textContent = data?.message || 'Gagal menyimpan profile.';
                return;
            }
            notice.textContent = 'Profile berhasil disimpan.';
            window.location.href = '/profile';
        });

        document.getElementById('cvForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.target);
            if (!formData.get('cv')) {
                return;
            }
            await request('/profile/cv', {
                method: 'POST',
                body: formData,
            });
            window.location.href = '/profile';
        });

        document.getElementById('avatarForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.target);
            if (!formData.get('avatar')) {
                return;
            }
            const token = localStorage.getItem('apiToken');
            const response = await fetch(baseUrl + '/profile/avatar', {
                method: 'POST',
                headers: token ? { Authorization: 'Bearer ' + token } : {},
                body: formData,
            });
            const notice = document.getElementById('notice');
            let payload = null;
            try {
                payload = await response.json();
            } catch (error) {
                payload = { message: 'Invalid JSON response' };
            }
            if (!response.ok) {
                notice.textContent = payload?.message || 'Upload gagal.';
                return;
            }
            notice.textContent = 'Avatar berhasil diupload.';
            window.location.href = '/profile';
        });
    </script>
@endsection
