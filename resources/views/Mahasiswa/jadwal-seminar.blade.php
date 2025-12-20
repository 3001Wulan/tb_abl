@extends('layouts.app')

@section('title', 'Jadwal Seminar KP')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 mt-14">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Jadwal Seminar KP</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Lihat jadwal seminar Kerja Praktek Anda</p>
        </div>

        {{-- Loading State --}}
        <div id="loading" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 mx-auto mb-2"></div>
            <p class="text-gray-500">Memuat data jadwal...</p>
        </div>

        {{-- Content Container --}}
        <div id="jadwal-container" class="hidden"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const loading = document.getElementById('loading');
    const container = document.getElementById('jadwal-container');
    const token = localStorage.getItem('abl_token');

    if (!token) {
        window.location.href = '/login';
        return;
    }

    try {
        // Ambil data user
        const userRes = await fetch('/api/me', {
            headers: { 
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });
        
        if (!userRes.ok) throw new Error('Gagal mengambil data user');
        const user = await userRes.json();

        // Ambil jadwal seminar berdasarkan student_id
        const jadwalRes = await fetch('/api/jadwal-seminar-me', {
    headers: { 
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json'
    }
});
        
        if (!jadwalRes.ok) throw new Error('Gagal mengambil data jadwal');
        const jadwalData = await jadwalRes.json();

        // Hide loading
        loading.classList.add('hidden');
        container.classList.remove('hidden');

        // Cek apakah ada data jadwal
        const jadwal = jadwalData.data;

        if (jadwal) {
            // Format tanggal
            let scheduledDate = 'Belum ditentukan';
            if (jadwal.scheduled_at) {
                const date = new Date(jadwal.scheduled_at);
                const options = { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' };
                scheduledDate = date.toLocaleDateString('id-ID', options);
            }

            // Status badge color
            let statusClass = '';
            if (jadwal.status === 'pending') statusClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
            else if (jadwal.status === 'scheduled') statusClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
            else if (jadwal.status === 'done') statusClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
            else statusClass = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';

            container.innerHTML = `
                {{-- Alert jika ada jadwal --}}
                <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 dark:bg-blue-900 dark:text-blue-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">Anda memiliki jadwal seminar!</span>
                    </div>
                </div>

                {{-- Card Jadwal Seminar --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    {{-- Header Card --}}
                    <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
                        <h2 class="text-xl font-semibold text-white">${jadwal.title}</h2>
                    </div>

                    {{-- Body Card --}}
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Informasi Mahasiswa --}}
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">Informasi Mahasiswa</h3>
                                <div class="space-y-3">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Nama</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-white">${user.name || '-'}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-white">${user.email || '-'}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Informasi Jadwal --}}
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">Detail Jadwal</h3>
                                <div class="space-y-3">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Waktu Seminar</p>
                                            <p class="text-base font-medium text-gray-900 dark:text-white">${scheduledDate}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${statusClass}">
                                                ${jadwal.status.charAt(0).toUpperCase() + jadwal.status.slice(1)}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        ${jadwal.notes ? `
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Catatan</h3>
                            <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">${jadwal.notes}</p>
                        </div>
                        ` : ''}

                        {{-- Informasi Tambahan --}}
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        <p class="font-medium text-gray-900 dark:text-white mb-1">Persiapan Seminar:</p>
                                        <ul class="list-disc list-inside space-y-1 ml-1">
                                            <li>Pastikan laporan KP sudah siap</li>
                                            <li>Siapkan presentasi/slide</li>
                                            <li>Hadir 15 menit sebelum jadwal</li>
                                            <li>Bawa dokumen pendukung yang diperlukan</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-lg transition duration-200">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Cetak Jadwal
                            </button>
                            <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Tambah ke Kalender
                            </button>
                        </div>
                    </div>
                </div>
            `;
        } else {
            // Belum ada jadwal
            container.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Belum Ada Jadwal Seminar</h3>
                    <p class="text-gray-600 dark:text-gray-400">Jadwal seminar KP Anda belum tersedia. Hubungi koordinator KP untuk informasi lebih lanjut.</p>
                </div>
            `;
        }

    } catch (err) {
        console.error('Error:', err);
        loading.innerHTML = `
            <div class="bg-red-50 dark:bg-red-900 rounded-lg p-6 text-center">
                <svg class="w-12 h-12 text-red-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-red-700 dark:text-red-200 font-medium">Gagal memuat data jadwal</p>
                <p class="text-red-600 dark:text-red-300 text-sm mt-1">${err.message}</p>
            </div>
        `;
    }
});
</script>
@endsection