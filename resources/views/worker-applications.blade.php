@php($title = 'Lamaran Saya - Job Connect')
@extends('layout')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-briefcase-check"></i> Lamaran Saya</h2>
                    <a href="/" class="btn btn-primary">
                        <i class="bi bi-search"></i> Cari Lowongan
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div id="applicationsList" class="mb-4">
                            <div class="text-center py-5">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function loadApplications() {
            try {
                const response = await fetch(baseUrl + '/worker/applications', {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem('apiToken') }
                });
                const data = await response.json();

                console.log('Applications response:', data);
                
                if (data.data && Array.isArray(data.data)) {
                    renderApplications(data.data);
                } else {
                    document.getElementById('applicationsList').innerHTML = '<p class="text-center text-muted">Anda belum melamar ke pekerjaan manapun</p>';
                }
            } catch (error) {
                console.error('Load applications error:', error);
                document.getElementById('applicationsList').innerHTML = '<p class="text-center text-danger">Error memuat data: ' + error.message + '</p>';
            }
        }

        function renderApplications(applications) {
            const listHtml = applications.map(app => {
                const employerName = app.jobPost?.employer?.name || 'Perusahaan';
                const jobTitle = app.jobPost?.title || 'Pekerjaan';
                return `
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="card-title mb-2">${jobTitle}</h5>
                                        <p class="text-muted mb-2">
                                            <i class="bi bi-building"></i> ${employerName}
                                        </p>
                                        <p class="mb-2">
                                            <small><strong>Status:</strong></small><br>
                                            <span class="badge ${getStatusBadge(app.status)}">
                                                ${getStatusLabel(app.status)}
                                            </span>
                                        </p>
                                        <p class="text-muted small">
                                            <i class="bi bi-calendar"></i> Dilamar: ${new Date(app.applied_at).toLocaleDateString('id-ID')}
                                        </p>
                                    </div>
                                    <div>
                                        <a href="/jobs/${app.job_post_id}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-arrow-right"></i> Lihat
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `}).join('');

            document.getElementById('applicationsList').innerHTML = listHtml || '<p class="text-center text-muted">Tidak ada lamaran</p>';
        }

        function getStatusBadge(status) {
            const badges = {
                'pending': 'bg-warning',
                'approved': 'bg-success',
                'rejected': 'bg-danger',
                'withdrawn': 'bg-secondary'
            };
            return badges[status] || 'bg-secondary';
        }

        function getStatusLabel(status) {
            const labels = {
                'pending': 'Menunggu',
                'approved': 'Disetujui',
                'rejected': 'Ditolak',
                'withdrawn': 'Dibatalkan'
            };
            return labels[status] || status;
        }

        loadApplications();
    </script>
@endsection
