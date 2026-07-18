<?php
require_once __DIR__ . '/bootstrap/app.php';
require_once INCLUDE_PATH . '/header.php';

// Dashboard Statistics
$totalContents = $pdo->query("
    SELECT COUNT(*)
    FROM contents
")->fetchColumn();

$totalPosts = $pdo->query("
    SELECT COUNT(*)
    FROM social_posts
")->fetchColumn();

$totalCategories = $pdo->query("
    SELECT COUNT(*)
    FROM content_categories
")->fetchColumn();

$generatedPosts = $pdo->query("
    SELECT COUNT(*)
    FROM social_posts
")->fetchColumn();

$draftPosts = $pdo->query("
    SELECT COUNT(*)
    FROM social_posts
    WHERE status = 'Draft'
")->fetchColumn();

$publishedPosts = $pdo->query("
    SELECT COUNT(*)
    FROM social_posts
    WHERE status = 'Published'
")->fetchColumn();
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

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-gray-500">AI Contents</h2>
            <p class="text-4xl font-bold mt-4 text-blue-600">
                <?= $totalContents ?>
            </p>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-gray-500">Social Posts</h2>
            <p class="text-4xl font-bold mt-4 text-green-600">
                <?= $totalPosts ?>
            </p>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-gray-500">Categories</h2>
            <p class="text-4xl font-bold mt-4 text-purple-600">
                <?= $totalCategories ?>
            </p>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-gray-500">Generated Posts</h2>
            <p class="text-4xl font-bold mt-4 text-orange-600">
                <?= $generatedPosts ?>
            </p>
        </div>
    </div>

    <!-- Draft & Published -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500">Draft Posts</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">
                        <?= $draftPosts ?>
                    </p>
                </div>
                <div class="text-5xl">📝</div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500">Published Posts</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">
                        <?= $publishedPosts ?>
                    </p>
                </div>
                <div class="text-5xl">🚀</div>
            </div>
        </div>
    </div>

    <!-- Quick Navigation -->
    <div class="mt-10 bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-semibold mb-6">
            Quick Navigation
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <a href="<?= url('pages/contents/index.php') ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-5 text-center transition">
                AI Content Inbox
            </a>

            <a href="<?= url('pages/contents/create.php') ?>" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg p-5 text-center transition">
                Add Content
            </a>

            <a href="<?= url('pages/social-accounts/index.php') ?>" 
               class="bg-green-600 hover:bg-green-700 text-white rounded-lg p-5 text-center transition">
                Social Accounts
            </a>

            <a href="<?= url('pages/content-categories/index.php') ?>" 
               class="bg-purple-600 hover:bg-purple-700 text-white rounded-lg p-5 text-center transition">
                Categories
            </a>

            <a href="<?= url('pages/students/index.php') ?>" 
               class="bg-sky-600 hover:bg-sky-700 text-white rounded-lg p-5 text-center transition">
                Students (Demo)
            </a>

            <button class="bg-gray-200 rounded-lg p-5 cursor-not-allowed">
                Media Library
            </button>
            
            <button class="bg-gray-200 rounded-lg p-5 cursor-not-allowed">
                Publishing Queue
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