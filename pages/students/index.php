<?php
$pageTitle = 'Students';
require_once __DIR__ . '/../../bootstrap/app.php';
require_once INCLUDE_PATH . '/header.php';

?>

<div class="max-w-7xl mx-auto px-6 py-8">

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800"><?= e($pageTitle ?? 'Social CMS') ?></h1>
            <p class="text-gray-500 mt-1">Manage all students from here.</p>
        </div>

        <a href="<?= url('pages/students/create.php') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow">
            + Add
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl shadow p-5 mb-6">
        <div class="flex items-center gap-3">
            <select id="numberOfRowsPerPage" class="border rounded-lg px-3 py-2">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="15" selected>15</option>
                <option value="25">25</option>
            </select>

            <input type="text" id="title_like" class="filterable_input flex-1 border rounded-lg px-4 py-2" placeholder="Search title...">

            <button id="clearFilterBtn" class="bg-gray-700 text-white px-5 py-2 rounded-lg">Clear</button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 w-8 text-left">#</th>
                    <th class="px-6 py-3 w-20 text-center">Picture</th>
                    <th class="px-6 py-3 w-32 text-left">Title</th>
                    <th class="px-6 py-3 w-20 text-left">Created At</th>
                    <th class="px-6 py-3 w-20 text-center">Action</th>
                </tr>
            </thead>
            <tbody id="tbody" class="divide-y divide-gray-200">
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div id="pagination" class="flex justify-between items-center mt-6"></div>

</div>

<script>
const Endpoint = "<?= url('api/students/get-data.php') ?>";
const tbody = document.querySelector('#tbody');
const rowsInput = document.querySelector('#numberOfRowsPerPage');
const filters = document.querySelectorAll('.filterable_input');

window.onload = () => index();

rowsInput.addEventListener('change', index);

filters.forEach(input => {
    input.addEventListener('keyup', delay(index, 500));
});

document.querySelector('#clearFilterBtn').onclick = function() {
    filters.forEach(item => item.value = '');
    index();
};

async function index(page = 1) {
    let query = 'rows_per_page=' + rowsInput.value + '&page=' + page;

    let filterColumns = '';
    filters.forEach(item => {
        filterColumns += item.id + '=>' + item.value + '|';
    });

    query += '&filterable_columns=' + filterColumns;

    tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Loading...</td></tr>';

    const response = await fetch(Endpoint + '?' + query);
    const data = await response.json();

    let sl = (data.sl || 0) + 1;
    tbody.innerHTML = '';

    if (data.records.data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">No students found.</td></tr>`;
        return;
    }

    data.records.data.forEach(row => {
        const picture = row.picture ? asset(row.picture) : asset('assets/images/default/dp.png');
        tbody.innerHTML += `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-gray-500">${sl++}</td>
                <td class="px-6 py-4 text-center">
                    <img src="${picture}" class="w-14 h-14 rounded object-cover border mx-auto">
                </td>
                <td class="px-6 py-4 font-medium">${row.title}</td>
                <td class="px-6 py-4 text-gray-600">${CUSTOM_DATE_TIME(row.created_at)}</td>
                <td class="px-6 py-4 text-center">
                    <a href="<?= url('pages/students/edit.php?id=') ?>${row.id}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded text-sm">Edit</a>
                    <a href="<?= url('pages/students/delete.php?id=') ?>${row.id}" onclick="return confirm('Delete this student?')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm ml-2">Delete</a>
                </td>
            </tr>
        `;
    });
}

function delay(fn, ms) {
    let timer = 0;
    return function() {
        clearTimeout(timer);
        timer = setTimeout(fn, ms);
    };
}
</script>

<?php
require_once INCLUDE_PATH . '/footer.php';
?>