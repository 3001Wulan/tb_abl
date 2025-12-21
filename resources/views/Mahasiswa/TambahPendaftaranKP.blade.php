@extends('layouts.app')

@section('title', 'Tambah Pendaftaran KP')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 mt-14 max-w-3xl mx-auto">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Tambah Pendaftaran Kerja Praktek</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Isi form berikut untuk menambahkan pendaftaran KP baru</p>
        </div>

        {{-- Form Container --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <form id="form-tambah-kp" class="space-y-4" enctype="multipart/form-data">
                <div>
                    <label class="block text-gray-700 dark:text-gray-300">Judul KP</label>
                    <input type="text" name="judul_kp" class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300">Lokasi</label>
                    <input type="text" name="lokasi" class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300">Periode</label>
                    <input type="text" name="periode" class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Contoh: 2025/2026" required>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300">Upload Proposal (PDF)</label>
                    <input type="file" name="proposal" accept=".pdf" class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>
                <div class="flex space-x-2 mt-4">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Simpan</button>
                    <button type="button" onclick="window.location.href='/pendaftaran-kp'" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Batal</button>
                </div>
            </form>
        </div>

        {{-- Alert --}}
        <div id="alert" class="mt-4 hidden p-4 rounded text-white"></div>
    </div>
</div>

<script>
document.getElementById('form-tambah-kp').addEventListener('submit', async function(e) {
    e.preventDefault();
    const alertBox = document.getElementById('alert');
    alertBox.classList.add('hidden');

    const token = localStorage.getItem('abl_token');
    if (!token) {
        window.location.href = '/login';
        return;
    }

    // Ambil data user dulu
    let user;
    try {
        const userRes = await fetch('/api/me', {
            headers: { 
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });
        if (!userRes.ok) throw new Error('Gagal mengambil data user');
        user = await userRes.json();
    } catch (err) {
        alertBox.textContent = 'Gagal mendapatkan data user: ' + err.message;
        alertBox.className = 'mt-4 p-4 rounded bg-red-600 text-white';
        alertBox.classList.remove('hidden');
        return;
    }

    // Pakai FormData untuk mengirim file
    const formData = new FormData(this);

    // Tambahkan student_id ke FormData
    formData.append('student_id', user.id);

    try {
        const res = await fetch('/api/pendaftaran-kp', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            body: formData
        });

        const resData = await res.json();

        if (res.ok) {
            alertBox.textContent = 'Pendaftaran KP berhasil ditambahkan!';
            alertBox.className = 'mt-4 p-4 rounded bg-green-600 text-white';
            alertBox.classList.remove('hidden');
            setTimeout(() => window.location.href = '/pendaftaran-kp', 1000);
        } else {
            const errors = resData.errors ? Object.values(resData.errors).flat().join(' | ') : resData.message;
            alertBox.textContent = errors || 'Gagal menambahkan pendaftaran';
            alertBox.className = 'mt-4 p-4 rounded bg-red-600 text-white';
            alertBox.classList.remove('hidden');
        }
    } catch (err) {
        alertBox.textContent = 'Terjadi kesalahan: ' + err.message;
        alertBox.className = 'mt-4 p-4 rounded bg-red-600 text-white';
        alertBox.classList.remove('hidden');
    }
});

</script>
@endsection
