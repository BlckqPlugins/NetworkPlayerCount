<?php

namespace NetworkPlayerCount\NetworkPlayerCount;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use NetworkPlayerCount\NetworkPlayerCount\tasks\StartTask;

class Main extends PluginBase{

	public static $playerCount = 0;
	public static $isQueryDone = TRUE;

	/**
	 * Function onEnable
	 * @return void
	 */
	public function onEnable(): void{
        @mkdir(Server::getInstance()->getDataPath());
        $this->saveResource("host.yml");
		$config = new Config(Server::getInstance()->getDataPath() . "host.yml", Config::YAML);

		$task = new StartTask($config->get("host"), $config->get("port"));
		$this->getScheduler()->scheduleRepeatingTask($task, 20 * 5);
	}

	/**
	 * Function getTotalNetworkPlayers
	 * @return int
	 */
	public static function getTotalNetworkPlayers(): int
    {
        return (int)Main::$playerCount;
    }
}
