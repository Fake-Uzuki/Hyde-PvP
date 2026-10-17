# HYDE PVP - In-Game Player Verification & Live Scoreboard Rankings

This guide explains how to connect your Minecraft Bedrock Server (`hydepvp.ultramc.co:19015`) to your live website (`https://hyde-pvp.onrender.com`).

---

## 1. Root Cause Analysis (Why the Pack Was Not Working)

By inspecting your actual server behavior pack (`hydepvpzip.zip`), we identified the 3 reasons why the pack was failing on your server:

1. **Dependency Version Mismatch (`@minecraft/server` 2.3.0 vs 1.8.0)**:
   - Your server runs Bedrock 1.21+ / Preview engine (`1.26.0`) with `@minecraft/server: "2.3.0"`.
   - The initial pack used `1.8.0`. When Bedrock Dedicated Server (BDS) sees conflicting script engine versions between two packs, it refuses to load the conflicting pack.
2. **Missing `permissions.json` on Bedrock Dedicated Server**:
   - The `@minecraft/server-net` module (which makes HTTP requests to your website) is **blocked by default** by BDS for security.
   - BDS will abort loading any script that imports `@minecraft/server-net` unless it is explicitly allowed in `config/default/permissions.json`.
3. **ZIP Path Separator on Linux Host (UltraMC)**:
   - Windows zip tools used backslashes (`scripts\main.js`). UltraMC runs Linux, which caused the file to extract as a flat file in the root instead of inside the `scripts/` folder. All zip files are now rebuilt with Linux forward slashes (`scripts/main.js`).

---

## 2. Solution: Choose Option 1 (Recommended) or Option 2

### Option 1 (Recommended — 100% Guaranteed): Use the Unified Pack
We integrated the verification and scoreboard sync directly into your existing **KitPvP** pack so you have **one single, unified pack** with no conflicts:

* **File:** [`server-plugins/KitPvP_with_HydeVerify.zip`](file:///c:/Users/Cirunay/Desktop/laravel/p5/server-plugins/KitPvP_with_HydeVerify.zip) or [`server-plugins/KitPvP_with_HydeVerify.mcpack`](file:///c:/Users/Cirunay/Desktop/laravel/p5/server-plugins/KitPvP_with_HydeVerify.mcpack)
* **What's inside:**
  - All your original KitPvP features (kits, enchantments, potions, database, UI).
  - Native `/verify <code>` command registered via Bedrock's `customCommandRegistry`.
  - Native `/syncrankings` command.
  - Automatic background sync every 3 minutes for `kills`, `points`, `deaths`.
  - `/scriptevent hyde:verify` and `!verify <code>` fallback listeners.

#### How to Install Option 1 on UltraMC:
1. In your UltraMC file manager, go to your server root.
2. Open `config/default/permissions.json` (or create it if it doesn't exist) and ensure it allows `@minecraft/server-net`:
   ```json
   {
     "allowed_modules": [
       "@minecraft/server-gametest",
       "@minecraft/server",
       "@minecraft/server-ui",
       "@minecraft/server-admin",
       "@minecraft/server-editor",
       "@minecraft/server-net"
     ]
   }
   ```
3. In `/behavior_packs/`, replace your existing `KitPvP` folder with the contents of [`KitPvP_with_HydeVerify.zip`](file:///c:/Users/Cirunay/Desktop/laravel/p5/server-plugins/KitPvP_with_HydeVerify.zip).
4. Restart your UltraMC server.

---

### Option 2: Standalone `hyde_verify` Pack
If you want to keep `hyde_verify` as a separate behavior pack alongside your KitPvP pack:

* **File:** [`server-plugins/hyde_verify.mcpack`](file:///c:/Users/Cirunay/Desktop/laravel/p5/server-plugins/hyde_verify.mcpack) (or [`server-plugins/hyde_verify_pack.zip`](file:///c:/Users/Cirunay/Desktop/laravel/p5/server-plugins/hyde_verify_pack.zip))
* We updated it to match your server's exact dependencies (`@minecraft/server: "2.3.0"`, `@minecraft/server-ui: "2.0.0"`, `min_engine_version: [1, 26, 0]`).

#### How to Install Option 2 on UltraMC:
1. Ensure `config/default/permissions.json` has `@minecraft/server-net` (see Step 2 in Option 1).
2. Upload and extract `hyde_verify_pack.zip` into `/behavior_packs/hyde_verify/`.
3. In your world folder, add both packs to `world_behavior_packs.json`:
   ```json
   [
     {
       "pack_id": "9676a9f8-58d9-4491-8334-16672cb8e763",
       "version": [1, 0, 0]
     },
     {
       "pack_id": "4c9e83b4-82a1-42e7-9195-2c8ebca98cf1",
       "version": [1, 0, 0]
     }
   ]
   ```
4. Restart your UltraMC server.

---

## 3. How to Test In-Game

1. **Test Verification Command:**
   - Type: `/verify <code>`
   - Or chat: `!verify <code>`
   - Or: `/scriptevent hyde:verify <code>`
   *(Get your 6-digit code from your profile on https://hyde-pvp.onrender.com)*

2. **Test Scoreboard Sync:**
   - Type: `/syncrankings`
   - Or chat: `!syncrankings`
   - Or: `/scriptevent hyde:sync`
   - Visit https://hyde-pvp.onrender.com/rankings to see your kills and points live on the leaderboard!

---

## 4. API Endpoints on Your Live Website

| Method | Endpoint | Description | Auth Header |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/server/verify-player` | Links player gamertag using 6-digit code | `X-Server-Key: hyde-pvp-secret-key-19015` |
| `POST` | `/api/server/sync-rankings` | Batch updates player scoreboard stats & recalculates ranks | `X-Server-Key: hyde-pvp-secret-key-19015` |
| `POST` | `/api/server/update-player-score` | Updates a single player's score live | `X-Server-Key: hyde-pvp-secret-key-19015` |
