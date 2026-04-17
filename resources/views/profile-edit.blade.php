@php($title = 'Edit Profile - Job Connect')
@extends('layout')

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 50px; border-radius: 10px; text-align: center;">
                <h1 class="mb-3" style="font-size: 2.5rem; font-weight: 700;">
                    <i class="bi bi-pencil-square"></i> Edit Profil
                </h1>
                <p style="font-size: 1.1rem;">Update informasi profil Anda</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <p id="notice" class="alert" style="display: none;"></p>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Informasi Pribadi</h5>
                </div>
                <div class="card-body">
                    <form id="profileForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>
                                <input name="name" type="text" class="form-control" required />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input name="email" type="email" class="form-control" required />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nomor HP</label>
                                <input name="phone_number" type="text" class="form-control" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bio (Deskripsi Singkat)</label>
                                <input name="bio" type="text" class="form-control" placeholder="Misal: Full Stack Developer dengan 5 tahun pengalaman" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Skills (pisahkan dengan koma)</label>
                            <input name="skills" type="text" class="form-control" placeholder="Misal: PHP, Laravel, React, MySQL" />
                            <small class="text-muted">Contoh: PHP, Laravel, React</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Simpan Perubahan
                            </button>
                            <a href="/profile" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali ke Profil
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-image"></i> Avatar</h5>
                </div>
                <div class="card-body">
                    <div id="avatarPreview" class="text-center mb-3" style="min-height: 100px;">
                    </div>
                    <form id="avatarForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Upload Avatar (JPG/JPEG/PNG)</label>
                            <input name="avatar" type="file" accept="image/jpeg,image/png" class="form-control" />
                            <small class="text-muted">Maksimal ukuran 2MB. Format: JPG, JPEG, atau PNG</small>
                        </div>
                        <button type="submit" class="btn btn-info">
                            <i class="bi bi-cloud-upload"></i> Upload Avatar
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-file-pdf"></i> CV/Resume</h5>
                </div>
                <div class="card-body">
                    <form id="cvForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Upload CV (PDF/DOC/DOCX)</label>
                            <input name="cv" type="file" class="form-control" />
                            <small class="text-muted">Format: PDF, DOC, atau DOCX. Maksimal 5MB</small>
                        </div>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-cloud-upload"></i> Upload CV
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fillProfileForm(user) {
            const profile = user.profile || {};
            const form = document.getElementById('profileForm');
            if (!form) return;
            form.name.value = user.name || '';
            form.email.value = user.email || '';
            form.phone_number.value = user.phone_number || '';
            form.bio.value = profile.bio || '';
            form.address.value = profile.address || '';
            if (Array.isArray(profile.skills)) {
                form.skills.value = profile.skills.join(', ');
            } else {
                form.skills.value = '';
            }

            const avatarPreview = document.getElementById('avatarPreview');
            avatarPreview.innerHTML = '';
            if (user.avatar) {
                const avatarImg = document.createElement('img');
                avatarImg.src = `/storage/${user.avatar}`;
                avatarImg.alt = 'Avatar';
                avatarImg.style.maxWidth = '200px';
                avatarImg.style.borderRadius = '10px';
                avatarImg.style.marginBottom = '10px';
                avatarPreview.appendChild(avatarImg);
            } else {
                avatarPreview.innerHTML = '<p class="text-muted">Belum ada avatar</p>';
            }
        }

        function buildSkills(value) {
            return value
                .split(',')
                .map((skill) => skill.trim())
                .filter((skill) => skill.length > 0);
        }

        function showNoticeAlert(message, isError = false) {
            const notice = document.getElementById('notice');
            if (notice) {
                notice.textContent = message;
                notice.className = 'alert ' + (isError ? 'alert-danger' : 'alert-success');
                notice.style.display = 'block';
                setTimeout(() => {
                    notice.style.display = 'none';
                }, 5000);
            }
        }

        async function loadProfile() {
            try {
                const data = await request('/auth/me', { method: 'GET' });
                if (data?.user) {
                    fillProfileForm(data.user);
                } else {
                    showNoticeAlert('Gagal memuat profil', true);
                }
            } catch (error) {
                console.error('Error loading profile:', error);
                showNoticeAlert('Error: ' + error.message, true);
            }
        }

        document.addEventListener('DOMContentLoaded', loadProfile);

        document.getElementById('profileForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.target);
            const payload = {
                name: formData.get('name'),
                email: formData.get('email'),
                phone_number: formData.get('phone_number'),
                bio: formData.get('bio'),
                address: formData.get('address'),
                skills: buildSkills(formData.get('skills') || ''),
            };
            const token = localStorage.getItem('apiToken');
            try {
                const response = await fetch(baseUrl + '/profile', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        ...(token ? { Authorization: 'Bearer ' + token } : {}),
                    },
                    body: JSON.stringify(payload),
                });
                let data = null;
                try {
                    data = await response.json();
                } catch (error) {
                    console.error('JSON parse error:', error);
                    data = { message: 'Server error' };
                }
                if (!response.ok) {
                    showNoticeAlert(data?.message || 'Gagal menyimpan profile.', true);
                    return;
                }
                showNoticeAlert('Profile berhasil disimpan!');
                setTimeout(() => {
                    window.location.href = '/profile';
                }, 1500);
            } catch (error) {
                console.error('Save profile error:', error);
                showNoticeAlert('Error: ' + error.message, true);
            }
        });

        document.getElementById('cvForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.target);
            if (!formData.get('cv')) {
                showNoticeAlert('Silakan pilih file CV terlebih dahulu', true);
                return;
            }
            try {
                const response = await fetch(baseUrl + '/profile/cv', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + localStorage.getItem('apiToken'),
                    },
                    body: formData,
                });
                let data = null;
                try {
                    data = await response.json();
                } catch (error) {
                    console.error('JSON parse error:', error);
                    data = { message: 'Server error' };
                }
                if (!response.ok) {
                    showNoticeAlert(data?.message || 'Upload CV gagal', true);
                    return;
                }
                showNoticeAlert('CV berhasil diupload!');
                setTimeout(() => {
                    window.location.href = '/profile';
                }, 1500);
            } catch (error) {
                console.error('CV upload error:', error);
                showNoticeAlert('Error: ' + error.message, true);
            }
        });

        document.getElementById('avatarForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(event.target);
            if (!formData.get('avatar')) {
                showNoticeAlert('Silakan pilih file avatar terlebih dahulu', true);
                return;
            }
            try {
                const token = localStorage.getItem('apiToken');
                const response = await fetch(baseUrl + '/profile/avatar', {
                    method: 'POST',
                    headers: token ? { Authorization: 'Bearer ' + token } : {},
                    body: formData,
                });
                let payload = null;
                try {
                    payload = await response.json();
                } catch (error) {
                    console.error('JSON parse error:', error);
                    payload = { message: 'Server error' };
                }
                if (!response.ok) {
                    console.error('Upload error response:', response.status, payload);
                    showNoticeAlert(payload?.message || 'Upload gagal. Status: ' + response.status, true);
                    return;
                }
                
                // Display new avatar immediately
                const file = formData.get('avatar');
                const reader = new FileReader();
                reader.onload = function(e) {
                    const avatarPreview = document.getElementById('avatarPreview');
                    avatarPreview.innerHTML = '';
                    const avatarImg = document.createElement('img');
                    avatarImg.src = e.target.result;
                    avatarImg.alt = 'Avatar';
                    avatarImg.style.maxWidth = '200px';
                    avatarImg.style.borderRadius = '10px';
                    avatarImg.style.marginBottom = '10px';
                    avatarPreview.appendChild(avatarImg);
                };
                reader.readAsDataURL(file);
                
                showNoticeAlert('Avatar berhasil diupload! Redirect ke profile...');
                event.target.reset();
                
                // Redirect after 1.5 seconds to see success message
                setTimeout(() => {
                    window.location.href = '/profile';
                }, 1500);
            } catch (error) {
                console.error('Avatar upload error:', error);
                showNoticeAlert('Error: ' + error.message, true);
            }
        });
    </script>
@endsection
