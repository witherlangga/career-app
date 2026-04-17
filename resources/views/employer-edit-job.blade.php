@php($title = 'Edit Lowongan - Job Connect')
@extends('layout')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Lowongan Pekerjaan</h5>
                    </div>
                    <div class="card-body">
                        <form id="updateJobForm">
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
                                    <input type="text" class="form-control" name="salary_range" />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi Pekerjaan <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="description" rows="4" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Requirement / Persyaratan <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="requirements" rows="4" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Benefit</label>
                                <textarea class="form-control" name="benefits" rows="3"></textarea>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="/employer/jobs" class="btn btn-outline-secondary">
                                    <i class="bi bi-x"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const jobId = window.location.pathname.split('/')[3];
        
        async function loadJobData() {
            try {
                const response = await fetch(baseUrl + '/jobs/' + jobId, {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem('apiToken') }
                });
                const data = await response.json();
                
                if (data.data) {
                    const job = data.data;
                    document.querySelector('[name="title"]').value = job.title || '';
                    document.querySelector('[name="employment_type"]').value = job.employment_type || '';
                    document.querySelector('[name="location"]').value = job.location || '';
                    document.querySelector('[name="salary_range"]').value = job.salary_range || '';
                    document.querySelector('[name="description"]').value = job.description || '';
                    document.querySelector('[name="requirements"]').value = job.requirements || '';
                    document.querySelector('[name="benefits"]').value = job.benefits || '';
                }
            } catch (error) {
                console.error(error);
                showNotice('Error memuat data lowongan', true);
            }
        }

        document.getElementById('updateJobForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const payload = formToJson(this);
            
            try {
                const response = await fetch(baseUrl + '/employer/jobs/' + jobId, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('apiToken')
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok) {
                    showNotice('Lowongan berhasil diupdate!');
                    setTimeout(() => {
                        window.location.href = '/employer/jobs';
                    }, 1500);
                } else {
                    showNotice(data.message || 'Gagal update lowongan', true);
                }
            } catch (error) {
                showNotice('Error: ' + error.message, true);
            }
        });

        loadJobData();
    </script>
@endsection
