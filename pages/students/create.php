<?php

require_once __DIR__ . '/../../bootstrap/app.php';
require_once INCLUDE_PATH . '/header.php';

?>

<div class="max-w-5xl mx-auto px-6 py-8">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Create Student</h1>
            <p class="text-gray-500 mt-1">Add a new student.</p>
        </div>

        <a href="<?= url('pages/students/index.php') ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg">
            ← Back
        </a>
    </div>

    <form action="<?= url('pages/students/save.php') ?>" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow">

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Picture -->
                <div>
                    <label class="block mb-2 font-semibold">Picture</label>
                    <div class="border rounded-lg p-5">
                        <img id="previewImage" src="<?= asset('assets/images/default/dp.png') ?>" class="w-48 h-48 object-cover rounded-lg border mx-auto">
                        <input type="file" name="picture" id="picture" accept="image/*" class="mt-5 w-full border rounded-lg px-3 py-2">
                        <p class="text-gray-500 text-sm mt-2">JPG, PNG, JPEG</p>
                    </div>
                </div>

                <!-- Information -->
                <div>
                    <div class="mb-5">
                        <label class="block mb-2 font-semibold">Student Title <span class="text-red-600">*</span></label>
                        <input type="text" name="title" value="<?= old('title') ?>" 
                               class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:outline-none" 
                               placeholder="Enter student title" required>
                    </div>
                </div>

            </div>
        </div>

        <div class="border-t bg-gray-50 px-6 py-4 flex justify-end gap-3">
            <a href="<?= url('pages/students/index.php') ?>" class="px-5 py-2 rounded-lg border">Cancel</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">Submit</button>
        </div>

    </form>

</div>

<script>
const picture = document.querySelector('#picture');
const preview = document.querySelector('#previewImage');

picture.addEventListener('change', function() {
    if (!this.files.length) return;
    const file = this.files[0];
    preview.src = URL.createObjectURL(file);
});
</script>

<?php
require_once '../../includes/footer.php';
?>