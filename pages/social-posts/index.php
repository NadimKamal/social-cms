<?php

require_once __DIR__ . '/../../bootstrap/app.php';
require_once INCLUDE_PATH . '/header.php';

?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <?php include INCLUDE_PATH . '/flash.php'; ?>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                Social Posts
            </h1>
            <p class="text-gray-500 mt-2">
                Manage AI generated social media posts.
            </p>
        </div>
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
                       placeholder="Caption / Hashtags / Keywords">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm mb-2 font-medium">Status</label>
                <select id="status" class="filterable_input border rounded-lg px-4 py-2 w-full">
                    <option value="">All Status</option>
                    <option value="Draft">Draft</option>
                    <option value="Ready">Ready</option>
                    <option value="Published">Published</option>
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
            <div class="text-gray-500 text-sm">Total Posts</div>
            <div id="totalContents" class="text-3xl font-bold mt-2">0</div>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <div class="text-gray-500 text-sm">Draft</div>
            <div id="draftPosts" class="text-3xl font-bold text-green-600 mt-2">0</div>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <div class="text-gray-500 text-sm">Ready</div>
            <div id="readyPosts" class="text-3xl font-bold text-yellow-600 mt-2">0</div>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <div class="text-gray-500 text-sm">Published</div>
            <div id="publishedPosts" class="text-3xl font-bold text-red-600 mt-2">0</div>
        </div>
    </div>

    <!-- Grid -->
    <div id="contentGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6"></div>

    <!-- Pagination -->
    <div id="pagination" class="flex justify-between items-center mt-8"></div>

    <!-- View Modal -->
    <div id="viewModal"
        class="fixed inset-0 bg-black/60 hidden z-50 overflow-y-auto">

        <div class="min-h-screen flex items-center justify-center p-8">

            <div class="bg-white rounded-xl shadow-xl w-full max-w-5xl">

                <div class="flex justify-between items-center border-b p-6">

                    <h2 class="text-2xl font-bold">
                        Social Post
                    </h2>

                    <button onclick="closeViewModal()"
                            class="text-3xl text-gray-500 hover:text-red-600">
                        &times;
                    </button>

                </div>

                <div class="p-6 space-y-6">

                    <img id="viewImage"
                        class="w-full max-h-[450px] object-cover rounded-lg">

                    <div>

                        <label class="font-semibold">
                            Caption
                        </label>

                        <div id="viewCaption"
                            class="mt-2 whitespace-pre-wrap text-gray-700"></div>

                    </div>

                    <div>

                        <label class="font-semibold">
                            Hashtags
                        </label>

                        <div id="viewHashtags"
                            class="flex flex-wrap gap-2 mt-3"></div>

                    </div>

                    <div>

                        <label class="font-semibold">
                            SEO Keywords
                        </label>

                        <div id="viewKeywords"
                            class="flex flex-wrap gap-2 mt-3"></div>

                    </div>

                    <div>

                        <label class="font-semibold">
                            Status
                        </label>

                        <div id="viewStatus"
                            class="mt-2"></div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script>
const Endpoint = "<?= url('api/social-posts/get-data.php') ?>";
const grid = qs('#contentGrid');
const rowsInput = qs('#numberOfRowsPerPage');
const filters = qsa('.filterable_input');

window.onload = () => {
    index();
};

/* Events */
rowsInput.addEventListener('change', index);

filters.forEach(item => {
    item.addEventListener('keyup', delay(index, 500));
    item.addEventListener('change', index);
});

qs('#clearFilterBtn').onclick = function() {
    rowsInput.value = 9;
    filters.forEach(item => item.value = '');
    index();
};

/* Load Data */
async function index(page = 1) {
    let query = `rows_per_page=${rowsInput.value}&page=${page}`;

    let filterColumns = '';
    filters.forEach(item => {
        filterColumns += item.id + '=>' + item.value + '|';
    });
    query += '&filterable_columns=' + filterColumns;

    grid.innerHTML = `<div class="col-span-full text-center py-16">Loading...</div>`;

    const response = await fetch(Endpoint + '?' + query);
    const data = await response.json();
    console.log(data);
    
    // Update Statistics
    qs('#totalContents').innerText = data.records.total;
    qs('#draftPosts').innerText = data.statistics.draft;
    qs('#readyPosts').innerText = data.statistics.ready;
    qs('#publishedPosts').innerText = data.statistics.published;

    // Render Cards
    grid.innerHTML = '';

    if (data.records.data.length === 0) {
        grid.innerHTML = `
            <div class="col-span-full">
                <div class="bg-white rounded-xl shadow p-10 text-center">
                    <h2 class="text-xl font-semibold">No content found</h2>
                    <p class="text-gray-500 mt-2">Try changing your search filters.</p>
                </div>
            </div>`;
    } else {
        data.records.data.forEach(row => renderCard(row));
    }

    renderPagination(data.records.current_page, data.records.last_page);
}

/* Pagination */
function renderPagination(current, last) {
    let html = `<div class="text-sm text-gray-500">Page ${current} of ${last}</div><div class="space-x-2">`;

    if (current > 1) {
        html += `<button onclick="index(${current-1})" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Previous</button>`;
    }
    if (current < last) {
        html += `<button onclick="index(${current+1})" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Next</button>`;
    }

    html += '</div>';
    qs('#pagination').innerHTML = html;
}

/* Render Content Card */
function renderCard(row)
{
    const image = row.image_path
        ? asset(row.image_path)
        : asset('assets/images/default/no-image.png');

    let statusBadge = '';

    switch (row.status)
    {
        case 'Published':
            statusBadge = '<span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs">Published</span>';
            break;

        case 'Ready':
            statusBadge = '<span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs">Ready</span>';
            break;

        default:
            statusBadge = '<span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs">Draft</span>';
    }

    grid.innerHTML += `
    <div class="bg-white rounded-xl shadow hover:shadow-lg overflow-hidden">

        <div class="h-52 overflow-hidden bg-gray-100">
            <img src="${image}" class="w-full h-full object-cover">
        </div>

        <div class="p-5">

            <div class="flex justify-between items-center">
                ${statusBadge}
            </div>

            <div class="mt-4">
                <p class="text-xs uppercase text-gray-500 mb-1">Caption</p>
                <p class="text-sm text-gray-700">
                    ${truncate(row.caption, 180)}
                </p>
            </div>

            <div class="border-t mt-5 pt-4 flex justify-between items-center">

                <span class="text-xs text-gray-500">
                    ${CUSTOM_DATE_TIME(row.created_at)}
                </span>

                <div class="flex gap-2">
                    <button
                        onclick="viewPost('${row.uuid}')"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm">
                        View
                    </button>

                    <a href="<?= url('pages/social-posts/edit.php?uuid=') ?>${row.uuid}"
                       class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded text-sm">
                        Edit
                    </a>

                    <a href="<?= url('pages/social-posts/delete.php?uuid=') ?>${row.uuid}"
                       onclick="return confirm('Delete this post?')"
                       class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-sm">
                        Delete
                    </a>
                </div>

            </div>

        </div>

    </div>`;
}

/* Helper Functions */
function truncate(text, length = 120) {
    if (!text) return '';
    text = text.replace(/(<([^>]+)>)/ig, '');
    if (text.length <= length) return escapeHtml(text);
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

async function viewPost(uuid)
{
    try
    {
        const response = await fetch(
            APP_URL + 'api/social-posts/view.php?uuid=' + uuid
        );

        const data = await response.json();

        if (!data.success)
        {
            alert(data.message);
            return;
        }

        const post = data.data;

        qs('#viewImage').src = asset(post.image_path);

        qs('#viewCaption').textContent = post.caption;

        qs('#viewStatus').innerHTML =
            `<span class="px-3 py-1 rounded bg-blue-100 text-blue-700">
                ${post.status}
            </span>`;

        renderTags('#viewHashtags', post.hashtags);

        renderTags('#viewKeywords', post.keywords);

        qs('#viewModal').classList.remove('hidden');
    }
    catch(e)
    {
        console.error(e);
        alert('Unable to load post.');
    }
}

function renderTags(selector, items)
{
    const container = qs(selector);

    container.innerHTML = '';

    items.forEach(item => {

        container.innerHTML += `
            <span class="px-3 py-1 rounded-full
                         bg-blue-100 text-blue-700
                         text-sm">
                ${escapeHtml(item)}
            </span>
        `;

    });
}

function closeViewModal()
{
    qs('#viewModal').classList.add('hidden');
}
</script>

<?php
require_once INCLUDE_PATH . '/footer.php';
?>