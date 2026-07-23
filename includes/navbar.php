<?php

$currentUser = user();

?>

<nav class="bg-white shadow border-b">

    <div class="px-8 h-16 flex justify-between items-center">

        <!-- Logo -->
        <div>
            <a href="<?= isLoggedIn() ? url('pages/dashboard/index.php') : url('pages/auth/login.php') ?>"
               class="text-2xl font-bold text-blue-600">
                Social CMS
            </a>
        </div>

        <!-- Right Side -->
        <div class="relative">

            <?php if (isLoggedIn()): ?>

                <button id="userDropdownBtn"
                        class="flex items-center gap-3 hover:bg-gray-100 rounded-lg px-3 py-2 transition">

                    <img id="navbarProfileImage" src="<?= !empty($currentUser['picture']) ? asset($currentUser['picture']) : asset('assets/images/default/dp.png') ?>"
                         class="w-10 h-10 rounded-full object-cover border">

                    <div class="text-left hidden md:block">
                        <div class="font-semibold text-gray-800">
                            <?= e($currentUser['name']) ?>
                        </div>
                        <div class="text-xs text-gray-500">
                            @<?= e($currentUser['username']) ?>
                        </div>
                    </div>

                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>

                </button>

                <!-- Dropdown -->
                <div id="userDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border overflow-hidden z-50">

                    <div class="px-4 py-3 border-b">
                        <div class="font-semibold"><?= e($currentUser['name']) ?></div>
                        <div class="text-sm text-gray-500"><?= e($currentUser['email']) ?></div>
                    </div>

                    <a href="<?= url('pages/profile/update.php') ?>" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-100">
                        <i class="fa-solid fa-user w-5"></i> Profile
                    </a>

                    <div class="border-t"></div>

                    <button type="button" onclick="logout()" class="flex items-center gap-3 w-full px-4 py-3 text-red-600 hover:bg-red-50">
                        <i class="fa-solid fa-right-from-bracket w-5"></i> Logout
                    </button>

                </div>

            <?php else: ?>

                <div class="flex items-center gap-3">
                    <a href="<?= url('pages/auth/login.php') ?>" class="px-5 py-2 rounded-lg border border-blue-600 text-blue-600 hover:bg-blue-50 transition">
                        Login
                    </a>
                    <a href="<?= url('pages/auth/register.php') ?>" class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                        Register
                    </a>
                </div>

            <?php endif; ?>

        </div>

    </div>

</nav>

<script>
const dropdownBtn = document.getElementById('userDropdownBtn');
const dropdown = document.getElementById('userDropdown');

if (dropdownBtn) {
    dropdownBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', function() {
        dropdown.classList.add('hidden');
    });
}

async function logout() {
    if (!confirm('Are you sure you want to logout?')) {
        return;
    }

    const response = await fetch(APP_URL + 'api/auth/logout.php', {
        method: 'POST'
    });

    const result = await response.json();

    if (!result.success) {
        toaster.error(result.message);
        return;
    }

    toaster.success(result.message || 'Logout successful.');

    setTimeout(() => {
        window.location.href = APP_URL + 'pages/auth/login.php';
    }, 800);
}
</script>