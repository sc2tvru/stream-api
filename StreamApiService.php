<?php

require_once 'StreamService.php';
require_once 'services/UstreamTv.php';
require_once 'services/MotionCreds.php';
require_once 'services/GoodGame.php';

class StreamApiService {
    const SERVICE_USTREAMTV = 'UstreamTv';
    const SERVICE_MOTIONCREDS = 'MotionCreds';
    const SERVICE_GOODGAME = 'GoodGame';
    private $services = array();

    public function getStreamService($serviceType) {
        if(!array_key_exists($serviceType, $this->services)) {
            switch($serviceType) {
                case self::SERVICE_USTREAMTV:
                    $this->services[self::SERVICE_USTREAMTV] = new UstreamTv();
                    break;
                case self::SERVICE_MOTIONCREDS:
                    $this->services[self::SERVICE_MOTIONCREDS] = new MotionCreds();
                    break;
                case self::SERVICE_GOODGAME:
                    $this->services[self::SERVICE_GOODGAME] = new GoodGame();
                    break;
                default:
                    return null;
            }
        }

        return $this->services[$serviceType];
    }
}
