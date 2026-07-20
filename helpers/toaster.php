<?php

if (!function_exists('toast')) {

    /**
     * Store a toast message in session.
     *
     * @param string $type
     * @param string $message
     */
    function toast(string $type, string $message): void
    {
        $allowed = [
            'success',
            'error',
            'warning',
            'info',
            'primary',
            'secondary',
            'dark'
        ];

        if (!in_array($type, $allowed, true)) {
            $type = 'info';
        }

        $_SESSION['_toast'] = [
            'type'    => $type,
            'message' => $message
        ];
    }

}

if (!function_exists('getToast')) {

    /**
     * Get the current toast and remove it from session.
     *
     * @return array|null
     */
    function getToast(): ?array
    {
        if (empty($_SESSION['_toast'])) {
            return null;
        }

        $toast = $_SESSION['_toast'];

        unset($_SESSION['_toast']);

        return $toast;
    }

}

if (!function_exists('clearToast')) {

    /**
     * Remove any stored toast.
     */
    function clearToast(): void
    {
        unset($_SESSION['_toast']);
    }

}

