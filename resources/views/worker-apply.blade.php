@php($title = 'Lamar Pekerjaan - Job Connect')
@extends('layout')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-hand-thumbs-up"></i> Lamar Pekerjaan</h5>
                    </div>
                    <div class="card-body">
                        <div id="jobInfo" class="mb-4 p-3 bg-light rounded">
                            <h6 id="jobTitle">Loading...</h6>
                            <p id="jobCompany" class="text-muted mb-0">-</p>
                        </div>

                        <form id="applicationForm">
                           <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" required disabled />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" required disabled />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nomor Telepon</label>
                                <input type="tel" class="form-control" id="phone" required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Pesan untuk Employer</label>
                                <textarea class="form-control" id="message" name="message" rows="5" placeholder="Jelaskan mengapa Anda cocok untuk pekerjaan ini..."></textarea>
                                <small class="text-muted">Opsional</small>
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> akan mengirim CV Anda terbaru kepada employer
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                                    <i class="bi bi-x"></i> Batal
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-hand-thumbs-up"></i> Kirim Lamaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const jobId = window.location.pathname.split('/')[2];
        
        async function loadJobInfo() {
            try {
                const response = await fetch(baseUrl + '/jobs/' + jobId);
                const data = await response.json();
                
                if (data.data) {
                    const job = data.data;
                    const employerName = job.employer?.name || 'PT Anonim';
                    document.getElementById('jobTitle').textContent = job.title;
                    document.getElementById('jobCompany').textContent = employerName;
                }
            } catch (error) {
                console.error('Load job info error:', error);
            }
        }

        async function loadUserInfo() {
            try {
                const response = await request('/profile');
                
                if (response.data) {
                    const profile = response.data;
                    document.getElementById('name').value = profile.user?.name || '';
                    document.getElementById('email').value = profile.user?.email || '';
                    document.getElementById('phone').value = profile.phone_number || '';
                }
            } catch (error) {
                console.error(error);
            }
        }

        document.getElementById('applicationForm').addEventListener('submit', async function(e) {
            e.preventDefault();
           const payload = {
                message: document.getElementById('message').value,
                phone: document.getElementById('phone').value
            };

            try {
                const response = await fetch(baseUrl + '/jobs/' + jobId + '/apply', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('apiToken')
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok) {
                    showNotice('Lamaran berhasil dikirim!');
                    setTimeout(() => {
                        window.location.href = '/worker/applications';
                    }, 1500);
                } else {
                    showNotice(data.message || 'Gagal mengirim lamaran', true);
                }
            } catch (error) {
                showNotice('Error: ' + error.message, true);
            }
        });

        loadJobInfo();
        loadUserInfo();
    </script>
@endsection
