@php($title = 'Daftar - Job Connect')
@extends('layout')

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 50px; border-radius: 10px; text-align: center;">
                <h1 class="mb-3" style="font-size: 2.5rem; font-weight: 700;">
                    <i class="bi bi-person-plus"></i> Daftar Akun Baru
                </h1>
                <p style="font-size: 1.1rem;">Bergabunglah dengan ribuan pengguna Job Connect</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div id="notice" class="alert" style="display: none;"></div>

            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <ul class="nav nav-pills mb-4 justify-content-center" role="tablist" style="gap: 10px;">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="workerTab" data-bs-toggle="tab" data-bs-target="#workerRole" type="button" role="tab" style="border-radius: 20px;">
                                <i class="bi bi-person"></i> Pekerja
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="employerTab" data-bs-toggle="tab" data-bs-target="#employerRole" type="button" role="tab" style="border-radius: 20px;">
                                <i class="bi bi-briefcase"></i> Employer
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Worker Registration Tab -->
                        <div class="tab-pane fade show active" id="workerRole" role="tabpanel">
                            <form id="workerRegisterForm" autocomplete="off">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-person"></i> Nama Lengkap
                                    </label>
                                    <input type="text" class="form-control form-control-lg" name="name" placeholder="Nama lengkap Anda" autocomplete="off" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-envelope"></i> Email
                                    </label>
                                    <input type="email" class="form-control form-control-lg" name="email" placeholder="email@example.com" autocomplete="off" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-lock"></i> Kata Sandi
                                    </label>
                                    <input type="password" class="form-control form-control-lg" name="password" placeholder="Minimal 8 karakter" autocomplete="off" required />
                                    <small class="text-muted">Gunakan kombinasi huruf, angka, dan simbol</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-lock-check"></i> Konfirmasi Kata Sandi
                                    </label>
                                    <input type="password" class="form-control form-control-lg" name="password_confirmation" placeholder="Ulangi kata sandi" autocomplete="off" required />
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                    <i class="bi bi-person-plus"></i> Daftar sebagai Pekerja
                                </button>
                            </form>
                        </div>

                        <!-- Employer Registration Tab -->
                        <div class="tab-pane fade" id="employerRole" role="tabpanel">
                            <form id="employerRegisterForm" autocomplete="off">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-briefcase"></i> Nama Perusahaan
                                    </label>
                                    <input type="text" class="form-control form-control-lg" name="name" placeholder="Nama perusahaan Anda" autocomplete="off" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-envelope"></i> Email Perusahaan
                                    </label>
                                    <input type="email" class="form-control form-control-lg" name="email" placeholder="email@company.com" autocomplete="off" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-lock"></i> Kata Sandi
                                    </label>
                                    <input type="password" class="form-control form-control-lg" name="password" placeholder="Minimal 8 karakter" autocomplete="off" required />
                                    <small class="text-muted">Gunakan kombinasi huruf, angka, dan simbol</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-lock-check"></i> Konfirmasi Kata Sandi
                                    </label>
                                    <input type="password" class="form-control form-control-lg" name="password_confirmation" placeholder="Ulangi kata sandi" autocomplete="off" required />
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                    <i class="bi bi-briefcase"></i> Daftar sebagai Employer
                                </button>
                            </form>
                        </div>
                    </div>

                    <hr class="my-4">
                    <p class="text-center mb-0">
                        Sudah punya akun? <a href="/login" class="btn-link">Login di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function validatePasswords(form) {
            const password = form.querySelector('input[name="password"]').value;
            const confirmation = form.querySelector('input[name="password_confirmation"]').value;
            
            if (password !== confirmation) {
                showNotice('Kata sandi tidak cocok. Silakan cek kembali.', true);
                return false;
            }
            
            if (password.length < 8) {
                showNotice('Kata sandi minimal 8 karakter.', true);
                return false;
            }
            
            return true;
        }

        document.getElementById('workerRegisterForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            if (!validatePasswords(this)) return;
            await registerUser('worker', this);
        });

        document.getElementById('employerRegisterForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            if (!validatePasswords(this)) return;
            await registerUser('employer', this);
        });

        async function registerUser(role, form) {
            const payload = formToJson(form);
            payload.role = role;
            
            console.log('Form role:', role);
            console.log('Sending payload:', payload);
            
            try {
                const response = await fetch(baseUrl + '/auth/register', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                
                console.log('Response status:', response.status);
                
                const contentType = response.headers.get('content-type');
                let data;

                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                    console.log('JSON response:', data);
                } else {
                    const text = await response.text();
                    console.error('Non-JSON response (first 500 chars):', text.substring(0, 500));
                    showNotice('Server error. Check console for details.', true);
                    return;
                }
                
                if (response.ok) {
                    localStorage.setItem('apiToken', data.data.token);
                    showNotice('Pendaftaran berhasil! Silakan lengkapi profil Anda.');
                    setTimeout(() => {
                        window.location.href = '/profile/edit';
                    }, 1500);
                } else {
                    showNotice(data.message || 'Pendaftaran gagal: ' + JSON.stringify(data), true);
                }
            } catch (error) {
                console.error('Register error:', error);
                showNotice('Error: ' + error.message, true);
            }
        }
    </script>
@endsection
