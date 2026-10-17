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

// Periodic scoreboard sync interval (in Minecraft ticks: 20 ticks = 1 sec, 3600 ticks = 3 minutes)
const SYNC_INTERVAL_TICKS = 3600;

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
            const data = JSON.parse(response.body);
            if (sender) {
                sender.sendMessage(`§a[HYDE] ✔ Synchronized ${playersList.length} player(s) to website leaderboards!`);
            }
        } else {
            if (sender) sender.sendMessage(`§c[HYDE] Sync failed: HTTP ${response.status}`);
        }
    } catch (err) {
        if (sender) sender.sendMessage("§c[HYDE] Error connecting to website API.");
    }
}

// Listen to in-game chat commands
world.beforeEvents.chatSend.subscribe((event) => {
    const message = event.message.trim();
    const player = event.sender;

    // Command: /verify <code>
    if (message.startsWith("/verify")) {
        event.cancel = true;
        const parts = message.split(" ");

        if (parts.length < 2 || !parts[1]) {
            player.sendMessage("§e[HYDE] Usage: §b/verify <code>\n§7Get your code from your profile on our website.");
            return;
        }

        const code = parts[1].trim();
        const gamertag = player.name;
        const xuid = player.id || "";

        player.sendMessage("§7[HYDE] Contacting website to verify your account...");

        system.run(async () => {
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
                    const data = JSON.parse(response.body);
                    player.sendMessage(`§c[HYDE] Verification failed: ${data.message || "Invalid code."}`);
                }
            } catch (err) {
                player.sendMessage("§c[HYDE] Could not connect to verification server. Please verify website URL.");
            }
        });
        return;
    }

    // Command: /syncrankings (Trigger manual scoreboard sync)
    if (message === "/syncrankings" || message.startsWith("/syncrankings ")) {
        event.cancel = true;
        player.sendMessage("§e[HYDE] Starting live scoreboard sync with website leaderboards...");
        system.run(async () => {
            await syncScoreboardsToWebsite(player);
        });
        return;
    }
});

// Periodic background sync every 3 minutes
system.runInterval(() => {
    syncScoreboardsToWebsite();
}, SYNC_INTERVAL_TICKS);
