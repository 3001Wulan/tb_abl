@extends('layouts.app')

@section('title', 'Nilai KP')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 mt-14">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Nilai Kerja Praktek</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Lihat hasil penilaian Kerja Praktek Anda</p>
        </div>

        {{-- Loading State --}}
        <div id="loading" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 mx-auto mb-2"></div>
            <p class="text-gray-500">Memuat data nilai...</p>
        </div>

        {{-- Content Container --}}
        <div id="nilai-container" class="hidden"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const loading = document.getElementById('loading');
    const container = document.getElementById('nilai-container');
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

        // Ambil nilai KP berdasarkan mahasiswa_id
        const nilaiRes = await fetch(`/api/penilaian-kp?mahasiswa_id=${user.id}`, {
            headers: { 
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });
        
        if (!nilaiRes.ok) throw new Error('Gagal mengambil data nilai');
        const nilaiData = await nilaiRes.json();

        // Hide loading
        loading.classList.add('hidden');
        container.classList.remove('hidden');

        // Cek apakah ada nilai (bisa array atau object dengan property data)
        const nilai = Array.isArray(nilaiData) ? nilaiData[0] : (nilaiData.data && nilaiData.data[0]);

        if (nilai) {
            // Tentukan warna badge nilai mutu
            let mutuClass = '';
            const mutu = nilai.nilai_mutu;
            if (mutu === 'A' || mutu === 'A-') mutuClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
            else if (mutu === 'B+' || mutu === 'B' || mutu === 'B-') mutuClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
            else if (mutu === 'C+' || mutu === 'C') mutuClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
            else mutuClass = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';

            container.innerHTML = `
                {{-- Alert Nilai Sudah Ada --}}
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 dark:bg-green-900 dark:text-green-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">Nilai KP Anda sudah tersedia!</span>
                    </div>
                </div>

                {{-- Card Nilai Akhir --}}
                <div class="bg-gradient-to-br from-blue-600 to-blue-800 rounded-lg shadow-xl p-8 mb-6 text-white">
                    <div class="text-center">
                        <p class="text-sm uppercase tracking-wide opacity-90 mb-2">Nilai Akhir</p>
                        <div class="flex items-center justify-center space-x-4">
                            <div class="text-6xl font-bold">${nilai.nilai_akhir}</div>
                            <div class="text-5xl font-bold px-6 py-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                ${nilai.nilai_mutu}
                            </div>
                        </div>
                        <p class="mt-3 text-sm opacity-75">Selamat! Anda telah menyelesaikan Kerja Praktek</p>
                    </div>
                </div>

                {{-- Detail Komponen Nilai --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-6">
                    <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Komponen Penilaian</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Nilai Laporan --}}
                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800 rounded-lg p-5 border border-purple-200 dark:border-purple-700">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-300 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <h3 class="font-semibold text-gray-700 dark:text-gray-200">Laporan</h3>
                                    </div>
                                </div>
                                <p class="text-3xl font-bold text-purple-700 dark:text-purple-200">${nilai.nilai_laporan || '-'}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Bobot: 50%</p>
                            </div>

                            {{-- Nilai Presentasi --}}
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-lg p-5 border border-blue-200 dark:border-blue-700">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-300 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                                        </svg>
                                        <h3 class="font-semibold text-gray-700 dark:text-gray-200">Presentasi</h3>
                                    </div>
                                </div>
                                <p class="text-3xl font-bold text-blue-700 dark:text-blue-200">${nilai.nilai_presentasi || '-'}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Bobot: 20%</p>
                            </div>

                            {{-- Nilai Aktivitas --}}
                            <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-lg p-5 border border-green-200 dark:border-green-700">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-green-600 dark:text-green-300 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                        <h3 class="font-semibold text-gray-700 dark:text-gray-200">Aktivitas</h3>
                                    </div>
                                </div>
                                <p class="text-3xl font-bold text-green-700 dark:text-green-200">${nilai.nilai_aktivitas_kp || '-'}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Bobot: 30%</p>
                            </div>
                        </div>

                        {{-- Keterangan Nilai Mutu --}}
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">Keterangan Nilai Mutu</h3>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-900 rounded">
                                    <p class="font-bold text-green-700 dark:text-green-400">A / A-</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">85-100 / 80-84</p>
                                </div>
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-900 rounded">
                                    <p class="font-bold text-blue-700 dark:text-blue-400">B+ / B / B-</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">75-79 / 70-74 / 65-69</p>
                                </div>
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-900 rounded">
                                    <p class="font-bold text-yellow-700 dark:text-yellow-400">C+ / C</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">60-64 / 55-59</p>
                                </div>
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-900 rounded">
                                    <p class="font-bold text-orange-700 dark:text-orange-400">D</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">40-54</p>
                                </div>
                                <div class="text-center p-3 bg-gray-50 dark:bg-gray-900 rounded">
                                    <p class="font-bold text-red-700 dark:text-red-400">E</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">0-39</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Informasi Tambahan --}}
                <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-blue-800 dark:text-blue-200">
                            <p class="font-medium mb-1">Catatan:</p>
                            <p>Nilai ini merupakan hasil akhir penilaian Kerja Praktek Anda. Jika ada pertanyaan mengenai nilai, silakan hubungi pembimbing atau admin departemen.</p>
                        </div>
                    </div>
                </div>
            `;
        } else {
            // Belum ada nilai
            container.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Nilai Belum Tersedia</h3>
                    <p class="text-gray-600 dark:text-gray-400">Nilai KP Anda belum diinput oleh pembimbing. Mohon ditunggu ya!</p>
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
                <p class="text-red-700 dark:text-red-200 font-medium">Gagal memuat data nilai</p>
                <p class="text-red-600 dark:text-red-300 text-sm mt-1">${err.message}</p>
            </div>
        `;
    }
});
</script>
@endsection