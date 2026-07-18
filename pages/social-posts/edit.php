<?php

require_once '../../bootstrap/app.php';

$uuid = $_GET['uuid'] ?? '';

$post = findByUuidOrFail(
    $pdo,
    'social_posts',
    $uuid
);

if (is_post()) {

    update(
        $pdo,
        'social_posts',
        [
            'caption'  => trim($_POST['caption']),
            'hashtags' => trim($_POST['hashtags']),
            'keywords' => trim($_POST['keywords']),
            'status'   => trim($_POST['status']),
        ],
        [
            'uuid' => $uuid
        ]
    );

    $_SESSION['flash'] = [

        'type'    => 'success',
        'message' => 'Social post updated successfully.'

    ];

    redirect('pages/social-posts/index.php');
}

require_once INCLUDE_PATH . '/header.php';

?>

<div class="max-w-5xl mx-auto py-8">

    <div class="bg-white rounded-xl shadow">

        <div class="border-b px-6 py-4">
            <h1 class="text-2xl font-bold">
                Edit Social Post
            </h1>
        </div>

        <form
            action="<?= url('pages/social-posts/update.php') ?>"
            method="POST"
            enctype="multipart/form-data">

            <div class="p-6 space-y-6">
                <input
                    type="hidden"
                    name="uuid"
                    value="<?= e($post['uuid']) ?>">
                <!-- Image -->

                <div>

                    <label class="block font-medium mb-2">
                        Current Image
                    </label>

                    <img
                        src="<?= asset($post['image_path']) ?>"
                        class="rounded-lg border max-h-96 w-full object-cover mb-4">

                    <label class="block font-medium mb-2">
                        Replace Image
                    </label>

                    <input
                        type="file"
                        name="image"
                        accept="image/*"
                        class="w-full border rounded-lg px-4 py-3">

                    <p class="text-sm text-gray-500 mt-2">
                        Leave empty to keep the current image.
                    </p>

                </div>

                <!-- Caption -->

                <div>

                    <label class="block font-medium mb-2">
                        Caption
                    </label>

                    <textarea
                        name="caption"
                        rows="8"
                        class="w-full border rounded-lg px-4 py-3"
                        required><?= e($post['caption']) ?></textarea>

                </div>

                <!-- Hashtags -->

                <div>

                    <label class="block font-medium mb-2">
                        Hashtags
                    </label>

                    <textarea
                        name="hashtags"
                        rows="3"
                        class="w-full border rounded-lg px-4 py-3"><?= e($post['hashtags']) ?></textarea>

                </div>

                <!-- Keywords -->

                <div>

                    <label class="block font-medium mb-2">
                        SEO Keywords
                    </label>

                    <textarea
                        name="keywords"
                        rows="3"
                        class="w-full border rounded-lg px-4 py-3"><?= e($post['keywords']) ?></textarea>

                </div>

                <!-- Status -->

                <div>

                    <label class="block font-medium mb-2">
                        Status
                    </label>

                    <select
                        name="status"
                        class="w-full border rounded-lg px-4 py-3">

                        <option value="Draft"
                            <?= $post['status']=='Draft'?'selected':'' ?>>
                            Draft
                        </option>

                        <option value="Ready"
                            <?= $post['status']=='Ready'?'selected':'' ?>>
                            Ready
                        </option>

                        <option value="Published"
                            <?= $post['status']=='Published'?'selected':'' ?>>
                            Published
                        </option>

                    </select>

                </div>

            </div>

            <div class="border-t px-6 py-4 flex justify-end gap-3">

                <a
                    href="<?= url('pages/social-posts/index.php') ?>"
                    class="px-5 py-2 rounded bg-gray-300 hover:bg-gray-400">

                    Cancel

                </a>

                <button
                    class="px-6 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">

                    Update Post

                </button>

            </div>

        </form>

    </div>

</div>

<?php require_once INCLUDE_PATH . '/footer.php'; ?>