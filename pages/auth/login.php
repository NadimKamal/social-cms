<?php

require_once __DIR__ . '/../../bootstrap/app.php';
$pageTitle = 'Login';

require_once INCLUDE_PATH . '/header.php';

?>

<div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-xl shadow-lg">
            <div class="border-b px-8 py-6 text-center">
                <h1 class="text-3xl font-bold text-gray-800">Welcome Back</h1>
                <p class="text-gray-500 mt-2">Login to your Social CMS account.</p>
            </div>

            <form id="loginForm" class="p-8 space-y-6">

                <!-- Username / Email -->
                <div>
                    <label class="block mb-2 font-medium">Username or Email</label>
                    <input type="text" name="login" required autofocus autocomplete="username"
                           class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Password -->
                <div>
                    <label class="block mb-2 font-medium">Password</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               class="w-full border rounded-lg px-4 py-3 pr-12 focus:ring-2 focus:ring-blue-500">

                        <button type="button" id="togglePassword" 
                                class="absolute inset-y-0 right-0 px-4 text-gray-500 hover:text-blue-600">
                            <i id="passwordIcon" class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember">
                    <span class="text-sm">Remember Me</span>
                </label>

                <!-- Submit Button -->
                <button id="loginBtn"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg py-3 font-semibold">
                    Login
                </button>

                <div class="flex justify-between text-sm">
                    <a href="<?= url('pages/auth/forgot-password.php') ?>" class="text-blue-600 hover:underline">
                        Forgot Password?
                    </a>
                    <a href="<?= url('pages/auth/register.php') ?>" class="text-blue-600 hover:underline">
                        Register
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
const form = qs('#loginForm');
const password = qs('#password');
const togglePassword = qs('#togglePassword');
const passwordIcon = qs('#passwordIcon');

form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const btn = qs('#loginBtn');
    btn.disabled = true;
    btn.innerHTML = 'Signing In...';

    const formData = new FormData(form);

    try {
        const response = await fetch(APP_URL + 'api/auth/login.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (!result.success) {
            toaster.error(result.message);
            btn.disabled = false;
            btn.innerHTML = 'Login';
            return;
        }

        toaster.success(result.data.message);

        setTimeout(() => {
            location.href = APP_URL + 'pages/dashboard/index.php';
        }, 800);

    } catch (error) {
        toaster.error('Unable to connect to server.');
        btn.disabled = false;
        btn.innerHTML = 'Login';
    }
});

togglePassword.addEventListener('click', function () {
    if (password.type === 'password') {
        password.type = 'text';
        passwordIcon.className = 'fa-regular fa-eye-slash';
    } else {
        password.type = 'password';
        passwordIcon.className = 'fa-regular fa-eye';
    }
});
</script>

<?php
require_once INCLUDE_PATH . '/footer.php';
?>