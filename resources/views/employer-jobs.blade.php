@php($title = 'Lowongan Saya - Job Connect')
@extends('layout')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-briefcase"></i> Lowongan Pekerjaan Saya</h2>
                    <a href="/employer/jobs/create" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Buat Lowongan Baru
                    </a>
                </div>

                <div class="row" id="jobsList">
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function loadMyJobs() {
            try {
                const response = await fetch(baseUrl + '/employer/jobs', {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem('apiToken') }
                });
                const data = await response.json();

                console.log('My jobs response:', data);
                
                if (data.data && Array.isArray(data.data)) {
                    renderJobs(data.data);
                } else {
                    document.getElementById('jobsList').innerHTML = '<div class="col-12"><p class="text-center text-muted">Anda belum membuat lowongan pekerjaan</p></div>';
                }
            } catch (error) {
                console.error('Load my jobs error:', error);
                document.getElementById('jobsList').innerHTML = '<div class="col-12"><p class="text-center text-danger">Error memuat data: ' + error.message + '</p></div>';
            }
        }

        function renderJobs(jobs) {
            if (jobs.length === 0) {
                document.getElementById('jobsList').innerHTML = '<div class="col-12"><p class="text-center text-muted">Anda belum membuat lowongan pekerjaan</p></div>';
                return;
            }

            const jobsHtml = jobs.map(job => {
                const jobType = job.type === 'full-time' ? 'Full-time' : 'Part-time';
                const displayStatus = job.status === 'open' ? 'Dibuka' : 'Ditutup';
                return `
                <div class="col-md-12 mb-3">
                    <div class="card job-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-2">${job.title}</h5>
                                    <p class="text-muted mb-2">
                                        <i class="bi bi-cash"></i> ${job.salary_range || 'Sesuai kesepakatan'}
                                    </p>
                                    <div class="mb-2">
                                        <span class="badge ${getJobStatusBadge(job.status)}">${displayStatus}</span>
                                        <span class="badge bg-info">${jobType}</span>
                                    </div>
                                    <p class="small text-muted mb-0">Posted: ${new Date(job.created_at).toLocaleDateString('id-ID')}</p>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="/jobs/${job.id}"><i class="bi bi-eye"></i> Lihat Detail</a></li>
                                        <li><a class="dropdown-item" href="/employer/jobs/${job.id}/edit"><i class="bi bi-pencil"></i> Edit</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteJob('${job.id}')\"><i class="bi bi-trash"></i> Hapus</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `}).join('');

            document.getElementById('jobsList').innerHTML = jobsHtml;
        }

        function getJobStatusBadge(status) {
            const badges = {
                'published': 'bg-success',
                'draft': 'bg-secondary',
                'closed': 'bg-danger'
            };
            return badges[status] || 'bg-secondary';
        }

        function getJobStatusLabel(status) {
            const labels = {
                'published': 'Published',
                'draft': 'Draft',
                'closed': 'Closed'
            };
            return labels[status] || status;
        }

        async function deleteJob(jobId) {
            if (confirm('Yakin ingin menghapus lowongan ini?')) {
                try {
                    const response = await fetch(baseUrl + '/employer/jobs/' + jobId, {
                        method: 'DELETE',
                        headers: { 'Authorization': 'Bearer ' + localStorage.getItem('apiToken') }
                    });

                    if (response.ok) {
                        showNotice('Lowongan berhasil dihapus');
                        setTimeout(() => {
                            loadMyJobs();
                        }, 1000);
                    } else {
                        showNotice('Gagal menghapus lowongan', true);
                    }
                } catch (error) {
                    showNotice('Error: ' + error.message, true);
                }
            }
        }

        loadMyJobs();
    </script>
@endsection
