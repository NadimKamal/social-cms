<?php

require_once __DIR__ . '/../../bootstrap/app.php';
require_once INCLUDE_PATH . '/header.php';

?>

<div class="max-w-4xl mx-auto px-6 py-8">

    <div class="flex items-center justify-between mb-6">

        <div>

            <h1 class="text-3xl font-bold text-gray-800">

                Add Social Account

            </h1>

            <p class="text-gray-500 mt-2">

                Store company social media accounts for future publishing.

            </p>

        </div>

        <a
            href="<?= url('pages/social-accounts/index.php') ?>"
            class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg">

            Back

        </a>

    </div>

    <?php include INCLUDE_PATH . '/flash.php'; ?>

    <form
        action="<?= url('pages/social-accounts/save.php') ?>"
        method="POST"
        class="bg-white rounded-xl shadow p-6">

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

                    <option value="">Select Platform</option>

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
                            <?= old('platform')==$platform ? 'selected' : '' ?>>

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
                        <?= old('is_active',1)==1?'selected':'' ?>>

                        Active

                    </option>

                    <option
                        value="0"
                        <?= old('is_active')==='0'?'selected':'' ?>>

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
                    value="<?= e(old('account_name')) ?>"
                    required
                    maxlength="150"
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
                    value="<?= e(old('account_username')) ?>"
                    maxlength="150"
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
                    value="<?= e(old('account_email')) ?>"
                    maxlength="150"
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
                    value="<?= e(old('account_url')) ?>"
                    placeholder="https://"
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
                    value="<?= e(old('page_name')) ?>"
                    maxlength="150"
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
                    value="<?= e(old('page_url')) ?>"
                    placeholder="https://"
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
                    class="w-full border rounded-lg px-4 py-2"><?= e(old('access_token')) ?></textarea>

                <p class="text-sm text-gray-500 mt-1">

                    Reserved for future publishing integration.

                </p>

            </div>

            <!-- Refresh Token -->

            <div class="md:col-span-2">

                <label class="block mb-2 font-medium">

                    Refresh Token

                </label>

                <textarea
                    name="refresh_token"
                    rows="3"
                    class="w-full border rounded-lg px-4 py-2"><?= e(old('refresh_token')) ?></textarea>

            </div>

        </div>

        <div class="mt-8 flex justify-end gap-3">

            <a
                href="<?= url('pages/social-accounts/index.php') ?>"
                class="px-6 py-2 rounded-lg bg-gray-300 hover:bg-gray-400">

                Cancel

            </a>

            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                Save Account
            </button>
        </div>
    </form>

</div>

<?php

require_once INCLUDE_PATH . '/footer.php';

?>