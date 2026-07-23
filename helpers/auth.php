<?php

/* Password Helpers */
if (!function_exists('hashPassword')) {
    function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}

if (!function_exists('verifyPassword')) {
    function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
}

/* Authentication State */
if (!function_exists('isLoggedIn')) {
    function isLoggedIn(): bool {
        return isset($_SESSION['user']);
    }
}

if (!function_exists('user')) {
    function user(?string $key = null) {
        if (!isLoggedIn()) {
            return null;
        }
        if ($key === null) {
            return $_SESSION['user'];
        }
        return $_SESSION['user'][$key] ?? null;
    }
}

if (!function_exists('auth')) {
    function auth(?string $key = null) {
        return user($key);
    }
}

if (!function_exists('guest')) {
    function guest(): bool {
        return !isLoggedIn();
    }
}

/* Generate Username */
if (!function_exists('generateUsername')) {
    function generateUsername(PDO $pdo, string $userType): string {
        $prefix = strtoupper($userType) === 'COMPANY' ? 'SCMSC' : 'SCMSP';

        $stmt = $pdo->prepare("
            SELECT username
            FROM users
            WHERE username LIKE ?
            ORDER BY id DESC
            LIMIT 1
        ");

        $stmt->execute([$prefix . '%']);

        $lastUsername = $stmt->fetchColumn();

        if (!$lastUsername) {
            return $prefix . '00001';
        }

        $number = (int) substr($lastUsername, strlen($prefix));
        $number++;

        return $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}

/* Login */
if (!function_exists('login')) {
    function login(array $user): void {
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'        => $user['id'],
            'uuid'      => $user['uuid'],
            'sys_id'    => $user['sys_id'],
            'user_type' => $user['user_type'],
            'name'      => $user['name'],
            'username'  => $user['username'],
            'email'     => $user['email'],
            'phone'     => $user['phone'],
            'picture'   => $user['picture'],
            'status'    => $user['status']
        ];
    }
}

/* Logout */
if (!function_exists('logout')) {
    function logout(): void {
        unset($_SESSION['user']);
        session_regenerate_id(true);
    }
}

/* Attempt Login */
if (!function_exists('attemptLogin')) {
    function attemptLogin(PDO $pdo, string $login, string $password): array {
        $stmt = $pdo->prepare("
            SELECT *
            FROM users
            WHERE username = ? OR email = ?
            LIMIT 1
        ");

        $stmt->execute([trim($login), trim($login)]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid username / email or password.'
            ];
        }

        if (!verifyPassword($password, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Invalid username/email or password.'
            ];
        }

        if ($user['status'] === 'Inactive') {
            return [
                'success' => false,
                'message' => 'Your account is inactive.'
            ];
        }

        if ($user['status'] === 'Blocked') {
            return [
                'success' => false,
                'message' => 'Your account has been blocked.'
            ];
        }

        login($user);

        $stmt = $pdo->prepare("
            UPDATE users
            SET last_login_at = ?
            WHERE id = ?
        ");

        $stmt->execute([now(), $user['id']]);

        return [
            'success' => true,
            'message' => 'Login successful.',
            'user'    => $user
        ];
    }
}

/* Register User */
if (!function_exists('register')) {
    function register(PDO $pdo, array $data): array {
        $stmt = $pdo->prepare("
            INSERT INTO users
            (
                uuid,
                sys_id,
                user_type,
                name,
                username,
                email,
                phone,
                password,
                picture,
                status,
                created_at
            )
            VALUES
            (?,?,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->execute([
            $data['uuid'],
            $data['sys_id'],
            $data['user_type'],
            $data['name'],
            $data['username'],
            $data['email'],
            $data['phone'],
            $data['password'],
            $data['picture'],
            $data['status'],
            $data['created_at']
        ]);

        return [
            'success' => true,
            'message' => 'Registration successful.'
        ];
    }
}