@php($title = 'Kelola Lowongan - Job Connect')
@extends('layout')

@section('content')
    <section>
        <h2>Kelola Lowongan (Employer)</h2>
        <p><a href="javascript:history.back()">Kembali</a></p>
        <p><a href="/employer/jobs/new">Tambah Lowongan</a></p>
        <p><a href="/">Lihat daftar lowongan</a></p>
        <button type="button" id="loadMyJobsBtn">Muat Lowongan Saya</button>
        <div id="myJobsList"></div>
    </section>

    <section>
        <h2>Edit Lowongan</h2>
        <form id="updateJobForm">
            <label>ID Lowongan <input name="job_id" type="number" required /></label><br />
            <label>Judul <input name="title" type="text" /></label><br />
            <label>Kategori <input name="category" type="text" /></label><br />
            <label>Tipe <input name="type" type="text" /></label><br />
            <label>Rentang Gaji <input name="salary_range" type="text" /></label><br />
            <label>Deskripsi <textarea name="description"></textarea></label><br />
            <label>Persyaratan <textarea name="requirements"></textarea></label><br />
            <label>Status <input name="status" type="text" /></label><br />
            <button type="submit">Simpan Perubahan</button>
        </form>
    </section>

    <section>
        <h2>Hapus Lowongan</h2>
        <form id="deleteJobForm">
            <label>ID Lowongan <input name="job_id" type="number" required /></label><br />
            <button type="submit">Hapus</button>
        </form>
    </section>

    <script>
        const myJobsList = document.getElementById('myJobsList');
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

        function renderMyJobs(jobs) {
            if (!Array.isArray(jobs) || jobs.length === 0) {
                myJobsList.textContent = 'Belum ada lowongan.';
                return;
            }

            const list = document.createElement('ul');
            jobs.forEach((job) => {
                const item = document.createElement('li');
                item.textContent = `${job.id} | ${job.title} | ${job.status}`;
                list.appendChild(item);
            });
            myJobsList.innerHTML = '';
            myJobsList.appendChild(list);
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

        document.getElementById('loadMyJobsBtn').addEventListener('click', async () => {
            const data = await request('/employer/jobs', { method: 'GET' });
            renderMyJobs(data?.data || []);
        });

        document.getElementById('updateJobForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = formToJson(event.target);
            const jobId = payload.job_id;
            delete payload.job_id;
            await request('/employer/jobs/' + jobId, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
        });

        document.getElementById('deleteJobForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = formToJson(event.target);
            await request('/employer/jobs/' + payload.job_id, { method: 'DELETE' });
        });
    </script>
@endsection
