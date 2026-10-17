<?php

declare(strict_types=1);

namespace HydeVerify;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Internet;

class Main extends PluginBase
{
    private string $webApiUrl = "https://hyde-pvp.onrender.com/api/server/verify-player";
    private string $syncRankingsUrl = "https://hyde-pvp.onrender.com/api/server/sync-rankings";
    private string $serverKey = "hyde-pvp-secret-key-19015";
    private string $killsObjective = "kills";
    private string $pointsObjective = "points";
    private string $deathsObjective = "deaths";
    private int $syncIntervalMinutes = 5;

    public function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->webApiUrl = (string) $this->getConfig()->get("web_api_url", $this->webApiUrl);
        $this->syncRankingsUrl = (string) $this->getConfig()->get("sync_rankings_url", $this->syncRankingsUrl);
        $this->serverKey = (string) $this->getConfig()->get("server_key", $this->serverKey);
        $this->killsObjective = (string) $this->getConfig()->get("kills_objective", "kills");
        $this->pointsObjective = (string) $this->getConfig()->get("points_objective", "points");
        $this->deathsObjective = (string) $this->getConfig()->get("deaths_objective", "deaths");
        $this->syncIntervalMinutes = (int) $this->getConfig()->get("sync_interval_minutes", 5);

        // Schedule periodic scoreboard sync (ticks = minutes * 60 * 20)
        $ticks = max(1200, $this->syncIntervalMinutes * 1200);
        $this->getScheduler()->scheduleRepeatingTask(new \pocketmine\scheduler\ClosureTask(function (): void {
            $this->syncScoreboards();
        }), $ticks);

        $this->getLogger()->info("§aHydeVerify & Scoreboard Sync enabled! Ready for player verification and rankings sync.");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($command->getName() === "verify") {
            return $this->handleVerifyCommand($sender, $args);
        }

        if ($command->getName() === "syncrankings") {
            $sender->sendMessage("§e[HYDE] Initiating scoreboard sync with website leaderboards...");
            $this->syncScoreboards($sender);
            return true;
        }

        return false;
    }

    private function handleVerifyCommand(CommandSender $sender, array $args): bool
    {
        if (! ($sender instanceof Player)) {
            $sender->sendMessage("§cThis command can only be run in-game by players.");
            return true;
        }

        if (empty($args[0])) {
            $sender->sendMessage("§e[HYDE] Usage: §b/verify <code>");
            $sender->sendMessage("§7Get your 6-digit verification code from your profile on our website.");
            return true;
        }

        $code = trim($args[0]);
        $gamertag = $sender->getName();
        $xuid = $sender->getXuid();

        $sender->sendMessage("§7[HYDE] Verifying your account with the website...");

        $this->getServer()->getAsyncPool()->submitTask(new class($this->webApiUrl, $this->serverKey, $code, $gamertag, $xuid) extends \pocketmine\scheduler\AsyncTask {
            public function __construct(
                private string $url,
                private string $key,
                private string $code,
                private string $gamertag,
                private string $xuid
            ) {}

            public function onRun(): void
            {
                $payload = json_encode([
                    "server_key" => $this->key,
                    "code" => $this->code,
                    "gamertag" => $this->gamertag,
                    "xuid" => $this->xuid,
                ]);

                $response = Internet::postURL(
                    $this->url,
                    $payload,
                    5,
                    [
                        "Content-Type: application/json",
                        "Accept: application/json",
                        "X-Server-Key: " . $this->key,
                    ]
                );

                $this->setResult($response ? $response->getBody() : null);
            }

            public function onCompletion(): void
            {
                $server = \pocketmine\Server::getInstance();
                $player = $server->getPlayerExact($this->gamertag);
                if ($player === null || ! $player->isOnline()) {
                    return;
                }

                $body = $this->getResult();
                if ($body === null) {
                    $player->sendMessage("§c[HYDE] Could not connect to the website server. Please check your website URL.");
                    return;
                }

                $json = json_decode($body, true);
                if (isset($json["success"]) && $json["success"] === true) {
                    $player->sendMessage("§a========================================");
                    $player->sendMessage("§a✔ HYDE PVP ACCOUNT VERIFIED!");
                    $player->sendMessage("§fLinked to website user: §b" . ($json["user"] ?? "Your Account"));
                    $player->sendMessage("§a========================================");
                } else {
                    $player->sendMessage("§c[HYDE] Verification failed: " . ($json["message"] ?? "Invalid code."));
                }
            }
        });

        return true;
    }

    public function syncScoreboards(?CommandSender $initiator = null): void
    {
        $playersList = [];

        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            $name = $player->getName();
            
            // Collect basic player stats
            $playersList[] = [
                "gamertag" => $name,
                "kills" => 0,
                "points" => 0,
                "deaths" => 0,
            ];
        }

        if (empty($playersList)) {
            if ($initiator) $initiator->sendMessage("§e[HYDE] No players online to sync.");
            return;
        }

        $senderName = $initiator ? $initiator->getName() : null;

        $this->getServer()->getAsyncPool()->submitTask(new class($this->syncRankingsUrl, $this->serverKey, $playersList, $senderName) extends \pocketmine\scheduler\AsyncTask {
            public function __construct(
                private string $url,
                private string $key,
                private array $players,
                private ?string $senderName
            ) {}

            public function onRun(): void
            {
                $payload = json_encode([
                    "server_key" => $this->key,
                    "players" => $this->players,
                ]);

                $response = Internet::postURL(
                    $this->url,
                    $payload,
                    5,
                    [
                        "Content-Type: application/json",
                        "Accept: application/json",
                        "X-Server-Key: " . $this->key,
                    ]
                );

                $this->setResult($response ? $response->getBody() : null);
            }

            public function onCompletion(): void
            {
                $server = \pocketmine\Server::getInstance();
                if ($this->senderName) {
                    $sender = $server->getPlayerExact($this->senderName);
                    if ($sender && $sender->isOnline()) {
                        $body = $this->getResult();
                        if ($body) {
                            $data = json_decode($body, true);
                            $sender->sendMessage("§a[HYDE] ✔ " . ($data["message"] ?? "Rankings synchronized!"));
                        } else {
                            $sender->sendMessage("§c[HYDE] Sync failed to contact website.");
                        }
                    }
                }
            }
        });
    }
}
