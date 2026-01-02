<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sistem KP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.0/dist/alpine.min.js" defer></script>
    <style>
        .dark .dark\:divide-gray-700 > :not([hidden]) ~ :not([hidden]) { border-color: rgba(55, 65, 81); }
        .dark .dark\:bg-gray-700 { background-color: rgba(55, 65, 81); }
        .dark .dark\:bg-gray-800 { background-color: rgba(31, 41, 55); }
        .dark .dark\:text-white { color: rgba(255, 255, 255); }
        .header-right { width: calc(100% - 3.5rem); }
        .sidebar:hover { width: 16rem; }
        @media only screen and (min-width: 768px) { .header-right { width: calc(100% - 16rem); } }
        .hidden { display: none; }
    </style>
</head>
<body x-data="setup()" :class="{ 'dark': isDark }" class="bg-white dark:bg-gray-700 text-black dark:text-white antialiased">
    <div class="min-h-screen flex flex-col flex-auto flex-shrink-0 transition-colors duration-200">

        @include('partials.header') 
        
        {{-- Memanggil 3 file sidebar yang berbeda --}}
        @include('partials.sidebar-mahasiswa')
        @include('partials.sidebar-dosen')
        @include('partials.sidebar-admin')

        <div class="h-full ml-14 mt-14 mb-10 md:ml-64">
            
            <div class="p-4 pb-0">
                <h1 class="text-xl font-bold">Halo, <span id="display-name">...</span>!</h1>
                <p class="text-xs text-gray-500 capitalize" id="display-role">Memuat akses...</p>
            </div>

            {{-- ========================================== --}}
            {{-- VIEW: MAHASISWA --}}
            {{-- ========================================== --}}
            <div id="mahasiswa-view" class="hidden">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 p-4 gap-4">
                    <div class="bg-blue-500 dark:bg-gray-800 shadow-lg rounded-md flex items-center justify-between p-3 border-b-4 border-blue-600 dark:border-gray-600 text-white font-medium group">
                        <div class="flex justify-center items-center w-14 h-14 bg-white rounded-full transition-all duration-300 transform group-hover:rotate-12">
                            <svg width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="stroke-current text-blue-800 dark:text-gray-800"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl" id="status-surat">Memuat...</p>
                            <p>Status Surat Izin KP</p>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-500 dark:bg-gray-800 shadow-lg rounded-md flex items-center justify-between p-3 border-b-4 border-yellow-600 dark:border-gray-600 text-white font-medium group">
                        <div class="flex justify-center items-center w-14 h-14 bg-white rounded-full transition-all duration-300 transform group-hover:rotate-12">
                            <svg width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="stroke-current text-yellow-800 dark:text-gray-800"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 4v-4z"></path></svg>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl" id="total-bimbingan">0x</p>
                            <p>Total Bimbingan</p>
                        </div>
                    </div>

                    <div class="bg-purple-500 dark:bg-gray-800 shadow-lg rounded-md flex items-center justify-between p-3 border-b-4 border-purple-600 dark:border-gray-600 text-white font-medium group">
                        <div class="flex justify-center items-center w-14 h-14 bg-white rounded-full transition-all duration-300 transform group-hover:rotate-12">
                            <svg width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="stroke-current text-purple-800 dark:text-gray-800"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div class="text-right">
                            <p class="text-xl" id="dosen-pembimbing">Memuat...</p>
                            <p>Dosen Pembimbing</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 p-4 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-800 shadow-lg rounded">
                        <div class="p-4 border-b dark:border-gray-700 font-semibold text-gray-900 dark:text-gray-50">Jadwal Seminar</div>
                        <table class="w-full text-sm">
                            <tbody class="divide-y dark:divide-gray-700">
                                <tr><th class="p-3 text-left w-1/3">Status</th><td id="status-seminar" class="p-3">Memuat...</td></tr>
                                <tr><th class="p-3 text-left">Tanggal</th><td id="tanggal-seminar" class="p-3">-</td></tr>
                                <tr><th class="p-3 text-left">Ruangan</th><td id="ruangan-seminar" class="p-3">-</td></tr>
                                <tr><th class="p-3 text-left">Penguji</th><td id="penguji-seminar" class="p-3">-</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 shadow-lg rounded">
                        <div class="p-4 border-b dark:border-gray-700 font-semibold text-gray-900 dark:text-gray-50">Logbook Terbaru</div>
                        <div class="bg-gray-100 dark:bg-gray-600 px-4 py-2 text-xs uppercase font-bold text-gray-500 dark:text-gray-100">Aktivitas Terakhir</div>
                        <ul id="list-aktivitas" class="p-2">
                            <li class="italic p-2 text-sm">Memuat aktivitas...</li>
                        </ul>
                    </div>
                </div>

                <div class="p-4">
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                        <div class="p-4 font-semibold text-lg border-b dark:border-gray-700">Riwayat Bimbingan</div>
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 uppercase text-xs">
                                <tr><th class="p-4">Pembimbing</th><th class="p-4">Topik</th><th class="p-4">Status</th><th class="p-4">Tanggal</th></tr>
                            </thead>
                            <tbody id="riwayat-bimbingan" class="divide-y dark:divide-gray-700"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ========================================== --}}
            {{-- VIEW: ADMINISTRATOR --}}
            {{-- ========================================== --}}
            <div id="admin-view" class="hidden">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 p-4 gap-4">
                    <div class="bg-blue-600 dark:bg-gray-800 shadow-lg rounded-md flex items-center justify-between p-3 border-b-4 border-blue-800 dark:border-gray-600 text-white font-medium group">
                        <div class="flex justify-center items-center w-14 h-14 bg-white rounded-full transition-all duration-300 transform group-hover:rotate-12">
                            <svg class="w-8 h-8 text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-black" id="stat-mhs">0</p>
                            <p class="text-xs uppercase opacity-75">Total Mahasiswa</p>
                        </div>
                    </div>
                    
                    <div class="bg-amber-500 dark:bg-gray-800 shadow-lg rounded-md flex items-center justify-between p-3 border-b-4 border-amber-700 dark:border-gray-600 text-white font-medium group">
                        <div class="flex justify-center items-center w-14 h-14 bg-white rounded-full transition-all duration-300 transform group-hover:rotate-12">
                            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-black" id="stat-surat-admin">0</p>
                            <p class="text-xs uppercase opacity-75">Antrean Surat</p>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-100 dark:border-gray-600">
                        <div class="p-5 border-b dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800">
                            <h3 class="font-bold text-lg">Antrean Pendaftaran Mahasiswa (Terbaru)</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-100 dark:bg-gray-700 text-xs uppercase text-gray-400 font-bold">
                                    <tr>
                                        <th class="px-6 py-4">Mahasiswa</th>
                                        <th class="px-6 py-4">Instansi</th>
                                        <th class="px-6 py-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="admin-table-body" class="divide-y dark:divide-gray-700">
                                    <tr><td colspan="3" class="text-center py-10 italic text-gray-400">Memproses data...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========================================== --}}
            {{-- VIEW: DOSEN --}}
            {{-- ========================================== --}}
            <div id="dosen-view" class="hidden p-4">
                <h2 class="text-2xl font-bold">Panel Dosen</h2>
                <p>Data bimbingan mahasiswa akan tampil di sini.</p>
            </div>

        </div>
    </div>

    <script>
    (async function init() {
        const token = localStorage.getItem('abl_token');
        if (!token) { window.location.replace('/login'); return; }

        try {
            const profileRes = await fetch('/api/me', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            if (!profileRes.ok) throw new Error("Unauthorized");
            const user = await profileRes.json();
            
            document.getElementById('display-name').innerText = user.name;
            document.getElementById('display-role').innerText = "Role: " + user.role;

            // LOGIKA UNTUK MENAMPILKAN SIDEBAR & VIEW BERDASARKAN ROLE
            if (user.role === 'admin') {
                document.getElementById('admin-view').classList.remove('hidden');
                document.getElementById('sidebar-admin')?.classList.remove('hidden');
                fetchAdminData(token);
            } else if (user.role === 'dosen') {
                document.getElementById('dosen-view').classList.remove('hidden');
                document.getElementById('sidebar-dosen')?.classList.remove('hidden');
            } else {
                document.getElementById('mahasiswa-view').classList.remove('hidden');
                document.getElementById('sidebar-mahasiswa')?.classList.remove('hidden');
                fetchMahasiswaData(token);
            }

        } catch (err) {
            console.error(err);
            window.location.replace('/login');
        }
    })();

    async function fetchAdminData(token) {
    try {
        // PANGGIL URL INI (Pastikan route di api.php sudah benar)
        const res = await fetch('/api/dashboard/kp', {
            headers: { 
                'Authorization': 'Bearer ' + token, 
                'Accept': 'application/json' 
            }
        });
        
        const result = await res.json();
        
        if (result.success) {
            const data = result.data;

            // Update Angka di Card
            document.getElementById('stat-mhs').innerText = data.stats.total_mahasiswa;
            document.getElementById('stat-surat-admin').innerText = data.stats.surat_pending;
            
            // Update Badge di Sidebar (lingkaran kuning)
            const sideBadge = document.querySelector('.bg-yellow-50.rounded-full');
            if (sideBadge) sideBadge.innerText = data.stats.surat_pending;

            // Isi Tabel
            const tbody = document.getElementById('admin-table-body');
            if (data.pendaftar_terbaru.length > 0) {
                tbody.innerHTML = data.pendaftar_terbaru.map(item => `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition duration-150">
                        <td class="px-6 py-4 font-medium text-white">${item.nama_mahasiswa}</td>
                        <td class="px-6 py-4 text-gray-400">${item.instansi}</td>
                        <td class="px-6 py-4">
            <a href="/admin/tentukanpembimbing?student_id=${item.id}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full text-xs inline-block">
                Tentukan Pembimbing
            </a>
        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center py-10 text-gray-500 italic">Belum ada pendaftaran baru.</td></tr>';
            }
        }
    } catch (e) { 
        console.error("Admin data error:", e); 
    }
}
    async function fetchMahasiswaData(token) {
        try {
            const res = await fetch('/api/dashboard/kp', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            const response = await res.json();
            const data = response.data;

            document.getElementById('status-surat').innerText = data.status_surat;
            document.getElementById('total-bimbingan').innerText = data.total_bimbingan + 'x';
            document.getElementById('dosen-pembimbing').innerText = data.dosen_pembimbing?.nama || 'Belum diplot';

            const activityList = document.getElementById('list-aktivitas');
            if(data.log_terbaru) {
                activityList.innerHTML = `
                    <li class="p-2">
                        <div class="text-sm"><b>Minggu ${data.log_terbaru.minggu_ke}:</b> ${data.log_terbaru.deskripsi.substring(0, 50)}...</div>
                        <div class="text-xs text-gray-400">${data.log_terbaru.tanggal}</div>
                    </li>`;
            } else {
                activityList.innerHTML = '<li class="p-2 text-sm italic">Belum ada logbook.</li>';
            }

            const tbody = document.getElementById('riwayat-bimbingan');
            tbody.innerHTML = data.riwayat_bimbingan.length 
                ? data.riwayat_bimbingan.map(item => `
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-900 border-b dark:border-gray-700">
                        <td class="p-4">${item.pembimbing}</td>
                        <td class="p-4">${item.topik}</td>
                        <td class="p-4 text-xs"><span class="px-2 py-1 bg-green-100 text-green-700 rounded-full">Selesai</span></td>
                        <td class="p-4">${item.tanggal}</td>
                    </tr>`).join('')
                : '<tr><td colspan="4" class="text-center p-4">Belum ada riwayat.</td></tr>';
        } catch (e) { console.error("Mahasiswa data error", e); }
    }

    function setup() {
        return {
            isDark: localStorage.getItem('dark') === 'true',
            toggleTheme() {
                this.isDark = !this.isDark;
                localStorage.setItem('dark', this.isDark);
            }
        }
    }
    </script>
</body> 
</html>