<?php
require_once dirname(__FILE__).'/../StreamService.php';

class JustinTv extends StreamService {
    
	const CHECK_STREAM_STATUS_URL = 'http://api.justin.tv/api/stream/list.json?channel=:channels';
    const THUMBNAIL_IMAGE_URL = 'http://static-cdn.jtvnw.net/previews/live_user_:channel_name-320x240.jpg';

    protected function loadChunkInfo($channels) {
		$raw = $this->loadResource(strtr(self::CHECK_STREAM_STATUS_URL, array(':channels' => join(',', $channels))), array('limit'=>100), 'GET');
		return $raw;
	}
	
	protected function mapInfo($v) {
		$channel_name = $v['channel']['login'];
		$info= array(
			'name' => $channel_name ,
			'id' => (int)$v['channel']['id'],
			'service' => StreamApiService::SERVICE_JUSTINTV,
			'live' => $v['stream_type'] == 'live' ? true : false,
			'thumbnail' => strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => strtolower($channel_name))),
			'title' => $v['channel']['title'],
			'viewers' => (int) $v['channel_count'],
		);
		return $info;
	}
	
	protected function decodeChunkInfo($raw) {
		$res = json_decode($raw, true);
		$info = array();
		if(is_array($res)) {
			foreach($res as $v) {
				$info[] = $this->mapInfo($v);
			}
			return $info;
		}
		return null;
	}
    
    public function getThumbnail($channel) {
        return strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => strtolower($channel['name'])));
    }
    

    public function getEmbedPlayerCode($channel, $width, $height) {
        return $this->renderTemplate('player/justintv', array('channelName' => $channel['name'],
            'width' => $width, 'height' => $height));
    }

}
