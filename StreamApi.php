<?php

require_once 'StreamService.php';
require_once 'services/JustinTv.php';
require_once 'services/Own3DTv.php';
require_once 'services/RegameTv.php';

function getStreamService($sServiceType) {
    switch($sServiceType) {
        case 'justin':
            return new JustinTv();
        case 'own3d':
            return new Own3DTv();
        case 'regame':
            return new RegameTv();
    }
}
