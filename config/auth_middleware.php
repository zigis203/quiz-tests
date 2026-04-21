<?php
/**
 * Authentication Middleware
 * Use this file to protect routes and check user roles/permissions
 * Usage: require_once __DIR__ . '/auth_middleware.php';
 */

require_once __DIR__ . '/config.php';

class AuthMiddleware
{
    /**
     * Require user to be logged in, otherwise redirect to login
     */
    public static function requireLogin(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login.php');
            exit;
        }
    }

    /**
     * Require user to be logged out, otherwise redirect to home
     */
    public static function requireLogout(): void
    {
        if (!empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/topics.php');
            exit;
        }
    }

    /**
     * Require user to be admin, otherwise show error
     */
    public static function requireAdmin(): void
    {
        self::requireLogin();
        
        if (($_SESSION['role'] ?? null) !== 'admin') {
            http_response_code(403);
            echo '<h1>403 - Access Denied</h1>';
            echo '<p>You do not have permission to access this page.</p>';
            echo '<a href="' . BASE_URL . '/topics.php">Back to Topics</a>';
            exit;
        }
    }

    /**
     * Get the currently logged-in user object
     */
    public static function getCurrentUser(): ?array
    {
        if (empty($_SESSION['user_id'])) {
            return null;
        }

        $db = Database::getInstance()->getConnection();
        $userModel = new User($db);
        return $userModel->findById((int)$_SESSION['user_id']);
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    /**
     * Check if user is admin
     */
    public static function isAdmin(): bool
    {
        return ($_SESSION['role'] ?? null) === 'admin';
    }

    /**
     * Log the user out and redirect to login
     */
    public static function logout(): void
    {
        User::logout();
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}
