# HYDE PVP - In-Game Player Verification & Scoreboard Sync Pack

This behavior pack connects your Minecraft Bedrock Server (`hydepvp.ultramc.co:19015`) to your live website (`https://hyde-pvp.onrender.com`).

> [!NOTE]
> **Your existing `KitPvP` pack is NOT touched.**  
> `hyde_verify` is a completely independent, standalone behavior pack designed to run smoothly alongside your existing packs.

---

## 1. What This Pack Does

1. **Player Account Verification (`/verify <code>`)**:
   - Links players' Bedrock Gamertags to their website profile.
   - Grants the **Verified Player** badge on the leaderboards.
   - Works via native command `/verify <code>`, chat command `!verify <code>`, or `/scriptevent hyde:verify <code>`.

2. **Automatic Scoreboard Rankings Sync**:
   - Automatically detects in-game scoreboard objectives (`kills`, `points`, `score`, `rating`, `deaths`).
   - Syncs online players' scores to the website every **3 minutes** in the background.
   - Instantly updates the **Top Points** and **Top Kills** leaderboards on the website!

3. **Manual Admin Sync Command (`/syncrankings` or `!syncrankings`)**:
   - Server operators can type `/syncrankings` (or `!syncrankings`) to force an immediate sync.
   - Shows which objectives were found (e.g. `Kills=[kills], Points=[points]`).

---

## 2. Step-by-Step Installation on UltraMC

### Step 1: Whitelist `@minecraft/server-net` in permissions.json
Bedrock Dedicated Server (BDS) blocks network requests by default. You must allow it:
1. In your UltraMC file manager, check if the folder `config/default/` exists. (If not, create the folders `config/` and then `default/` inside your server root).
2. Inside `config/default/`, open or create `permissions.json` and paste:
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
   *(A ready-to-use file is in [`server-plugins/bds-config/config/default/permissions.json`](file:///c:/Users/Cirunay/Desktop/laravel/p5/server-plugins/bds-config/config/default/permissions.json))*

---

### Step 2: Upload the `hyde_verify` Pack
1. In your UltraMC file manager, open `/behavior_packs/`.
2. Upload and extract [`server-plugins/hyde_verify_pack.zip`](file:///c:/Users/Cirunay/Desktop/laravel/p5/server-plugins/hyde_verify_pack.zip) into a folder named `/behavior_packs/hyde_verify/`.
   *(Ensure inside `/behavior_packs/hyde_verify/` you see `manifest.json`, `pack_icon.png`, and the `scripts/` folder).*

---

### Step 3: Register in `world_behavior_packs.json`
1. Go into your active world folder (usually `worlds/Bedrock level/` or your world name).
2. Open `world_behavior_packs.json` and add `hyde_verify` alongside your existing packs:
   ```json
   [
     {
       "pack_id": "4c9e83b4-82a1-42e7-9195-2c8ebca98cf1",
       "version": [1, 0, 0]
     }
   ]
   ```
   *(Keep any existing packs in the list; just make sure this object is included).*

---

### Step 4: Restart Your Server
Restart the server on the UltraMC panel.

---

## 3. Scoreboard Objectives Setup (In-Game)

The pack automatically reads any scoreboard objective named:
- **Kills**: `kills`, `kill`, `Kills`
- **Points**: `points`, `score`, `rating`, `Points`

If you haven't created your scoreboard objectives yet, run these in-game or from server console:
```text
/scoreboard objectives add kills dummy "Kills"
/scoreboard objectives add points dummy "Points"
```
To show points on the sidebar (optional):
```text
/scoreboard objectives setdisplay sidebar points
```

---

## 4. How to Test In-Game

1. **Test Verification:**
   - Go to [hyde-pvp.onrender.com/profile](https://hyde-pvp.onrender.com/profile) and generate a 6-digit code.
   - In-game, type:
     ```text
     /verify 123456
     ```
     *(or chat: `!verify 123456` or `/scriptevent hyde:verify 123456`)*
   - The chat will print: `✔ HYDE PVP ACCOUNT VERIFIED!`

2. **Test Scoreboard Sync:**
   - In-game, type:
     ```text
     /syncrankings
     ```
     *(or chat: `!syncrankings` or `/scriptevent hyde:sync`)*
   - The chat will report the detected objectives and confirmed sync!
   - Visit [hyde-pvp.onrender.com/rankings](https://hyde-pvp.onrender.com/rankings) or the Homepage to see your Kills and Points live!
