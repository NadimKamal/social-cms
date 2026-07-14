<?php
require_once __DIR__ . '/../../bootstrap/app.php';
require_once INCLUDE_PATH . '/header.php';
?>

<div class="max-w-5xl mx-auto px-6 py-8">

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">

        <div>

            <h1 class="text-3xl font-bold text-gray-800">
                Create Content Category
            </h1>

            <p class="text-gray-500 mt-2">
                Create a new content category for organizing AI contents.
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
        action="<?= url('pages/content-categories/save.php') ?>"
        method="POST"
        class="bg-white rounded-xl shadow p-8 space-y-6">

        <!-- Title -->

        <div>

            <label class="block font-medium mb-2">

                Category Title
                <span class="text-red-500">*</span>

            </label>

            <input
                type="text"
                name="title"
                value="<?= e(old('title')) ?>"
                class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none"
                placeholder="Example: Trending"
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
                placeholder="Short description about this category"><?= e(old('description')) ?></textarea>

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
                    value="<?= old('color', '#2563eb') ?>"
                    class="w-16 h-12 border rounded cursor-pointer">

                <input
                    type="text"
                    id="colorText"
                    name="color_text"
                    value="<?= old('color', '#2563eb') ?>"
                    class="border rounded-lg px-4 py-3 w-40">

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
                value="<?= e(old('icon')) ?>"
                class="w-full border rounded-lg px-4 py-3"
                placeholder="Example: fire, tag, book-open">

            <p class="text-gray-400 text-sm mt-2">

                You may use Heroicons/Lucide icon names.

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
                    <?= old('is_active','1')=='1' ? 'selected':'' ?>>

                    Active

                </option>

                <option
                    value="0"
                    <?= old('is_active')=='0' ? 'selected':'' ?>>

                    Inactive

                </option>

            </select>

        </div>

        <!-- Preview -->

        <div>

            <label class="block font-medium mb-3">

                Preview

            </label>

            <div
                id="previewBox"
                class="rounded-xl p-5 text-white font-semibold shadow"
                style="background:#2563eb">

                Sample Category

            </div>

        </div>

        <!-- Buttons -->

        <div class="flex justify-end gap-3 pt-6 border-t">

            <a
                href="<?= url('pages/content-categories/index.php') ?>"
                class="bg-gray-200 hover:bg-gray-300 px-6 py-3 rounded-lg">

                Cancel

            </a>

            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">

                Save Category

            </button>

        </div>

    </form>

</div>

<script>

const picker = document.querySelector('#colorPicker');

const text = document.querySelector('#colorText');

const preview = document.querySelector('#previewBox');

picker.addEventListener('input', function(){

    text.value = this.value;

    preview.style.background = this.value;

});

text.addEventListener('keyup', function(){

    picker.value = this.value;

    preview.style.background = this.value;

});

</script>

<?php
clearOld();
require_once INCLUDE_PATH . '/footer.php';

?>