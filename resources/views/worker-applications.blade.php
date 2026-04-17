@php($title = 'Lamaran Saya - Job Connect')
@extends('layout')

@section('content')
    <section>
        <h2>Lamaran Saya</h2>
        <p><a href="javascript:history.back()">Kembali</a></p>
        <div id="appsList"></div>
    </section>

    <script>
        const appsList = document.getElementById('appsList');
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

        function renderApplications(apps) {
            if (!Array.isArray(apps) || apps.length === 0) {
                appsList.textContent = 'Belum ada lamaran.';
                return;
            }

            const list = document.createElement('ul');
            apps.forEach((app) => {
                const job = app.job_post || app.jobPost || {};
                const item = document.createElement('li');
                const title = job.title || 'Lowongan';
                const status = app.status || '-';
                const link = document.createElement('a');
                link.href = `/jobs/${job.id}?src=applications`;
                link.textContent = title;
                item.appendChild(link);
                item.appendChild(document.createTextNode(` | Status: ${status}`));
                list.appendChild(item);
            });
            appsList.innerHTML = '';
            appsList.appendChild(list);
        }

        (async function init() {
            const data = await request('/worker/applications', { method: 'GET' });
            renderApplications(data?.data || []);
        })();
    </script>
@endsection
