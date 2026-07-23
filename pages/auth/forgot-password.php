<?php

require_once __DIR__ . '/../../bootstrap/app.php';
$pageTitle = 'Forgot Password';
require_once INCLUDE_PATH . '/header.php';

?>

<div class="min-h-[80vh] flex items-center justify-center px-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg overflow-hidden">

        <div class="border-b px-8 py-6">
            <h1 class="text-3xl font-bold">Forgot Password</h1>
            <p class="text-gray-500 mt-2">Verify your account to reset your password.</p>
        </div>

        <form id="verifyForm" class="p-8 space-y-6">

            <div>
                <label class="block mb-2 font-medium">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" required class="w-full border rounded-lg px-4 py-3">
            </div>

            <div>
                <label class="block mb-2 font-medium">Phone (Optional)</label>
                <input type="text" name="phone" class="w-full border rounded-lg px-4 py-3">
            </div>

            <button id="verifyBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg">
                Verify Account
            </button>

        </form>

        <div class="border-t px-8 py-5 text-center">
            <a href="<?= url('pages/auth/login.php') ?>" class="text-blue-600 hover:underline">Back to Login</a>
        </div>

    </div>

</div>

<!-- Reset Password Modal -->
<div id="resetModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">

    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">

        <div class="flex justify-between items-center border-b px-6 py-4">
            <h2 class="text-xl font-bold">Reset Password</h2>
            <button type="button" id="closeModal" class="text-2xl hover:text-red-600">&times;</button>
        </div>

        <form id="resetForm" class="p-6 space-y-5">

            <input type="hidden" name="uuid" id="uuid">

            <div>
                <label class="block mb-2">Phone <span class="text-red-500">*</span></label>
                <input id="resetPhone" type="text" name="phone" required class="w-full border rounded-lg px-4 py-3">
            </div>

            <div>
                <label class="block mb-2">New Password</label>
                <div class="relative">
                    <input id="newPassword" type="password" name="password" class="w-full border rounded-lg px-4 py-3 pr-12">
                    <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2" data-target="newPassword">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                <span id="passwordMessage" class="text-xs mt-1 block"></span>
            </div>

            <div>
                <label class="block mb-2">Confirm Password</label>
                <div class="relative">
                    <input id="confirmPassword" type="password" name="confirm_password" class="w-full border rounded-lg px-4 py-3 pr-12">
                    <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2" data-target="confirmPassword">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                <span id="confirmPasswordMessage" class="text-xs mt-1 block"></span>
            </div>

            <div class="flex justify-end gap-3 pt-3">
                <button type="button" id="cancelModal" class="border px-5 py-2 rounded-lg">Cancel</button>
                <button id="resetBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">Reset Password</button>
            </div>

        </form>

    </div>

</div>

<script>
const verifyForm = qs('#verifyForm');
const resetForm = qs('#resetForm');
const resetModal = qs('#resetModal');
const passwordMessage = qs('#passwordMessage');
const confirmPasswordMessage = qs('#confirmPasswordMessage');
const newPassword = qs('#newPassword');
const confirmPassword = qs('#confirmPassword');

/* Verify Account */
verifyForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    const btn = qs('#verifyBtn');
    btn.disabled = true;
    btn.innerHTML = 'Verifying...';

    const response = await fetch(APP_URL + 'api/auth/verify-user.php', {
        method: 'POST',
        body: new FormData(verifyForm)
    });

    const result = await response.json();

    btn.disabled = false;
    btn.innerHTML = 'Verify Account';

    if (!result.success) {
        toaster.error(result.message);
        return;
    }

    qs('#uuid').value = result.data.uuid;
    qs('#resetPhone').value = result.data.phone ?? '';

    resetModal.classList.remove('hidden');
});

/* Close Modal */
function closeModal() {
    resetForm.reset();
    passwordMessage.innerHTML = '';
    confirmPasswordMessage.innerHTML = '';
    resetModal.classList.add('hidden');
}

qs('#closeModal').onclick = closeModal;
qs('#cancelModal').onclick = closeModal;

resetModal.onclick = function(e) {
    if (e.target === resetModal) closeModal();
};

/* Toggle Password Visibility */
qsa('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        const input = qs('#' + this.dataset.target);
        const icon = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fa-solid fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fa-solid fa-eye';
        }
    });
});

/* Password Validation */
async function validatePassword() {
    if (newPassword.value === '') {
        passwordMessage.innerHTML = '';
        return;
    }

    const formData = new FormData();
    formData.append('password', newPassword.value);

    const response = await fetch(APP_URL + 'api/auth/check-password.php', {
        method: 'POST',
        body: formData
    });

    const result = await response.json();

    passwordMessage.innerHTML = result.message;
    passwordMessage.className = 'text-xs mt-1';
    passwordMessage.classList.add(result.success ? 'text-green-600' : 'text-red-600');

    validatePasswordMatch();
}

function validatePasswordMatch() {
    if (confirmPassword.value === '') {
        confirmPasswordMessage.innerHTML = '';
        return;
    }

    confirmPasswordMessage.className = 'text-xs mt-1';
    if (newPassword.value === confirmPassword.value) {
        confirmPasswordMessage.innerHTML = '✓ Password matched';
        confirmPasswordMessage.classList.add('text-green-600');
    } else {
        confirmPasswordMessage.innerHTML = '✗ Password does not match';
        confirmPasswordMessage.classList.add('text-red-600');
    }
}

newPassword.addEventListener('keyup', () => delay(validatePassword, 300)());
confirmPassword.addEventListener('keyup', validatePasswordMatch);

/* Reset Password */
resetForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    const btn = qs('#resetBtn');
    btn.disabled = true;
    btn.innerHTML = 'Updating...';

    try {
        const response = await fetch(APP_URL + 'api/auth/reset-password.php', {
            method: 'POST',
            body: new FormData(resetForm)
        });

        const result = await response.json();

        btn.disabled = false;
        btn.innerHTML = 'Reset Password';

        if (!result.success) {
            toaster.error(result.message);
            return;
        }

        toaster.success(result.message);
        closeModal();

        setTimeout(() => {
            location.href = APP_URL + 'pages/auth/login.php';
        }, 800);
    } catch (error) {
        btn.disabled = false;
        btn.innerHTML = 'Reset Password';
        toaster.error('Error found.');
    }
});
</script>

<?php
require_once INCLUDE_PATH . '/footer.php';
?>