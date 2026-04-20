<?php
/**
 * includes/db.php — PDO singleton
 * Set env vars or edit defaults below.
 */
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $host   = getenv('DB_HOST') ?: 'localhost';
        $dbname = getenv('DB_NAME') ?: 'labuyo_db';
        $user   = getenv('DB_USER') ?: 'root';
        $pass   = getenv('DB_PASS') ?: '';
        $pdo = new PDO(
            "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
            $user, $pass,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }
    return $pdo;
}
