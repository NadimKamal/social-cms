<?php

require_once __DIR__ . '/../../bootstrap/app.php';

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {

    setFlash('error', 'Invalid Category.');

    redirect('pages/content-categories/index.php');

}

/*
|--------------------------------------------------------------------------
| Get Category
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM content_categories
    WHERE id=?
    LIMIT 1
");

$stmt->execute([$id]);

$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {

    setFlash('error', 'Category not found.');

    redirect('pages/content-categories/index.php');

}

/*
|--------------------------------------------------------------------------
| Old Input Support
|--------------------------------------------------------------------------
*/

$title = old('title', $category['title']);

$description = old('description', $category['description']);

$color = old('color', $category['color']);

$icon = old('icon', $category['icon']);

$isActive = old('is_active', $category['is_active']);

require_once INCLUDE_PATH . '/header.php';

?>

<div class="max-w-5xl mx-auto px-6 py-8">

    <!-- Header -->

    <div class="flex items-center justify-between mb-8">

        <div>

            <h1 class="text-3xl font-bold text-gray-800">

                Edit Content Category

            </h1>

            <p class="text-gray-500 mt-2">

                Update content category information.

            </p>

        </div>

        <a
            href="<?= url('pages/content-categories/index.php') ?>"
            class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2.5 rounded-lg">

            ← Back

        </a>

    </div>

    <?php include INCLUDE_PATH . '/flash.php'; ?>
        <!-- Form -->
    <form
        action="<?= url('pages/content-categories/update.php') ?>"
        method="POST"
        class="bg-white rounded-xl shadow p-8 space-y-6">

        <input
            type="hidden"
            name="id"
            value="<?= $category['id'] ?>">

        <!-- Title -->

        <div>

            <label class="block font-medium mb-2">

                Category Title

                <span class="text-red-500">*</span>

            </label>

            <input
                type="text"
                name="title"
                value="<?= e($title) ?>"
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none"
                placeholder="Example : Trending"
                required>

        </div>

        <!-- Description -->

        <div>

            <label class="block font-medium mb-2">

                Description

            </label>

            <textarea
                name="description"
                rows="4"
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none"
                placeholder="Short description..."><?= e($description) ?></textarea>

        </div>

        <!-- Color -->

        <div>

            <label class="block font-medium mb-2">

                Category Color

            </label>

            <div class="flex items-center gap-4">

                <input
                    type="color"
                    id="colorPicker"
                    name="color"
                    value="<?= e($color) ?>"
                    class="w-16 h-12 rounded border cursor-pointer">

                <input
                    type="text"
                    id="colorText"
                    value="<?= e($color) ?>"
                    class="border rounded-lg px-4 py-3 w-40">

            </div>

        </div>

        <!-- Preview -->

        <div>

            <label class="block font-medium mb-3">

                Color Preview

            </label>

            <div
                id="previewBox"
                class="rounded-xl shadow p-5 text-white font-semibold"
                style="background:<?= e($color) ?>">

                <?= e($title ?: 'Sample Category') ?>

            </div>

        </div>

        <!-- Icon -->

        <div>

            <label class="block font-medium mb-2">

                Icon

            </label>

            <input
                type="text"
                name="icon"
                value="<?= e($icon) ?>"
                class="w-full border rounded-lg px-4 py-3"
                placeholder="fire | tag | newspaper | book-open">

            <p class="text-sm text-gray-400 mt-2">

                Store only the icon name.

            </p>

        </div>

        <!-- Status -->

        <div>

            <label class="block font-medium mb-2">

                Status

            </label>

            <select
                name="is_active"
                class="w-full border rounded-lg px-4 py-3">

                <option
                    value="1"
                    <?= $isActive==1 ? 'selected' : '' ?>>

                    Active

                </option>

                <option
                    value="0"
                    <?= $isActive==0 ? 'selected' : '' ?>>

                    Inactive

                </option>

            </select>

        </div>

        <!-- Information -->

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>

                <label class="block font-medium mb-2">

                    Created At

                </label>

                <input
                    type="text"
                    readonly
                    value="<?= CUSTOM_DATE_TIME($category['created_at']) ?>"
                    class="w-full bg-gray-100 border rounded-lg px-4 py-3">

            </div>

            <div>

                <label class="block font-medium mb-2">

                    Last Updated

                </label>

                <input
                    type="text"
                    readonly
                    value="<?= CUSTOM_DATE_TIME($category['updated_at']) ?>"
                    class="w-full bg-gray-100 border rounded-lg px-4 py-3">

            </div>

        </div>

        <!-- Buttons -->

        <div class="border-t pt-6 flex justify-end gap-3">

            <a
                href="<?= url('pages/content-categories/index.php') ?>"
                class="bg-gray-200 hover:bg-gray-300 px-6 py-3 rounded-lg">

                Cancel

            </a>

            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">

                Update Category

            </button>

        </div>

    </form>

</div>

<script>

const titleInput = document.querySelector('input[name="title"]');

const colorPicker = document.querySelector('#colorPicker');

const colorText = document.querySelector('#colorText');

const previewBox = document.querySelector('#previewBox');

/*
|--------------------------------------------------------------------------
| Update Preview
|--------------------------------------------------------------------------
*/

function updatePreview() {

    previewBox.style.background = colorPicker.value;

    previewBox.innerHTML = titleInput.value.trim() !== ''
        ? titleInput.value
        : 'Sample Category';

}

/*
|--------------------------------------------------------------------------
| Color Picker -> Text
|--------------------------------------------------------------------------
*/

colorPicker.addEventListener('input', function () {

    colorText.value = this.value;

    updatePreview();

});

/*
|--------------------------------------------------------------------------
| Text -> Color Picker
|--------------------------------------------------------------------------
*/

colorText.addEventListener('keyup', function () {

    if(/^#[0-9A-Fa-f]{6}$/.test(this.value)){

        colorPicker.value = this.value;

        updatePreview();

    }

});

/*
|--------------------------------------------------------------------------
| Title Preview
|--------------------------------------------------------------------------
*/

titleInput.addEventListener('keyup', updatePreview);

/*
|--------------------------------------------------------------------------
| Initialize
|--------------------------------------------------------------------------
*/

updatePreview();

</script>

<?php

require_once INCLUDE_PATH . '/footer.php';