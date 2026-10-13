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
    /** Change this to your live website domain (e.g. https://hydepvp.com or your server IP) */
    private string $webApiUrl = "http://localhost:8000/api/server/verify-player";

    /** Must match HYDE_SERVER_KEY in your website .env */
    private string $serverKey = "hyde-pvp-secret-key-19015";

    public function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->webApiUrl = $this->getConfig()->get("web_api_url", $this->webApiUrl);
        $this->serverKey = $this->getConfig()->get("server_key", $this->serverKey);
        $this->getLogger()->info("§aHydeVerify enabled! In-game verification system ready.");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($command->getName() !== "verify") {
            return false;
        }

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

        // Dispatch async HTTP request to Laravel API
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
                    $player->sendMessage("§c[HYDE] Could not connect to the website server. Please try again later.");
                    return;
                }

                $json = json_decode($body, true);
                if (isset($json["success"]) && $json["success"] === true) {
                    $player->sendMessage("§a========================================");
                    $player->sendMessage("§a✔ HYDE PVP ACCOUNT VERIFIED!");
                    $player->sendMessage("§fLinked to website user: §b" . ($json["user"] ?? "Your Account"));
                    $player->sendMessage("§7Your leaderboards and combat stats are now linked.");
                    $player->sendMessage("§a========================================");
                } else {
                    $error = $json["message"] ?? "Invalid code. Please generate a new code on the website.";
                    $player->sendMessage("§c[HYDE] Verification failed: " . $error);
                }
            }
        });

        return true;
    }
}
