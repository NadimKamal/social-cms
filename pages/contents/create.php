<?php

require_once '../../bootstrap/app.php';
require_once INCLUDE_PATH.'/header.php';

$categories = $pdo->query("
SELECT *
FROM content_categories
WHERE is_active=1
ORDER BY title
")->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="max-w-5xl mx-auto py-8">

    <?php include INCLUDE_PATH.'/flash.php'; ?>

    <div class="bg-white rounded-xl shadow">

        <div class="border-b px-6 py-4">

            <h2 class="text-2xl font-bold">

                Create Content

            </h2>

            <p class="text-gray-500 mt-1">

                Upload marketing content and generate AI summary.

            </p>

        </div>

        <form
            action="<?= url('pages/contents/save.php') ?>"
            method="POST"
            enctype="multipart/form-data"
            class="p-6 space-y-6">

            <!-- Category -->

            <div>

                <label class="font-medium">

                    Category *

                </label>

                <select
                    name="content_category_id"
                    required
                    class="w-full border rounded-lg px-4 py-3 mt-2">

                    <option value="">

                        Select Category

                    </option>

                    <?php foreach($categories as $category): ?>

                        <option
                            value="<?= $category['id'] ?>">

                            <?= e($category['title']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <!-- Title -->

            <div>

                <label class="font-medium">

                    Title *

                </label>

                <input
                    type="text"
                    name="title"
                    required
                    class="w-full border rounded-lg px-4 py-3 mt-2">

            </div>

            <!-- Original Text -->

            <div>

                <label class="font-medium">

                    Original Text *

                </label>

                <textarea
                    name="original_text"
                    rows="8"
                    required
                    class="w-full border rounded-lg px-4 py-3 mt-2"></textarea>

            </div>

            <!-- Image -->

            <div>

                <label class="font-medium">

                    Image

                </label>

                <input
                    type="file"
                    name="image"
                    accept="image/*"
                    class="w-full mt-2">

            </div>

            <!-- Video URL -->

            <div>

                <label class="font-medium">

                    Video URL

                </label>

                <input
                    id="video_url"
                    type="url"
                    name="video_url"
                    class="w-full border rounded-lg px-4 py-3 mt-2">

            </div>

            <div class="text-center text-gray-500">

                OR

            </div>

            <!-- Video Upload -->

            <div>

                <label class="font-medium">

                    Upload Video

                </label>

                <input
                    id="video_file"
                    type="file"
                    name="video"
                    accept="video/*"
                    class="w-full mt-2">

            </div>

            <!-- Generate -->

            <div>

                <button
                    type="button"
                    id="summarizeBtn"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg">
                    Generate AI Summary
                </button>

            </div>

            <!-- Summary -->

            <div>

                <label class="font-medium">

                    Video Summary

                </label>

                <textarea
                    id="ai_summary"
                    name="ai_summary"
                    rows="10"
                    readonly
                    class="w-full border rounded-lg px-4 py-3 mt-2 bg-gray-50"></textarea>

            </div>

            <div class="pt-4">

                <button
                    id="saveBtn"
                    disabled
                    class="bg-blue-600 text-white px-8 py-3 rounded-lg disabled:opacity-50">

                    Save Content

                </button>

            </div>

        </form>

    </div>

</div>

<script>

const videoUrl = qs('#video_url');
const videoFile = qs('#video_file');
const summarizeBtn = qs('#summarizeBtn');
const saveBtn = qs('#saveBtn');
const summary = qs('#ai_summary');

/*
|--------------------------------------------------------------------------
| Toggle URL/File
|--------------------------------------------------------------------------
*/

videoUrl.addEventListener('input',()=>{
    if(videoUrl.value.trim()!=''){
        videoFile.value='';
        videoFile.disabled=true;
    }else{
        videoFile.disabled=false;
    }
});

videoFile.addEventListener('change',()=>{
    if(videoFile.files.length){
        videoUrl.value='';
        videoUrl.disabled=true;
    }else{
        videoUrl.disabled=false;
    }
});

/*
|--------------------------------------------------------------------------
| Summarize
|--------------------------------------------------------------------------
*/

summarizeBtn.onclick = async () => {

    const originalText = qs('[name="original_text"]').value.trim();
    const videoUrlText = videoUrl.value.trim();
    const hasVideoFile = videoFile.files.length > 0;

    if ( originalText === '' && videoUrlText === '' && !hasVideoFile ) {
        alert('Please enter text, a YouTube URL, or upload a video.');
        return;

    }

    summarizeBtn.disabled = true;
    summarizeBtn.innerHTML = 'Generating...';
    summary.value = '';

    const form = new FormData();

    form.append('original_text', originalText);
    form.append('video_url', videoUrlText);

    if (hasVideoFile) {
        form.append('video', videoFile.files[0]);
    }
    try {

        const response = await fetch(
            APP_URL + 'api/contents/summarize.php',
            {
                method: 'POST',
                body: form
            }
        );
        const data = await response.json();
        if (data.success) {
            summary.value = data.data.summary;
            saveBtn.disabled = false;
        } else {
            alert(data.message);
            saveBtn.disabled = true;
        }

    } catch (e) {
        alert('Unable to connect to AI server.');
    }

    summarizeBtn.disabled = false;
    summarizeBtn.innerHTML = '✨ Generate AI Summary';

};

</script>

<?php require_once INCLUDE_PATH.'/footer.php'; ?>