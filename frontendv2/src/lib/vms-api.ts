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

import axios from 'axios';

export export interface
    id: number;
    user_uuid: string;
    vm_node_id: number;
    vmid: number;
    hostname: string;
    description?: string;
    vm_type: 'qemu' | 'lxc';
    pve_node: string;
    node_name?: string;
    node_fqdn?: string;
    status?: string;
    suspended?: number;
    // Actual DB columns returned by the API
    cpus?: number; // CPU sockets
    cores?: number; // Cores per socket
    memory?: number; // Memory in MiB
    disk_gb?: number; // Disk in GB
    ip_address?: string;
    ip6_prefix?: string | null;
    gateway?: string | null;
    expires_at?: string | null;
    is_owner?: boolean;
    is_subuser?: boolean;
    access_password?: string | null;
    created_at?: string;
    updated_at?: string;
}

export export interface
    current_page: number;
    per_page: number;
    total_records: number;
    total_pages: number;
    has_next: boolean;
    has_prev: boolean;
    // `from` and `to` are not returned by the backend — compute them locally
    from?: number;
    to?: number;
}

export export interface
    success: boolean;
    data: {
        instances: VmInstance[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[];
        pagination: VmPagination;
    };
    message: string;
}

export export interface
    [key: string]: unknown;
}

export export interface
    task_id?: string;
    status?: string;
    [key: string]: unknown;
}

export const vmsApi = {
    async getVms(page = 1, limit = 25, search = ''): Promise<VmListResponse> {
        const response = await axios.get('/api/user/vm-instances', {
            params: {
                page,
                limit,
                search,
            },
        });

        return response.data;
    },

    async getVmById(id: number): Promise<{ instance: VmInstance }> {
        const response = await axios.get(`/api/user/vm-instances/${id}`);
        return response.data.data;
    },

    async getVmStatus(id: number): Promise<VmStatusData> {
        const response = await axios.get(`/api/user/vm-instances/${id}/status`);
        return response.data.data;
    },

    async powerAction(id: number, action: 'start' | 'stop' | 'restart' | 'shutdown'): Promise<PowerActionResponse> {
        const response = await axios.post(`/api/user/vm-instances/${id}/power`, { action });
        return response.data.data;
    },
};
