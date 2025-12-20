@extends('layouts.app')

@section('title', 'Laporan Akhir KP')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 mt-14">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Laporan Akhir KP</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Status dan manajemen laporan Kerja Praktek Anda</p>
        </div>

        {{-- Loading State --}}
        <div id="loading" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 mx-auto mb-2"></div>
            <p class="text-gray-500 dark:text-gray-400">Memuat data...</p>
        </div>

        {{-- Content Container --}}
        <div id="content-container" class="hidden">
            
            {{-- Status: BELUM UPLOAD --}}
            <div id="no-laporan-section" class="hidden">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden max-w-2xl mx-auto">
                    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 px-6 py-4">
                        <h2 class="text-xl font-bold text-white">Status Laporan</h2>
                    </div>
                    
                    <div class="p-8 text-center">
                        <div class="mb-6">
                            <div class="w-20 h-20 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Belum Ada Laporan</h3>
                            <p class="text-gray-600 dark:text-gray-400">Anda belum mengupload laporan akhir Kerja Praktek</p>
                        </div>

                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6 text-left">
                            <p class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-2">📋 Persyaratan Upload:</p>
                            <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1 ml-4">
                                <li>✓ Format file: PDF</li>
                                <li>✓ Ukuran maksimal: 10 MB</li>
                                <li>✓ Laporan sudah disetujui pembimbing</li>
                            </ul>
                        </div>

                        <button onclick="window.location.href='{{ route('mahasiswa.laporan-upload') }}'" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200 flex items-center justify-center mx-auto shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Upload Laporan Sekarang
                        </button>
                    </div>
                </div>
            </div>

            {{-- Status: SUDAH UPLOAD --}}
            <div id="has-laporan-section" class="hidden">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <span class="font-semibold text-green-800 dark:text-green-200">Laporan Sudah Terupload</span>
                            <p class="text-sm text-green-700 dark:text-green-300">Terakhir diperbarui: <span id="last-update"></span></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Detail Laporan</h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status Format</p>
                                </div>
                                <div id="format-badge"></div>
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">ID Laporan</p>
                                </div>
                                <p id="laporan-id" class="text-lg font-mono font-semibold text-gray-900 dark:text-white"></p>
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal Upload</p>
                                </div>
                                <p id="upload-date" class="text-base font-medium text-gray-900 dark:text-white"></p>
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Terakhir Diperbarui</p>
                                </div>
                                <p id="update-date" class="text-base font-medium text-gray-900 dark:text-white"></p>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                AKSI
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <button onclick="previewLaporan()" class="px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition duration-200 flex items-center justify-center shadow-md hover:shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Preview
                                </button>
                                <button onclick="downloadLaporan()" class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition duration-200 flex items-center justify-center shadow-md hover:shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download
                                </button>
                                <button onclick="window.location.href='{{ route('mahasiswa.laporan-upload') }}'" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200 flex items-center justify-center shadow-md hover:shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Update
                                </button>
                                <button onclick="confirmDelete()" class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition duration-200 flex items-center justify-center shadow-md hover:shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentLaporan = null;
const token = localStorage.getItem('abl_token');

document.addEventListener('DOMContentLoaded', async () => {
    if (!token) {
        window.location.href = '/login';
        return;
    }

    loadLaporan();
});

async function loadLaporan() {
    const loading = document.getElementById('loading');
    const contentContainer = document.getElementById('content-container');
    const noLaporanSection = document.getElementById('no-laporan-section');
    const hasLaporanSection = document.getElementById('has-laporan-section');

    try {
        const userRes = await fetch('/api/me', {
            headers: { 
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });
        
        if (!userRes.ok) throw new Error('Gagal mengambil data user');
        const user = await userRes.json();

        const laporanRes = await fetch('/api/laporan-kp', {
            headers: { 
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        const laporanResponseData = await laporanRes.json();

        loading.classList.add('hidden');
        contentContainer.classList.remove('hidden');

        // PERBAIKAN: Handle response format {data: [...]} atau langsung [...]
        let laporanData = laporanResponseData.data || laporanResponseData;
        
        // Pastikan laporanData adalah array
        if (!Array.isArray(laporanData)) {
            laporanData = [];
        }

       if (laporanData.length > 0) {
    const myLaporan = laporanData.find(l => String(l.mahasiswa_id) === String(user.id));
    
    console.log('=== FOUND MY LAPORAN ===');
    console.log('My Laporan:', myLaporan);
    console.log('========================');

            if (myLaporan) {
                currentLaporan = myLaporan;
                displayLaporan(myLaporan);
                noLaporanSection.classList.add('hidden');
                hasLaporanSection.classList.remove('hidden');
            } else {
                noLaporanSection.classList.remove('hidden');
                hasLaporanSection.classList.add('hidden');
            }
        } else {
            noLaporanSection.classList.remove('hidden');
            hasLaporanSection.classList.add('hidden');
        }
    } catch (err) {
        console.error('Error:', err);
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Gagal memuat data: ' + err.message,
            confirmButtonColor: '#3b82f6'
        });
        loading.classList.add('hidden');
        contentContainer.classList.remove('hidden');
        noLaporanSection.classList.remove('hidden');
    }
}

function displayLaporan(laporan) {
    // DEBUG - CEK DATA LENGKAP
    console.log('=== DEBUG LAPORAN ===');
    console.log('Full Laporan Object:', laporan);
    console.log('File Laporan Field:', laporan.file_laporan);
    console.log('Has file_laporan?', 'file_laporan' in laporan);
    console.log('Type:', typeof laporan.file_laporan);
    console.log('====================');
    
    const formatBadge = laporan.is_format_valid 
        ? '<span class="inline-flex items-center px-4 py-2 text-sm font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Valid</span>'
        : '<span class="inline-flex items-center px-4 py-2 text-sm font-semibold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full"><svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>Tidak Valid</span>';

    document.getElementById('format-badge').innerHTML = formatBadge;
    document.getElementById('upload-date').textContent = new Date(laporan.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    document.getElementById('update-date').textContent = new Date(laporan.updated_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    document.getElementById('last-update').textContent = new Date(laporan.updated_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    document.getElementById('laporan-id').textContent = '#' + laporan.id.toString().padStart(4, '0');
}

function previewLaporan() {
    if (!currentLaporan) return;
    
    // Langsung buka di tab baru
    const fileUrl = `/storage/${currentLaporan.file_laporan}`;
    const previewWindow = window.open(fileUrl, '_blank');
    
    if (!previewWindow) {
        Swal.fire({
            icon: 'error',
            title: 'Popup Diblokir',
            text: 'Browser memblokir popup. Izinkan popup untuk preview file.',
            confirmButtonColor: '#3b82f6'
        });
    }
}

function downloadLaporan() {
    if (!currentLaporan) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Data laporan tidak ditemukan',
            confirmButtonColor: '#3b82f6'
        });
        return;
    }
    
    const fileUrl = `/storage/${currentLaporan.file_laporan}`;
    
    // Gunakan nama file asli kalau ada, kalau tidak gunakan nama dari path
    const fileName = currentLaporan.original_filename || currentLaporan.file_laporan.split('/').pop();
    
    // Loading indicator
    Swal.fire({
        title: 'Mengunduh...',
        text: 'Mohon tunggu',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Fetch dan download
    fetch(fileUrl)
        .then(response => {
            if (!response.ok) throw new Error('File tidak ditemukan');
            return response.blob();
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = fileName; // ← Pakai nama asli
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
            
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'File berhasil didownload',
                timer: 1500,
                showConfirmButton: false,
                confirmButtonColor: '#3b82f6'
            });
        })
        .catch(err => {
            console.error('Error:', err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Download',
                text: 'File tidak dapat didownload. Pastikan file masih ada di server.',
                confirmButtonColor: '#3b82f6'
            });
        });
}

function confirmDelete() {
    Swal.fire({
        title: 'Hapus Laporan?',
        text: "Apakah Anda yakin ingin menghapus laporan ini? Tindakan ini tidak dapat dibatalkan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteLaporan();
        }
    });
}

async function deleteLaporan() {
    if (!currentLaporan) return;

    Swal.fire({
        title: 'Menghapus...',
        text: 'Mohon tunggu',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const deleteRes = await fetch(`/api/laporan-kp/${currentLaporan.id}`, {
            method: 'DELETE',
            headers: { 
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        if (!deleteRes.ok) {
            const errorData = await deleteRes.json();
            throw new Error(errorData.message || 'Hapus gagal');
        }

        Swal.fire({
            icon: 'success',
            title: 'Terhapus!',
            text: 'Laporan berhasil dihapus!',
            confirmButtonColor: '#3b82f6',
            timer: 2000,
            timerProgressBar: true
        }).then(() => {
            location.reload();
        });

    } catch (err) {
        console.error('Error:', err);
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Gagal menghapus laporan: ' + err.message,
            confirmButtonColor: '#3b82f6'
        });
    }
}
</script>
@endsection