@extends('layouts.app')

@section('title', 'Upload Laporan KP')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 mt-14">
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center mb-4">
                <button onclick="window.location.href='{{ route('mahasiswa.laporan-akhir') }}'" class="mr-4 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </button>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Upload Laporan Akhir KP</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Upload atau perbarui laporan Kerja Praktek Anda</p>
                </div>
            </div>
        </div>

        {{-- Loading State --}}
        <div id="loading" class="hidden bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 mx-auto mb-2"></div>
            <p class="text-gray-500 dark:text-gray-400">Memproses...</p>
        </div>

        {{-- Upload Form --}}
        <div id="upload-section" class="max-w-3xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span id="form-title">Upload Laporan Baru</span>
                    </h2>
                </div>
                
                <div class="p-6">
                    {{-- Current Status (jika update) --}}
                    <div id="current-status" class="hidden mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <p class="font-medium text-blue-800 dark:text-blue-200 mb-1">Laporan Saat Ini:</p>
                                <div class="text-sm text-blue-700 dark:text-blue-300">
                                    <p>Status: <span id="current-status-badge"></span></p>
                                    <p>Upload: <span id="current-upload-date"></span></p>
                                    <p class="mt-1 text-xs">File baru akan menggantikan file yang sudah ada</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form id="uploadForm" enctype="multipart/form-data">
                        {{-- File Input --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                File Laporan (PDF) <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-blue-500 transition-colors duration-200 cursor-pointer" onclick="document.getElementById('file-upload').click()">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                        <span class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 hover:text-blue-500">
                                            Klik untuk upload
                                        </span>
                                        <p class="pl-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">PDF hingga 10MB</p>
                                    <div id="file-info" class="hidden mt-3 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <svg class="w-8 h-8 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                </svg>
                                                <div>
                                                    <p id="file-name" class="text-sm font-medium text-gray-900 dark:text-white"></p>
                                                    <p id="file-size" class="text-xs text-gray-500 dark:text-gray-400"></p>
                                                </div>
                                            </div>
                                            <button type="button" onclick="clearFile(event)" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <input id="file-upload" name="file_laporan" type="file" accept=".pdf" class="hidden" required>
                                </div>
                            </div>
                        </div>

                        {{-- Info Box --}}
                        <div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div class="text-sm text-blue-800 dark:text-blue-200">
                                    <p class="font-semibold mb-2">📋 Persyaratan Upload:</p>
                                    <ul class="space-y-1">
                                        <li class="flex items-start">
                                            <span class="text-blue-600 dark:text-blue-400 mr-2">•</span>
                                            <span>Format file harus <strong>PDF</strong></span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="text-blue-600 dark:text-blue-400 mr-2">•</span>
                                            <span>Ukuran maksimal <strong>10 MB</strong></span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="text-blue-600 dark:text-blue-400 mr-2">•</span>
                                            <span>Pastikan laporan sudah <strong>final</strong> dan disetujui pembimbing</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="text-blue-600 dark:text-blue-400 mr-2">•</span>
                                            <span>Laporan akan divalidasi secara otomatis</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="window.location.href='{{ route('mahasiswa.laporan-akhir') }}'" class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg transition duration-200 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Batal
                            </button>
                            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200 flex items-center shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <span id="submit-text">Upload Laporan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentLaporan = null;
let isUpdate = false;
const token = localStorage.getItem('abl_token');

document.addEventListener('DOMContentLoaded', async () => {
    if (!token) {
        window.location.href = '/login';
        return;
    }

    // Check if updating existing laporan
    await checkExistingLaporan();

    // File input handler
    const fileInput = document.getElementById('file-upload');
    fileInput.addEventListener('change', handleFileSelect);

    // Upload form handler
    document.getElementById('uploadForm').addEventListener('submit', handleSubmit);
});

async function checkExistingLaporan() {
    try {
        const userRes = await fetch('/api/me', {
            headers: { 
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });
        
        if (!userRes.ok) return;
        const user = await userRes.json();

        const laporanRes = await fetch('/api/laporan-kp', {
            headers: { 
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        if (laporanRes.ok) {
            const laporanResponseData = await laporanRes.json();
            
            // PERBAIKAN: Handle response format
            let laporanData = laporanResponseData.data || laporanResponseData;
            
            // Pastikan array
            if (!Array.isArray(laporanData)) {
                laporanData = [];
            }

            // Cari laporan user
            const myLaporan = laporanData.find(l => String(l.mahasiswa_id) === String(user.id));

            if (myLaporan) {
                isUpdate = true;
                currentLaporan = myLaporan;
                
                // Update UI untuk mode update
                document.getElementById('form-title').textContent = 'Update Laporan';
                document.getElementById('submit-text').textContent = 'Update Laporan';
                document.getElementById('current-status').classList.remove('hidden');
                
                const statusBadge = myLaporan.is_format_valid 
                    ? '<span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded">Valid</span>'
                    : '<span class="text-xs px-2 py-1 bg-red-100 text-red-800 rounded">Tidak Valid</span>';
                
                document.getElementById('current-status-badge').innerHTML = statusBadge;
                document.getElementById('current-upload-date').textContent = new Date(myLaporan.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            }
        }
    } catch (err) {
        console.error('Error checking laporan:', err);
        // Tidak perlu alert, biarkan user tetap bisa upload
    }
}

function handleFileSelect(e) {
    const file = e.target.files[0];
    if (file) {
        document.getElementById('file-name').textContent = file.name;
        document.getElementById('file-size').textContent = formatFileSize(file.size);
        document.getElementById('file-info').classList.remove('hidden');
    }
}

function clearFile(e) {
    e.stopPropagation();
    document.getElementById('file-upload').value = '';
    document.getElementById('file-info').classList.add('hidden');
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

async function handleSubmit(e) {
    e.preventDefault();
    const file = document.getElementById('file-upload').files[0];

    if (!file) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: 'Pilih file terlebih dahulu!',
            confirmButtonColor: '#3b82f6'
        });
        return;
    }

    if (file.type !== 'application/pdf') {
        Swal.fire({
            icon: 'error',
            title: 'Format Salah!',
            text: 'File harus berformat PDF!',
            confirmButtonColor: '#3b82f6'
        });
        return;
    }

    if (file.size > 10 * 1024 * 1024) {
        Swal.fire({
            icon: 'error',
            title: 'File Terlalu Besar!',
            text: 'Ukuran file maksimal 10MB!',
            confirmButtonColor: '#3b82f6'
        });
        return;
    }

    document.getElementById('loading').classList.remove('hidden');
    document.getElementById('upload-section').classList.add('hidden');

    try {
        if (isUpdate) {
            await updateLaporan(file);
        } else {
            await uploadLaporan(file);
        }
    } catch (err) {
        console.error('Error:', err);
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: err.message,
            confirmButtonColor: '#3b82f6'
        });
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('upload-section').classList.remove('hidden');
    }
}

async function uploadLaporan(file) {
    const userRes = await fetch('/api/me', {
        headers: { 
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
        }
    });
    const user = await userRes.json();

    const formData = new FormData();
    formData.append('mahasiswa_id', user.id);
    formData.append('file_laporan', file);

    const uploadRes = await fetch('/api/laporan-kp/upload', {
        method: 'POST',
        headers: { 
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
        },
        body: formData
    });

    if (!uploadRes.ok) {
        const errorData = await uploadRes.json();
        throw new Error(errorData.message || 'Upload gagal');
    }

    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Laporan berhasil diupload!',
        confirmButtonColor: '#3b82f6',
        timer: 2000,
        timerProgressBar: true
    }).then(() => {
        window.location.href = '{{ route("mahasiswa.laporan-akhir") }}';
    });
}

async function updateLaporan(file) {
    const formData = new FormData();
    formData.append('file_laporan', file);
    formData.append('_method', 'PUT');

    const updateRes = await fetch(`/api/laporan-kp/${currentLaporan.id}`, {
        method: 'POST',
        headers: { 
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
        },
        body: formData
    });

    if (!updateRes.ok) {
        const errorData = await updateRes.json();
        throw new Error(errorData.message || 'Update gagal');
    }

    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Laporan berhasil diperbarui!',
        confirmButtonColor: '#3b82f6',
        timer: 2000,
        timerProgressBar: true
    }).then(() => {
        window.location.href = '{{ route("mahasiswa.laporan-akhir") }}';
    });
}
</script>
@endsection