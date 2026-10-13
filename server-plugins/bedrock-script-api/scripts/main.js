/**
 * HYDE PVP - Bedrock Server In-Game Account Verification Script
 * For Minecraft Bedrock Dedicated Server (BDS) Scripting API
 * Uses @minecraft/server and @minecraft/server-net
 */

import { world, system } from "@minecraft/server";
import { http, HttpRequest, HttpRequestMethod, HttpHeader } from "@minecraft/server-net";

const WEB_API_URL = "http://YOUR_WEBSITE_DOMAIN/api/server/verify-player";
const SERVER_KEY = "hyde-pvp-secret-key-19015";

// Listen to chat messages for "/verify <code>"
world.beforeEvents.chatSend.subscribe((event) => {
    const message = event.message.trim();
    if (!message.startsWith("/verify")) {
        return;
    }

    event.cancel = true; // Don't broadcast to other players
    const player = event.sender;
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
            const request = new HttpRequest(WEB_API_URL);
            request.setMethod(HttpRequestMethod.Post);
            request.setHeaders([
                new HttpHeader("Content-Type", "application/json"),
                new HttpHeader("Accept", "application/json"),
                new HttpHeader("X-Server-Key", SERVER_KEY),
            ]);
            request.setBody(JSON.stringify({
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
            player.sendMessage("§c[HYDE] Could not connect to the verification server. Please check your website URL.");
        }
    });
});
