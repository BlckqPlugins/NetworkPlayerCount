<?php

namespace NetworkPlayerCount\NetworkPlayerCount\tasks;

use NetworkPlayerCount\NetworkPlayerCount\Main;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class AsyncQuery extends AsyncTask{
    /** @var int */
    protected $port;

    /** @var string */
    protected $host;



    /**
     * AsyncQuery constructor.
     * @param int $port
     */
    public function __construct(string $host, int $port){
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * Function onRun
     * @return void
     */
    public function onRun(): void{
        $this->setResult($this->query($this->host, $this->port));
    }

    /**
     * Function onCompletion
     * @return void
     */
    public function onCompletion(): void{
        $result = $this->getResult();

        if ($result){
            Main::$playerCount = $result["num"];
        }

        Main::$isQueryDone = TRUE;

    }

    public static function query(string $host, int $port, int $timeout = 4) {
        $socket = @fsockopen('udp://' . $host, $port, $errno, $errstr, $timeout);
        if($errno or $socket === false) {
            return FALSE;
        }
        stream_Set_Timeout($socket, $timeout);
        stream_Set_Blocking($socket, true);
        $randInt = mt_rand(1, 999999999);
        $reqPacket = "\x01";
        $reqPacket .= pack('Q*', $randInt);
        $reqPacket .= "\x00\xff\xff\x00\xfe\xfe\xfe\xfe\xfd\xfd\xfd\xfd\x12\x34\x56\x78";
        $reqPacket .= pack('Q*', 0);
        fwrite($socket, $reqPacket, strlen($reqPacket));
        $response = fread($socket, 4096);
        fclose($socket);
        if (empty($response) or $response === false) {
            return FALSE;
        }
        if (substr($response, 0, 1) !== "\x1C") {
            return FALSE;
        }
        $serverInfo = substr($response, 35);
        //$serverInfo = preg_replace("#§.#", "", $serverInfo);
        $serverInfo = explode(';', $serverInfo);
        return [
            'motd' => $serverInfo[1],
            'num' => $serverInfo[4] ?? 0,
            'max' => $serverInfo[5] ?? 0,
            'version' =>  $serverInfo[3]
        ];
    }

}