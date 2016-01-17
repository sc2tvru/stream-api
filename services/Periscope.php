<?php
require_once dirname(__FILE__).'/../StreamService.php';

class Periscope extends StreamService {

    const CHECK_STREAM_STATUS_URL = 'http://funstream.tv/player/check/periscope?channels=:channels';

    protected function loadChunkInfo($channels) {
        $raw = $this->loadResource(strtr(self::CHECK_STREAM_STATUS_URL, array(':channels' => join(',', $channels))), array(), 'GET');
        return $raw;
    }

    protected function mapInfo($v) {
        $channel_name = $v['id'];
        $info = array(
            'name' => $channel_name ,
            'id' => $v['id'],
            'service' => StreamApiService::SERVICE_PERISCOPE,
            'live' => $v['live'] == true,
            'thumbnail' => empty($v['thumbnail']) ? null : $v['thumbnail'],
            'title' => $v['title'],
            'viewers' => (int) $v['viewers'],
        );
        return $info;
    }

    protected function decodeChunkInfo($raw) {
        $res = json_decode($raw, true);
        $info = array();
        if($res) {
            foreach($res as $v) {
                $info[] = $this->mapInfo($v);
            }
            return $info;
        }
        return null;
    }

    public function getThumbnail($channel) {
        return null;
    }

    public function getEmbedPlayerCode($channel, $width, $height) {
        return $this->renderTemplate('player/periscope', array('channelName' => $channel['name'], 'width' => $width, 'height' => $height));
    }

}
