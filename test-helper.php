<?php

require_once __DIR__ . '/bootstrap/app.php';
require_once INCLUDE_PATH . '/header.php';

?>

<div class="max-w-7xl mx-auto p-8">

    <div class="bg-white rounded-xl shadow p-6">

        <h1 class="text-3xl font-bold mb-6">

            Helper Function Test

        </h1>

        <table class="table-auto">

            <tr>
                <td class="pr-8 font-semibold">
                    APP URL
                </td>
                <td>
                    <?= APP_URL ?>
                </td>
            </tr>

            <tr>
                <td class="pr-8 font-semibold">
                    Asset Helper
                </td>
                <td>
                    <?= asset('assets/js/tailwind.min.js') ?>
                </td>
            </tr>

            <tr>

                <td class="pr-8 font-semibold">

                    Date

                </td>

                <td>
                    <?= CUSTOM_DATE(now()) ?>
                </td>

            </tr>

            <tr>

                <td class="pr-8 font-semibold">

                    Date Time

                </td>

                <td>

                    <?= CUSTOM_DATE_TIME(now()) ?>

                </td>

            </tr>

        </table>

    </div>

</div>

<?php

require_once INCLUDE_PATH . '/footer.php';