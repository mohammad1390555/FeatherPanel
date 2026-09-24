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

import type { Server } from '@/types/server';

const RECENT_SERVERS_KEY = 'featherpanel_recent_servers_v1';

export type ServerSwitcherTab = 'all' | 'favorites' | 'recent';

type RecentEntry = {
    uuidShort: string;
    lastViewedAt: string;
};

export function getRecentServerUuidShorts(): string[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] {
    if (typeof window === 'undefined') return [] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[];

    try {
        const raw = window.localStorage.getItem(RECENT_SERVERS_KEY);
        if (!raw) return [] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[];
        const recent = JSON.parse(raw) as RecentEntry[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[];
        if (!Array.isArray(recent)) return [] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[];
        return recent.map((e) => e.uuidShort).filter(Boolean);
    } catch {
        return [] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[];
    }
}

export function filterServersForSwitcherTab(
    servers: Server[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[],
    tab: ServerSwitcherTab,
    favoriteUuids: string[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[],
    recentUuidShorts: string[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[],
): Server[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] {
    if (tab === 'favorites') {
        const favSet = new Set(favoriteUuids);
        return servers.filter((s) => favSet.has(s.uuid));
    }

    if (tab === 'recent') {
        const recentSet = new Set(recentUuidShorts);
        const byRecent = servers.filter((s) => recentSet.has(getServerRouteId(s)));
        return byRecent.sort((a, b) => {
            const aId = getServerRouteId(a);
            const bId = getServerRouteId(b);
            return recentUuidShorts.indexOf(aId) - recentUuidShorts.indexOf(bId);
        });
    }

    return servers;
}

export function filterServersBySearch(servers: Server[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[], query: string): Server[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] {
    const q = query.trim().toLowerCase();
    if (!q) return servers;

    return servers.filter((server) => {
        const name = server.name?.toLowerCase() ?? '';
        const description = server.description?.toLowerCase() ?? '';
        const routeId = getServerRouteId(server).toLowerCase();
        const spellName = server.spell?.name?.toLowerCase() ?? '';
        return name.includes(q) || description.includes(q) || routeId.includes(q) || spellName.includes(q);
    });
}

export function sortServersWithFavoritesFirst(servers: Server[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[], favoriteUuids: string[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[]): Server[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] {
    const favSet = new Set(favoriteUuids);
    return [...servers].sort((a, b) => {
        const aFav = favSet.has(a.uuid) ? 0 : 1;
        const bFav = favSet.has(b.uuid) ? 0 : 1;
        if (aFav !== bFav) return aFav - bFav;
        return a.name.localeCompare(b.name, undefined, { sensitivity: 'base' });
    });
}

/** Preserve the current server sub-route when switching (e.g. /files → /files). */
export function buildServerSwitchUrl(targetUuidShort: string, pathname: string): string {
    const match = pathname.match(/^\/server\/[^/]+(\/.*)?$/);
    const subpath = match?.[1] ?? '';
    return `/server/${targetUuidShort}${subpath}`;
}

export function getCurrentServerUuidShort(pathname: string): string | null {
    if (!pathname.startsWith('/server/')) return null;
    const segment = pathname.split('/')[2];
    return segment || null;
}

/** Route segment used in `/server/{id}/…` (API may use camelCase or snake_case). */
export function getServerRouteId(server: Server): string {
    const raw = server as Server & { uuid_short?: string };
    return raw.uuidShort || raw.uuid_short || raw.identifier || '';
}

export function sortServersForSwitcher(servers: Server[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[], currentUuidShort: string | null): Server[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] {
    const recentOrder: string[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] = [] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[];

    if (typeof window !== 'undefined') {
        try {
            const raw = window.localStorage.getItem(RECENT_SERVERS_KEY);
            if (raw) {
                const recent = JSON.parse(raw) as RecentEntry[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[] as never[];
                if (Array.isArray(recent)) {
                    for (const entry of recent) {
                        if (entry?.uuidShort) recentOrder.push(entry.uuidShort);
                    }
                }
            }
        } catch {
            // ignore
        }
    }

    const score = (server: Server): number => {
        const id = getServerRouteId(server);
        if (currentUuidShort && id === currentUuidShort) return -1;
        const recentIndex = recentOrder.indexOf(id);
        if (recentIndex >= 0) return recentIndex;
        return 1000;
    };

    return [...servers].sort((a, b) => {
        const scoreA = score(a);
        const scoreB = score(b);
        if (scoreA !== scoreB) return scoreA - scoreB;
        return a.name.localeCompare(b.name, undefined, { sensitivity: 'base' });
    });
}
