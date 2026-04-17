@php($title = 'Kelola Lamaran - Job Connect')
@extends('layout')

@section('content')
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-md-12">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px; border-radius: 10px; text-align: center;">
                    <h1 class="mb-2" style="font-size: 2rem; font-weight: 700;">
                        <i class="bi bi-inbox"></i> Kelola Lamaran
                    </h1>
                    <p style="font-size: 1rem;">Review dan kelola semua lamaran untuk pekerjaan Anda</p>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Filter Status</label>
                                <select id="statusFilter" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="pending">Menunggu</option>
                                    <option value="accepted">Diterima</option>
                                    <option value="rejected">Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Filter Pekerjaan</label>
                                <select id="jobFilter" class="form-select">
                                    <option value="">Semua Pekerjaan</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button class="btn btn-primary w-100" id="applyFilterBtn">
                                    <i class="bi bi-funnel"></i> Apply Filter
                                </button>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button class="btn btn-outline-secondary w-100" id="resetFilterBtn">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div id="notification" class="alert" style="display: none;"></div>

        <!-- Applications List -->
        <div class="row">
            <div class="col-md-12">
                <div id="applicationsList">
                    <div class="text-center py-5">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3">Memuat lamaran...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let allApplications = [];
        let currentJobId = null;

        async function loadJobs() {
            try {
                const token = localStorage.getItem('apiToken');
                const response = await fetch(baseUrl + '/employer/jobs', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                
                if (!response.ok) throw new Error('Failed to load jobs');
                
                const data = await response.json();
                const jobs = Array.isArray(data.data) ? data.data : data.data?.data || [];
                
                const jobFilter = document.getElementById('jobFilter');
                jobs.forEach(job => {
                    const option = document.createElement('option');
                    option.value = job.id;
                    option.textContent = job.title;
                    jobFilter.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading jobs:', error);
            }
        }

        async function loadApplicationsForJob(jobId) {
            try {
                const token = localStorage.getItem('apiToken');
                const response = await fetch(baseUrl + `/employer/jobs/${jobId}/applications`, {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                
                if (!response.ok) throw new Error('Failed to load applications');
                
                const data = await response.json();
                allApplications = Array.isArray(data.data) ? data.data : data.data?.data || [];
                
                renderApplications(allApplications);
            } catch (error) {
                console.error('Error loading applications:', error);
                showNotification('Error memuat lamaran', true);
            }
        }

        async function loadAllApplications() {
            try {
                const token = localStorage.getItem('apiToken');
                const response = await fetch(baseUrl + '/employer/jobs', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                
                if (!response.ok) throw new Error('Failed to load jobs');
                
                const jobsData = await response.json();
                const jobs = Array.isArray(jobsData.data) ? jobsData.data : jobsData.data?.data || [];
                
                let allApps = [];
                
                for (const job of jobs) {
                    try {
                        const response = await fetch(baseUrl + `/employer/jobs/${job.id}/applications`, {
                            headers: { 'Authorization': 'Bearer ' + token }
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
                            const apps = Array.isArray(data.data) ? data.data : data.data?.data || [];
                            allApps = [...allApps, ...apps.map(app => ({...app, jobPost: job}))];
                        }
                    } catch (err) {
                        console.error('Error loading applications for job ' + job.id, err);
                    }
                }
                
                allApplications = allApps;
                renderApplications(allApplications);
            } catch (error) {
                console.error('Error loading all applications:', error);
                showNotification('Error memuat lamaran', true);
            }
        }

        function renderApplications(applications) {
            const container = document.getElementById('applicationsList');
            
            if (!applications || applications.length === 0) {
                container.innerHTML = '<div class="alert alert-info">Tidak ada lamaran ditemukan</div>';
                return;
            }

            let html = '';
            applications.forEach(app => {
                const jobTitle = app.jobPost?.title || app.job_post?.title || 'Untitled Job';
                const workerName = app.worker?.name || 'Unknown';
                const workerEmail = app.worker?.email || '';
                const statusColor = app.status === 'pending' ? 'warning' : app.status === 'accepted' ? 'success' : 'danger';
                const statusLabel = app.status === 'pending' ? 'Menunggu' : app.status === 'accepted' ? 'Diterima' : 'Ditolak';
                
                html += `
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="card-title mb-2">${jobTitle}</h6>
                                    <p class="mb-1">
                                        <strong>${workerName}</strong>
                                        <br>
                                        <i class="bi bi-envelope"></i> ${workerEmail}
                                    </p>
                                    ${app.worker?.phone_number ? `<p class="mb-0 text-muted"><i class="bi bi-telephone"></i> ${app.worker.phone_number}</p>` : ''}
                                </div>
                                <div class="col-md-4 text-end">
                                    <p class="mb-2">
                                        <span class="badge bg-${statusColor}">${statusLabel}</span>
                                    </p>
                                    <small class="text-muted">
                                        Dilamar: ${new Date(app.applied_at).toLocaleDateString('id-ID')}
                                    </small>
                                    <div class="mt-2 gap-2 d-flex justify-content-end">
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewApplicant(${app.worker?.id || app.worker_id})">
                                            <i class="bi bi-eye"></i> Lihat Profil
                                        </button>
                                        <div class="btn-group btn-group-sm" role="group">
                                            ${app.status !== 'accepted' ? `<button class="btn btn-sm btn-success" onclick="updateApplicationStatus(${app.id}, 'accepted')"><i class="bi bi-check"></i> Terima</button>` : ''}
                                            ${app.status !== 'rejected' ? `<button class="btn btn-sm btn-danger" onclick="updateApplicationStatus(${app.id}, 'rejected')"><i class="bi bi-x"></i> Tolak</button>` : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        async function updateApplicationStatus(appId, newStatus) {
            if (!confirm('Apakah Anda yakin?')) return;
            
            try {
                const token = localStorage.getItem('apiToken');
                const response = await fetch(baseUrl + `/applications/${appId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({ status: newStatus })
                });
                
                if (!response.ok) throw new Error('Failed to update status');
                
                showNotification('Status lamaran berhasil diubah');
                
                const jobFilter = document.getElementById('jobFilter').value;
                if (jobFilter) {
                    loadApplicationsForJob(jobFilter);
                } else {
                    loadAllApplications();
                }
            } catch (error) {
                console.error('Error updating status:', error);
                showNotification('Error mengubah status: ' + error.message, true);
            }
        }

        function viewApplicant(workerId) {
            window.location.href = `/profile?worker_id=${workerId}`;
        }

        function showNotification(message, isError = false) {
            const notif = document.getElementById('notification');
            notif.textContent = message;
            notif.className = 'alert ' + (isError ? 'alert-danger' : 'alert-success');
            notif.style.display = 'block';
            setTimeout(() => {
                notif.style.display = 'none';
            }, 4000);
        }

        function applyFilters() {
            const status = document.getElementById('statusFilter').value;
            const jobId = document.getElementById('jobFilter').value;
            
            let filtered = [...allApplications];
            
            if (status) {
                filtered = filtered.filter(app => app.status === status);
            }
            
            if (jobId) {
                const jobPostId = app => app.jobPost?.id || app.job_post?.id;
                filtered = filtered.filter(app => jobPostId(app) == jobId);
            }
            
            renderApplications(filtered);
        }

        document.getElementById('applyFilterBtn').addEventListener('click', applyFilters);
        document.getElementById('resetFilterBtn').addEventListener('click', () => {
            document.getElementById('statusFilter').value = '';
            document.getElementById('jobFilter').value = '';
            renderApplications(allApplications);
        });

        // Load on page start
        loadJobs();
        loadAllApplications();
    </script>
@endsection
