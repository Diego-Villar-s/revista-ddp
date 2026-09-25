<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Validator;
use App\Models\PasswordReset;
use App\Models\Usuario;

class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect(BASE_URL . '/admin');
        }
        $error = '';
        if (isset($_GET['error'])) {
            $error = match ((string) $_GET['error']) {
                'credenciales' => 'Credenciales incorrectas.',
                'bloqueado' => 'Usuario bloqueado temporalmente por intentos fallidos.',
                'inactivo' => 'El usuario está desactivado. Contacte al administrador.',
                'noauth' => 'Debe iniciar sesión para acceder al panel.',
                default => 'Error al iniciar sesión.',
            };
        }
        $this->standalone('auth/login', compact('error'));
    }

    public function procesarLogin(): void
    {
        $email = Validator::sanitize((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if ($email === '' || $password === '' || !$this->validateCsrf()) {
            $this->redirect(BASE_URL . '/admin/login?error=credenciales');
        }

        if (Auth::attempt($email, $password)) {
            Database::getInstance()->insert(
                "INSERT INTO logs_actividad (usuario_id, accion, entidad, entidad_id, detalles) VALUES (?, 'login', 'usuario', ?, 'Inicio de sesión')",
                [Auth::id(), Auth::id()]
            );
            $this->redirect(BASE_URL . '/admin');
        }

        $user = (new Usuario())->findBy('email', $email);
        if ($user && !empty($user['bloqueado_hasta']) && strtotime($user['bloqueado_hasta']) > time()) {
            $this->redirect(BASE_URL . '/admin/login?error=bloqueado');
        }
        if ($user && !(bool) $user['activo']) {
            $this->redirect(BASE_URL . '/admin/login?error=inactivo');
        }
        $this->redirect(BASE_URL . '/admin/login?error=credenciales');
    }

    public function logout(): void
    {
        if (Auth::check()) {
            Database::getInstance()->insert(
                "INSERT INTO logs_actividad (usuario_id, accion, entidad, entidad_id, detalles) VALUES (?, 'logout', 'usuario', ?, 'Cierre de sesión')",
                [Auth::id(), Auth::id()]
            );
        }
        Auth::logout();
        $this->redirect(BASE_URL . '/admin/login');
    }

    public function recuperar(): void
    {
        $linkReset = (string) ($_SESSION['link_reset'] ?? '');
        unset($_SESSION['link_reset']);
        $this->standalone('auth/recuperar', compact('linkReset'));
    }

    /**
     * En XAMPP no hay SMTP: el enlace se muestra en pantalla. En producción
     * se debe enviar mediante PHPMailer después de crear el token.
     */
    public function procesarRecuperar(): void
    {
        $email = Validator::sanitize((string) ($_POST['email'] ?? ''));
        if (!$this->validateCsrf() || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect(BASE_URL . '/admin/recuperar');
        }

        $user = (new Usuario())->findBy('email', $email);
        if ($user && (bool) $user['activo']) {
            $resets = new PasswordReset();
            $resets->invalidateForUser((int) $user['id']);
            $token = $resets->createForUser((int) $user['id']);
            if (ENVIRONMENT === 'local') {
                $_SESSION['link_reset'] = BASE_URL . '/admin/reset/' . $token['token'];
            }
            // Producción: enviar BASE_URL . '/admin/reset/' . $token['token'] por PHPMailer.
        } else {
            // No se revela si el correo existe; en local se puede informar al usuario.
            $_SESSION['link_reset'] = 'Si el correo está registrado, se generará un enlace de recuperación.';
        }
        $this->redirect(BASE_URL . '/admin/recuperar');
    }

    public function mostrarReset(): void
    {
        $token = (string) ($_GET['token'] ?? '');
        $valid = (new PasswordReset())->findValidToken($token);
        if (!$valid) {
            $this->standalone('auth/reset-expirado');
            return;
        }
        $error = isset($_GET['error']) ? 'La contraseña debe tener al menos 8 caracteres.' : '';
        $this->standalone('auth/reset', compact('token', 'error'));
    }

    public function procesarReset(): void
    {
        $token = (string) ($_GET['token'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $password2 = (string) ($_POST['password2'] ?? '');
        if (!$this->validateCsrf()) {
            $this->redirect(BASE_URL . '/admin/reset/' . rawurlencode($token));
        }

        $resets = new PasswordReset();
        $valid = $resets->findValidToken($token);
        if (!$valid) {
            $this->standalone('auth/reset-expirado');
            return;
        }
        if (strlen($password) < 8 || $password !== $password2) {
            $this->redirect(BASE_URL . '/admin/reset/' . rawurlencode($token) . '?error=1');
        }

        (new Usuario())->update((int) $valid['usuario_id'], ['password_hash' => password_hash($password, PASSWORD_DEFAULT)]);
        $resets->markAsUsed((int) $valid['id']);
        $resets->invalidateForUser((int) $valid['usuario_id']);
        Database::getInstance()->insert(
            "INSERT INTO logs_actividad (usuario_id, accion, entidad, entidad_id, detalles) VALUES (?, 'reset_password', 'usuario', ?, 'Contraseña restablecida')",
            [$valid['usuario_id'], $valid['usuario_id']]
        );
        $this->redirect(BASE_URL . '/admin/login?reset=1');
    }
}
