@php($title = 'Edit Lowongan - Job Connect')
@extends('layout')

@section('content')
    <section>
        <h2>Edit Lowongan</h2>
        <p><a href="#" onclick="return goBackAndRefresh();">Kembali</a></p>
        <form id="updateJobForm">
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

            let data;
            try {
                data = await response.json();
            } catch (error) {
                data = { message: 'Invalid JSON response' };
            }

            return data;
        }

        function fillForm(job) {
            const form = document.getElementById('updateJobForm');
            form.title.value = job.title || '';
            form.category.value = job.category || '';
            form.type.value = job.type || '';
            form.salary_range.value = job.salary_range || '';
            form.description.value = job.description || '';
            form.requirements.value = job.requirements || '';
            form.status.value = job.status || '';
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

        async function init() {
            const jobResponse = await request('/jobs/' + jobId, { method: 'GET' });
            const job = jobResponse?.job;
            if (!job) {
                return;
            }
            fillForm(job);
        }

        document.getElementById('updateJobForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = formToJson(event.target);
            await request('/employer/jobs/' + jobId, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
        });

        init();
    </script>
@endsection
