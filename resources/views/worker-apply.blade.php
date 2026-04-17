@php($title = 'Lamar Pekerjaan - Job Connect')
@extends('layout')

@section('content')
    <section>
        <h2>Lamar Pekerjaan</h2>
        <p><a href="#" onclick="return goBackAndRefresh();">Kembali</a></p>
        <form id="applyForm" enctype="multipart/form-data">
            <label>Nama Lengkap <input name="full_name" type="text" required /></label><br />
            <label>Email <input name="email" type="email" required /></label><br />
            <label>Nomor HP <input name="phone_number" type="text" required /></label><br />
            <label>Alamat <textarea name="address" required></textarea></label><br />
            <label>Posisi Saat Ini <input name="current_role" type="text" required /></label><br />
            <label>Lama Pengalaman (tahun) <input name="experience_years" type="number" min="0" required /></label><br />
            <label>Keahlian (pisahkan dengan koma) <input name="skills" type="text" required /></label><br />
            <label>Ekspektasi Gaji <input name="expected_salary" type="text" required /></label><br />
            <label>Ringkasan / Surat Lamaran <textarea name="cover_letter" required></textarea></label><br />
            <label>Upload CV (PDF/DOC/DOCX) <input name="cv" type="file" required /></label><br />
            <button type="submit">Kirim Lamaran</button>
        </form>
    </section>

    <script>
        const baseUrl = 'http://127.0.0.1:8000/api/v1';
        const jobId = window.location.pathname.split('/').slice(-2, -1)[0];

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

        async function ensureWorker() {
            const me = await request('/auth/me', { method: 'GET' });
            if (me?.user?.role !== 'worker') {
                window.location.href = '/';
                return false;
            }
            return true;
        }

        function buildBio(form) {
            const data = new FormData(form);
            const currentRole = data.get('current_role');
            const experience = data.get('experience_years');
            const expectedSalary = data.get('expected_salary');
            const coverLetter = data.get('cover_letter');

            return `Posisi saat ini: ${currentRole}\nPengalaman: ${experience} tahun\nEkspektasi gaji: ${expectedSalary}\n\n${coverLetter}`;
        }

        function buildSkills(value) {
            return value
                .split(',')
                .map((skill) => skill.trim())
                .filter((skill) => skill.length > 0);
        }

        document.getElementById('applyForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            const ok = await ensureWorker();
            if (!ok) return;
            const form = event.target;
            const formData = new FormData(form);

            const profilePayload = {
                bio: buildBio(form),
                address: formData.get('address'),
                skills: buildSkills(formData.get('skills') || ''),
            };

            await request('/profile', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(profilePayload),
            });

            const cvForm = new FormData();
            cvForm.append('cv', formData.get('cv'));
            await request('/profile/cv', {
                method: 'POST',
                body: cvForm,
            });

            await request('/jobs/' + jobId + '/apply', { method: 'POST' });
            window.location.href = '/jobs/' + jobId;
        });

        (async function init() {
            await ensureWorker();
        })();
    </script>
@endsection
