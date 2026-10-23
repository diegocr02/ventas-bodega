<?php
declare(strict_types=1);

namespace App\Security;

use LogicException;

final class Csrf
{
    /**
     * Obtiene el token de la sesión actual y lo crea cuando aún no existe.
     *
     * @throws LogicException Si la sesión no se inició.
     */
    public static function token(): string
    {
        self::requireActiveSession();

        if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Comprueba de forma segura que el token recibido coincide con el de la sesión.
     *
     * @param mixed $submittedToken Token recibido del formulario.
     */
    public static function isValid(mixed $submittedToken): bool
    {
        self::requireActiveSession();

        $sessionToken = $_SESSION['csrf_token'] ?? null;
        if (!is_string($submittedToken) || !is_string($sessionToken)) {
            return false;
        }

        return hash_equals($sessionToken, $submittedToken);
    }

    /**
     * Exige que la sesión esté iniciada antes de leer o crear el token.
     *
     * @throws LogicException Si la sesión no se inició.
     */
    private static function requireActiveSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new LogicException('La sesión debe iniciarse antes de usar protección CSRF.');
        }
    }
}
