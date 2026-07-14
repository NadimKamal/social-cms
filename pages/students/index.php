<?php

require_once __DIR__ . '/../../bootstrap/app.php';
require_once INCLUDE_PATH . '/header.php';

?>

<div class="max-w-7xl mx-auto px-6 py-8">

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                Students
            </h1>

            <p class="text-gray-500 mt-1">
                Manage all students from here.
            </p>
        </div>

        <a href="<?= url('pages/students/create.php') ?>"
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow">
            + Add Student
        </a>

    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl shadow p-5 mb-6">
        <div class="flex items-center gap-3">
            <select id="numberOfRowsPerPage"
                class="border rounded-lg px-3 py-2">

                <option value="5">5</option>
                <option value="10">10</option>
                <option value="15" selected>15</option>
                <option value="25">25</option>

            </select>

            <input
                type="text"
                id="title_like"
                class="filterable_input flex-1 border rounded-lg px-4 py-2"
                placeholder="Search title...">

            <button
                id="clearFilterBtn"
                class="bg-gray-700 text-white px-5 py-2 rounded-lg">
                Clear
            </button>

        </div>

    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow overflow-hidden">

    <table class="min-w-full">

        <thead class="bg-gray-100">

            <tr>

                <th class="px-4 py-3 w-20">#</th>

                <th class="px-4 py-3">
                    Picture
                </th>

                <th class="px-4 py-3">
                    Title
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

    <!-- Pagination -->
    <div id="pagination" class="flex justify-between items-center mt-6"></div>

</div>

<script>
    const Endpoint = "<?= url('api/students/get-data.php') ?>";
    const tbody = document.querySelector('#tbody');
    const rowsInput = document.querySelector('#numberOfRowsPerPage');
    const filters = document.querySelectorAll('.filterable_input');
    window.onload = () => {
        index();
    };

    rowsInput.addEventListener('change', index);

    filters.forEach(input => {
        input.addEventListener(
            'keyup',
            delay(index,500)
        );

    });

    document.querySelector('#clearFilterBtn').onclick=function(){
        filters.forEach(item=>item.value='');
        index();

    }

    async function index(page=1){

        let query='rows_per_page='+rowsInput.value;

        query+='&page='+page;

        let filterColumns='';

        filters.forEach(item=>{

            filterColumns+=item.id+'=>'+item.value+'|';

        });

        query+='&filterable_columns='+filterColumns;

        tbody.innerHTML='';

        const response=await fetch(Endpoint+'?'+query);

        const data=await response.json();

        let sl=data.sl+1;

        data.records.data.forEach(row=>{
            
            tbody.innerHTML+=`

            <tr class="border-t hover:bg-gray-50">

                <td class="px-4 py-4">${sl++}</td>

                <td class="px-4 py-4">
                    <img src="${asset(row.picture)}" class="w-14 h-14 rounded object-cover border">
                </td>

                <td class="px-4 py-4">
                    ${row.title}
                </td>

                <td>${CUSTOM_DATE_TIME(row.created_at)}</td>

                <td class="px-4 py-4 text-center">
                    <a href="<?= url('pages/students/edit.php?id=') ?>${row.id}" class="bg-yellow-500 text-white px-3 py-2 rounded"> Edit </a>
                    <a href="<?= url('pages/students/delete.php?id=') ?>${row.id}" onclick="return confirm('Delete this student?')" class="bg-red-600 text-white px-3 py-2 rounded ml-2"> Delete </a>
                </td>

            </tr>

            `;

        });

    }

    function delay(fn,ms){

        let timer=0;

        return function(){

            clearTimeout(timer);

            timer=setTimeout(fn,ms);

        }

    }

</script>
<?php

require_once '../../includes/footer.php';

?>