@php($title = 'Buat Lowongan - Job Connect')
@extends('layout')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Buat Lowongan Pekerjaan Baru</h5>
                    </div>
                    <div class="card-body">
                        <form id="createJobForm">
                            <div class="mb-3">
                                <label class="form-label">Judul Pekerjaan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" required />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tipe Pekerjaan <span class="text-danger">*</span></label>
                                <select class="form-select" name="employment_type" required>
                                    <option value="">- Pilih -</option>
                                    <option value="Full-time">Full-time</option>
                                    <option value="Part-time">Part-time</option>
                                    <option value="Contract">Contract</option>
                                    <option value="Freelance">Freelance</option>
                                    <option value="Internship">Internship</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="location" required />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Rentang Gaji</label>
                                    <input type="text" class="form-control" name="salary_range" placeholder="Contoh: Rp 5-10 juta" />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi Pekerjaan <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="description" rows="4" required></textarea>
                                <small class="text-muted">Jelaskan detail pekerjaan, tanggung jawab, dan ekspektasi</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Requirement / Persyaratan <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="requirements" rows="4" required></textarea>
                                <small class="text-muted">Tuliskan persyaratan dan kualifikasi yang dibutuhkan</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Benefit</label>
                                <textarea class="form-control" name="benefits" rows="3"></textarea>
                                <small class="text-muted">Opsional - Sebutkan benefit dan keuntungan bekerja</small>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="/employer/jobs" class="btn btn-outline-secondary">
                                    <i class="bi bi-x"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check"></i> Buat Lowongan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('createJobForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const payload = formToJson(this);
            
            console.log('Create job payload:', payload);
            
            try {
                const response = await fetch(baseUrl + '/employer/jobs', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('apiToken')
                    },
                    body: JSON.stringify(payload)
                });

                console.log('Create job response status:', response.status);
                
                const data = await response.json();
                console.log('Create job response data:', data);

                if (response.ok) {
                    showNotice('Lowongan berhasil dibuat!');
                    setTimeout(() => {
                        window.location.href = '/employer/jobs';
                    }, 1500);
                } else {
                    showNotice(data.message || JSON.stringify(data), true);
                }
            } catch (error) {
                console.error('Create job error:', error);
                showNotice('Error: ' + error.message, true);
            }
        });
    </script>
@endsection
