<?php

require_once __DIR__ . '/../../bootstrap/app.php';
require_once INCLUDE_PATH . '/header.php';

$id = intval($_GET['id'] ?? 0);

$stmt = $pdo->prepare("

    SELECT *

    FROM social_accounts

    WHERE id = ?

    LIMIT 1

");

$stmt->execute([$id]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {

    setFlash(
        'error',
        'Social account not found.'
    );

    redirect('pages/social-accounts/index.php');

}

?>

<div class="max-w-4xl mx-auto px-6 py-8">

    <?php include INCLUDE_PATH . '/flash.php'; ?>

    <div class="flex items-center justify-between mb-6">

        <div>

            <h1 class="text-3xl font-bold text-gray-800">

                Edit Social Account

            </h1>

            <p class="text-gray-500 mt-2">

                Update company social media account information.

            </p>

        </div>

        <a
            href="<?= url('pages/social-accounts/index.php') ?>"
            class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg">

            Back

        </a>

    </div>

    <form
        action="<?= url('pages/social-accounts/update.php') ?>"
        method="POST"
        class="bg-white rounded-xl shadow p-6">

        <input
            type="hidden"
            name="id"
            value="<?= $row['id'] ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Platform -->

            <div>

                <label class="block mb-2 font-medium">

                    Platform <span class="text-red-500">*</span>

                </label>

                <select
                    name="platform"
                    required
                    class="w-full border rounded-lg px-4 py-2">

                    <?php

                    $platforms = [

                        'Facebook',
                        'Instagram',
                        'X',
                        'LinkedIn',
                        'YouTube',
                        'Threads'

                    ];

                    foreach($platforms as $platform){

                    ?>

                    <option
                        value="<?= $platform ?>"
                        <?= $row['platform']==$platform ? 'selected' : '' ?>>

                        <?= $platform ?>

                    </option>

                    <?php } ?>

                </select>

            </div>

            <!-- Status -->

            <div>

                <label class="block mb-2 font-medium">

                    Status

                </label>

                <select
                    name="is_active"
                    class="w-full border rounded-lg px-4 py-2">

                    <option
                        value="1"
                        <?= $row['is_active']==1 ? 'selected' : '' ?>>

                        Active

                    </option>

                    <option
                        value="0"
                        <?= $row['is_active']==0 ? 'selected' : '' ?>>

                        Inactive

                    </option>

                </select>

            </div>

            <!-- Account Name -->

            <div>

                <label class="block mb-2 font-medium">

                    Account Name <span class="text-red-500">*</span>

                </label>

                <input
                    type="text"
                    name="account_name"
                    required
                    maxlength="150"
                    value="<?= e(old('account_name', $row['account_name'])) ?>"
                    class="w-full border rounded-lg px-4 py-2">

            </div>

            <!-- Username -->

            <div>

                <label class="block mb-2 font-medium">

                    Username

                </label>

                <input
                    type="text"
                    name="account_username"
                    maxlength="150"
                    value="<?= e(old('account_username', $row['account_username'])) ?>"
                    class="w-full border rounded-lg px-4 py-2">

            </div>

            <!-- Email -->

            <div>

                <label class="block mb-2 font-medium">

                    Email

                </label>

                <input
                    type="email"
                    name="account_email"
                    value="<?= e(old('account_email', $row['account_email'])) ?>"
                    class="w-full border rounded-lg px-4 py-2">

            </div>

            <!-- Account URL -->

            <div>

                <label class="block mb-2 font-medium">

                    Account URL

                </label>

                <input
                    type="url"
                    name="account_url"
                    value="<?= e(old('account_url', $row['account_url'])) ?>"
                    class="w-full border rounded-lg px-4 py-2">

            </div>

            <!-- Page Name -->

            <div>

                <label class="block mb-2 font-medium">

                    Page Name

                </label>

                <input
                    type="text"
                    name="page_name"
                    value="<?= e(old('page_name', $row['page_name'])) ?>"
                    class="w-full border rounded-lg px-4 py-2">

            </div>

            <!-- Page URL -->

            <div>

                <label class="block mb-2 font-medium">

                    Page URL

                </label>

                <input
                    type="url"
                    name="page_url"
                    value="<?= e(old('page_url', $row['page_url'])) ?>"
                    class="w-full border rounded-lg px-4 py-2">

            </div>

            <!-- Access Token -->

            <div class="md:col-span-2">

                <label class="block mb-2 font-medium">

                    Access Token

                </label>

                <textarea
                    name="access_token"
                    rows="3"
                    class="w-full border rounded-lg px-4 py-2"><?= e(old('access_token', $row['access_token'])) ?></textarea>

            </div>

            <!-- Refresh Token -->

            <div class="md:col-span-2">

                <label class="block mb-2 font-medium">

                    Refresh Token

                </label>

                <textarea
                    name="refresh_token"
                    rows="3"
                    class="w-full border rounded-lg px-4 py-2"><?= e(old('refresh_token', $row['refresh_token'])) ?></textarea>

            </div>

        </div>

        <div class="mt-8 flex justify-end gap-3">

            <a
                href="<?= url('pages/social-accounts/index.php') ?>"
                class="bg-gray-300 hover:bg-gray-400 px-6 py-2 rounded-lg">

                Cancel

            </a>

            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">

                Update Account

            </button>

        </div>

    </form>

</div>

<?php

require_once INCLUDE_PATH . '/footer.php';

?>