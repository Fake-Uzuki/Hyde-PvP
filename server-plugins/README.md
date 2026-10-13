# HYDE PVP - In-Game Player Verification Integration

This directory contains ready-to-use server-side scripts for your Minecraft Bedrock server to enable the `/verify <code>` command.

---

## How It Works

1. A player logs into the HYDE PVP website and visits their **Account Profile** (`/profile`).
2. They click **"Generate In-Game Code"** and receive a 6-digit PIN (e.g. `849201`).
3. They join your Bedrock server (`hydepvp.ultramc.co:19015`) and run:
   ```text
   /verify 849201
   ```
4. The server contacts `POST /api/server/verify-player` with the code, gamertag, and XUID.
5. The player's website account instantly becomes **Verified**, locking their Bedrock Gamertag and granting the **Verified Player Badge**!

---

## Option 1: PocketMine-MP (PHP Server) — *Recommended for Bedrock PvP*

If your server runs **PocketMine-MP**:
1. Copy the `pocketmine/` directory into your server's `plugins/` folder (or package it as a `.phar`).
2. Open `plugins/HydeVerify/config.yml` (generated on first boot) and set:
   ```yaml
   web_api_url: "https://your-domain.com/api/server/verify-player"
   server_key: "hyde-pvp-secret-key-19015" # Must match HYDE_SERVER_KEY in your .env
   ```
3. Restart or run `/reload` on your server.
4. Players can now use `/verify <code>`.

---

## Option 2: Bedrock Dedicated Server (BDS) Script API

If your server runs official Mojang BDS:
1. Place the `bedrock-script-api/` folder inside your server's `behavior_packs/` folder.
2. In `scripts/main.js`, update `WEB_API_URL` to your website's URL.
3. Enable `Beta APIs` in your world's `world_behavior_packs.json`.
4. Players can now type `/verify <code>` in in-game chat.
