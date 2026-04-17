@php($title = 'Beranda - Job Connect')
@extends('layout')

@section('content')
    <div class="container">
        <!-- Hero Section -->
        <div class="row mb-5">
            <div class="col-md-12">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 60px 30px; border-radius: 10px; text-align: center;">
                    <h1 class="mb-3" style="font-size: 2.5rem; font-weight: 700;">
                        Temukan Pekerjaan Impianmu
                    </h1>
                    <p class="mb-4" style="font-size: 1.1rem;">
                        Bergabunglah dengan ribuan pekerja dalam mencari peluang karir terbaik
                    </p>
                </div>
            </div>
        </div>

        <!-- Search & Filters -->
        <div class="row mb-4">
            <div class="col-md-12">
                <form id="searchForm" class="mb-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="q" placeholder="Cari posisi, perusahaan..." value="">
                        </div>
                        <div class="col-md-2">
                            <select name="type" class="form-select">
                                <option value="">Semua Tipe</option>
                                <option value="full-time">Full-time</option>
                                <option value="part-time">Part-time</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="category" class="form-select">
                                <option value="">Semua Kategori</option>
                                <option value="technology">Technology</option>
                                <option value="marketing">Marketing</option>
                                <option value="sales">Sales</option>
                                <option value="design">Design</option>
                                <option value="hr">HR</option>
                                <option value="finance">Finance</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Cari
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Job Listings -->
        <div class="row">
            <div class="col-md-9">
                <h3 class="mb-4">
                    <i class="bi bi-briefcase"></i> Lowongan Pekerjaan Terbaru
                </h3>
                <div id="jobsList" class="row g-3">
                    <div class="col-12">
                        <div class="text-center py-5">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3">Memuat lowongan pekerjaan...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Stats -->
            <div class="col-md-3">
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Info Job Connect</h6>
                        <p class="mb-2">
                            <strong>Platform terpercaya</strong> untuk mencari dan menempatkan pekerjaan
                        </p>
                        <ul class="list-unstyled small">
                            <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Lowongan terverifikasi</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Proses cepat & mudah</li>
                            <li><i class="bi bi-check-circle text-success"></i> Dukungan profesional</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card" id="applicationsWidget" style="display: none;">
                    <div class="card-body">
                        <h6 class="card-title">Lamaran Saya</h6>
                        <p class="mb-0">
                            Anda telah melamar <strong id="applicationCount">0</strong> pekerjaan
                        </p>
                        <a href="/worker/applications" class="btn btn-sm btn-outline-primary mt-2">
                            Lihat Semua
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;

        async function loadJobs(page = 1) {
            try {
                const searchQuery = document.querySelector('input[name="q"]').value;
                const jobType = document.querySelector('select[name="type"]').value;
                const category = document.querySelector('select[name="category"]').value;
                const token = localStorage.getItem('apiToken');
                
                const params = { page, q: searchQuery };
                if (jobType) params.type = jobType;
                if (category) params.category = category;
                
                const query = buildQuery(params);
                
                const response = await fetch(baseUrl + '/jobs' + query, {
                    headers: token ? { 'Authorization': 'Bearer ' + token } : {}
                });
                const data = await response.json();

                if (data && Array.isArray(data.data)) {
                    renderJobs(data.data);
                    currentPage = page;
                } else {
                    console.error('Unexpected API response:', data);
                    document.getElementById('jobsList').innerHTML = '<div class="col-12"><p class="text-center text-danger">Format API tidak sesuai</p></div>';
                }
            } catch (error) {
                document.getElementById('jobsList').innerHTML = '<div class="col-12"><p class="text-center text-danger">Error memuat data</p></div>';
                console.error('Load jobs error:', error);
            }
        }

        function renderJobs(jobs) {
            if (jobs.length === 0) {
                document.getElementById('jobsList').innerHTML = '<div class="col-12"><p class="text-center">Tidak ada lowongan ditemukan</p></div>';
                return;
            }

            const jobsList = document.getElementById('jobsList');
            jobsList.innerHTML = '';

            jobs.forEach(job => {
                const jobCard = document.createElement('div');
                jobCard.className = 'col-12';
                const employerName = job.employer?.name || 'PT Anonim';
                const jobType = job.type || 'full-time';
                const displayType = jobType === 'full-time' ? 'Full-time' : 'Part-time';
                
                jobCard.innerHTML = `
                    <div class="card job-card h-100" style="cursor: pointer;" onclick="goToJob('${job.id}')">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-2">${job.title}</h5>
                                    <p class="text-muted mb-2">
                                        <i class="bi bi-building"></i> ${employerName}
                                    </p>
                                </div>
                                <span class="badge bg-primary">${displayType}</span>
                            </div>
                            <p class="card-text text-muted mb-3">${job.description.substring(0, 150)}...</p>
                            <div class="d-flex gap-2 align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-cash"></i> ${job.salary_range || 'Sesuai kesepakatan'}
                                </small>
                                <small class="text-muted ms-2">
                                    <i class="bi bi-clock"></i> ${job.created_at ? timeAgo(job.created_at) : 'Baru'}
                                </small>
                            </div>
                        </div>
                    </div>
                `;
                jobsList.appendChild(jobCard);
            });
        }

        function goToJob(jobId) {
            window.location.href = '/jobs/' + jobId;
        }

        function timeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const seconds = Math.floor((now - date) / 1000);
            const minutes = Math.floor(seconds / 60);
            const hours = Math.floor(minutes / 60);
            const days = Math.floor(hours / 24);

            if (days > 0) return days + ' hari lalu';
            if (hours > 0) return hours + ' jam lalu';
            if (minutes > 0) return minutes + ' menit lalu';
            return 'Baru saja';
        }

        async function loadApplicationsCount() {
            const token = localStorage.getItem('apiToken');
            if (!token) return;

            try {
                const response = await fetch(baseUrl + '/worker/applications', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    const widget = document.getElementById('applicationsWidget');
                    // Handle both paginated and direct array responses
                    const applications = Array.isArray(data.data) ? data.data : (data.data?.data || []);
                    const count = Array.isArray(applications) ? applications.length : 0;
                    
                    if (widget && count > 0) {
                        widget.style.display = 'block';
                        document.getElementById('applicationCount').textContent = count;
                    }
                }
            } catch (error) {
                console.error('Load applications count error:', error);
            }
        }

        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            loadJobs(1);
        });

        loadJobs(1);
        loadApplicationsCount();
    </script>
@endsection
