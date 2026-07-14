<?php

require_once '../../bootstrap/app.php';
require_once INCLUDE_PATH . '/header.php';

$uuid = $_GET['uuid'] ?? '';

$content = findByUuidOrFail(
    $pdo,
    'contents',
    $uuid
);

$category = findOrFail(
    $pdo,
    'content_categories',
    [
        'id' => $content['content_category_id']
    ]
);

?>

<div class="max-w-5xl mx-auto py-8">

    <div class="bg-white rounded-xl shadow">

        <div class="border-b px-6 py-5">

            <h1 class="text-3xl font-bold">

                <?= e($content['title']) ?>

            </h1>

        </div>

        <div class="p-6 space-y-8">

            <div>

                <span
                    class="px-3 py-1 rounded-full text-white text-sm"
                    style="background:<?= e($category['color']) ?>">

                    <?= e($category['title']) ?>

                </span>

            </div>

            <?php if($content['image_path']): ?>

                <img
                    src="<?= asset($content['image_path']) ?>"
                    width="200"
                    height="200"
                    class="rounded-xl object-cover">

            <?php endif; ?>

            <div>

                <h2 class="font-semibold text-xl mb-3">

                    Original Content

                </h2>

                <div class="prose max-w-none">

                    <?= nl2br(e($content['original_text'])) ?>

                </div>

            </div>

            <div>

                <h2 class="font-semibold text-xl mb-3">

                    AI Summary

                </h2>

                <div class="prose max-w-none">

                    <?= nl2br(e($content['ai_summary'])) ?>

                </div>

            </div>

            <div class="grid md:grid-cols-2 gap-5">

                <div>

                    <strong>Status</strong>

                    <br>

                    <?= e($content['status']) ?>

                </div>

                <div>

                    <strong>Created</strong>

                    <br>

                    <?= CUSTOM_DATE_TIME($content['created_at']) ?>

                </div>

            </div>

            <div class="pt-5">

                <a
                    href="<?= url('pages/contents/index.php') ?>"
                    class="bg-gray-700 hover:bg-gray-800 text-white px-6 py-3 rounded">
                    Back
                </a>

                <!-- <a
                    href="<?= url('pages/contents/edit.php?uuid='.$content['uuid']) ?>"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded ml-2">
                    Edit
                </a> -->

            </div>

        </div>

    </div>

</div>

<?php require_once INCLUDE_PATH.'/footer.php'; ?>