@php($title = 'Login - Job Connect')
@extends('layout')

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 50px; border-radius: 10px; text-align: center;">
                <h1 class="mb-3" style="font-size: 2.5rem; font-weight: 700;">
                    <i class="bi bi-box-arrow-in-right"></i> Login Akun
                </h1>
                <p style="font-size: 1.1rem;">Masuk untuk melanjutkan perjalanan karir Anda</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div id="notice" class="alert" style="display: none;"></div>

            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <form id="loginForm" autocomplete="off">
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope"></i> Email
                            </label>
                            <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="email@example.com" autocomplete="off" required />
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="bi bi-lock"></i> Kata Sandi
                            </label>
                            <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="••••••••" autocomplete="off" required />
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe" />
                            <label class="form-check-label" for="rememberMe">
                                Ingat saya
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </form>

                    <hr class="my-4">
                    <p class="text-center mb-2">
                        Belum punya akun? <a href="/register" class="btn-link">Daftar di sini</a>
                    </p>
                    <p class="text-center">
                        <a href="#" class="btn-link" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
                            <i class="bi bi-question-circle"></i> Lupa kata sandi?
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-key"></i> Reset Kata Sandi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="forgotPasswordForm" autocomplete="off">
                        <div class="mb-3">
                            <label for="forgotEmail" class="form-label">Email Anda</label>
                            <input type="email" class="form-control" id="forgotEmail" name="email" placeholder="email@example.com" autocomplete="off" required />
                        </div>
                        <p class="text-muted small">
                            <i class="bi bi-info-circle"></i> Kami akan mengirim link untuk reset kata sandi ke email Anda.
                        </p>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="submitForgotPassword()">
                        <i class="bi bi-send"></i> Kirim
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const payload = formToJson(this);
            
            try {
                const response = await fetch(baseUrl + '/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                
                const contentType = response.headers.get('content-type');
                let data;

                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    const text = await response.text();
                    console.error('Server response:', text.substring(0, 200));
                    showNotice('Server error. Check console for details.', true);
                    return;
                }
                
                if (response.ok) {
                    localStorage.setItem('apiToken', data.data.token);
                    showNotice('Login berhasil! Mengalihkan...');
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 1500);
                } else {
                    showNotice(data.message || 'Login gagal', true);
                }
            } catch (error) {
                console.error('Login error:', error);
                showNotice('Error: ' + error.message, true);
            }
        });

        async function submitForgotPassword() {
            const email = document.getElementById('forgotEmail').value;
            if (!email) {
                showNotice('Silakan masukkan email Anda', true);
                return;
            }

            try {
                const response = await fetch(baseUrl + '/auth/forgot-password', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    showNotice('Link reset kata sandi telah dikirim ke email Anda');
                    document.getElementById('forgotPasswordForm').reset();
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal')).hide();
                    }, 1500);
                } else {
                    showNotice(data.message || 'Gagal mengirim link', true);
                }
            } catch (error) {
                showNotice('Error: ' + error.message, true);
            }
        }
    </script>
@endsection
