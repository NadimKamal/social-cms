<?php

require_once __DIR__ . '/../../bootstrap/app.php';

$pageTitle = 'Register';

require_once INCLUDE_PATH . '/header.php';

?>

<div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="w-full max-w-3xl bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="border-b px-8 py-6">
            <h1 class="text-3xl font-bold">Create Account</h1>
            <p class="text-gray-500 mt-2">Register as a Person or Company.</p>
        </div>

        <!-- Tabs -->
        <div class="flex border-b">
            <button id="personTab" type="button" 
                    class="flex-1 py-4 font-semibold border-b-2 border-blue-600 text-blue-600">
                Person
            </button>
            <button id="companyTab" type="button" 
                    class="flex-1 py-4 font-semibold border-b-2 border-transparent">
                Company
            </button>
        </div>

        <form id="registerForm" class="p-8 space-y-6">

            <input type="hidden" id="user_type" name="user_type" value="Person">

            <div>
                <label class="block mb-2 font-medium">Name</label>
                <input type="text" id="name" name="name" 
                       class="w-full border rounded-lg px-4 py-3" required>
            </div>

            <div>
                <label class="block mb-2 font-medium">Email</label>
                <input type="email" id="email" name="email" 
                       class="w-full border rounded-lg px-4 py-3" required>
            </div>

            <div>
                <label class="block mb-2 font-medium">Phone</label>
                <input type="text" id="phone" name="phone" 
                       class="w-full border rounded-lg px-4 py-3">
            </div>

            <div>
                <label class="block mb-2 font-medium">Password</label>
                <div class="relative">
                    <input id="password" type="password" name="password" 
                           class="w-full border rounded-lg px-4 py-3 pr-12" required>
                    <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2" 
                            data-target="password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                <span id="passwordMessage" class="text-xs mt-1 block"></span>
            </div>

            <div>
                <label class="block mb-2 font-medium">Confirm Password</label>
                <div class="relative">
                    <input id="password_confirmation" type="password" name="password_confirmation" 
                           class="w-full border rounded-lg px-4 py-3 pr-12" required>
                    <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2" 
                            data-target="password_confirmation">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                <span id="confirmPasswordMessage" class="text-xs mt-1 block"></span>
            </div>

            <button type="submit" id="registerBtn" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold">
                Create Account
            </button>

        </form>

        <div class="border-t px-8 py-5 text-center">
            Already have an account?
            <a href="<?= url('pages/auth/login.php') ?>" class="text-blue-600 font-semibold">Login</a>
        </div>
    </div>
</div>

<script>
const personTab = qs('#personTab');
const companyTab = qs('#companyTab');
const userType = qs('#user_type');
const password = qs('#password');
const confirmPassword = qs('#password_confirmation');
const passwordMessage = qs('#passwordMessage');
const confirmPasswordMessage = qs('#confirmPasswordMessage');

/* Tab Switching */
personTab.onclick = () => {
    userType.value = 'Person';
    personTab.className = 'flex-1 py-4 font-semibold border-b-2 border-blue-600 text-blue-600';
    companyTab.className = 'flex-1 py-4 font-semibold border-b-2 border-transparent';
};

companyTab.onclick = () => {
    userType.value = 'Company';
    companyTab.className = 'flex-1 py-4 font-semibold border-b-2 border-blue-600 text-blue-600';
    personTab.className = 'flex-1 py-4 font-semibold border-b-2 border-transparent';
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

/* Password Strength Validation */
async function validatePassword() {
    if (password.value === '') {
        passwordMessage.innerHTML = '';
        return;
    }

    const formData = new FormData();
    formData.append('password', password.value);

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
    if (password.value === confirmPassword.value) {
        confirmPasswordMessage.innerHTML = '✓ Password matched.';
        confirmPasswordMessage.classList.add('text-green-600');
    } else {
        confirmPasswordMessage.innerHTML = '✗ Password does not match.';
        confirmPasswordMessage.classList.add('text-red-600');
    }
}

password.addEventListener('keyup', () => delay(validatePassword, 300)());
confirmPassword.addEventListener('keyup', validatePasswordMatch);

/* Register Form Submit */
qs('#registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = new FormData(this);
    const btn = qs('#registerBtn');

    btn.disabled = true;
    btn.innerHTML = 'Creating...';

    try {
        const response = await fetch(APP_URL + 'api/auth/register.php', {
            method: 'POST',
            body: form
        });

        const data = await response.json();

        btn.disabled = false;
        btn.innerHTML = 'Create Account';

        if (!data.success) {
            toaster.error(data.message);
            return;
        }

        toaster.success(data.message);

        setTimeout(() => {
            window.location = APP_URL + 'pages/auth/login.php';
        }, 1500);

    } catch (e) {
        btn.disabled = false;
        btn.innerHTML = 'Create Account';
        toaster.error('Unable to connect to server.');
    }
});
</script>

<?php
require_once INCLUDE_PATH . '/footer.php';
?>