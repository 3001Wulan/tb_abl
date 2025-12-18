<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Harian / Mingguan Kerja Praktik - Lann</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .header-right { width: calc(100% - 3.5rem); }
        .sidebar:hover { width: 16rem; }
        @media only screen and (min-width: 768px) {
            .header-right { width: calc(100% - 16rem); }         
        }
        .dark .swal2-popup { background-color: #1f2937 !important; color: white !important; }
    </style>
</head>
<body x-data="setup()" :class="{ 'dark': isDark }">
<div class="min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased bg-white dark:bg-gray-700 text-black dark:text-white transition-colors duration-200">

    @include('partials.header') 
    @include('partials.sidebar')

    <div class="h-full ml-14 mt-14 mb-10 md:ml-64 p-4">

        <h1 class="text-2xl font-bold mb-6">Log Harian / Mingguan Kerja Praktik</h1>

        <div class="flex justify-end mb-6">
            <a href="/mahasiswa/logbook/create"
               class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg shadow-lg flex items-center transition duration-200">
                <span class="mr-2">+</span> Tambah Logbook
            </a>
        </div>

        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-600">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900 text-xs uppercase text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-4 text-center font-bold w-16">No</th>
                        <th class="px-6 py-4 text-left font-bold">Minggu</th>
                        <th class="px-6 py-4 text-left font-bold">Periode</th>
                        <th class="px-6 py-4 text-left font-bold">Deskripsi</th>
                        <th class="px-6 py-4 text-left font-bold">Status</th>
                        <th class="px-6 py-4 text-center font-bold">File</th> <th class="px-6 py-4 text-center font-bold">Aksi</th>
                    </tr>
                </thead>

                <tbody id="logbook-body" class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr>
                        <td colspan="7" class="text-center p-10">
                            <div class="flex flex-col items-center">
                                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 mb-2"></div>
                                <p class="text-gray-500">Memuat data logbook...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@2.x.x/dist/alpine.min.js" defer></script>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const tbody = document.getElementById('logbook-body');
    const token = localStorage.getItem('abl_token');

    if (!token) { window.location.href = '/login'; return; }

    try {
        const userRes = await fetch('/api/me', {
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        });
        if (!userRes.ok) throw new Error('Gagal mengambil data user');
        const user = await userRes.json();

        const res = await fetch(`/api/logbook/${user.id}`, {
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error('Gagal mengambil data logbook');

        const data = await res.json();
        tbody.innerHTML = '';

        if (!Array.isArray(data) || data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center p-10 text-gray-500">Belum ada logbook yang diisi.</td></tr>`;
            return;
        }

        data.forEach((log, index) => {
            let statusBadge = '';
            let canEdit = true;
            let canDelete = true;

            let fileDownload = `<span class="text-gray-400">—</span>`;
            if (log.file_kegiatan) {
                const fileUrl = `/storage/${log.file_kegiatan}`;
                fileDownload = `
                    <a href="${fileUrl}" download 
                       class="inline-flex items-center text-blue-500 hover:text-blue-700 transition transform hover:scale-110" 
                       title="Unduh File">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </a>`;
            }

            if (log.status === 'Pending') {
                statusBadge = `<span class="px-3 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded-full">Pending</span>`;
            } else if (log.status === 'Disetujui') {
                statusBadge = `<span class="px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">Disetujui</span>`;
                canEdit = false;
                canDelete = false;
            } else {
                statusBadge = `<span class="px-3 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">Revisi</span>`;
            }

            tbody.innerHTML += `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                    <td class="px-6 py-4 text-center text-sm font-bold text-gray-400">${index + 1}</td>
                    <td class="px-6 py-4 font-semibold text-blue-600 dark:text-blue-400 text-lg">Mg-${log.minggu_ke}</td>
                    <td class="px-6 py-4 text-sm">${log.tanggal_mulai}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 max-w-xs truncate">${log.deskripsi_kegiatan}</td>
                    <td class="px-6 py-4">${statusBadge}</td>
                    <td class="px-6 py-4 text-center">${fileDownload}</td> <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center space-x-3">
                            <a href="/mahasiswa/logbook/${log.id}/edit"
                               class="flex items-center text-blue-600 hover:text-blue-800 font-medium ${!canEdit ? 'opacity-30 pointer-events-none' : ''}"
                               title="Edit Data">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <button onclick="confirmDelete(${log.id})"
                               class="flex items-center text-red-600 hover:text-red-800 font-medium ${!canDelete ? 'opacity-30 pointer-events-none' : ''}"
                               title="Hapus Data">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>`;
        });

    } catch (err) {
        console.error(err);
        tbody.innerHTML = `<tr><td colspan="7" class="text-center text-red-500 p-10 font-bold">Gagal memuat data logbook.</td></tr>`;
    }
});

function confirmDelete(id) {
    const token = localStorage.getItem('abl_token');
    Swal.fire({
        title: 'Hapus Logbook?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
            fetch(`/api/logbook/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            })
            .then(res => {
                if (res.ok) {
                    Swal.fire({ icon: 'success', title: 'Terhapus!', timer: 1500, showConfirmButton: false }).then(() => location.reload());
                } else {
                    Swal.fire('Gagal!', 'Data tidak bisa dihapus.', 'error');
                }
            });
        }
    });
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