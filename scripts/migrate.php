<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

$pdo = db();
$pdo->exec('CREATE TABLE IF NOT EXISTS schema_migrations (version VARCHAR(50) PRIMARY KEY, executed_at TIMESTAMPTZ NOT NULL DEFAULT NOW())');

$applied = $pdo->query('SELECT version FROM schema_migrations')->fetchAll(PDO::FETCH_COLUMN);
$appliedMap = array_flip($applied ?: []);

$files = glob(__DIR__ . '/../sql/migrations/*.sql');
sort($files);

foreach ($files as $file) {
    $version = basename($file, '.sql');

    if (isset($appliedMap[$version])) {
        echo "[skip] $version\n";
        continue;
    }

    $sql = file_get_contents($file);
    if ($sql === false) {
        throw new RuntimeException("Falha ao ler migration: $file");
    }

    echo "[run ] $version\n";
    $pdo->exec($sql);

    $stmt = $pdo->prepare('INSERT INTO schema_migrations (version) VALUES (:version)');
    $stmt->execute([':version' => $version]);
}

echo "Migrações concluídas.\n";
