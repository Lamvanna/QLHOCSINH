<?php
// models/BaseModel.php
require_once __DIR__ . '/../core/Database.php';

abstract class BaseModel {
    protected static string $table = '';
    protected static string $primaryKey = 'id';
    protected static bool $softDelete = false;

    public static function getPdo(): PDO {
        return Database::getConnection();
    }

    public static function all(string $orderBy = 'id DESC'): array {
        $pdo = self::getPdo();
        $where = static::$softDelete ? "WHERE deleted_at IS NULL" : "";
        $stmt = $pdo->query("SELECT * FROM `" . static::$table . "` {$where} ORDER BY {$orderBy}");
        return $stmt->fetchAll();
    }

    public static function find(int|string $id): ?array {
        $pdo = self::getPdo();
        $where = static::$softDelete ? "AND deleted_at IS NULL" : "";
        $stmt = $pdo->prepare("SELECT * FROM `" . static::$table . "` WHERE `" . static::$primaryKey . "` = ? {$where}");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function where(string $column, mixed $value, string $operator = '='): array {
        $pdo = self::getPdo();
        $soft = static::$softDelete ? "AND deleted_at IS NULL" : "";
        $stmt = $pdo->prepare("SELECT * FROM `" . static::$table . "` WHERE `{$column}` {$operator} ? {$soft}");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int {
        $pdo = self::getPdo();
        $columns = array_keys($data);
        $fields = implode(', ', array_map(fn($col) => "`{$col}`", $columns));
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));

        $stmt = $pdo->prepare("INSERT INTO `" . static::$table . "` ({$fields}) VALUES ({$placeholders})");
        $stmt->execute(array_values($data));
        return (int)$pdo->lastInsertId();
    }

    public static function update(int|string $id, array $data): bool {
        $pdo = self::getPdo();
        $setFields = implode(', ', array_map(fn($col) => "`{$col}` = ?", array_keys($data)));
        $values = array_values($data);
        $values[] = $id;

        $stmt = $pdo->prepare("UPDATE `" . static::$table . "` SET {$setFields} WHERE `" . static::$primaryKey . "` = ?");
        return $stmt->execute($values);
    }

    public static function delete(int|string $id): bool {
        $pdo = self::getPdo();
        if (static::$softDelete) {
            $stmt = $pdo->prepare("UPDATE `" . static::$table . "` SET deleted_at = NOW() WHERE `" . static::$primaryKey . "` = ?");
            return $stmt->execute([$id]);
        } else {
            $stmt = $pdo->prepare("DELETE FROM `" . static::$table . "` WHERE `" . static::$primaryKey . "` = ?");
            return $stmt->execute([$id]);
        }
    }

    public static function restore(int|string $id): bool {
        if (!static::$softDelete) return false;
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("UPDATE `" . static::$table . "` SET deleted_at = NULL WHERE `" . static::$primaryKey . "` = ?");
        return $stmt->execute([$id]);
    }

    public static function count(string $whereClause = '', array $params = []): int {
        $pdo = self::getPdo();
        $soft = static::$softDelete ? (empty($whereClause) ? "WHERE deleted_at IS NULL" : " AND deleted_at IS NULL") : "";
        $sql = "SELECT COUNT(*) FROM `" . static::$table . "` {$whereClause} {$soft}";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
}
