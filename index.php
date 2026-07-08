<?php

require_once __DIR__ . '/bootstrap/app.php';
require_once INCLUDE_PATH . '/header.php';

?>

<div class="max-w-7xl mx-auto px-6 py-10">

    <div class="mb-8">

        <h1 class="text-3xl font-bold">
            Dashboard
        </h1>

        <p class="text-gray-500 mt-2">
            Welcome to your Social Media CMS.
        </p>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <div class="bg-white rounded-xl shadow p-6">

            <h2 class="text-gray-500">
                Students
            </h2>

            <p class="text-4xl font-bold mt-4">
                0
            </p>

        </div>

        <div class="bg-white rounded-xl shadow p-6">

            <h2 class="text-gray-500">
                Posts
            </h2>

            <p class="text-4xl font-bold mt-4">
                0
            </p>

        </div>

        <div class="bg-white rounded-xl shadow p-6">

            <h2 class="text-gray-500">
                Categories
            </h2>

            <p class="text-4xl font-bold mt-4">
                0
            </p>

        </div>

        <div class="bg-white rounded-xl shadow p-6">

            <h2 class="text-gray-500">
                Users
            </h2>

            <p class="text-4xl font-bold mt-4">
                1
            </p>

        </div>

    </div>

    <div class="mt-10 bg-white rounded-xl shadow p-6">

        <h2 class="text-xl font-semibold mb-4">
            Quick Navigation
        </h2>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            <a href="<?= url('pages/students/index.php') ?>" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-5 text-center transition">
                Student Module
            </a>

            <button class="bg-gray-200 rounded-lg p-5 cursor-not-allowed">
                Posts
            </button>

            <button class="bg-gray-200 rounded-lg p-5 cursor-not-allowed">
                Media
            </button>

            <button class="bg-gray-200 rounded-lg p-5 cursor-not-allowed">
                Settings
            </button>
        </div>
    </div>
</div>

<?php

require_once 'includes/footer.php';

?>