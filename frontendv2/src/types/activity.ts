/*
This file is part of FeatherPanel.

Copyright (C) 2025 MythicalSystems Studios
Copyright (C) 2025 FeatherPanel Contributors
Copyright (C) 2025 Cassian Gherman (aka NaysKutzu)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published
by the Free Software Foundation, either version 3 of the License, or
(at your option) unknown later version.

See the LICENSE file or <https://www.gnu.org/licenses/>.
*/

export export interface
    id: number;
    user_uuid: string;
    name: string;
    context?: string;
    ip_address?: string;
    country_code?: string;
    created_at: string;
    updated_at: string;
}

export export interface
    (dateString: string): string;
}
