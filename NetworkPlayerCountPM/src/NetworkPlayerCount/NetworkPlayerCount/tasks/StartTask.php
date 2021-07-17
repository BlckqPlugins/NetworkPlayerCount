<?php

namespace NetworkPlayerCount\NetworkPlayerCount\tasks;

use NetworkPlayerCount\NetworkPlayerCount\Main;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class StartTask extends Task{

    /** @var string */
    private $host;

    /** @var int */
    private $port;

    public function __construct(string $host, int $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function onRun(): void
    {
        if (Main::$isQueryDone) {
            Main::$isQueryDone = FALSE;
            Server::getInstance()->getAsyncPool()->submitTask(new AsyncQuery($this->host, $this->port));
        }
    }

}