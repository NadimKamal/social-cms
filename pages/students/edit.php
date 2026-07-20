<?php

require_once __DIR__ . '/../../bootstrap/app.php';
require_once INCLUDE_PATH . '/header.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('pages/students/index.php');
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    redirect('pages/students/index.php');
}

?>

<div class="max-w-5xl mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Edit Student</h1>
            <p class="text-gray-500 mt-1">Update student information.</p>
        </div>

        <a href="<?= url('pages/students/index.php') ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg">
            ← Back
        </a>
    </div>

    <form action="<?= url('pages/students/update.php') ?>" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow">

        <input type="hidden" name="id" value="<?= $student['id'] ?>">
        <input type="hidden" name="old_picture" value="<?= e($student['picture']) ?>">

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Picture -->
                <div>
                    <label class="block mb-2 font-semibold">Picture</label>
                    <div class="border rounded-lg p-5">
                        <img id="previewImage" 
                             src="<?= !empty($student['picture']) ? asset($student['picture']) : asset('assets/images/default/dp.png') ?>" 
                             class="w-48 h-48 object-cover rounded-lg border mx-auto">

                        <input type="file" name="picture" id="picture" accept="image/*" 
                               class="mt-5 w-full border rounded-lg px-3 py-2">

                        <p class="text-sm text-gray-500 mt-2">Leave empty to keep the current image.</p>
                    </div>
                </div>

                <!-- Details -->
                <div>
                    <div class="mb-5">
                        <label class="block mb-2 font-semibold">Student Title <span class="text-red-600">*</span></label>
                        <input type="text" name="title" value="<?= e($student['title']) ?>" 
                               class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                    </div>

                    <div class="mb-5">
                        <label class="block mb-2 font-semibold">Created At</label>
                        <input type="text" class="w-full border rounded-lg px-4 py-3 bg-gray-100" 
                               value="<?= CUSTOM_DATE_TIME($student['created_at']) ?>" readonly>
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
const picture = document.getElementById('picture');
const preview = document.getElementById('previewImage');

picture.addEventListener('change', function () {
    if (!this.files.length) return;
    preview.src = URL.createObjectURL(this.files[0]);
});
</script>

<?php
require_once INCLUDE_PATH . '/footer.php';
?>