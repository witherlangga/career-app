@php($title = 'Detail Lowongan - Job Connect')
@extends('layout')

@section('content')
    <section>
        <h2>Detail Lowongan</h2>
        <p><a href="javascript:history.back()">Kembali</a></p>
        <div id="jobBox"></div>
    </section>

    <section id="applySection" style="display: none;">
        <h2>Melamar</h2>
        <p><a id="applyLink" href="#">Lamar Pekerjaan</a></p>
    </section>

    <section id="ownerSection" style="display: none;">
        <h2>Kelola Lowongan</h2>
        <p><a id="editJobLink" href="#">Edit Lowongan</a></p>
        <button type="button" id="deleteJobBtn">Hapus Lowongan</button>
    </section>

    <script>
        const jobBox = document.getElementById('jobBox');
        const applySection = document.getElementById('applySection');
        const ownerSection = document.getElementById('ownerSection');
        const baseUrl = 'http://127.0.0.1:8000/api/v1';
        const jobId = window.location.pathname.split('/').pop();
        const source = new URLSearchParams(window.location.search).get('src');

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

        function renderJob(job) {
            if (!job) {
                jobBox.textContent = 'Lowongan tidak ditemukan.';
                return;
            }

            const list = document.createElement('ul');
            const items = [
                `Judul: ${job.title || '-'}`,
                `Kategori: ${job.category || '-'}`,
                `Tipe: ${job.type || '-'}`,
                `Rentang Gaji: ${job.salary_range || '-'}`,
                `Status: ${job.status || '-'}`,
                `Deskripsi: ${job.description || '-'}`,
                `Persyaratan: ${job.requirements || '-'}`,
            ];
            items.forEach((text) => {
                const li = document.createElement('li');
                li.textContent = text;
                list.appendChild(li);
            });
            jobBox.innerHTML = '';
            jobBox.appendChild(list);
        }

        async function init() {
            const jobResponse = await request('/jobs/' + jobId, { method: 'GET' });
            const job = jobResponse?.job;
            renderJob(job);

            const token = localStorage.getItem('apiToken');
            if (!token) {
                applySection.style.display = 'none';
                ownerSection.style.display = 'none';
                return;
            }

            const me = await request('/auth/me', { method: 'GET' });
            const user = me?.user;
            if (!user || !job) {
                return;
            }

            const isOwner = job.employer_id === user.id;
            if (isOwner) {
                ownerSection.style.display = 'block';
                applySection.style.display = 'none';
                const editLink = document.getElementById('editJobLink');
                if (editLink) {
                    editLink.href = `/employer/jobs/${job.id}/edit`;
                }
            } else {
                ownerSection.style.display = 'none';
                const canApply = user.role === 'worker' && source === 'dashboard';
                applySection.style.display = canApply ? 'block' : 'none';
                const applyLink = document.getElementById('applyLink');
                if (applyLink) {
                    applyLink.href = `/jobs/${job.id}/apply`;
                }
            }
        }

        document.getElementById('deleteJobBtn').addEventListener('click', async () => {
            await request('/employer/jobs/' + jobId, { method: 'DELETE' });
            window.location.href = '/';
        });

        init();
    </script>
@endsection
