@php($title = 'Dashboard - Job Connect')
@extends('layout')

@section('content')
    <section>
        <h2>Lowongan Terbaru</h2>
        <p><a id="addJobLink" href="/employer/jobs/new" style="display: none;">Tambah Lowongan</a></p>
        <form id="searchForm">
            <label>Cari <input name="q" type="text" /></label>
            <button type="submit">Cari</button>
        </form>
        <div id="jobsList"></div>
    </section>


    <script>
        const jobsList = document.getElementById('jobsList');
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

        function buildQuery(params) {
            const searchParams = new URLSearchParams();
            Object.entries(params).forEach(([key, value]) => {
                if (value !== '' && value !== null && typeof value !== 'undefined') {
                    searchParams.append(key, value);
                }
            });
            const qs = searchParams.toString();
            return qs ? '?' + qs : '';
        }

        function renderJobs(jobs) {
            if (!Array.isArray(jobs) || jobs.length === 0) {
                jobsList.textContent = 'Tidak ada lowongan.';
                return;
            }

            const list = document.createElement('ul');
            jobs.forEach((job) => {
                const item = document.createElement('li');
                const link = document.createElement('a');
                link.href = `/jobs/${job.id}?src=dashboard`;
                link.textContent = `${job.title} | ${job.category} | ${job.type} | ${job.status}`;
                item.appendChild(link);
                list.appendChild(item);
            });
            jobsList.innerHTML = '';
            jobsList.appendChild(list);
        }

        async function toggleEmployerLink() {
            const addJobLink = document.getElementById('addJobLink');
            const token = localStorage.getItem('apiToken');
            if (!addJobLink || !token) {
                if (addJobLink) addJobLink.style.display = 'none';
                return;
            }

            const data = await request('/auth/me', { method: 'GET' });
            const role = data?.user?.role || null;
            addJobLink.style.display = role === 'employer' ? 'inline' : 'none';
        }

        (async function init() {
            const data = await request('/jobs', { method: 'GET' });
            renderJobs(data?.data || []);
            await toggleEmployerLink();
        })();

        document.getElementById('searchForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.target);
            const query = buildQuery({ q: formData.get('q') });
            const data = await request('/jobs' + query, { method: 'GET' });
            renderJobs(data?.data || []);
        });
    </script>
@endsection
