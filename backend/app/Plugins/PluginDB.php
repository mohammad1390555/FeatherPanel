<?php

/*
 * This file is part of FeatherPanel.
 *
 * Copyright (C) 2025 MythicalSystems Studios
 * Copyright (C) 2025 FeatherPanel Contributors
 * Copyright (C) 2025 Cassian Gherman (aka NaysKutzu)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See the LICENSE file or <https://www.gnu.org/licenses/>.
 */

namespace App\Plugins;

use App\Chat\Database;

/**
 * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressiondeprecated Please use PluginHelper or PluginManager instead!
 */
class PluginDB extends Database
{
    public const PLUGIN_TABLE = 'featherpanel_addons';

    /**
     * Get all the plugins.
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressiondeprecated Please use PluginManager::getLoadedPlugins() instead
     */
    public static function getPlugins(): array
    {
        try {
            $conn = self::getPdoConnection();
            $stmt = $conn->prepare('SELECT * FROM ' . self::PLUGIN_TABLE);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get plugins from database: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Register a new plugin in the database.
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $name The unique name/identifier of the plugin
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $displayName The display name of the plugin
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn bool True if registration successful, false if already exists
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressiondeprecated Use PluginHelper::getPluginConfig() instead
     */
    public static function registerPlugin(string $name, string $displayName): bool
    {

        try {
            $conn = Database::getPdoConnection();

            // Check if plugin already exists
            if (self::isPluginRegistered($name)) {
                return false;
            }

            $stmt = $conn->prepare('
                INSERT INTO featherpanel_addons 
                (name) 
                VALUES (:name)
            ');

            $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
            $result = $stmt->execute();

            if (!$result) {
                self::db_Error('Failed to execute plugin registration query');

                return false;
            }

            return true;
        } catch (\Exception $e) {
            self::db_Error('Failed to register plugin: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Check if a plugin is registered.
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $name The name of the plugin
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn bool True if plugin exists and isn't deleted
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressiondeprecated Use PluginHelper::getPluginConfig() instead
     */
    public static function isPluginRegistered(string $name): bool
    {
        try {
            $conn = Database::getPdoConnection();

            $stmt = $conn->prepare("
                SELECT id 
                FROM featherpanel_addons 
                WHERE name = :name 
                AND deleted = 'false'
                LIMIT 1
            ");

            $stmt->execute([':name' => $name]);

            return (bool) $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to check if plugin is registered: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Enable or disable a plugin.
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $name The name of the plugin
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam bool $enabled Whether to enable or disable the plugin
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressiondeprecated Use PluginHelper::getPluginConfig() instead
     */
    public static function setPluginEnabled(string $name, bool $enabled): void
    {
        try {
            $conn = Database::getPdoConnection();

            $stmt = $conn->prepare('
                UPDATE featherpanel_addons 
                SET enabled = :enabled,
                    date = CURRENT_TIMESTAMP
                WHERE name = :name
            ');

            $stmt->execute([
                ':name' => $name,
                ':enabled' => $enabled ? 'true' : 'false',
            ]);
        } catch (\Exception $e) {
            self::db_Error('Failed to set plugin enabled: ' . $e->getMessage());
        }
    }

    /**
     * Check if a plugin is enabled.
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $name The name of the plugin
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn bool True if plugin is enabled
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressiondeprecated Use PluginHelper::getPluginConfig() instead
     */
    public static function isPluginEnabled(string $name): bool
    {
        try {
            $conn = Database::getPdoConnection();

            $stmt = $conn->prepare("
                SELECT enabled 
                FROM featherpanel_addons 
                WHERE name = :name 
                AND deleted = 'false'
                LIMIT 1
            ");

            $stmt->execute([':name' => $name]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $result && $result['enabled'] === 'true';
        } catch (\Exception $e) {
            self::db_Error('Failed to check if plugin is enabled: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Delete a plugin.
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $name The name of the plugin
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressiondeprecated Use PluginHelper::getPluginConfig() instead
     */
    public static function deletePlugin(string $name): void
    {
        try {
            $conn = Database::getPdoConnection();

            $stmt = $conn->prepare("
                UPDATE featherpanel_addons 
                SET deleted = 'true',
                    date = CURRENT_TIMESTAMP
                WHERE name = :name
            ");

            $stmt->execute([':name' => $name]);
        } catch (\Exception $e) {
            self::db_Error('Failed to delete plugin: ' . $e->getMessage());
        }
    }

    /**
     * Get plugin information.
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $name The name of the plugin
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn array|null Plugin information or null if not found
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressiondeprecated Use PluginHelper::getPluginConfig() instead
     */
    public static function getPluginInfo(string $name): ?array
    {
        try {
            $conn = Database::getPdoConnection();

            $stmt = $conn->prepare("
                SELECT name, enabled, locked 
                FROM featherpanel_addons 
                WHERE name = :name 
                AND deleted = 'false'
                LIMIT 1
            ");

            $stmt->execute([':name' => $name]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $result ?: null;
        } catch (\Exception $e) {
            self::db_Error('Failed to get plugin info: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * List all registered plugins.
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam bool $includeDisabled Whether to include disabled plugins
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn array List of plugins
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressiondeprecated Use PluginHelper::getPluginConfig() instead
     */
    public static function listPlugins(bool $includeDisabled = false): array
    {
        try {
            $conn = Database::getPdoConnection();

            $sql = "
                SELECT name, enabled, locked 
                FROM featherpanel_addons 
                WHERE deleted = 'false'
            ";

            if (!$includeDisabled) {
                $sql .= " AND enabled = 'true'";
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to list plugins: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Convert an ID to a name.
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam int $id The ID to convert
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn string The name
     *
     * // // // // // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressiondeprecated Use PluginHelper::getPluginConfig() instead
     */
    public static function convertIdToName(int $id): string
    {
        try {
            $conn = Database::getPdoConnection();
            $stmt = $conn->prepare('SELECT name FROM featherpanel_addons WHERE id = :id');
            $stmt->execute([':id' => $id]);

            return $stmt->fetch(\PDO::FETCH_ASSOC)['name'];
        } catch (\Exception $e) {
            self::db_Error('Failed to convert ID to name: ' . $e->getMessage());

            return '';
        }
    }
}
