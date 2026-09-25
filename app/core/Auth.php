<?php
namespace App\Core;

/**
 * Autenticación basada en sesión y password_hash/password_verify.
 */
final class Auth
{
    public static function attempt(string $email, string $password): bool
    {
        $db = Database::getInstance();
        $user = $db->selectOne(
            'SELECT * FROM usuarios WHERE email = ? AND activo = 1',
            [$email]
        );

        if (!$user) {
            return false;
        }

        if (!empty($user['bloqueado_hasta']) && strtotime($user['bloqueado_hasta']) > time()) {
            return false;
        }

        if (!password_verify($password, (string) $user['password_hash'])) {
            self::registerFailedAttempt((int) $user['id']);
            return false;
        }

        $db->execute(
            'UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = ?',
            [$user['id']]
        );

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'nombres' => $user['nombres'],
            'ap_paterno' => $user['ap_paterno'],
            'ap_materno' => $user['ap_materno'] ?? '',
            'email' => $user['email'],
            'rol' => $user['rol'],
        ];

        return true;
    }

    private static function registerFailedAttempt(int $userId): void
    {
        $db = Database::getInstance();
        $db->execute(
            'UPDATE usuarios SET intentos_fallidos = intentos_fallidos + 1 WHERE id = ?',
            [$userId]
        );
        $user = $db->selectOne(
            'SELECT intentos_fallidos FROM usuarios WHERE id = ?',
            [$userId]
        );
        if ($user && (int) $user['intentos_fallidos'] >= MAX_LOGIN_ATTEMPTS) {
            $blockedUntil = date('Y-m-d H:i:s', time() + LOGIN_LOCKOUT_MINUTES * 60);
            $db->execute(
                'UPDATE usuarios SET bloqueado_hasta = ? WHERE id = ?',
                [$blockedUntil, $userId]
            );
        }
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']) && is_array($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return self::check() ? $_SESSION['user'] : null;
    }

    public static function id(): ?int
    {
        return self::check() ? (int) $_SESSION['user']['id'] : null;
    }

    public static function hasRole(string ...$roles): bool
    {
        $user = self::user();
        return $user !== null && in_array($user['rol'] ?? '', $roles, true);
    }

    public static function isAdmin(): bool
    {
        return self::hasRole('admin');
    }

    public static function isEditorOrAbove(): bool
    {
        return self::hasRole('admin', 'editor');
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}
