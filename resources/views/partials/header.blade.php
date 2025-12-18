<div class="fixed w-full flex items-center justify-between h-14 text-white z-20">
    <div class="flex items-center justify-start md:justify-center pl-3 w-14 md:w-64 h-14 bg-blue-800 dark:bg-gray-800 border-none transition-all duration-300">
        <img class="w-7 h-7 md:w-10 md:h-10 mr-2 rounded-md overflow-hidden object-cover flex-shrink-0" 
             src="{{ asset('uploads/images.jpeg') }}" 
             alt="User Profile" />
        
        <div class="hidden md:flex flex-col leading-tight overflow-hidden px-2">
            <span id="user-name" class="text-[11px] font-bold truncate tracking-wide uppercase whitespace-nowrap">
                Memuat Nama...
            </span>
            <span id="user-role" class="text-[9px] text-blue-200 dark:text-gray-400 font-medium truncate uppercase whitespace-nowrap">
                Role
            </span>
        </div>
    </div>

    <div class="flex justify-between items-center h-14 bg-blue-800 dark:bg-gray-800 header-right transition-all duration-300 flex-grow px-4">
        <div class="bg-white rounded flex items-center w-full max-w-xl mr-4 p-2 shadow-sm border border-gray-200">
            <button class="outline-none focus:outline-none">
                <svg class="w-5 text-gray-600 h-5" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>
            <input type="search" placeholder="Cari Dokumen/Info..." class="w-full pl-3 text-sm text-black outline-none focus:outline-none bg-transparent" />
        </div>

        <ul class="flex items-center">
            <li><div class="block w-px h-6 mx-3 bg-gray-400 dark:bg-gray-700"></div></li>
            <li>
                <button onclick="handleLogout()" class="flex items-center mr-4 hover:text-red-200 transition-colors duration-200">
                    <span class="inline-flex mr-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </span>
                    Logout
                </button>
            </li>
        </ul>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', async () => {
    const userNameSpan = document.getElementById('user-name');
    const userRoleSpan = document.getElementById('user-role');
    const token = localStorage.getItem('abl_token');

    if (!token) {
        window.location.href = '/login';
        return;
    }

    try {
        const response = await fetch('/api/me', {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            
            // Masukkan Nama ke elemen atas
            userNameSpan.textContent = data.name;

            // Masukkan Role ke elemen bawah (Default Mahasiswa jika null)
            userRoleSpan.textContent = data.role ? data.role : 'MAHASISWA';
            
        } else if (response.status === 401) {
            localStorage.removeItem('abl_token');
            window.location.href = '/login';
        }
    } catch (error) {
        console.error('Gagal memuat profil:', error);
        userNameSpan.textContent = 'ERROR';
    }
});

function handleLogout() {
    Swal.fire({
        title: 'Keluar dari aplikasi?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Logout',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            localStorage.removeItem('abl_token');
            window.location.href = '/login';
        }
    });
}
</script>