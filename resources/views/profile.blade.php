@php($title = 'Profile - Job Connect')
@extends('layout')

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 50px; border-radius: 10px; text-align: center;">
                <h1 class="mb-3" style="font-size: 2.5rem; font-weight: 700;">
                    <i class="bi bi-person-circle"></i> Profil Saya
                </h1>
                <p style="font-size: 1.1rem;">Kelola informasi profil dan lamaran pekerjaan Anda</p>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-8 offset-md-2">
            <div id="profileContainer">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Memuat profil...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function loadProfile() {
            try {
                console.log('Starting loadProfile...');
                const data = await request('/auth/me', { method: 'GET' });
                console.log('Got profile data:', data);
                renderProfile(data);
            } catch (error) {
                console.error('Full error loading profile:', error);
                const container = document.getElementById('profileContainer');
                container.innerHTML = `<div class="alert alert-danger">
                    <strong>Error loading profile:</strong><br>
                    ${error.message || 'Unknown error'}<br>
                    <small class="text-muted">Check browser console for details</small>
                </div>`;
            }
        }

        function renderProfile(data) {
            const container = document.getElementById('profileContainer');
            
            if (!data || !data.user) {
                const errorMsg = data?.message || 'Profil tidak tersedia. Silakan login kembali.';
                console.error('Invalid profile response:', errorMsg, data);
                container.innerHTML = `<div class="alert alert-warning"><i class="bi bi-exclamation-triangle"></i> ${errorMsg}</div>`;
                return;
            }

            const user = data.user;
            const profile = user.profile || {};
            
            // Construct avatar URL
            let avatarPath = null;
            if (user.avatar) {
                if (user.avatar.startsWith('http')) {
                    avatarPath = user.avatar;
                } else {
                    avatarPath = `/storage/${user.avatar}`;
                }
            }

            let skillsHtml = '-';
            if (Array.isArray(profile.skills) && profile.skills.length > 0) {
                skillsHtml = profile.skills.map(skill => `<span class="badge bg-info me-2 mb-2" style="font-size: 0.85rem; padding: 6px 10px;">${skill}</span>`).join('');
            }

            const phone = user.phone_number || profile.phone_number || '-';
            
            // Build avatar HTML
            let avatarHtml = '';
            if (avatarPath) {
                avatarHtml = `<img src="${avatarPath}" alt="Avatar" class="rounded-circle mb-3" style="width: 200px; height: 200px; object-fit: cover; border: 5px solid #667eea; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" onerror="console.log('Avatar load error'); this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="rounded-circle bg-light d-none align-items-center justify-content-center mb-3" style="width: 200px; height: 200px; margin: 0 auto; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                <i class="bi bi-person" style="font-size: 5rem; color: #667eea;"></i>
                            </div>`;
            } else {
                avatarHtml = `<div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 200px; height: 200px; margin: 0 auto; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                <i class="bi bi-person" style="font-size: 5rem; color: #667eea;"></i>
                            </div>`;
            }
            
            container.innerHTML = `
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <!-- Avatar Card -->
                        <div class="card shadow-lg border-0 mb-3">
                            <div class="card-body text-center pt-4">
                                <div style="display: flex; justify-content: center; align-items: center; height: 220px; margin-bottom: 15px;">
                                    ${avatarHtml}
                                </div>
                                <h3 class="mb-1" style="color: #333; font-weight: 700;">${user.name || '-'}</h3>
                                <p class="text-muted mb-3" style="font-size: 0.95rem;">
                                    <span class="badge ${user.role === 'worker' ? 'bg-success' : 'bg-info'}" style="font-size: 0.85rem; padding: 6px 12px;">
                                        ${user.role === 'worker' ? '👤 Pekerja' : '🏢 Perusahaan'}
                                    </span>
                                </p>
                                
                                <div class="d-grid gap-2">
                                    <a href="/profile/edit" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 10px; font-weight: 600;">
                                        <i class="bi bi-pencil-square"></i> Edit Profile
                                    </a>
                                    <button type="button" class="btn btn-outline-danger logoutProfileBtn" style="padding: 10px; font-weight: 600;">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8 mb-4">
                        <!-- Info Cards -->
                        <div class="card shadow-lg border-0 mb-3">
                            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 20px; border-radius: 10px 10px 0 0;">
                                <h5 class="mb-0" style="font-weight: 700;">
                                    <i class="bi bi-person-lines-fill"></i> Informasi Pribadi
                                </h5>
                            </div>
                            <div class="card-body" style="padding: 25px;">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-bold">Email</label>
                                        <p class="mb-0" style="font-size: 1rem; color: #333; font-weight: 500;">
                                            <i class="bi bi-envelope me-2" style="color: #667eea;"></i>${user.email || '-'}
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small fw-bold">Nomor HP</label>
                                        <p class="mb-0" style="font-size: 1rem; color: #333; font-weight: 500;">
                                            <i class="bi bi-telephone me-2" style="color: #667eea;"></i>${phone}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <label class="text-muted small fw-bold">Bio</label>
                                        <p class="mb-0" style="font-size: 1rem; color: #555;">
                                            ${profile.bio || '<span class="text-muted">-</span>'}
                                        </p>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <label class="text-muted small fw-bold">Alamat</label>
                                        <p class="mb-0" style="font-size: 1rem; color: #555;">
                                            <i class="bi bi-geo-alt me-2" style="color: #667eea;"></i>${profile.address || '<span class="text-muted">-</span>'}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Skills Card -->
                        <div class="card shadow-lg border-0 mb-3">
                            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 15px 20px; border-radius: 10px 10px 0 0;">
                                <h6 class="mb-0" style="font-weight: 700;">
                                    <i class="bi bi-star"></i> Keahlian
                                </h6>
                            </div>
                            <div class="card-body" style="padding: 20px;">
                                <div>
                                    ${skillsHtml}
                                </div>
                            </div>
                        </div>

                        <!-- CV Card -->
                        ${profile.cv ? 
                            `<div class="card shadow-lg border-0">
                                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 15px 20px; border-radius: 10px 10px 0 0;">
                                    <h6 class="mb-0" style="font-weight: 700;">
                                        <i class="bi bi-file-pdf"></i> Curriculum Vitae
                                    </h6>
                                </div>
                                <div class="card-body" style="padding: 20px;">
                                    <a href="/storage/${profile.cv}" target="_blank" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                        <i class="bi bi-download"></i> Download CV
                                    </a>
                                </div>
                            </div>` 
                            : ''
                        }
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <a id="workerAppsLink" href="/worker/applications" class="btn btn-lg btn-outline-primary" style="display: none; padding: 12px 30px; font-weight: 600;">
                        <i class="bi bi-clipboard-check"></i> Lihat Lamaran Saya
                    </a>
                </div>
            `;

            // Set worker link visibility
            const role = data?.user?.role || null;
            const workerAppsLink = document.getElementById('workerAppsLink');
            if (workerAppsLink && role === 'worker') {
                workerAppsLink.style.display = 'inline-block';
            }

            // Logout handler
            const logoutBtn = container.querySelector('.logoutProfileBtn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', async function() {
                    try {
                        await request('/auth/logout', { method: 'POST' });
                        localStorage.removeItem('apiToken');
                        window.location.href = '/login';
                    } catch (error) {
                        console.error('Logout error:', error);
                        alert('Logout gagal');
                    }
                });
            }
        }

        // Load profile on page load
        document.addEventListener('DOMContentLoaded', loadProfile);
    </script>
@endsection
