@extends('layouts.app')

@section('title', 'Pendaftaran KP')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 mt-14">
        {{-- Header --}}
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Pendaftaran Kerja Praktek</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Lihat daftar pendaftaran KP dan status proposal Anda</p>
            </div>
            <button onclick="window.location.href='/pendaftaran-kp/tambah'" 
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                + Tambah Pendaftaran
            </button>
        </div>

        {{-- Loading State --}}
        <div id="loading" class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 mx-auto mb-2"></div>
            <p class="text-gray-500">Memuat data pendaftaran KP...</p>
        </div>

        {{-- Content Container --}}
        <div id="pendaftaran-container" class="hidden"></div>
    </div>
</div>

{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const loading = document.getElementById('loading');
    const container = document.getElementById('pendaftaran-container');
    const token = localStorage.getItem('abl_token');

    if (!token) { window.location.href = '/login'; return; }

    try {
        const userRes = await fetch('/api/me', {
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        });
        if (!userRes.ok) throw new Error('Gagal mengambil data user');
        const user = await userRes.json();

        const daftarRes = await fetch(`/api/pendaftaran-kp/saya/${user.id}`, {
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        });
        if (!daftarRes.ok) throw new Error('Gagal mengambil data pendaftaran KP');
        const daftarData = await daftarRes.json();

        loading.classList.add('hidden');
        container.classList.remove('hidden');

        if (daftarData.data && daftarData.data.length > 0) {
            let html = '';
            daftarData.data.forEach(item => {
                html += `
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6 hover:shadow-2xl transition">
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">${item.judul_kp}</h2>
                        <span class="px-3 py-1 rounded-full text-sm font-medium ${
                            item.status === 'approved' ? 'bg-green-200 text-green-900' :
                            item.status === 'rejected' ? 'bg-red-200 text-red-900' :
                            'bg-yellow-200 text-yellow-900'
                        }">${item.status}</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-1"><strong>Lokasi:</strong> ${item.lokasi}</p>
                    <p class="text-gray-600 dark:text-gray-400 mb-1"><strong>Periode:</strong> ${item.periode}</p>
                    <p class="text-gray-600 dark:text-gray-400 mb-1"><strong>Tanggal Daftar:</strong> ${new Date(item.created_at).toLocaleDateString()}</p>
                    <p class="text-gray-600 dark:text-gray-400 mb-3"><strong>Proposal:</strong> ${
                        item.proposal ? `<a href="${item.proposal}" target="_blank" class="text-blue-600 dark:text-blue-400 underline">Lihat Proposal</a>` : 'Belum diunggah'
                    }</p>
                    <div class="flex space-x-2">
                        <button onclick="window.location.href='/pendaftaran-kp/edit/${item.id}'" 
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Edit</button>
                        <button onclick="hapus(${item.id}, '${item.judul_kp}')" 
                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Hapus</button>
                    </div>
                </div>`;
            });
            container.innerHTML = html;
        } else {
            container.innerHTML = `
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-12 text-center">
                    <p class="text-gray-600 dark:text-gray-400">Anda belum mendaftar KP. Silakan daftar terlebih dahulu.</p>
                </div>`;
        }
    } catch (err) {
        console.error('Error:', err);
        loading.innerHTML = `
            <div class="bg-red-50 dark:bg-red-900 rounded-lg p-6 text-center">
                <p class="text-red-700 dark:text-red-200 font-medium">Gagal memuat data pendaftaran KP</p>
                <p class="text-red-600 dark:text-red-300 text-sm mt-1">${err.message}</p>
            </div>`;
    }
});

// Fungsi hapus dengan SweetAlert2
async function hapus(id, nama) {
    const result = await Swal.fire({
        title: 'Hapus Pendaftaran KP',
        text: `Apakah Anda yakin ingin menghapus pendaftaran "${nama}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        const token = localStorage.getItem('abl_token');
        try {
            const res = await fetch(`/api/pendaftaran-kp/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (res.ok) {
                Swal.fire(
                    'Berhasil!',
                    'Pendaftaran berhasil dihapus.',
                    'success'
                ).then(() => window.location.reload());
            } else {
                Swal.fire('Gagal!', data.message || 'Gagal menghapus pendaftaran', 'error');
            }
        } catch (err) {
            Swal.fire('Error!', 'Terjadi kesalahan: ' + err.message, 'error');
        }
    }
}
</script>
@endsection
