<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Logbook KP - Lann</title>
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

        <div class="h-full ml-14 mt-14 mb-10 md:ml-64 p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Edit Logbook Mingguan</h1>
                <p class="text-gray-500 dark:text-gray-400">Perbarui informasi kegiatan Anda di bawah ini.</p>
            </div>

            <div class="w-full max-w-6xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-xl p-8 border border-gray-100 dark:border-gray-600">
                
                <form id="editLogbookForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" id="logbook_id" value="{{ $id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                        <div class="space-y-6">
                            <div>
                                <label class="block mb-2 font-semibold text-gray-700 dark:text-gray-300">Minggu Ke-</label>
                                <input type="number" name="minggu_ke" id="minggu_ke" required
                                       class="w-full px-4 py-2.5 rounded-lg border dark:bg-gray-700 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none transition shadow-sm">
                            </div>

                            <div>
                                <label class="block mb-2 font-semibold text-gray-700 dark:text-gray-300">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" required
                                       class="w-full px-4 py-2.5 rounded-lg border dark:bg-gray-700 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none transition shadow-sm">
                            </div>

                            <div>
                                <label class="block mb-2 font-semibold text-gray-700 dark:text-gray-300">File Kegiatan</label>
                                <div class="flex items-center justify-center w-full">
                                    <label for="file_kegiatan" class="flex flex-col items-center justify-center w-full h-44 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 transition shadow-inner">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                                            <svg id="upload-icon" class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                            <p id="file-name-display" class="mb-2 text-sm text-gray-500 dark:text-gray-400 font-medium">Memuat informasi file...</p>
                                        </div>
                                        <input type="file" name="file_kegiatan" id="file_kegiatan" class="hidden" onchange="previewFileName(this)" />
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <label class="block mb-2 font-semibold text-gray-700 dark:text-gray-300">Deskripsi Kegiatan</label>
                            <textarea name="deskripsi_kegiatan" id="deskripsi_kegiatan" required
                                      class="w-full flex-grow px-4 py-2 rounded-lg border dark:bg-gray-700 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none transition min-h-[200px] md:min-h-full shadow-sm"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-8 pt-6 border-t dark:border-gray-700">
                        <a href="/mahasiswa/logbook" class="px-6 py-2.5 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white rounded-lg hover:bg-gray-300 transition font-medium">Batal</a>
                        <button type="submit" id="btnSubmit" class="px-10 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-lg transition font-bold transform hover:scale-105 active:scale-95">Update Logbook</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@2.x.x/dist/alpine.min.js" defer></script>

<script>
const logbookId = document.getElementById('logbook_id').value;
const token = localStorage.getItem('abl_token');

document.addEventListener('DOMContentLoaded', async () => {
    if (!token) { window.location.href = '/login'; return; }

    try {
        const response = await fetch(`/api/logbook/detail/${logbookId}`, {
            headers: { 
                'Authorization': 'Bearer ' + token, 
                'Accept': 'application/json' 
            }
        });
        
        if (!response.ok) throw new Error('Gagal mengambil data logbook dari server');
        
        const log = await response.json();

        document.getElementById('minggu_ke').value = log.minggu_ke;
        document.getElementById('tanggal_mulai').value = log.tanggal_mulai;
        document.getElementById('deskripsi_kegiatan').value = log.deskripsi_kegiatan;
        
        const display = document.getElementById('file-name-display');
        if (log.file_kegiatan) {
            const fileName = log.file_kegiatan.split('/').pop();
            display.innerHTML = `File saat ini: <span class="text-blue-500 font-bold">${fileName}</span><br><span class="text-xs text-gray-400">Klik untuk mengganti</span>`;
        } else {
            display.innerHTML = `<span class="font-semibold">Klik untuk upload file</span> atau seret ke sini`;
        }

    } catch (error) {
        console.error(error);
        Swal.fire('Error', error.message, 'error');
    }
});

document.getElementById('editLogbookForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    
    Swal.fire({ 
        title: 'Memperbarui...', 
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false, 
        didOpen: () => { Swal.showLoading(); } 
    });

    try {
        const formData = new FormData(this);

        const response = await fetch(`/api/logbook/${logbookId}`, {
            method: 'POST', 
            headers: { 
                'Authorization': 'Bearer ' + token, 
                'Accept': 'application/json' 
            },
            body: formData
        });

        const result = await response.json();

        if (response.ok) {
            Swal.fire({ 
                icon: 'success', 
                title: 'Berhasil!', 
                text: 'Logbook diperbarui.', 
                showConfirmButton: false, 
                timer: 2000 
            }).then(() => window.location.href = '/mahasiswa/logbook');
        } else {
            let errorText = result.message || 'Terjadi kesalahan validasi.';
            if (result.errors) {
                errorText = Object.values(result.errors).flat().join('<br>');
            }
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                html: errorText
            });
        }
    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'Gagal menghubungkan ke server.', 'error');
    }
});

function previewFileName(input) {
    const display = document.getElementById('file-name-display');
    const icon = document.getElementById('upload-icon');
    if (input.files.length > 0) {
        display.innerHTML = `<span class="text-blue-600 font-bold text-sm">File baru siap diunggah: <br>${input.files[0].name}</span>`;
        icon.classList.add('text-blue-500');
    }
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