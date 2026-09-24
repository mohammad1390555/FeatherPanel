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

namespace App\Cli;

interface CommandBuilder
{
    /**
     * The description of the command.
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionvar string
     */
    public static function getDescription(): string;

    /**
     * The subcommands of the command.
     */
    public static function getSubCommands(): array;

    /**
     * Execute the command.
     *
     * // // // // // @error suppressionerror suppressionerror suppressionerror suppressionerror suppressionparam array $args the arguments passed to the command
     */
    public static function execute(array $args): void;
}
