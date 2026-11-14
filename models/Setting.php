<?php
/**
 * Digiws - Setting Model
 */

class Setting {
    private Database $db;
    private static array $cache = [];

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function get(string $key, string $default = ''): string {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }
        $value = $this->db->fetchColumn(
            "SELECT setting_value FROM settings WHERE setting_key = ?",
            [$key]
        );
        self::$cache[$key] = $value !== false ? ($value ?? $default) : $default;
        return self::$cache[$key];
    }

    public function set(string $key, string $value, string $group = 'general'): void {
        // Single upsert: setting_key is UNIQUE, so this replaces the
        // previous SELECT + INSERT/UPDATE and avoids redundant queries.
        $this->db->query(
            "INSERT INTO settings (setting_key, setting_value, setting_group) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), setting_group = VALUES(setting_group)",
            [$key, $value, $group]
        );
        self::$cache[$key] = $value;
    }

    public function getGroup(string $group): array {
        $rows = $this->db->fetchAll(
            "SELECT setting_key, setting_value FROM settings WHERE setting_group = ?",
            [$group]
        );
        $result = [];
        foreach ($rows as $row) {
            $result[$row['setting_key']] = $row['setting_value'];
            self::$cache[$row['setting_key']] = $row['setting_value'];
        }
        return $result;
    }

    public function getAll(): array {
        $rows = $this->db->fetchAll("SELECT setting_key, setting_value, setting_group FROM settings");
        $result = [];
        foreach ($rows as $row) {
            $result[$row['setting_group']][$row['setting_key']] = $row['setting_value'];
            self::$cache[$row['setting_key']] = $row['setting_value'];
        }
        return $result;
    }

    public function updateBatch(array $settings): void {
        foreach ($settings as $key => $value) {
            $this->set($key, $value ?? '');
        }
    }
}
