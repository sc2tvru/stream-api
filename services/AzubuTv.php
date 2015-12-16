<?php
require_once dirname(__FILE__).'/../StreamService.php';

class AzubuTv extends StreamService {

    const CHECK_STREAM_STATUS_URL = 'http://api.azubu.tv/public/channel/list';

    protected function loadChunkInfo($channels) {
        $raw = $this->loadResource(self::CHECK_STREAM_STATUS_URL, array('channels'=>$channels), 'GET');
        return $raw;
    }

    protected function mapInfo($v) {
        $info= array(
            'name' => $v['user']['username'] ,
            'id' => $v['user']['id'],
            'service' => StreamApiService::SERVICE_AZUBUTV,
            'live' => ($v['is_live'] == true),
            'thumbnail' => $v['url_thumbnail'],
            'title' => $v['title'],
            'viewers' => (int) $v['view_count'],
        );
        return $info;
    }

    protected function decodeChunkInfo($raw) {
        $res = json_decode($raw, true);
        $info = array();
        if($res) {
            if(isset($res['data']) && is_array($res['data'])) {
                foreach ($res['data'] as $v) {
                    $info[] = $this->mapInfo($v);
                }
            }
            return $info;
        }
        return null;
    }

    public function getThumbnail($channel) {
        return null;
    }

    public function getEmbedPlayerCode($channel, $width, $height) {
        return $this->renderTemplate('player/azubutv', array('channelName' => $channel['name'], 'width' => $width, 'height' => $height));
    }

}

