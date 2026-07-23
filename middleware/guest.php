<?php

// Guest Middleware
//-------------------
// Prevent logged-in users from accessing
// Login / Register pages.

if (isLoggedIn()) {
    redirect('pages/dashboard/index.php');
    exit;

}