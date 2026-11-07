<?php
function ligarBaseDados(): PDO
{
  static $pdo = null;

  if ($pdo === null) {
    $dbUser = getenv('DUALHOME_DB_USER') ?: 'root';
    $dbPassword = getenv('DUALHOME_DB_PASSWORD') ?: '';
    $pdo = new PDO(
      'mysql:host=localhost;dbname=dualhome_db;charset=utf8mb4',
      $dbUser,
      $dbPassword,
      [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
  }

  return $pdo;
}