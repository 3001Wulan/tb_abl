<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentukan Dosen Pembimbing - Lann</title>
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

        {{-- Gunakan partials yang sama dengan dashboard --}}
        @include('partials.header') 
        @include('partials.sidebar-admin')

        <div class="h-full ml-14 mt-14 mb-10 md:ml-64 p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Plotting Dosen Pembimbing</h1>
                <p class="text-gray-500 dark:text-gray-400">Tentukan dosen pembimbing dan judul kerja praktik mahasiswa.</p>
            </div>

            <div class="w-full max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-xl p-8 border border-gray-100 dark:border-gray-600">
                
                <form id="pembimbingForm">
                    @csrf
                    {{-- Hidden input untuk menangkap student_id dari URL --}}
                    <input type="hidden" name="student_id" id="student_id_hidden" value="{{ request('student_id') }}">

                    <div class="space-y-6">
                        {{-- Nama Mahasiswa (Readonly) --}}
                        <div>
                            <label class="block mb-2 font-semibold text-gray-700 dark:text-gray-300">Mahasiswa Terpilih</label>
                            <input type="text" id="student_name_display" readonly
                                   class="w-full px-4 py-2.5 rounded-lg border bg-gray-100 dark:bg-gray-600 dark:border-gray-500 outline-none cursor-not-allowed shadow-sm"
                                   placeholder="Memuat data mahasiswa...">
                        </div>

                        {{-- Dropdown Dosen --}}
                        <div>
                            <label class="block mb-2 font-semibold text-gray-700 dark:text-gray-300">Pilih Dosen Pembimbing</label>
                            <select name="lecturer_id" id="lecturer_id" required
                                    class="w-full px-4 py-2.5 rounded-lg border dark:bg-gray-700 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none transition shadow-sm">
                                <option value="">-- Pilih Dosen --</option>
                                @foreach($lecturers as $dosen)
                                    <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Input Judul --}}
                        <div>
                            <label class="block mb-2 font-semibold text-gray-700 dark:text-gray-300">Judul Kerja Praktik</label>
                            <textarea name="judul" id="judul" required
                                      placeholder="Masukkan judul rencana kerja praktik..."
                                      class="w-full px-4 py-2.5 rounded-lg border dark:bg-gray-700 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 outline-none transition min-h-[120px] shadow-sm"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-8 pt-6 border-t dark:border-gray-700">
                        <a href="/dashboard" class="px-6 py-2.5 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white rounded-lg hover:bg-gray-300 transition font-medium">Batal</a>
                        <button type="submit" id="btnSubmit" class="px-10 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-lg transition font-bold transform hover:scale-105 active:scale-95">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.0/dist/alpine.min.js" defer></script>

<script>
const token = localStorage.getItem('abl_token');
const studentId = document.getElementById('student_id_hidden').value;

// 1. Cek Token & Ambil Detail Mahasiswa saat halaman dimuat
document.addEventListener('DOMContentLoaded', async () => {
    if (!token) { window.location.href = '/login'; return; }

    if (!studentId) {
        Swal.fire('Error', 'ID Mahasiswa tidak ditemukan!', 'error').then(() => window.location.href = '/dashboard');
        return;
    }

    try {
        // Ambil data mahasiswa untuk ditampilkan namanya
        const res = await fetch(`/api/students/${studentId}`, {
            headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
        });
        const student = await res.json();
        
        if (res.ok) {
            document.getElementById('student_name_display').value = `${student.nim} - ${student.nama}`;
        } else {
            throw new Error('Gagal mengambil data mahasiswa');
        }
    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'Gagal memuat data mahasiswa', 'error');
    }
});

// 2. Kirim Data Form (Create Supervision)
document.getElementById('pembimbingForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    
    Swal.fire({ 
        title: 'Menyimpan...', 
        text: 'Sedang menetapkan dosen pembimbing',
        allowOutsideClick: false, 
        didOpen: () => { Swal.showLoading(); } 
    });

    try {
        const formData = new FormData(this);

        const response = await fetch(`/api/pembimbing`, {
            method: 'POST',
            headers: { 
                'Authorization': 'Bearer ' + token, 
                'Accept': 'application/json' 
            },
            body: formData
        });

        const result = await response.json();

        if (response.status === 201 || response.status === 200) {
            Swal.fire({ 
                icon: 'success', 
                title: 'Berhasil!', 
                text: 'Dosen pembimbing telah ditetapkan.', 
                showConfirmButton: false, 
                timer: 2000 
            }).then(() => window.location.href = '/dashboard');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: result.message || 'Terjadi kesalahan saat menyimpan data.'
            });
        }
    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'Gagal menghubungkan ke server.', 'error');
    }
});

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