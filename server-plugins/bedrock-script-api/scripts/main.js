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

console.warn("[Hyde Verify] Standalone scoreboard & verification bridge initialized for Bedrock 2.3.0...");

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
 * Detect scoreboard objectives dynamically from all objectives registered on server
 */
function findScoreboardObjectives(scoreboard, customKills = null, customPoints = null) {
    let killsObj = null;
    let pointsObj = null;
    let deathsObj = null;
    let winsObj = null;

    try {
        if (customKills) killsObj = scoreboard.getObjective(customKills);
        if (customPoints) pointsObj = scoreboard.getObjective(customPoints);

        const objectives = scoreboard.getObjectives();
        for (const obj of objectives) {
            const id = obj.id.toLowerCase();
            const dName = (obj.displayName || "").toLowerCase();

            // Match Kills
            if (!killsObj) {
                if (id === "kills" || id === "kill" || dName === "kills" || dName === "kill" || id.includes("kill") || dName.includes("kill")) {
                    killsObj = obj;
                }
            }

            // Match Deaths
            if (!deathsObj) {
                if (id === "deaths" || id === "death" || dName === "deaths" || dName === "death" || id.includes("death") || dName.includes("death")) {
                    deathsObj = obj;
                }
            }

            // Match Points / Score / Rating / Streak / Money
            if (!pointsObj) {
                if (id === "points" || id === "score" || id === "rating" || id.includes("point") || id.includes("score") || dName.includes("point") || dName.includes("score") || id.includes("streak") || id.includes("coin") || id.includes("money")) {
                    pointsObj = obj;
                }
            }

            // Match Wins
            if (!winsObj) {
                if (id === "wins" || id === "win" || dName.includes("win")) {
                    winsObj = obj;
                }
            }
        }

        // Fallback: If points objective still not identified, pick first objective that isn't kills or deaths
        if (!pointsObj) {
            for (const obj of objectives) {
                if (obj !== killsObj && obj !== deathsObj) {
                    pointsObj = obj;
                    break;
                }
            }
        }
    } catch (e) {
        console.warn(`[Hyde Verify] Error discovering scoreboard objectives: ${e}`);
    }

    return { killsObj, pointsObj, deathsObj, winsObj };
}

/**
 * Collect scores from scoreboard (BOTH online and offline participants) and sync with website
 */
export async function syncScoreboardToWebsite(sender = null, customKills = null, customPoints = null) {
    try {
        const scoreboard = world.scoreboard;
        if (!scoreboard) {
            if (sender) sender.sendMessage("§c[HYDE] Scoreboard system not available.");
            return;
        }

        const allObjectives = scoreboard.getObjectives();
        const objListStr = allObjectives.map(o => o.id).join(", ") || "none";

        const { killsObj, pointsObj, deathsObj } = findScoreboardObjectives(scoreboard, customKills, customPoints);

        if (sender) {
            const kName = killsObj ? killsObj.id : "NOT FOUND";
            const pName = pointsObj ? pointsObj.id : "NOT FOUND";
            const dName = deathsObj ? deathsObj.id : "NOT FOUND";
            sender.sendMessage(`§7[HYDE] Server objectives found: [§f${objListStr}§7]`);
            sender.sendMessage(`§7[HYDE] Target mapping: Kills=[§b${kName}§7], Points=[§e${pName}§7], Deaths=[§c${dName}§7]`);
        }

        const playersMap = {};

        function ensurePlayer(gamertag) {
            if (!gamertag || typeof gamertag !== "string") return null;
            const clean = gamertag.trim();
            // Filter out dummy/fake participants starting with !, #, $, etc.
            if (!clean || clean.startsWith("!") || clean.startsWith("#") || clean.startsWith("$") || clean.startsWith("%")) return null;
            if (!playersMap[clean]) {
                playersMap[clean] = {
                    gamertag: clean,
                    kills: 0,
                    points: 0,
                    deaths: 0,
                    wins: 0,
                    losses: 0,
                };
            }
            return playersMap[clean];
        }

        // 1. Ingest ALL players from Kills objective (including offline players like BDLxREKTTTT, etc.)
        if (killsObj) {
            try {
                const killScores = killsObj.getScores();
                for (const entry of killScores) {
                    const name = entry.participant ? entry.participant.displayName : null;
                    const p = ensurePlayer(name);
                    if (p) {
                        p.kills = Math.max(0, Number(entry.score) || 0);
                    }
                }
            } catch (e) {
                console.warn(`[Hyde Verify] Error reading kill scores: ${e}`);
            }
        }

        // 2. Ingest ALL players from Points objective (including offline players)
        if (pointsObj) {
            try {
                const pointScores = pointsObj.getScores();
                for (const entry of pointScores) {
                    const name = entry.participant ? entry.participant.displayName : null;
                    const p = ensurePlayer(name);
                    if (p) {
                        p.points = Math.max(0, Number(entry.score) || 0);
                    }
                }
            } catch (e) {
                console.warn(`[Hyde Verify] Error reading point scores: ${e}`);
            }
        }

        // 3. Ingest ALL players from Deaths objective (including offline players)
        if (deathsObj) {
            try {
                const deathScores = deathsObj.getScores();
                for (const entry of deathScores) {
                    const name = entry.participant ? entry.participant.displayName : null;
                    const p = ensurePlayer(name);
                    if (p) {
                        p.deaths = Math.max(0, Number(entry.score) || 0);
                    }
                }
            } catch (e) {
                console.warn(`[Hyde Verify] Error reading death scores: ${e}`);
            }
        }

        // 4. Ingest currently online players (captures anyone newly joined with 0 stats)
        for (const player of world.getAllPlayers()) {
            if (!player || !player.name) continue;
            const p = ensurePlayer(player.name);
            if (p && player.scoreboardIdentity) {
                try {
                    if (killsObj && p.kills === 0) p.kills = Math.max(0, killsObj.getScore(player.scoreboardIdentity) ?? 0);
                    if (pointsObj && p.points === 0) p.points = Math.max(0, pointsObj.getScore(player.scoreboardIdentity) ?? 0);
                    if (deathsObj && p.deaths === 0) p.deaths = Math.max(0, deathsObj.getScore(player.scoreboardIdentity) ?? 0);
                } catch (_) {}
            }
        }

        const playersList = Object.values(playersMap);

        if (playersList.length === 0) {
            if (sender) sender.sendMessage("§e[HYDE] No player scores found on the scoreboard to sync.");
            return;
        }

        if (sender) {
            sender.sendMessage(`§7[HYDE] Syncing §a${playersList.length} total player records§7 (online & offline) to website...`);
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
                sender.sendMessage(`§a[HYDE] ✔ Successfully synchronized ${playersList.length} player(s) to website leaderboards!`);
            }
            console.warn(`[Hyde Verify] Synced ${playersList.length} total players to website.`);
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
        const parts = (message || "").trim().split(" ");
        const kObj = parts[0] || null;
        const pObj = parts[1] || null;
        system.run(async () => {
            await syncScoreboardToWebsite(sourceEntity, kObj, pObj);
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
            const parts = raw.split(" ");
            const kObj = parts.length > 1 ? parts[1].trim() : null;
            const pObj = parts.length > 2 ? parts[2].trim() : null;
            system.run(async () => {
                await syncScoreboardToWebsite(player, kObj, pObj);
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
