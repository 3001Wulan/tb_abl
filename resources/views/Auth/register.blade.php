<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Mahasiswa - Lann</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { font-family: sans-serif; margin: 0; }
    .register-card { z-index: 10; transition: transform 0.5s ease, box-shadow 0.5s ease; }
    .register-card:hover { transform: translateY(-10px) scale(1.02); box-shadow: 0 20px 40px rgba(0,0,0,0.4); }
    .left-image { background-image: url('assets/images/bg-login.jpeg'); background-size: cover; background-position: center; }
  </style>
</head>
<body class="flex h-screen w-screen">

  <div class="left-image w-1/2 h-full hidden sm:block"></div>

  <div class="w-full sm:w-1/2 flex items-center justify-center p-6">
    <div class="relative sm:max-w-md w-full register-card">
      
      <div class="card bg-blue-400 shadow-lg w-full h-full rounded-3xl absolute transform -rotate-6 opacity-80"></div>
      <div class="card bg-red-400 shadow-lg w-full h-full rounded-3xl absolute transform rotate-6 opacity-80"></div>

      <div class="relative w-full rounded-3xl px-8 py-10 bg-white/90 shadow-md backdrop-blur-sm">
        <h2 class="text-xl text-gray-700 text-center font-bold mb-6">Register Mahasiswa</h2>

        <form id="registerForm">
          <div>
            <input type="text" name="name" placeholder="Nama Lengkap" required
              class="mt-1 block w-full border-none bg-gray-100 h-12 rounded-xl shadow-md hover:bg-blue-50 focus:bg-blue-100 focus:ring-0 transition px-4 text-gray-700">
          </div>

          <div class="mt-4">
            <input type="text" name="nim" placeholder="Nomor Induk Mahasiswa (NIM)" required
              class="mt-1 block w-full border-none bg-gray-100 h-12 rounded-xl shadow-md hover:bg-blue-50 focus:bg-blue-100 focus:ring-0 transition px-4 text-gray-700">
          </div>

          <div class="mt-4">
            <input type="email" name="email" placeholder="Email" required
              class="mt-1 block w-full border-none bg-gray-100 h-12 rounded-xl shadow-md hover:bg-blue-50 focus:bg-blue-100 focus:ring-0 transition px-4 text-gray-700">
          </div>

          <div class="mt-4 relative">
            <input type="password" id="password" name="password" placeholder="Password" required
              class="mt-1 block w-full border-none bg-gray-100 h-12 rounded-xl shadow-md hover:bg-blue-50 focus:bg-blue-100 focus:ring-0 transition px-4 pr-16 text-gray-700">
            <button type="button" id="togglePassword" class="absolute right-4 top-3 text-gray-500 hover:text-gray-700 font-semibold text-sm">
              Show
            </button>
          </div>

          <div class="mt-4">
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi Password" required
              class="mt-1 block w-full border-none bg-gray-100 h-12 rounded-xl shadow-md hover:bg-blue-50 focus:bg-blue-100 focus:ring-0 transition px-4 text-gray-700">
          </div>

          <div class="mt-8">
            <button type="submit"
              class="bg-blue-500 w-full py-4 rounded-xl text-white shadow-xl hover:shadow-inner transition duration-300 transform hover:scale-105 text-lg font-semibold">
              Register Sekarang
            </button>
          </div>

          <p class="mt-6 text-center text-gray-600 text-sm">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-blue-500 font-semibold hover:underline">Login</a>
          </p>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Toggle password
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    togglePassword.addEventListener('click', () => {
      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        togglePassword.textContent = "Hide";
      } else {
        passwordInput.type = "password";
        togglePassword.textContent = "Show";
      }
    });

    // Form submit
    document.getElementById('registerForm').addEventListener('submit', async function(e) {
      e.preventDefault();

      // Menampilkan Loading
      Swal.fire({
          title: 'Mendaftarkan Akun...',
          allowOutsideClick: false,
          didOpen: () => { Swal.showLoading(); }
      });

      const payload = {
          name: this.name.value,
          nim: this.nim.value, // Kirim NIM ke server
          email: this.email.value,
          password: this.password.value,
          password_confirmation: this.password_confirmation.value
      };

      try {
        const response = await fetch('/api/register', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (response.ok) {
          Swal.fire({
              icon: 'success',
              title: 'Berhasil!',
              text: 'Akun Anda dan Data Mahasiswa berhasil dibuat.',
              timer: 2000,
              showConfirmButton: false
          }).then(() => {
              window.location.href = '/login';
          });
        } else {
          Swal.fire('Gagal Register', data.message || 'Cek kembali data Anda (Email mungkin sudah terdaftar)', 'error');
        }
      } catch (error) {
        console.error(error);
        Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
      }
    });
  </script>

</body>
</html>