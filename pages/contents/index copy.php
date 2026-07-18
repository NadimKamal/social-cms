<?php

require_once __DIR__ . '/../../bootstrap/app.php';
require_once INCLUDE_PATH . '/header.php';

/*
|--------------------------------------------------------------------------
| Load Categories
|--------------------------------------------------------------------------
*/

$categories = $pdo->query("
    SELECT
        id,
        title
    FROM content_categories
    WHERE is_active = 1
    ORDER BY title ASC
")->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="max-w-7xl mx-auto px-6 py-8">

    <?php include INCLUDE_PATH . '/flash.php'; ?>

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                AI Content Inbox
            </h1>
            <p class="text-gray-500 mt-2">
                Upload, organize and summarize marketing contents using Google Gemini.
            </p>
        </div>

        <a href="<?= url('pages/contents/create.php') ?>" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow">
            + Add Content
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">

            <!-- Rows -->
            <div>
                <label class="block text-sm mb-2 font-medium">Rows</label>
                <select id="numberOfRowsPerPage" class="border rounded-lg px-4 py-2 w-full">
                    <option value="6">6</option>
                    <option value="9" selected>9</option>
                    <option value="12">12</option>
                    <option value="18">18</option>
                </select>
            </div>

            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-sm mb-2 font-medium">Search</label>
                <input type="text" 
                       id="search_like" 
                       class="filterable_input border rounded-lg px-4 py-2 w-full"
                       placeholder="Title / Original Text / Content Summary">
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm mb-2 font-medium">Category</label>
                <select id="content_category_id" class="filterable_input border rounded-lg px-4 py-2 w-full">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>">
                            <?= e($category['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm mb-2 font-medium">Status</label>
                <select id="status" class="filterable_input border rounded-lg px-4 py-2 w-full">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Processing">Processing</option>
                    <option value="Completed">Completed</option>
                    <option value="Failed">Failed</option>
                </select>
            </div>

            <!-- Created From -->
            <div>
                <label class="block text-sm mb-2 font-medium">From</label>
                <input type="date" 
                       id="created_at_from" 
                       class="filterable_input border rounded-lg px-4 py-2 w-full">
            </div>

            <!-- Created To -->
            <div>
                <label class="block text-sm mb-2 font-medium">To</label>
                <input type="date" 
                       id="created_at_to" 
                       class="filterable_input border rounded-lg px-4 py-2 w-full">
            </div>
        </div>

        <div class="mt-5 flex justify-end">
            <button id="clearFilterBtn" 
                    class="bg-gray-700 hover:bg-gray-800 text-white px-6 py-2 rounded-lg">
                Clear Filters
            </button>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-xl shadow p-5">
            <div class="text-gray-500 text-sm">Total Contents</div>
            <div id="totalContents" class="text-3xl font-bold mt-2">0</div>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <div class="text-gray-500 text-sm">Completed</div>
            <div id="completedContents" class="text-3xl font-bold text-green-600 mt-2">0</div>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <div class="text-gray-500 text-sm">Pending</div>
            <div id="pendingContents" class="text-3xl font-bold text-yellow-600 mt-2">0</div>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <div class="text-gray-500 text-sm">Failed</div>
            <div id="failedContents" class="text-3xl font-bold text-red-600 mt-2">0</div>
        </div>
    </div>

    <!-- Grid -->
    <div id="contentGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6"></div>

    <!-- Pagination -->
    <div id="pagination" class="flex justify-between items-center mt-8"></div>

</div>

<script>
const Endpoint = "<?= url('api/contents/get-data.php') ?>";

const grid = qs('#contentGrid');
const rowsInput = qs('#numberOfRowsPerPage');
const filters = qsa('.filterable_input');

window.onload = () => {
    index();
};

/*
|--------------------------------------------------------------------------
| Events
|--------------------------------------------------------------------------
*/

rowsInput.addEventListener('change', index);

filters.forEach(item => {
    item.addEventListener('keyup', delay(index, 500));
    item.addEventListener('change', index);
});

qs('#clearFilterBtn').onclick = function() {
    rowsInput.value = 9;
    filters.forEach(item => {
        item.value = '';
    });
    index();
};

/*
|--------------------------------------------------------------------------
| Load Data
|--------------------------------------------------------------------------
*/

async function index(page = 1) {
    let query = '';
    query += 'rows_per_page=' + rowsInput.value;
    query += '&page=' + page;

    let filterColumns = '';
    filters.forEach(item => {
        filterColumns += item.id + '=>' + item.value + '|';
    });
    query += '&filterable_columns=' + filterColumns;

    grid.innerHTML = `
        <div class="col-span-full text-center py-16">
            Loading...
        </div>
    `;

    const response = await fetch(Endpoint + '?' + query);
    const data = await response.json();

    // Statistics
    qs('#totalContents').innerText = data.records.total;
    qs('#completedContents').innerText = data.statistics.completed;
    qs('#pendingContents').innerText = data.statistics.pending;
    qs('#failedContents').innerText = data.statistics.failed;

    // Cards
    grid.innerHTML = '';

    if (data.records.data.length === 0) {
        grid.innerHTML = `
            <div class="col-span-full">
                <div class="bg-white rounded-xl shadow p-10 text-center">
                    <h2 class="text-xl font-semibold">No content found</h2>
                    <p class="text-gray-500 mt-2">Try changing your search filters.</p>
                </div>
            </div>
        `;
    } else {
        data.records.data.forEach(row => {
            renderCard(row);
        });
    }

    renderPagination(data.records.current_page, data.records.last_page);
}

/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

function renderPagination(current, last) {
    let html = `
        <div class="text-sm text-gray-500">
            Page ${current} of ${last}
        </div>
        <div class="space-x-2">
    `;

    if (current > 1) {
        html += `
            <button onclick="index(${current-1})" 
                    class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                Previous
            </button>
        `;
    }

    if (current < last) {
        html += `
            <button onclick="index(${current+1})" 
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Next
            </button>
        `;
    }

    html += '</div>';
    qs('#pagination').innerHTML = html;
}
</script>

<script>
/*
|--------------------------------------------------------------------------
| Render Content Card
|--------------------------------------------------------------------------
*/

function renderCard(row) {
    const image = row.image_path 
        ? asset(row.image_path) 
        : asset('assets/images/default/no-image.png');

    const categoryBadge = `
        <span class="px-3 py-1 rounded-full text-xs font-medium text-white" 
              style="background:${row.category_color ?? '#2563eb'}">
            ${row.category_name ?? 'Unknown'}
        </span>
    `;

    let statusBadge = '';
    switch (row.status) {
        case 'Completed':
            statusBadge = `<span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-medium">Completed</span>`;
            break;
        case 'Processing':
            statusBadge = `<span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-medium">Processing</span>`;
            break;
        case 'Failed':
            statusBadge = `<span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-medium">Failed</span>`;
            break;
        default:
            statusBadge = `<span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-medium">Pending</span>`;
    }

    grid.innerHTML += `
    <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">
        <!-- Image -->
        <div class="h-52 bg-gray-100 overflow-hidden">
            <img src="${image}" class="w-full h-full object-cover" loading="lazy">
        </div>

        <div class="p-5">
            <!-- Badges -->
            <div class="flex justify-between items-center mb-4">
                ${categoryBadge}
                ${statusBadge}
            </div>

            <!-- Title -->
            <h2 class="text-xl font-semibold text-gray-800 line-clamp-2">
                ${escapeHtml(row.title)}
            </h2>

            <!-- Original Text -->
            <div class="mt-4">
                <p class="text-xs uppercase text-gray-400 mb-1">Original Content</p>
                <p class="text-gray-600 text-sm leading-6">
                    ${truncate(row.original_text, 150)}
                </p>
            </div>

            <!-- Content Summary -->
            <div class="mt-5">
                <p class="text-xs uppercase text-blue-600 font-semibold mb-1">Content Summary</p>
                <p class="text-gray-700 text-sm leading-6">
                    ${row.ai_summary 
                        ? truncate(row.ai_summary, 180) 
                        : '<span class="italic text-gray-400">No summary generated.</span>'}
                </p>
            </div>

            <!-- Footer -->
            <div class="border-t mt-5 pt-4">
                <div class="flex items-center justify-between">
                    <!-- Left -->
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox"
                            class="content_checkbox w-4 h-4"
                            value="${row.uuid}">
                        <span>Select</span>
                    </label>

                    <!-- Right -->
                    <div class="flex items-center gap-2">
                        <a href="<?= url('pages/contents/view.php?uuid=') ?>${row.uuid}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm">
                            View
                        </a>

                        <!--<a href="<?= url('pages/contents/edit.php?uuid=') ?>${row.uuid}"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded text-sm">
                            Edit
                        </a>-->

                        <a href="<?= url('pages/contents/delete.php?uuid=') ?>${row.uuid}"
                        onclick="return confirm('Delete this content?')"
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-sm">
                            Delete
                        </a>
                    </div>
                </div>
                <div class="mt-3 text-xs text-gray-500">
                    ${CUSTOM_DATE_TIME(row.created_at)}
                </div>
            </div>
        </div>
    </div>`;
}

/*
|--------------------------------------------------------------------------
| Helper Functions
|--------------------------------------------------------------------------
*/

function truncate(text, length = 120) {
    if (!text) return '';
    text = text.replace(/(<([^>]+)>)/ig, '');
    if (text.length <= length) {
        return escapeHtml(text);
    }
    return escapeHtml(text.substring(0, length)) + '...';
}

function escapeHtml(text) {
    if (!text) return '';
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

</script>


<!-- =======================================================
Generate Social Post Modal
======================================================= -->

<div
    id="generatePostModal"
    class="fixed inset-0 bg-black/60 hidden z-50 overflow-y-auto">

    <div class="min-h-screen flex items-center justify-center p-8">

        <div class="bg-white rounded-xl shadow-xl w-full max-w-5xl">

            <!-- Header -->

            <div class="flex justify-between items-center border-b px-6 py-4">

                <h2 class="text-2xl font-bold">

                    Generate Social Post

                </h2>

                <button
                    onclick="closeGeneratePostModal()"
                    class="text-2xl text-gray-500 hover:text-red-600">

                    &times;

                </button>

            </div>

            <!-- Body -->

            <div class="p-6 space-y-6">

                <input
                    type="hidden"
                    id="modal_content_uuid">
                <input
                    type="hidden"
                    id="modal_image_path">

                <input
                    type="hidden"
                    id="modal_video_path">

                <!-- Caption -->

                <div>

                    <label class="font-medium">

                        Caption

                    </label>

                    <textarea
                        id="modal_caption"
                        rows="6"
                        class="w-full border rounded-lg px-4 py-3 mt-2"></textarea>

                </div>

                <!-- Image Prompt -->

                <div>

                    <label class="font-medium">

                        Image Prompt

                    </label>

                    <textarea
                        id="modal_image_prompt"
                        rows="4"
                        class="w-full border rounded-lg px-4 py-3 mt-2"></textarea>

                </div>

                <!-- Image -->

                <div>

                    <label class="font-medium">

                        Generated Image

                    </label>

                    <div
                        class="border rounded-lg p-5 mt-2 bg-gray-50">

                        <img
                            id="modal_image_preview"
                            src=""
                            class="hidden rounded-lg w-full max-h-96 object-cover">

                        <div
                            id="modal_image_placeholder"
                            class="text-center text-gray-400 py-16">

                            No image generated.

                        </div>

                    </div>

                </div>

                <!-- Video Prompt -->

                <div>

                    <label class="font-medium">

                        Video Prompt

                    </label>

                    <textarea
                        id="modal_video_prompt"
                        rows="4"
                        class="w-full border rounded-lg px-4 py-3 mt-2"></textarea>

                </div>

                <!-- Video -->

                <div>

                    <label class="font-medium">

                        Generated Video

                    </label>

                    <div
                        class="border rounded-lg p-5 mt-2 bg-gray-50">

                        <video

                            id="modal_video_preview"

                            controls

                            class="hidden w-full rounded-lg">

                        </video>

                        <div
                            id="modal_video_placeholder"
                            class="text-center text-gray-400 py-16">

                            No video generated.

                        </div>

                    </div>

                </div>

                <!-- Hashtags -->

                <div>

                    <label class="font-medium">

                        Hashtags

                    </label>

                    <textarea
                        id="modal_hashtags"
                        rows="3"
                        class="w-full border rounded-lg px-4 py-3 mt-2"></textarea>

                </div>

                <!-- Keywords -->

                <div>

                    <label class="font-medium">

                        SEO Keywords

                    </label>

                    <textarea
                        id="modal_keywords"
                        rows="3"
                        class="w-full border rounded-lg px-4 py-3 mt-2"></textarea>

                </div>

            </div>

            <!-- Footer -->

            <div
                class="border-t px-6 py-4 flex justify-end gap-3">

                <button
                    id="generateAgainBtn"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-lg">
                    Generate Again
                </button>

                <button
                    id="generateImageBtn"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">
                    Generate Image
                </button>

                <button
                    id="generateVideoBtn"
                    class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg">
                    Generate Video
                </button>

                <button
                    id="savePostBtn"
                    class="bg-gray-900 hover:bg-black text-white px-5 py-2 rounded-lg" 
                    onclick="saveGeneratedPost()">
                    Save
                </button>

                <button

                    onclick="closeGeneratePostModal()"

                    class="bg-gray-300 hover:bg-gray-400 px-5 py-2 rounded-lg">

                    Close

                </button>

            </div>

        </div>

    </div>

</div>

<div

    id="postLoading"

    class="fixed inset-0 bg-black/50 hidden z-[60]">

    <div class="flex items-center justify-center h-full">

        <div class="bg-white rounded-xl px-10 py-8 shadow-xl">

            <svg

                class="animate-spin h-10 w-10 mx-auto text-purple-600"

                viewBox="0 0 24 24">

                <circle

                    cx="12"

                    cy="12"

                    r="10"

                    stroke="currentColor"

                    stroke-width="4"

                    fill="none"

                    opacity=".2">

                </circle>

                <path

                    d="M22 12A10 10 0 0012 2"

                    stroke="currentColor"

                    stroke-width="4">

                </path>

            </svg>

            <p class="mt-5">

                Gemini is generating social content...

            </p>

        </div>

    </div>

</div>

<script>
    const modal = qs('#generatePostModal');

    async function openGeneratePostModal(uuid)
    {
        const modal = qs('#generatePostModal');

        modal.classList.remove('hidden');

        qs('#modal_content_uuid').value = uuid;

        qs('#modal_caption').value = '';
        qs('#modal_image_prompt').value = '';
        qs('#modal_video_prompt').value = '';
        qs('#modal_hashtags').value = '';
        qs('#modal_keywords').value = '';
        qs('#modal_image_preview').src = '';
        qs('#modal_image_preview').classList.add('hidden');
        qs('#modal_image_placeholder').classList.remove('hidden');

        showPostLoading();

        try{

            const form = new FormData();

            form.append('uuid', uuid);

            const response = await fetch(
                APP_URL + 'api/social-posts/generate-caption.php',

                {
                    method:'POST',
                    body:form
                }

            );

            const data = await response.json();

            hidePostLoading();

            if(!data.success){

                alert(data.message);

                return;

            }

            if (data.success) {

                qs('#modal_caption').value = data.data.caption;

                qs('#modal_image_prompt').value = data.data.image_prompt;

                qs('#modal_video_prompt').value = data.data.video_prompt;

                qs('#modal_hashtags').value = data.data.hashtags;

                qs('#modal_keywords').value = data.data.keywords;

                if (data.data.image_path) {

                    qs('#modal_image_preview').src = asset(data.data.image_path);
                    qs('#modal_image_path').value = data.data.image_path;
                    // qs('#modal_video_path').value = data.data.video_path;

                    qs('#modal_image_preview').classList.remove('hidden');

                    qs('#modal_image_placeholder').classList.add('hidden');

                }
                qs('#generateAgainBtn').classList.add('hidden');
                qs('#generateImageBtn').classList.add('hidden');
                qs('#generateVideoBtn').classList.add('hidden');
                qs('#savePostBtn').classList.remove('hidden');

            }

        }

        catch(e){
            hidePostLoading();
            alert('Unable to connect.');

        }

    }

    function closeGeneratePostModal()
    {
        modal.classList.add('hidden');
    }

    function showPostLoading()
    {
        qs('#postLoading').classList.remove('hidden');
    }

    function hidePostLoading()
    {
        qs('#postLoading').classList.add('hidden');
    }

    async function saveGeneratedPost()
    {
        const form = new FormData();

        form.append(
            'content_uuid',
            qs('#modal_content_uuid').value
        );

        form.append(
            'caption',
            qs('#modal_caption').value
        );

        form.append(
            'image_prompt',
            qs('#modal_image_prompt').value
        );

        form.append(
            'image_path',
            qs('#modal_image_path').value
        );

        form.append(
            'video_prompt',
            qs('#modal_video_prompt').value
        );

        form.append(
            'video_path',
            qs('#modal_video_path').value
        );

        form.append(
            'hashtags',
            qs('#modal_hashtags').value
        );

        form.append(
            'keywords',
            qs('#modal_keywords').value
        );

        try
        {
            const response = await fetch(
                APP_URL + 'pages/social-posts/save.php',
                {
                    method:'POST',
                    body:form
                }
            );

            const data = await response.json();

            if(!data.success)
            {
                alert(data.message);
                return;
            }

            alert('Social post saved successfully.');

            closeGeneratePostModal();

            index();

        }
        catch(e)
        {
            alert('Unable to save social post.');
        }
    }

    async function openGeneratedPost(uuid)
    {
        modal.classList.remove('hidden');

        showPostLoading();

        try
        {
            const response = await fetch(
                APP_URL + 'api/social-posts/view.php?uuid=' + uuid
            );

            const data = await response.json();

            hidePostLoading();

            if(!data.success){
                alert(data.message);
                return;
            }

            qs('#modal_content_uuid').value = uuid;

            qs('#modal_caption').value = data.data.caption;

            qs('#modal_image_prompt').value = data.data.image_prompt;

            qs('#modal_video_prompt').value = data.data.video_prompt;

            qs('#modal_hashtags').value = data.data.hashtags;

            qs('#modal_keywords').value = data.data.keywords;

            qs('#modal_image_path').value = data.data.image_path ?? '';

            qs('#modal_video_path').value = data.data.video_path ?? '';

            if(data.data.image_path){

                qs('#modal_image_preview').src = asset(data.data.image_path);

                qs('#modal_image_preview').classList.remove('hidden');

                qs('#modal_image_placeholder').classList.add('hidden');

            }
            else{

                qs('#modal_image_preview').classList.add('hidden');

                qs('#modal_image_placeholder').classList.remove('hidden');

            }

            if(data.data.video_path){

                qs('#modal_video_preview').src = asset(data.data.video_path);

                qs('#modal_video_preview').classList.remove('hidden');

                qs('#modal_video_placeholder').classList.add('hidden');

            }
            else{

                qs('#modal_video_preview').classList.add('hidden');

                qs('#modal_video_placeholder').classList.remove('hidden');

            }
            qs('#generateAgainBtn').classList.add('hidden');
            qs('#generateImageBtn').classList.add('hidden');
            qs('#generateVideoBtn').classList.add('hidden');
            qs('#savePostBtn').classList.add('hidden');

        }
        catch(e){

            hidePostLoading();

            alert('Unable to load social post.');

        }
    }
</script>
<?php
require_once INCLUDE_PATH . '/footer.php';
?>