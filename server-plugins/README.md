# HYDE PVP - In-Game Player Verification & Live Scoreboard Rankings

This directory contains everything you need to connect your Minecraft Bedrock Server (`hydepvp.ultramc.co:19015`) to your live website (`https://hyde-pvp.onrender.com`).

---

## 1. Why `/verify` Didn't Work Initially

Minecraft Bedrock (vanilla) does **not** come with a `/verify` command out of the box. 

When you type `/verify <code>`, Minecraft only recognizes it if the **HYDE PVP Server Bridge** (plugin or behavior pack) is installed in your Minecraft server's files on UltraMC!

---

## 2. Features Included in this Bridge

1. **Player Account Verification (`/verify <code>`)**:
   - Links players' Bedrock Gamertag to their website profile.
   - Grants the **Verified Player Badge** on the leaderboards.
2. **Automatic Scoreboard Rankings Sync**:
   - Reads your in-game scoreboard objectives (`kills`, `points`, `deaths`).
   - Automatically posts scores to the website every **3–5 minutes**.
   - Recalculates ranks (1st, 2nd, 3rd...) live on `/rankings` and the homepage top 5 leaderboard!
3. **Manual Instant Sync Command (`/syncrankings`)**:
   - Server operators/admins can type `/syncrankings` in-game to immediately update website leaderboards!

---

## 3. How to Install on UltraMC

### Option A: Bedrock Dedicated Server (BDS) / Geyser (Behavior Pack) — Recommended
If your UltraMC server runs standard Bedrock:
1. In your UltraMC file manager, navigate to:
   ```text
   /behavior_packs/
   ```
2. Upload the `hyde_verify.mcpack` (or extract `bedrock-script-api/` into `/behavior_packs/hyde_verify/`).
3. Open `world_behavior_packs.json` inside your active world folder and add the pack UUID:
   ```json
   [
     {
       "pack_id": "c3947f61-39bc-4df2-8cb2-87a329c38102",
       "version": [1, 0, 0]
     }
   ]
   ```
4. Restart your server.
5. In game, test `/verify 123456` and `/syncrankings`!

---

### Option B: PocketMine-MP (PHP Bedrock Server)
If your UltraMC server runs PocketMine-MP:
1. In your UltraMC file manager, go to `/plugins/`.
2. Copy or upload the `pocketmine/` folder as `/plugins/HydeVerify/`.
3. Open `/plugins/HydeVerify/config.yml` and ensure:
   ```yaml
   web_api_url: "https://hyde-pvp.onrender.com/api/server/verify-player"
   sync_rankings_url: "https://hyde-pvp.onrender.com/api/server/sync-rankings"
   server_key: "hyde-pvp-secret-key-19015"
   sync_interval_minutes: 5
   ```
4. Run `/reload` or restart your server.
5. In game, test `/verify <code>` and `/syncrankings`!

---

## 4. API Endpoints on Your Live Website

| Method | Endpoint | Description | Auth Header |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/server/verify-player` | Links player gamertag using 6-digit code | `X-Server-Key: hyde-pvp-secret-key-19015` |
| `POST` | `/api/server/sync-rankings` | Batch updates player scoreboard stats & recalculates ranks | `X-Server-Key: hyde-pvp-secret-key-19015` |
| `POST` | `/api/server/update-player-score` | Updates a single player's score live | `X-Server-Key: hyde-pvp-secret-key-19015` |
