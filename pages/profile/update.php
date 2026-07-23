<?php

require_once __DIR__ . '/../../bootstrap/app.php';
$pageTitle = 'My Profile';
$currentUser = user();
require_once INCLUDE_PATH . '/header.php';

?>

<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="border-b px-8 py-6">
            <h1 class="text-3xl font-bold">My Profile</h1>
            <p class="text-gray-500 mt-2">Manage your account information.</p>
        </div>

        <form id="profileForm" class="p-8">
            <div class="grid lg:grid-cols-3 gap-8">

                <!-- Profile Picture -->
                <div class="text-center">
                    <img id="profilePreview" 
                         src="<?= !empty($currentUser['picture']) ? asset($currentUser['picture']) : asset('assets/images/default/dp.png') ?>" 
                         class="w-40 h-40 rounded-full object-cover border-4 border-gray-200 mx-auto">

                    <label class="mt-5 inline-flex cursor-pointer bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">
                        <i class="fa-solid fa-pen-to-square mr-2 mt-1"></i> Picture
                        <input id="picture" type="file" accept="image/*" class="hidden">
                    </label>

                    <button type="button" id="deletePictureBtn"
                        class="mt-3 inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg <?= empty($currentUser['picture']) ? 'hidden' : '' ?>">
                        <i class="fas fa-trash-alt mr-2"></i>
                        Picture
                    </button>

                    <button type="button" id="openPasswordModal" 
                            class="mt-3 inline-flex items-center bg-gray-700 hover:bg-gray-800 text-white px-5 py-2 rounded-lg">
                        <i class="fa-solid fa-pen-to-square mr-2"></i> Password
                    </button>
                </div>

                <!-- Profile Details -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="block mb-2 font-medium">System ID</label>
                            <input type="text" readonly value="<?= e($currentUser['sys_id']) ?>" 
                                   class="w-full border rounded-lg px-4 py-3 bg-gray-100">
                        </div>
                        <div>
                            <label class="block mb-2 font-medium">Username</label>
                            <input type="text" readonly value="<?= e($currentUser['username']) ?>" 
                                   class="w-full border rounded-lg px-4 py-3 bg-gray-100">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="block mb-2 font-medium">User Type</label>
                            <input type="text" readonly value="<?= e($currentUser['user_type']) ?>" 
                                   class="w-full border rounded-lg px-4 py-3 bg-gray-100">
                        </div>
                        <div>
                            <label class="block mb-2 font-medium">Status</label>
                            <input type="text" readonly value="<?= e($currentUser['status']) ?>" 
                                   class="w-full border rounded-lg px-4 py-3 bg-gray-100">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 font-medium">Name</label>
                        <input type="text" name="name" required value="<?= e($currentUser['name']) ?>" 
                               class="w-full border rounded-lg px-4 py-3">
                    </div>

                    <div>
                        <label class="block mb-2 font-medium">Email</label>
                        <input type="email" name="email" required value="<?= e($currentUser['email']) ?>" 
                               class="w-full border rounded-lg px-4 py-3">
                    </div>

                    <div>
                        <label class="block mb-2 font-medium">Phone</label>
                        <input type="text" name="phone" value="<?= e($currentUser['phone']) ?>" 
                               class="w-full border rounded-lg px-4 py-3">
                    </div>

                    <button id="updateBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg">
                        Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Password Modal -->
<div id="passwordModal" class="fixed inset-0 bg-black/40 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">

        <!-- Header -->
        <div class="flex justify-between items-center border-b px-6 py-4">
            <h2 class="text-xl font-bold">Change Password</h2>
            <button id="closePasswordModal" class="text-2xl hover:text-red-600">&times;</button>
        </div>

        <!-- Form -->
        <form id="passwordForm" class="p-6 space-y-5">
            <div>
                <label class="block mb-2">Current Password</label>
                <div class="relative">
                    <input id="current_password" type="password" name="current_password" 
                           class="w-full border rounded-lg px-4 py-3 pr-12">
                    <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2" 
                            data-target="current_password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <div>
                <label class="block mb-2">New Password</label>
                <div class="relative">
                    <input id="newPassword" type="password" name="new_password" 
                           class="w-full border rounded-lg px-4 py-3 pr-12">
                    <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2" 
                            data-target="new_password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                <span id="passwordMessage" class="text-xs mt-1 block"></span>
            </div>

            <div>
                <label class="block mb-2">Retype New Password</label>
                <div class="relative">
                    <input id="confirmPassword" type="password" name="confirm_password" 
                           class="w-full border rounded-lg px-4 py-3 pr-12">
                    <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2" 
                            data-target="confirm_password">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                <span id="confirmPasswordMessage" class="text-xs mt-1 block"></span>
            </div>

            <div class="flex justify-end gap-3 pt-3">
                <button type="button" id="cancelPasswordModal" class="px-5 py-2 rounded-lg border">Cancel</button>
                <button id="passwordBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">Update</button>
            </div>
        </form>

    </div>
</div>

<script>
const picture = qs('#picture');
const preview = qs('#profilePreview');
const passwordModal = qs('#passwordModal');
const openPasswordModal = qs('#openPasswordModal');
const closePasswordModal = qs('#closePasswordModal');
const cancelPasswordModal = qs('#cancelPasswordModal');
const passwordForm = qs('#passwordForm');
const newPassword = qs('#newPassword');
const confirmPassword = qs('#confirmPassword');
const passwordMessage = qs('#passwordMessage');
const confirmPasswordMessage = qs('#confirmPasswordMessage');
const deletePictureBtn = qs('#deletePictureBtn');

/* Image Preview */
picture.addEventListener('change', function() {
    if (this.files.length) {
        preview.src = URL.createObjectURL(this.files[0]);
        if (deletePictureBtn) {
            deletePictureBtn.classList.remove('hidden');
        }
    }
});

/* Profile Update */
const form = qs('#profileForm');
form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const btn = qs('#updateBtn');
    btn.disabled = true;
    btn.innerHTML = 'Updating...';

    const formData = new FormData(form);

    try {
        const response = await fetch(APP_URL + 'api/profile/update.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (!result.success) {
            toaster.error(result.message);
            btn.disabled = false;
            btn.innerHTML = 'Update';
            return;
        }

        toaster.success(result.message);
    } catch (error) {
        toaster.error('Unable to connect to server.');
    }

    btn.disabled = false;
    btn.innerHTML = 'Update';
});

picture.addEventListener('change', async function() {
    if (!this.files.length) return;

    const formData = new FormData();
    formData.append('picture', this.files[0]);

    try {
        const response = await fetch(APP_URL + 'api/profile/upload-picture.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (!result.success) {
            toaster.error(result.message);
            return;
        }
        preview.src = result.data.picture + '?t=' + Date.now();
        if (deletePictureBtn) {
            deletePictureBtn.classList.remove('hidden');
        }
        const navbarImage = qs('#navbarProfileImage');
        if (navbarImage) {
            navbarImage.src = result.data.picture + '?t=' + Date.now();
        }
        toaster.success(result.message);
    } catch (error) {
        toaster.error('Upload failed.');
    }
});

/* Password Modal */
function closePassword() {
    passwordForm.reset();
    passwordMessage.innerHTML = '';
    confirmPasswordMessage.innerHTML = '';
    passwordModal.classList.add('hidden');
    passwordModal.classList.remove('flex');
}

openPasswordModal.onclick = () => {
    passwordModal.classList.remove('hidden');
    passwordModal.classList.add('flex');
};

closePasswordModal.onclick = closePassword;
cancelPasswordModal.onclick = closePassword;

passwordModal.addEventListener('click', function(e) {
    if (e.target === passwordModal) closePassword();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePassword();
});

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
    const password = newPassword.value;
    if (password === '') {
        passwordMessage.innerHTML = '';
        return;
    }

    const formData = new FormData();
    formData.append('password', password);

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
        confirmPasswordMessage.innerHTML = '✓ Password matched.';
        confirmPasswordMessage.classList.add('text-green-600');
    } else {
        confirmPasswordMessage.innerHTML = '✗ Password does not match.';
        confirmPasswordMessage.classList.add('text-red-600');
    }
}

newPassword.addEventListener('keyup', () => delay(validatePassword, 300)());
confirmPassword.addEventListener('keyup', validatePasswordMatch);

/* Password Update */
passwordForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    const btn = qs('#passwordBtn');
    btn.disabled = true;
    btn.innerHTML = 'Updating...';

    try {
        const response = await fetch(APP_URL + 'api/profile/password-update.php', {
            method: 'POST',
            body: new FormData(passwordForm)
        });

        const result = await response.json();

        if (!result.success) {
            toaster.error(result.message);
            btn.disabled = false;
            btn.innerHTML = 'Update';
            return;
        }

        toaster.success(result.message);
        closePassword();
    } catch (error) {
        toaster.error('Unable to update password.');
    }

    btn.disabled = false;
    btn.innerHTML = 'Update';
});

if (deletePictureBtn) {
    deletePictureBtn.addEventListener('click', async function () {
        if (!confirm('Delete your profile picture?')) {
            return;
        }

        deletePictureBtn.disabled = true;
        deletePictureBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Deleting...';

        try {
            const response = await fetch(APP_URL + 'api/profile/delete-picture.php', {
                method: 'POST'
            });

            const result = await response.json();

            if (!result.success) {
                toaster.error(result.message);
                deletePictureBtn.disabled = false;
                deletePictureBtn.innerHTML = '<i class="fas fa-trash-alt mr-2"></i>Picture';
                return;
            }

            preview.src = result.data.picture + '?t=' + Date.now();
            if (deletePictureBtn) {
                deletePictureBtn.classList.add('hidden');
            }
            const navbarImage = qs('#navbarProfileImage');
            if (navbarImage) {
                navbarImage.src = result.data.picture + '?t=' + Date.now();
            }

            toaster.success(result.message);
        } catch (error) {
            toaster.error('Unable to delete picture.');
        }

        deletePictureBtn.disabled = false;
        deletePictureBtn.innerHTML = '<i class="fas fa-trash-alt mr-2"></i>Picture';
    });
}
</script>

<?php
require_once INCLUDE_PATH . '/footer.php';
?>