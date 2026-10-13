# HYDE PVP — Minecraft Bedrock Community Website

Official community website and competitive hub for **HYDE PVP**, the premier Minecraft: Bedrock Edition PvP server.

Built with **Laravel 13**, **Laravel Breeze**, **Tailwind CSS**, and **Alpine.js**, adhering to the **Option C — Modern Gaming Community** design direction.

---

## 🎮 Features Overview

- **Design Option C (Modern Gaming Community):**
  - Dark charcoal and navy backdrop (`#070A12` / `#0D1424`) with glowing purple and magenta accents inspired by the HYDE PVP logo.
  - White primary typography, muted gray secondary text, and ice-blue highlights.
  - Official HYDE PVP logo integrated throughout navbar, hero, browser favicon, and admin dashboard.
  - Fully responsive on desktop, tablet, and mobile devices.

- **Community Pages:**
  - **Home:** Hero section, Bedrock connection card with instant one-click copy address, server highlights (optimized knockback, duel ladders), latest announcement cards, top verified rankings, and prominent Discord CTA.
  - **News & Announcements:** Paginated post index, detailed readable article layout with publication dates and author badges.
  - **Server Information:** Dedicated Minecraft Bedrock connection details, port, supported client version, and step-by-step setup guides for Mobile (iOS/Android), Windows 10/11, and Consoles (Xbox, PlayStation, Switch via DNS/BedrockConnect).
  - **Official Rules:** Categorized server guidelines (General Conduct, Gameplay & PvP, Chat & Conduct, Clan & Competition, Enforcement & Appeals) and Discord ban appeal ticket directions.
  - **Season Rankings:** Verified player and clan leaderboards with ELO, W/L records, and K/D ratios. Displays an honest empty state when season records have not yet been registered.

- **Role-Based Access Control (RBAC):**
  - **Guest:** Public view access, follows Discord invites, cannot post comments or access administration.
  - **Member:** Can comment on news posts after login. Cannot access admin panel or modify site content. Default role assigned upon registration.
  - **Moderator:** Can access the staff dashboard and moderate comments (hide, unhide, delete). Cannot alter site settings, edit news/rules, or assign user roles.
  - **Admin:** Full access to site settings, news CRUD, server rules CRUD, ranking leaderboards, comment moderation, and user role management.
  - Server-side authorization enforced via middleware (`CheckRole`), route guards, and controllers. Direct HTTP requests cannot bypass RBAC.

- **Anti-Spam & Comment System:**
  - Authenticated members only.
  - **500-word limit:** Enforced on both frontend (live word counter) and backend validation.
  - **60-second cooldown:** Backend rate-limiting per user with exact remaining seconds displayed on early submission.
  - **Network rate-limiting:** Additional IP-based throttle against automated floods.
  - **Anti-bot Honeypot:** Invisible field silently catches automated submissions.
  - **Content Filters:** Blocks duplicate comments, excessive links (>2 URLs), and repetitive character spam.
  - **XSS Protection:** All comment content is escaped before rendering; raw HTML is never trusted.

- **Admin Control Panel (`/admin`):**
  - Real-time dashboard statistics (members, posts, rules, leaderboards, comments).
  - News Management (create, edit, publish/draft, delete).
  - Rules Management (categorized rules with ordering).
  - Leaderboard Management (player and clan ranking entries).
  - Comment Moderation (view all comments, filter hidden, hide/restore, delete).
  - Site Settings (configure server host, port, version, Discord invite link, Discord widget server ID, comment limits).
  - User Role Assignments (search users, update roles, self-demotion safety guard).

---

## 🚀 Getting Started

### 1. Requirements

- **PHP:** 8.3 or higher (PHP 8.5 tested)
- **Extensions:** `pdo`, `pdo_mysql` or `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `curl`
- **Composer:** 2.x
- **Node.js:** 20.x or higher & npm

### 2. Installation

1. Clone or navigate to the repository:
   ```bash
   cd c:\Users\Cirunay\Desktop\laravel\p5
   ```

2. Copy environment configuration:
   ```bash
   copy .env.example .env
   ```

3. Install PHP and JavaScript dependencies:
   ```bash
   composer install
   npm install
   ```

4. Generate application encryption key:
   ```bash
   php artisan key:generate
   ```

5. Configure database in `.env` (MySQL or SQLite):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=p5db
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

6. Run migrations:
   ```bash
   php artisan migrate
   ```

7. Seed initial settings, default rules, welcome announcement, and initial accounts:
   ```bash
   php artisan db:seed
   ```

8. Build frontend production assets:
   ```bash
   npm run build
   ```

9. Start the local development server:
   ```bash
   php artisan serve
   ```
   Access the website at `http://127.0.0.1:8000`.

---

## ⚙️ Environment Variables

Add or modify these variables in `.env` as needed:

| Variable | Default Value | Description |
| :--- | :--- | :--- |
| `BEDROCK_SERVER_ADDRESS` | `play.hydepvp.net` | Host/IP displayed on connect cards |
| `BEDROCK_SERVER_PORT` | `19132` | Minecraft Bedrock port |
| `BEDROCK_SERVER_VERSION` | `v1.21.x (Bedrock)` | Supported Minecraft Bedrock version |
| `DISCORD_INVITE_URL` | `https://discord.gg/hydepvp` | Official Discord invite link |
| `DISCORD_SERVER_ID` | `""` | Optional Discord Server ID for widget embed |

*Note: All settings can also be updated directly in the Admin Panel under **Site Settings** without redeploying.*

---

## 🛡️ Creating & Promoting Administrators

Newly registered accounts are always assigned the `member` role for security.

To promote an account to **Administrator** or **Moderator**, run the dedicated Artisan command from your terminal:

```bash
# Promote an account to Admin
php artisan user:promote admin@hydepvp.net admin

# Promote an account to Moderator
php artisan user:promote staff@hydepvp.net moderator

# Demote an account back to Member
php artisan user:promote player@hydepvp.net member
```

Seeded credentials (created by `php artisan db:seed`):
- **Admin:** `admin@hydepvp.net` / `AdminPassword123!`
- **Moderator:** `moderator@hydepvp.net` / `ModPassword123!`

---

## 💬 Discord Integration & Future Bot Roadmap

All clan wars, UHC tournaments, and event coordination take place on our Discord server. The website links directly to Discord rather than duplicating event registration.

### Current Implementation
- Configurable Discord invite URL stored in `site_settings`.
- Optional official Discord server widget on the Server Info page.
- Zero Discord bot secrets or bot tokens stored in frontend code or git repository.

### Future Discord Bot Architecture
A future Discord bot can integrate with the website via authenticated API endpoints:
1. **API Token Authentication:** The bot connects via `Authorization: Bearer <bot_token>` to protected endpoints (`/api/v1/bot/...`).
2. **Announcement Webhook / Sync:** When an administrator publishes news in the Admin Panel, Laravel dispatches an event that posts an embed to Discord's `#announcements` channel.
3. **Account Linking & Role Sync:** Users can verify their Discord identity by running `/link` with a short-lived token generated on their website profile.

---

## ⛏️ Minecraft Bedrock Account Integration Roadmap

### Current Implementation
- Users can optionally provide a `minecraft_gamertag` on registration or profile settings.
- This field is **explicitly marked as unverified** in the UI to prevent impersonation.
- The website **never** requests Microsoft/Xbox passwords.

### Secure Verification Flow (Future Specification)
To verify ownership of an Xbox gamertag without compromising user credentials:
1. **Generate Claim Code:** The player clicks "Verify Bedrock Account" on the website. The server generates a single-use 6-character alphanumeric code with a 5-minute TTL.
2. **In-Game Verification Command:** On the HYDE Bedrock server, the player executes `/verify <code>`.
3. **Server-Side Validation:** The Bedrock server script verifies the sender's Gamertag and XUID against the website verification API using a secure server-to-server HMAC secret.
4. **Account Linked:** The website marks the gamertag as verified.

### Bedrock Dedicated Server (BDS) Host Limitations
Before deploying in-game verification or leaderboard sync:
- **Scripting API Support:** Requires Minecraft Scripting API module `@minecraft/server-net` to perform outbound HTTP requests (`http.request()`).
- **Server Host Networking:** Some shared Bedrock hosting providers restrict outbound network sockets from server addons for security. If restricted, a lightweight external bridge daemon or websocket listener on the host machine must relay messages between BDS and Laravel.

---

## 🧪 Automated Testing

Run the automated test suite with PHPUnit:

```bash
php vendor/phpunit/phpunit/phpunit
```

### Test Coverage Summary:
- **CommunityPagesTest:** Homepage branding, IP copy data, news index and detail rendering, rules categories, server info guides, rankings empty states.
- **RbacAndAdminTest:** Member role assignment on registration, guest redirect, member 403 Forbidden checks, moderator allowed/forbidden routes, admin full access, `user:promote` command validation, sole admin protection.
- **CommentSystemAndSpamTest:** Guest restriction, valid comment posting, 500-word limit backend enforcement, 60-second cooldown per user, duplicate comment rejection, honeypot detection, excessive link rejection, XSS escaping, moderator toggle and delete actions.
- **Auth & Profile Tests:** Registration, login, logout, password reset, profile update with optional gamertag.

---

## 📄 License

The Laravel framework is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
Website implementation © HYDE PVP. Not approved by or associated with Mojang or Microsoft.
