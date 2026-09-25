<?php
namespace App\Core;

use PDO;
use PDOException;

/**
 * Conexión PDO única para MySQL/MariaDB.
 * Todas las operaciones de datos usan prepared statements.
 */
final class Database
{
    private static ?self $instance = null;
    private ?PDO $pdo = null;

    private function __construct()
    {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $baseOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $this->pdo = null;
        $lastError = null;

        // Diagnostico seguro: longitudes y una huella de la contrasena para
        // detectar si Railway entrega los mismos bytes que MySQL espera.
        // No se registra el valor.
        error_log(sprintf(
            'DDP DB cfg: host=%s port=%s db=%s user=%s pass_len=%d pass_md5=%s getenv=%s tls_ca=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_USER,
            strlen((string) DB_PASS),
            substr(md5((string) DB_PASS), 0, 8),
            getenv('DB_PASS') !== false ? 'yes' : 'no',
            $this->resolveCaPath() ?? 'none'
        ));

        foreach ($this->connectionAttempts($baseOptions) as $label => $options) {
            try {
                $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                $this->pdo->exec("SET NAMES utf8mb4");
                error_log('DDP DB connected via ' . $label);
                break;
            } catch (PDOException $exception) {
                $lastError = $exception;
                error_log('DDP DB attempt failed [' . $label . ']: ' . $exception->getMessage());
            }
        }

        if ($this->pdo === null) {
            $message = $lastError !== null ? $lastError->getMessage() : 'sin intentos';
            error_log('DDP DB connection error: ' . $message);

            // TEMPORAL (diagnostico): con DDP_BOOTSTRAP=1 se responde 200 para
            // que el healthcheck de Railway valide el contenedor y quede
            // accesible por SSH aunque la base de datos no responda.
            if (getenv('DDP_BOOTSTRAP') === '1') {
                http_response_code(200);
                header('Content-Type: text/plain; charset=utf-8');
                echo "bootstrap-ok\n";
                echo "php=" . PHP_VERSION . "\n";
                echo "pdo_mysql=" . (extension_loaded('pdo_mysql') ? 'yes' : 'no') . "\n";
                echo "openssl=" . (extension_loaded('openssl') ? 'yes' : 'no') . "\n";
                echo "ca=" . ($this->resolveCaPath() ?? 'none') . "\n";
                echo "pubkey=" . (is_file(CONFIG_PATH . '/mysql-public-key.pem') ? 'yes' : 'no') . "\n";
                echo "err=" . $message . "\n";
                exit;
            }

            http_response_code(500);
            echo ENVIRONMENT === 'local'
                ? 'Error de conexión: ' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
                : 'Error de conexión a la base de datos.';
            exit;
        }
    }

    /**
     * Construye las combinaciones de conexión a probar, en orden.
     *
     * MySQL 8.0+/9.x autentica con caching_sha2_password. Sobre un canal sin
     * TLS el cliente necesita la clave pública del servidor; si no la
     * obtiene, el fallo se reporta como "Access denied" aunque la contraseña
     * sea correcta. Por eso se intenta primero TLS.
     *
     * @param  array<int, mixed> $baseOptions
     * @return array<string, array<int, mixed>>
     */
    private function connectionAttempts(array $baseOptions): array
    {
        $attempts = [];
        $caPath = $this->resolveCaPath();

        if ($caPath !== null) {
            $attempts['tls'] = $baseOptions + [
                PDO::MYSQL_ATTR_SSL_CA => $caPath,
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => ddp_env('DB_SSL_VERIFY', '0') === '1',
            ];
        }

        // Alternativa cuando no hay CA: la clave publica del servidor.
        // MySQL 8/9 usa caching_sha2_password y, sin TLS, el cliente la
        // necesita para cifrar la contrasena. El CLI de MySQL 9.4 la pide
        // solo; mysqlnd de PHP hay que senialarsela con
        // PDO::MYSQL_ATTR_SERVER_PUBLIC_KEY. Sin esto el servidor responde
        // 1045 Access denied aunque la contrasena sea correcta.
        $publicKeyPath = ddp_env('DB_SERVER_PUBLIC_KEY');
        if ($publicKeyPath === null || $publicKeyPath === '') {
            $isLocal = in_array(DB_HOST, ['localhost', '127.0.0.1', '::1'], true);
            $bundled = CONFIG_PATH . '/mysql-public-key.pem';
            $publicKeyPath = (!$isLocal && is_file($bundled)) ? $bundled : null;
        }
        if ($publicKeyPath !== null && $publicKeyPath !== '' && is_file($publicKeyPath)) {
            $attempts['public-key'] = $baseOptions + [
                PDO::MYSQL_ATTR_SERVER_PUBLIC_KEY => $publicKeyPath,
            ];
        }

        // Ultimo recurso: sin cifrar, para MySQL 5.7/8 y MariaDB.
        $attempts['plain'] = $baseOptions;

        return $attempts;
    }

    /**
     * Localiza el CA del servidor. Usa DB_SSL_CA y, si no se indica, el
     * archivo incluido en el repositorio cuando la base no es local.
     */
    private function resolveCaPath(): ?string
    {
        $configured = ddp_env('DB_SSL_CA');
        if ($configured === null || $configured === '') {
            $isLocal = in_array(DB_HOST, ['localhost', '127.0.0.1', '::1'], true);
            $bundled = CONFIG_PATH . '/mysql-ca.pem';
            $configured = (!$isLocal && is_file($bundled)) ? $bundled : null;
        }

        return ($configured !== null && $configured !== '' && is_file($configured)) ? $configured : null;
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function getConnection(): PDO
    {
        if ($this->pdo === null) {
            throw new \RuntimeException('La conexión a la base de datos no está inicializada.');
        }
        return $this->pdo;
    }

    public function select(string $sql, array $params = []): array
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function selectOne(string $sql, array $params = []): ?array
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        $row = $statement->fetch();
        return $row === false ? null : $row;
    }

    public function scalar(string $sql, array $params = [])
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->fetchColumn();
    }

    public function insert(string $sql, array $params = []): int
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return (int) $this->pdo->lastInsertId();
    }

    public function execute(string $sql, array $params = []): int
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->rowCount();
    }

    public function count(string $sql, array $params = []): int
    {
        return (int) $this->scalar($sql, $params);
    }

    public function transaction(callable $callback): mixed
    {
        $this->pdo->beginTransaction();
        try {
            $result = $callback($this);
            $this->pdo->commit();
            return $result;
        } catch (\Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }

    private function __clone()
    {
    }
}
