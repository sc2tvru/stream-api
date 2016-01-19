<?php
require_once dirname(__FILE__).'/../StreamService.php';

class Afreeca extends StreamService {

    const CHECK_STREAM_STATUS_URL = NULL;

    protected function loadChunkInfo($channels) {
        $info = array();
        foreach($channels as $channel_name){
            $info[] = array(
                'id' => $channel_name,
                'name' => $channel_name,
                'status' => 'live',
                'thumbnail' => '',
                'titile' => '',
                'viewers' => 0,
            );
        }
        return $info;
    }

    protected function mapInfo($v) {
        $info= array(
            'name' => $v['name'] ,
            'id' => $v['id'],
            'service' => StreamApiService::SERVICE_AFREECA,
            'live' => strtolower($v['status'])=='live'?true:false,
            'thumbnail' => empty($v['thumbnail'])?null:$v['thumbnail'],
            'title' => $v['title'],
            'viewers' => (int) $v['viewers'],
        );
        return $info;
    }

    protected function decodeChunkInfo($raw) {
        $res = $raw;
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
        return $this->renderTemplate('player/afreeca', array('channelName' => $channel['name'], 'width' => $width, 'height' => $height));
    }

}

