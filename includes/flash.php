<?php

$flash = getFlash();

if (!$flash) {

    return;

}

?>

<div class="mb-6">

    <div class="rounded-lg border px-5 py-4

        <?= $flash['type'] === 'success'
            ? 'bg-green-100 border-green-300 text-green-700'
            : 'bg-red-100 border-red-300 text-red-700'
        ?>">

        <?= e($flash['message']) ?>

    </div>

</div>