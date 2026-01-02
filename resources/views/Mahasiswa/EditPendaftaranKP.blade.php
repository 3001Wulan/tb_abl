@extends('layouts.app')

@section('title', 'Edit Pendaftaran KP')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="p-4 mt-14 max-w-3xl mx-auto">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Pendaftaran Kerja Praktek</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Ubah data pendaftaran KP dan unggah proposal terbaru jika diperlukan</p>
        </div>

        {{-- Form Container --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <form id="form-edit-kp" class="space-y-4" enctype="multipart/form-data">
                <div>
                    <label class="block text-gray-700 dark:text-gray-300">Judul KP</label>
                    <input type="text" name="judul_kp" value="{{ $pendaftaran->judul_kp }}" 
                        class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300">Lokasi</label>
                    <input type="text" name="lokasi" value="{{ $pendaftaran->lokasi }}" 
                        class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300">Periode</label>
                    <input type="text" name="periode" value="{{ $pendaftaran->periode }}" 
                        class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300">Proposal (PDF)</label>
                    @if($pendaftaran->proposal)
                        <p class="mb-2 text-sm text-blue-600 dark:text-blue-400">
                            Proposal saat ini: <a href="{{ $pendaftaran->proposal }}" target="_blank" class="underline">Lihat Proposal</a>
                        </p>
                    @endif
                    <input type="file" name="proposal" accept=".pdf" 
                        class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <p class="text-gray-500 text-sm mt-1">Biarkan kosong jika tidak ingin mengganti proposal</p>
                </div>
                <div class="flex space-x-2 mt-4">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update</button>
                    <button type="button" onclick="window.location.href='/pendaftaran-kp'" 
                        class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Batal</button>
                </div>
            </form>
        </div>

        {{-- Alert --}}
        <div id="alert" class="mt-4 hidden p-4 rounded text-white"></div>
    </div>
</div>

<script>
document.getElementById('form-edit-kp').addEventListener('submit', async function(e) {
    e.preventDefault();
    const alertBox = document.getElementById('alert');
    alertBox.classList.add('hidden');

    const token = localStorage.getItem('abl_token');
    if (!token) {
        window.location.href = '/login';
        return;
    }

    const formData = new FormData(this);
    formData.append('_method', 'PUT'); // tambahkan method spoofing agar Laravel anggap PUT

    try {
        const res = await fetch(`/api/pendaftaran-kp/{{ $pendaftaran->id }}`, {
            method: 'POST', // tetap POST karena FormData
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            },
            body: formData
        });

        const resData = await res.json();

        if (res.ok) {
            alertBox.textContent = 'Pendaftaran KP berhasil diperbarui!';
            alertBox.className = 'mt-4 p-4 rounded bg-green-600 text-white';
            alertBox.classList.remove('hidden');
            setTimeout(() => window.location.href = '/pendaftaran-kp', 1000);
        } else {
            alertBox.textContent = resData.message || 'Gagal memperbarui pendaftaran';
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
