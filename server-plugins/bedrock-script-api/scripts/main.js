/**
 * HYDE PVP - Bedrock Server In-Game Account Verification & Live Scoreboard Rankings Sync
 * For Minecraft Bedrock Dedicated Server (BDS) Scripting API
 * Uses @minecraft/server and @minecraft/server-net
 */

import { world, system } from "@minecraft/server";
import { http, HttpRequest, HttpRequestMethod, HttpHeader } from "@minecraft/server-net";

const VERIFY_API_URL = "https://hyde-pvp.onrender.com/api/server/verify-player";
const SYNC_RANKINGS_URL = "https://hyde-pvp.onrender.com/api/server/sync-rankings";
const SERVER_KEY = "hyde-pvp-secret-key-19015";

// Periodic scoreboard sync interval (20 ticks = 1 sec, 3600 ticks = 3 minutes)
const SYNC_INTERVAL_TICKS = 3600;

console.warn("[HYDE PVP] Script API Bridge is initializing...");

/**
 * Perform Player Verification
 */
async function processVerification(player, code) {
    if (!code) {
        player.sendMessage("§e[HYDE] Usage: §b!verify <code> §7or §b/scriptevent hyde:verify <code>\n§7Get your 6-digit code from your profile on our website.");
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
            const data = JSON.parse(response.body);
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
        player.sendMessage("§c[HYDE] Could not connect to verification server. Please check server internet connectivity.");
        console.error(`[HYDE PVP] Verify Error: ${err}`);
    }
}

/**
 * Collect scores from scoreboard objectives and transmit to website
 */
async function syncScoreboardsToWebsite(sender = null) {
    try {
        const scoreboard = world.scoreboard;
        if (!scoreboard) return;

        // Detect common scoreboard objectives used for PvP stats
        const killsObj = scoreboard.getObjective("kills") || scoreboard.getObjective("Kills") || scoreboard.getObjective("kill");
        const pointsObj = scoreboard.getObjective("points") || scoreboard.getObjective("Points") || scoreboard.getObjective("score") || scoreboard.getObjective("Score") || scoreboard.getObjective("rating");
        const deathsObj = scoreboard.getObjective("deaths") || scoreboard.getObjective("Deaths") || scoreboard.getObjective("death");
        const winsObj = scoreboard.getObjective("wins") || scoreboard.getObjective("Wins");

        const playersMap = {};

        // 1. Check all online players
        for (const player of world.getAllPlayers()) {
            const name = player.name;
            if (!name) continue;

            const kills = killsObj ? (killsObj.getScore(player.scoreboardIdentity) ?? 0) : 0;
            const points = pointsObj ? (pointsObj.getScore(player.scoreboardIdentity) ?? 0) : 0;
            const deaths = deathsObj ? (deathsObj.getScore(player.scoreboardIdentity) ?? 0) : 0;
            const wins = winsObj ? (winsObj.getScore(player.scoreboardIdentity) ?? 0) : 0;

            playersMap[name] = {
                gamertag: name,
                kills: Math.max(0, kills),
                points: Math.max(0, points),
                deaths: Math.max(0, deaths),
                wins: Math.max(0, wins),
            };
        }

        const playersList = Object.values(playersMap);
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
            console.warn(`[HYDE PVP] Successfully synced ${playersList.length} player(s) to website.`);
        } else {
            if (sender) sender.sendMessage(`§c[HYDE] Sync failed: HTTP ${response.status}`);
        }
    } catch (err) {
        if (sender) sender.sendMessage("§c[HYDE] Error connecting to website API.");
        console.error(`[HYDE PVP] Sync Error: ${err}`);
    }
}

/**
 * Listen to vanilla /scriptevent commands
 * Vanilla Bedrock natively supports /scriptevent with zero client conflicts:
 * Usage:
 *   /scriptevent hyde:verify 123456
 *   /scriptevent hyde:sync
 */
system.afterEvents.scriptEventReceive.subscribe((event) => {
    const { id, message, sourceEntity } = event;

    if (id === "hyde:verify") {
        if (!sourceEntity || sourceEntity.typeId !== "minecraft:player") return;
        system.run(async () => {
            await processVerification(sourceEntity, message.trim());
        });
        return;
    }

    if (id === "hyde:sync" || id === "hyde:syncrankings") {
        if (sourceEntity && sourceEntity.typeId === "minecraft:player") {
            sourceEntity.sendMessage("§e[HYDE] Starting live scoreboard sync with website leaderboards...");
        }
        system.run(async () => {
            await syncScoreboardsToWebsite(sourceEntity);
        });
        return;
    }
});

/**
 * Listen to chat commands
 * Supports:
 *   !verify <code>  or  .verify <code>  or  /verify <code>
 *   !syncrankings   or  .syncrankings   or  /syncrankings
 */
world.beforeEvents.chatSend.subscribe((event) => {
    const raw = event.message.trim();
    const player = event.sender;

    // Check for verify command
    const verifyPrefixes = ["!verify", ".verify", "-verify", "/verify"];
    for (const prefix of verifyPrefixes) {
        if (raw.toLowerCase().startsWith(prefix)) {
            event.cancel = true; // prevent chat from being broadcast
            const parts = raw.split(" ");
            const code = parts.length > 1 ? parts[1].trim() : "";
            system.run(async () => {
                await processVerification(player, code);
            });
            return;
        }
    }

    // Check for sync command
    const syncPrefixes = ["!syncrankings", ".syncrankings", "-syncrankings", "/syncrankings", "!sync", ".sync"];
    for (const prefix of syncPrefixes) {
        if (raw.toLowerCase().startsWith(prefix)) {
            event.cancel = true;
            player.sendMessage("§e[HYDE] Starting live scoreboard sync with website leaderboards...");
            system.run(async () => {
                await syncScoreboardsToWebsite(player);
            });
            return;
        }
    }
});

/**
 * Notify player on spawn that the bridge is running
 */
world.afterEvents.playerSpawn.subscribe((event) => {
    if (event.initialSpawn && event.player) {
        system.runTimeout(() => {
            try {
                event.player.sendMessage("§6[HYDE PVP] §7Server bridge connected! Verify account using §b!verify <code>");
            } catch (_) {}
        }, 40); // 2 seconds after spawn
    }
});

// Periodic background sync every 3 minutes
system.runInterval(() => {
    syncScoreboardsToWebsite();
}, SYNC_INTERVAL_TICKS);

console.warn("[HYDE PVP] Script API Bridge loaded and running!");
