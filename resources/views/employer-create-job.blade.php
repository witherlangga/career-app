@php($title = 'Tambah Lowongan - Job Connect')
@extends('layout')

@section('content')
    <section>
        <h2>Tambah Lowongan</h2>
        <p><a href="#" onclick="return goBackAndRefresh();">Kembali</a></p>
        <form id="createJobForm">
            <label>Judul <input name="title" type="text" required /></label><br />
            <label>Kategori <input name="category" type="text" required /></label><br />
            <label>Tipe <input name="type" type="text" value="full-time" required /></label><br />
            <label>Rentang Gaji <input name="salary_range" type="text" /></label><br />
            <label>Deskripsi <textarea name="description" required></textarea></label><br />
            <label>Persyaratan <textarea name="requirements"></textarea></label><br />
            <label>Status <input name="status" type="text" value="open" /></label><br />
            <button type="submit">Simpan</button>
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

        document.getElementById('createJobForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = formToJson(event.target);
            const result = await request('/employer/jobs', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
            const jobId = result?.job?.id;
            if (jobId) {
                window.location.href = `/jobs/${jobId}?src=dashboard`;
            }
        });
    </script>
@endsection
