@php($title = 'Detail Lowongan - Job Connect')
@extends('layout')

@section('content')
    <div class="container py-4">
        <!-- Hero Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px; border-radius: 10px;">
                    <h1 class="h2 mb-2" id="jobTitle" style="font-weight: 700;">Loading...</h1>
                    <p class="mb-0" style="font-size: 1.1rem;">
                        <i class="bi bi-building"></i> <span id="companyName" style="font-weight: 500;">-</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-md-8">
                <!-- Basic Info -->
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="text-muted small">TIPE PEKERJAAN</p>
                                <p class="mb-3"><strong><span id="employmentType" class="badge bg-info">-</span></strong></p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted small">KISARAN GAJI</p>
                                <p class="mb-0"><strong id="salary">-</strong></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-muted small">LOKASI</p>
                                <p class="mb-0"><strong id="location">-</strong></p>
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted small">DIPOST</p>
                                <p class="mb-0"><strong id="postedDate">-</strong></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                        <h5 class="mb-0"><i class="bi bi-file-text"></i> Deskripsi Pekerjaan</h5>
                    </div>
                    <div class="card-body" style="line-height: 1.8;">
                        <div id="description">Loading...</div>
                    </div>
                </div>

                <!-- Requirements -->
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                        <h5 class="mb-0"><i class="bi bi-list-check"></i> Persyaratan</h5>
                    </div>
                    <div class="card-body">
                        <div id="requirements">Loading...</div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-between mb-4">
                    <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='/'">
                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                    </button>
                    <div>
                        <button type="button" class="btn btn-danger me-2" id="deleteBtn" style="display: none;" onclick="deleteJob()">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                        <a href="#" class="btn btn-warning me-2" id="editBtn" style="display: none;">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <button type="button" class="btn btn-primary btn-lg" id="applyBtn" onclick="goToApply()" style="display: none; padding: 12px 40px;">
                            <i class="bi bi-hand-thumbs-up"></i> Lamar Sekarang
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Company Info -->
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                        <h6 class="mb-0"><i class="bi bi-building"></i> Informasi Perusahaan</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong id="companyNameInfo">-</strong>
                        </p>
                        <p class="text-muted small" id="companyDescription">
                            Tidak ada deskripsi
                        </p>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                        <h6 class="mb-0"><i class="bi bi-bar-chart"></i> Statistik</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <p class="text-muted small">PELAMAR</p>
                                <p class="h5 mb-0"><strong id="applicantCount">0</strong></p>
                            </div>
                            <div class="col-6">
                                <p class="text-muted small">DILIHAT</p>
                                <p class="h5 mb-0"><strong id="viewCount">0</strong></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Apply Button for Mobile -->
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary btn-lg" id="applyBtnMobile" onclick="goToApply()" style="display: none;">
                        <i class="bi bi-hand-thumbs-up"></i> Lamar Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const jobId = window.location.pathname.split('/').pop();
        
        async function loadJobDetail() {
            try {
                const token = localStorage.getItem('apiToken');
                console.log('Fetching job detail for ID:', jobId);
                
                const response = await fetch(baseUrl + '/jobs/' + jobId, {
                    headers: token ? { 'Authorization': 'Bearer ' + token } : {}
                });
                
                console.log('Response status:', response.status);
                
                const data = await response.json();
                console.log('Response data:', data);

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to load job detail');
                }

                if (data.data) {
                    const job = data.data;
                    console.log('Job data loaded:', job);
                    
                    const employerName = job.employer?.name || 'PT Anonim';
                    document.getElementById('jobTitle').textContent = job.title || 'Untitled';
                    document.getElementById('companyName').textContent = employerName;
                    document.getElementById('companyNameInfo').textContent = employerName;
                    
                    const jobType = job.type === 'full-time' ? 'Full-time' : 'Part-time';
                    document.getElementById('employmentType').textContent = jobType;
                    document.getElementById('salary').textContent = job.salary_range || 'Sesuai kesepakatan';
                    document.getElementById('location').textContent = job.location || 'Tidak ditentukan';
                    
                    if (job.created_at) {
                        document.getElementById('postedDate').textContent = new Date(job.created_at).toLocaleDateString('id-ID');
                    }
                    
                    document.getElementById('description').innerHTML = job.description ? '<p>' + job.description.replace(/\n/g, '</p><p>') + '</p>' : '<p class="text-muted">Tidak ada deskripsi</p>';
                    document.getElementById('requirements').innerHTML = formatList(job.requirements);
                    document.getElementById('viewCount').textContent = job.view_count || 0;

                    // Show apply button if user is logged in and is worker
                    if (token) {
                        const userResponse = await fetch(baseUrl + '/auth/me', {
                            headers: { 'Authorization': 'Bearer ' + token }
                        });
                        const userData = await userResponse.json();
                        
                        if (userData.user) {
                            if (userData.user.role === 'worker') {
                                document.getElementById('applyBtn').style.display = 'block';
                                document.getElementById('applyBtnMobile').style.display = 'block';
                            } else if (userData.user.role === 'employer') {
                                document.getElementById('deleteBtn').style.display = 'block';
                                document.getElementById('editBtn').style.display = 'block';
                                document.getElementById('editBtn').href = '/employer/jobs/' + jobId + '/edit';
                            }
                        }
                    }
                } else {
                    throw new Error('No data in response');
                }
            } catch (error) {
                console.error('Full error:', error);
                document.getElementById('description').innerHTML = '<p class="text-danger"><i class="bi bi-exclamation-triangle"></i> Gagal memuat detail pekerjaan: ' + error.message + '</p>';
            }
        }

        function formatList(text) {
            if (!text) return '<p class="text-muted">Tidak ada informasi</p>';
            
            let html = '<ul style="list-style: none; padding-left: 0;">';
            
            if (text.includes('\n')) {
                const items = text.split('\n').filter(item => item.trim());
                html = items.map(item => {
                    const trimmed = item.trim();
                    return `<li style="margin-bottom: 10px; padding-left: 25px; position: relative;">
                        <i class="bi bi-check-circle" style="position: absolute; left: 0; color: #667eea;"></i>
                        ${trimmed}
                    </li>`;
                }).join('');
                html = '<ul style="list-style: none; padding-left: 0;">' + html + '</ul>';
            } else if (text.includes(',')) {
                const items = text.split(',').filter(item => item.trim());
                html = items.map(item => {
                    const trimmed = item.trim();
                    return `<li style="margin-bottom: 10px; padding-left: 25px; position: relative;">
                        <i class="bi bi-check-circle" style="position: absolute; left: 0; color: #667eea;"></i>
                        ${trimmed}
                    </li>`;
                }).join('');
                html = '<ul style="list-style: none; padding-left: 0;">' + html + '</ul>';
            } else {
                html = '<p>' + text + '</p>';
            }
            
            return html;
        }

        function goToApply() {
            window.location.href = '/jobs/' + jobId + '/apply';
        }

        async function deleteJob() {
            if (confirm('Yakin ingin menghapus lowongan ini?')) {
                try {
                    const response = await fetch(baseUrl + '/employer/jobs/' + jobId, {
                        method: 'DELETE',
                        headers: { 'Authorization': 'Bearer ' + localStorage.getItem('apiToken') }
                    });
                    if (response.ok) {
                        showNotice('Lowongan berhasil dihapus');
                        setTimeout(() => {
                            window.location.href = '/employer/jobs';
                        }, 1500);
                    }
                } catch (error) {
                    showNotice('Error menghapus lowongan', true);
                }
            }
        }

        loadJobDetail();
    </script>
@endsection
