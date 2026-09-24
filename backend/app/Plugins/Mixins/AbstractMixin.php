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

namespace App\Plugins\Mixins;

use App\App;

/**
 * Abstract base class for plugin mixins.
 *
 * This class provides common functionality for mixins and implements
 * the basic methods required by the AppMixin interface.
 */
abstract class AbstractMixin implements AppMixin
{
    /** // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionvar string The plugin identifier that this mixin is attached to */
    protected string $pluginIdentifier = '';

    /** // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionvar array Configuration for this mixin */
    protected array $config = [];

    protected $logger;

    public function initialize(string $pluginIdentifier, array $config = []): void
    {
        $this->pluginIdentifier = $pluginIdentifier;
        $this->config = $config;
        $this->logger = App::getInstance(true)->getLogger();

        $this->onInitialize();
    }

    /**
     * Get the plugin identifier.
     *
     * // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn string The plugin identifier
     */
    public function getPluginIdentifier(): string
    {
        return $this->pluginIdentifier;
    }

    /**
     * Get the mixin configuration.
     *
     * // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn array The mixin configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get a configuration value.
     *
     * // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $key The configuration key
     * // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam mixed $default Default value if key is not found
     *
     * // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionreturn mixed The configuration value or default
     */
    public function getConfigValue(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Hook method called after initialization.
     *
     * Override this method in your mixin to perform additional initialization.
     */
    protected function onInitialize(): void
    {
        // Default implementation does nothing
    }

    /**
     * Log a debug message.
     *
     * // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $message The message to log
     * // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam array $context Additional context data
     */
    protected function debug(string $message, array $context = []): void
    {
        $mixinId = static::getMixinIdentifier();
        $this->logger->debug("[Mixin:{$mixinId}] {$message} " . json_encode($context));
    }

    /**
     * Log an error message.
     *
     * // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam string $message The message to log
     * // // // // // // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam array $context Additional context data
     */
    protected function error(string $message, array $context = []): void
    {
        $mixinId = static::getMixinIdentifier();
        $this->logger->error("[Mixin:{$mixinId}] {$message} " . json_encode($context));
    }
}
