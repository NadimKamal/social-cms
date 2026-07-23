<?php

// Authentication Middleware
// --------------------------
// Allow only logged-in users.

if (!isLoggedIn()) {

    toast('warning','Please login first.');
    redirect('pages/auth/login.php');
    exit;
    
}