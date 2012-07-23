<?php

require_once 'StreamService.php';
require_once 'services/UstreamTv.php';
require_once 'services/MotionCreds.php';

class StreamApiService {
    const SERVICE_USTREAMTV = 'UstreamTv';
    const SERVICE_MOTIONCREDS = 'MotionCreds';
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
                default:
                    return null;
            }
        }

        return $this->services[$serviceType];
    }
}
