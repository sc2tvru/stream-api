<?php

require_once 'StreamService.php';
require_once 'services/UstreamTv.php';

class StreamApiService {
    const SERVICE_USTREAMTV = 'UstreamTv';
    private $services = array();

    public function getStreamService($serviceType) {
        if(!array_key_exists($serviceType, $this->services)) {
            switch($serviceType) {
                case self::SERVICE_USTREAMTV:
                    $this->services[self::SERVICE_USTREAMTV] = new UstreamTv();
                    break;
                default:
                    return null;
            }
        }

        return $this->services[$serviceType];
    }
}
