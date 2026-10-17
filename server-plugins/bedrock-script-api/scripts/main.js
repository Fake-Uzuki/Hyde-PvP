import {
    world,
    system,
    CustomCommandParamType,
    CustomCommandStatus,
    CommandPermissionLevel
} from "@minecraft/server";

import { http, HttpRequest, HttpRequestMethod, HttpHeader } from "@minecraft/server-net";

const VERIFY_API_URL = "https://hyde-pvp.onrender.com/api/server/verify-player";
const SYNC_RANKINGS_URL = "https://hyde-pvp.onrender.com/api/server/sync-rankings";
const SERVER_KEY = "hyde-pvp-secret-key-19015";
const SYNC_INTERVAL_TICKS = 3600; // 3 minutes

console.warn("[Hyde Verify] Standalone bridge initializing for Bedrock 2.3.0...");

/**
 * Handle account verification request
 */
export async function verifyPlayerAccount(player, code) {
    if (!player) return;

    code = String(code || "").trim();
    if (!code) {
        player.sendMessage("§e[HYDE] Usage: §b/verify <code> §7or §b!verify <code>\n§7Get your 6-digit code from your profile on https://hyde-pvp.onrender.com");
        return;
    }

    const gamertag = player.name;
    const xuid = player.id || "";

    player.sendMessage("§7[HYDE] Contacting website to verify your account...");

    try {
        const request = new HttpRequest(VERIFY_API_URL);
        request.setMethod(HttpRequestMethod.Post);
        request.setHeaders([
            new HttpHeader("Content-Type", "application/json"),
            new HttpHeader("Accept", "application/json"),
            new HttpHeader("X-Server-Key", SERVER_KEY),
        ]);
        request.setBody(JSON.stringify({
            server_key: SERVER_KEY,
            code: code,
            gamertag: gamertag,
            xuid: xuid,
        }));

        const response = await http.request(request);
        if (response.status === 200) {
            let data = {};
            try { data = JSON.parse(response.body); } catch (_) {}
            player.sendMessage("§a========================================");
            player.sendMessage("§a✔ HYDE PVP ACCOUNT VERIFIED!");
            player.sendMessage(`§fLinked to website user: §b${data.user || "Your Account"}`);
            player.sendMessage("§a========================================");
        } else {
            let errorMsg = "Invalid code.";
            try {
                const data = JSON.parse(response.body);
                errorMsg = data.message || errorMsg;
            } catch (_) {}
            player.sendMessage(`§c[HYDE] Verification failed: ${errorMsg}`);
        }
    } catch (err) {
        player.sendMessage("§c[HYDE] Could not connect to verification server. Please check internet connection.");
        console.error(`[Hyde Verify] Verify error: ${err}`);
    }
}

/**
 * Detect scoreboard objectives dynamically
 */
function findScoreboardObjectives(scoreboard) {
    let killsObj = null;
    let pointsObj = null;
    let deathsObj = null;
    let winsObj = null;

    try {
        const objectives = scoreboard.getObjectives();
        for (const obj of objectives) {
            const id = obj.id.toLowerCase();
            if (!killsObj && (id === "kills" || id === "kill" || id.includes("kill"))) {
                killsObj = obj;
            }
            if (!pointsObj && (id === "points" || id === "score" || id === "rating" || id.includes("point") || id.includes("score"))) {
                pointsObj = obj;
            }
            if (!deathsObj && (id === "deaths" || id === "death" || id.includes("death"))) {
                deathsObj = obj;
            }
            if (!winsObj && (id === "wins" || id === "win" || id.includes("win"))) {
                winsObj = obj;
            }
        }
    } catch (e) {
        console.warn(`[Hyde Verify] Error finding scoreboard objectives: ${e}`);
    }

    return { killsObj, pointsObj, deathsObj, winsObj };
}

/**
 * Collect scores from scoreboard and sync with website leaderboards
 */
export async function syncScoreboardToWebsite(sender = null) {
    try {
        const scoreboard = world.scoreboard;
        if (!scoreboard) {
            if (sender) sender.sendMessage("§c[HYDE] Scoreboard system not available.");
            return;
        }

        const { killsObj, pointsObj, deathsObj, winsObj } = findScoreboardObjectives(scoreboard);

        if (sender) {
            const killsName = killsObj ? killsObj.id : "NOT FOUND (create with: /scoreboard objectives add kills dummy)";
            const pointsName = pointsObj ? pointsObj.id : "NOT FOUND (create with: /scoreboard objectives add points dummy)";
            sender.sendMessage(`§7[HYDE] Objectives: Kills=[§b${killsName}§7], Points=[§e${pointsName}§7]`);
        }

        const playersList = [];

        for (const player of world.getAllPlayers()) {
            if (!player || !player.name) continue;

            const name = player.name;
            let kills = 0;
            let points = 0;
            let deaths = 0;
            let wins = 0;

            try {
                if (player.scoreboardIdentity) {
                    if (killsObj) kills = killsObj.getScore(player.scoreboardIdentity) ?? 0;
                    if (pointsObj) points = pointsObj.getScore(player.scoreboardIdentity) ?? 0;
                    if (deathsObj) deaths = deathsObj.getScore(player.scoreboardIdentity) ?? 0;
                    if (winsObj) wins = winsObj.getScore(player.scoreboardIdentity) ?? 0;
                }
            } catch (_) {}

            playersList.push({
                gamertag: name,
                kills: Math.max(0, kills),
                points: Math.max(0, points),
                deaths: Math.max(0, deaths),
                wins: Math.max(0, wins),
            });
        }

        if (playersList.length === 0) {
            if (sender) sender.sendMessage("§e[HYDE] No online players found to sync.");
            return;
        }

        const request = new HttpRequest(SYNC_RANKINGS_URL);
        request.setMethod(HttpRequestMethod.Post);
        request.setHeaders([
            new HttpHeader("Content-Type", "application/json"),
            new HttpHeader("Accept", "application/json"),
            new HttpHeader("X-Server-Key", SERVER_KEY),
        ]);
        request.setBody(JSON.stringify({
            server_key: SERVER_KEY,
            players: playersList,
        }));

        const response = await http.request(request);
        if (response.status === 200) {
            if (sender) {
                sender.sendMessage(`§a[HYDE] ✔ Synchronized ${playersList.length} player(s) to website leaderboards!`);
            }
            console.warn(`[Hyde Verify] Synced ${playersList.length} player(s) to website.`);
        } else {
            if (sender) sender.sendMessage(`§c[HYDE] Sync failed: HTTP ${response.status}`);
        }
    } catch (err) {
        if (sender) sender.sendMessage("§c[HYDE] Error connecting to website API.");
        console.error(`[Hyde Verify] Sync error: ${err}`);
    }
}

// ==========================================
// 1. Custom Command Registration (Bedrock 1.21+)
// ==========================================
try {
    if (system.beforeEvents && system.beforeEvents.startup) {
        system.beforeEvents.startup.subscribe((event) => {
            const registry = event.customCommandRegistry;
            if (!registry) return;

            // Register /verify <code>
            try {
                registry.registerCommand(
                    {
                        name: "verify",
                        description: "Verify your Hyde PvP website profile with 6-digit code",
                        permissionLevel: CommandPermissionLevel.Any,
                        cheatsRequired: false,
                        mandatoryParameters: [
                            {
                                name: "code",
                                type: CustomCommandParamType.String
                            }
                        ]
                    },
                    (origin, code) => {
                        const player = origin.sourceEntity;
                        if (!player) return { status: CustomCommandStatus.Failure };
                        system.run(async () => {
                            await verifyPlayerAccount(player, code);
                        });
                        return { status: CustomCommandStatus.Success };
                    }
                );
            } catch (e) {
                console.warn(`[Hyde Verify] /verify registration: ${e}`);
            }

            // Register /syncrankings
            try {
                registry.registerCommand(
                    {
                        name: "syncrankings",
                        description: "Synchronize in-game scoreboard kills and points to website leaderboards",
                        permissionLevel: CommandPermissionLevel.Operator,
                        cheatsRequired: false
                    },
                    (origin) => {
                        const player = origin.sourceEntity;
                        system.run(async () => {
                            await syncScoreboardToWebsite(player);
                        });
                        return { status: CustomCommandStatus.Success };
                    }
                );
            } catch (e) {
                console.warn(`[Hyde Verify] /syncrankings registration: ${e}`);
            }
        });
    }
} catch (e) {
    console.warn(`[Hyde Verify] Startup event error: ${e}`);
}

// ==========================================
// 2. Vanilla /scriptevent listeners
// ==========================================
system.afterEvents.scriptEventReceive.subscribe((event) => {
    const { id, message, sourceEntity } = event;

    if (id === "hyde:verify" || id === "verify") {
        if (!sourceEntity) return;
        system.run(async () => {
            await verifyPlayerAccount(sourceEntity, message.trim());
        });
        return;
    }

    if (id === "hyde:sync" || id === "hyde:syncrankings") {
        if (sourceEntity) {
            sourceEntity.sendMessage("§e[HYDE] Starting live scoreboard sync with website leaderboards...");
        }
        system.run(async () => {
            await syncScoreboardToWebsite(sourceEntity);
        });
        return;
    }
});

// ==========================================
// 3. In-Game Chat Listeners (!verify and /verify)
// ==========================================
world.beforeEvents.chatSend.subscribe((event) => {
    const raw = event.message.trim();
    const player = event.sender;

    const verifyPrefixes = ["!verify", ".verify", "-verify", "/verify"];
    for (const prefix of verifyPrefixes) {
        if (raw.toLowerCase().startsWith(prefix)) {
            event.cancel = true;
            const parts = raw.split(" ");
            const code = parts.length > 1 ? parts[1].trim() : "";
            system.run(async () => {
                await verifyPlayerAccount(player, code);
            });
            return;
        }
    }

    const syncPrefixes = ["!syncrankings", ".syncrankings", "-syncrankings", "/syncrankings", "!sync", ".sync"];
    for (const prefix of syncPrefixes) {
        if (raw.toLowerCase().startsWith(prefix)) {
            event.cancel = true;
            player.sendMessage("§e[HYDE] Starting live scoreboard sync with website leaderboards...");
            system.run(async () => {
                await syncScoreboardToWebsite(player);
            });
            return;
        }
    }
});

// ==========================================
// 4. Welcome Message on Spawn
// ==========================================
world.afterEvents.playerSpawn.subscribe((event) => {
    if (event.initialSpawn && event.player) {
        system.runTimeout(() => {
            try {
                event.player.sendMessage("§6[HYDE PVP] §7Server bridge connected! Verify account using §b/verify <code> §7or §b!verify <code>");
            } catch (_) {}
        }, 50);
    }
});

// ==========================================
// 5. Periodic Background Sync (Every 3 Mins)
// ==========================================
system.runInterval(() => {
    syncScoreboardToWebsite();
}, SYNC_INTERVAL_TICKS);

console.warn("[Hyde Verify] Standalone pack loaded successfully!");
