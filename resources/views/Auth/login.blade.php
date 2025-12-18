<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Mahasiswa</title>
  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      /* Background gambar dari folder lokal */
      background-image: url('assets/images/bg-login.jpeg'); /* ganti path sesuai foldermu */
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      position: relative;
      min-height: 100vh;
    }

    /* Overlay hitam + blur */
    body::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.5); /* warna hitam semi-transparan */
      backdrop-filter: blur(6px); /* blur background */
      z-index: 0;
    }

    /* Card login di atas overlay */
    .login-card {
      z-index: 10;
    }

    /* Animasi muncul / hover */
    .login-card {
      transition: transform 0.5s ease, box-shadow 0.5s ease;
    }

    .login-card:hover {
      transform: translateY(-10px) scale(1.02);
      box-shadow: 0 20px 40px rgba(0,0,0,0.4);
    }
  </style>
</head>
<body class="font-sans flex items-center justify-center">

  <div class="relative sm:max-w-md w-full login-card">
    <!-- Background cards (opsional efek dekoratif) -->
    <div class="card bg-blue-400 shadow-lg w-full h-full rounded-3xl absolute transform -rotate-6 transition-all duration-500 hover:rotate-0 hover:scale-105 opacity-80"></div>
    <div class="card bg-red-400 shadow-lg w-full h-full rounded-3xl absolute transform rotate-6 transition-all duration-500 hover:rotate-0 hover:scale-105 opacity-80"></div>

    <!-- Login Card -->
    <div class="relative w-full rounded-3xl px-8 py-12 bg-white/90 shadow-md backdrop-blur-sm">
      <h2 class="block mt-3 text-xl text-gray-700 text-center font-bold mb-8">Login Mahasiswa</h2>

      <form id="loginForm">
        <!-- Email -->
        <div>
          <input type="email" name="email" placeholder="Email" required
            class="mt-1 block w-full border-none bg-gray-100 h-14 rounded-xl shadow-lg hover:bg-blue-100 focus:bg-blue-100 focus:ring-0 transition-colors duration-300 text-gray-700 px-4">
        </div>

        <!-- Password -->
        <div class="mt-6 relative">
          <input type="password" id="password" name="password" placeholder="Password" required
            class="mt-1 block w-full border-none bg-gray-100 h-14 rounded-xl shadow-lg hover:bg-blue-100 focus:bg-blue-100 focus:ring-0 transition-colors duration-300 text-gray-700 px-4 pr-16">
          <button type="button" id="togglePassword" class="absolute right-4 top-4 text-gray-500 hover:text-gray-700 font-semibold">
            Show
          </button>
        </div>

        <!-- Remember Me & Register -->
        <div class="mt-6 flex items-center justify-between text-gray-700">
          <label for="remember_me" class="inline-flex items-center cursor-pointer">
            <input id="remember_me" type="checkbox" name="remember"
              class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <span class="ml-2 text-sm">Remember Me</span>
          </label>
          <a href="{{ route('register') }}" class="underline text-sm hover:text-gray-900">
  Register Here?
</a>

        </div>

        <!-- Submit Button -->
        <div class="mt-8">
          <button type="submit"
            class="bg-blue-500 w-full py-4 rounded-xl text-white shadow-xl hover:shadow-inner focus:outline-none transition duration-500 ease-in-out transform hover:-translate-x hover:scale-105 text-lg font-semibold">
            Login
          </button>
        </div>

      </form>
    </div>
  </div>

  <!-- JS untuk Login AJAX dan Show/Hide Password -->
  <script>
    // Toggle Show/Hide Password
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

    // Login Form AJAX
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
      e.preventDefault();

      const email = this.email.value;
      const password = this.password.value;

      try {
        const response = await fetch('/api/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (response.ok) {
          localStorage.setItem('abl_token', data.token);
          window.location.href = '/dashboard';
        } else {
          alert(data.message || 'Login gagal');
        }
      } catch (error) {
        console.error(error);
        alert('Terjadi kesalahan pada server');
      }
    });
  </script>

</body>
</html>
