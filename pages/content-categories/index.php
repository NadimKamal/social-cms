<?php

require_once __DIR__ . '/../../bootstrap/app.php';
require_once INCLUDE_PATH . '/header.php';

?>

<div class="max-w-7xl mx-auto px-6 py-8">
    <?php include INCLUDE_PATH . '/flash.php'; ?>
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                Content Categories
            </h1>

            <p class="text-gray-500 mt-1">
                Manage all content categories from here.
            </p>

        </div>

        <a href="<?= url('pages/content-categories/create.php') ?>"
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow">

            + Add Category

        </a>

    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow p-5 mb-6">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <select
                id="numberOfRowsPerPage"
                class="border rounded-lg px-3 py-2">

                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="25">25</option>

            </select>

            <input
                type="text"
                id="title_like"
                class="filterable_input border rounded-lg px-4 py-2"
                placeholder="Search title">

            <select id="is_active" class="filterable_input border rounded-lg px-4 py-2">
                <option value="">All Status</option>
                <option value="1">
                    Active
                </option>
                <option value="0">
                    Inactive
                </option>
            </select>

            <button
                id="clearFilterBtn"
                class="bg-gray-700 hover:bg-gray-800 text-white rounded-lg">
                Clear Filter

            </button>

        </div>

    </div>

    <!-- Table -->

    <div class="bg-white rounded-xl shadow overflow-hidden">

        <table class="min-w-full">

            <thead class="bg-gray-100">

                <tr>

                    <th class="px-4 py-3 w-16">
                        #
                    </th>

                    <th class="px-4 py-3">
                        Title
                    </th>

                    <th class="px-4 py-3">
                        Description
                    </th>

                    <th class="px-4 py-3">
                        Color
                    </th>

                    <th class="px-4 py-3">
                        Status
                    </th>

                    <th class="px-4 py-3">
                        Created At
                    </th>

                    <th class="px-4 py-3 text-center">
                        Action
                    </th>

                </tr>

            </thead>

            <tbody id="tbody">

            </tbody>

        </table>

    </div>

    <div
        id="pagination"
        class="flex justify-between items-center mt-6">

    </div>

</div>

<script>

const Endpoint = "<?= url('api/content-categories/get-data.php') ?>";

const tbody = qs('#tbody');

const rowsInput = qs('#numberOfRowsPerPage');

const filters = qsa('.filterable_input');

window.onload = () => {

    index();

};

rowsInput.addEventListener('change', index);

filters.forEach(item => {

    item.addEventListener(

        'keyup',

        delay(index,500)

    );

    item.addEventListener(

        'change',

        index

    );

});

qs('#clearFilterBtn').onclick = function(){

    filters.forEach(item=>{

        item.value='';

    });

    index();

};

async function index(page = 1){

    let query = '';

    query += 'rows_per_page=' + rowsInput.value;

    query += '&page=' + page;

    let filterColumns='';

    filters.forEach(item=>{

        filterColumns += item.id + '=>' + item.value + '|';

    });

    query += '&filterable_columns=' + filterColumns;

    tbody.innerHTML='';

    const response = await fetch(

        Endpoint + '?' + query

    );

    const data = await response.json();

    let sl = data.sl + 1;

    data.records.data.forEach(row=>{
        tbody.innerHTML += `
        <tr class="border-t hover:bg-gray-50">

            <td class="px-4 py-4">

                ${sl++}

            </td>

            <td class="px-4 py-4 font-semibold">

                ${row.title}

            </td>

            <td class="px-4 py-4">

                ${row.description ?? ''}

            </td>

            <td class="px-4 py-4">

                <span
                    class="inline-block w-6 h-6 rounded-full border"
                    style="background:${row.color};">

                </span>

            </td>

            <td class="px-4 py-4">

                ${
                    row.is_active==1

                    ?

                    `<span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs">

                        Active

                    </span>`

                    :

                    `<span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs">

                        Inactive

                    </span>`

                }

            </td>

            <td class="px-4 py-4">

                ${CUSTOM_DATE_TIME(row.created_at)}

            </td>

            <td class="px-4 py-4 text-center">

                <a
                    href="<?= url('pages/content-categories/edit.php?id=') ?>${row.id}"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded">

                    Edit

                </a>

                <a
                    href="<?= url('pages/content-categories/delete.php?id=') ?>${row.id}"
                    onclick="return confirm('Delete this category?')"
                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded ml-2">
                    Delete
                </a>

            </td>

        </tr>

        `;

    });

    renderPagination(data.records.current_page,data.records.last_page);

}

function renderPagination(current,last){

    let html='';

    html += `<div class="text-sm text-gray-500">

                Page ${current} of ${last}

            </div>`;

    html += `<div class="space-x-2">`;

    if(current>1){

        html += `<button
                    onclick="index(${current-1})"
                    class="px-4 py-2 bg-gray-200 rounded">

                    Previous

                </button>`;

    }

    if(current<last){

        html += `<button
                    onclick="index(${current+1})"
                    class="px-4 py-2 bg-blue-600 text-white rounded">

                    Next

                </button>`;

    }

    html += `</div>`;

    qs('#pagination').innerHTML=html;

}

</script>

<?php

require_once INCLUDE_PATH . '/footer.php';

?>