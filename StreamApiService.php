<?php

require_once 'StreamService.php';
require_once 'services/CyberGameTv.php';
require_once 'services/HashdTv.php';
require_once 'services/JustinTv.php';
require_once 'services/TwitchTv.php';
require_once 'services/Livestream.php';
require_once 'services/UstreamTv.php';
require_once 'services/MotionCreds.php';
require_once 'services/GoodGame.php';
require_once 'services/YouTube.php';
require_once 'services/YaTv.php';

class StreamApiService {
    const SERVICE_LIVESTREAM = 'Livestream';
    const SERVICE_CYBERGAMETV = 'Cybergame';
    const SERVICE_JUSTINTV = 'Justin.tv';
    const SERVICE_TWITCHTV = 'TwitchTv';
    const SERVICE_YATV = 'YaTV';
    const SERVICE_HASHDTV = 'HashdTv';
    const SERVICE_USTREAMTV = 'UstreamTv';
    const SERVICE_MOTIONCREDS = 'MotionCreds';
    const SERVICE_GOODGAME = 'GoodGame';
    const SERVICE_YOUTUBE = 'YouTube';

    public $services = array();

    private function __construct() {
        $this->services[self::SERVICE_LIVESTREAM] = new Livestream();
        $this->services[self::SERVICE_JUSTINTV] = new JustinTv();
        $this->services[self::SERVICE_TWITCHTV] = new TwitchTv();
        $this->services[self::SERVICE_GOODGAME] = new GoodGame();
        $this->services[self::SERVICE_HASHDTV] = new HashdTv();
        $this->services[self::SERVICE_MOTIONCREDS] = new MotionCreds();
        $this->services[self::SERVICE_USTREAMTV] = new UstreamTv();
        $this->services[self::SERVICE_YATV] = new YaTv();
        $this->services[self::SERVICE_CYBERGAMETV] = new CyberGameTv();
        $this->services[self::SERVICE_YOUTUBE] = new YouTube();
    }

    public static function getInstance() {
        static $instance = null;

        if ($instance === null) {
            $instance = new StreamApiService();
        }

        return $instance;
    }
}
