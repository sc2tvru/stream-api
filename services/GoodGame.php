<?php
require_once dirname(__FILE__).'/../StreamService.php';

class GoodGame extends StreamService {
    
	const CHECK_STREAM_STATUS_URL = 'http://goodgame.ru/api/getggchannelstatus?id=:channels&fmt=json';

    protected function loadChunkInfo($channels) {
		$raw = $this->loadResource(strtr(self::CHECK_STREAM_STATUS_URL, array(':channels' => join(',', $channels))), array(), 'GET');
		return $raw;
	}
	
	protected function mapInfo($v) {
		$channel_name = $v['key'];
		$info= array(
			'name' => $channel_name ,
			'id' => $v['stream_id'],
			'service' => StreamApiService::SERVICE_GOODGAME,
			'live' => strtolower($v['status'])=='live'?true:false,
			'thumbnail' => empty($v['thumb'])?null:$v['thumb'],
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
        return $this->renderTemplate('player/goodgame', array('channelName' => $channel['name'], 'width' => $width, 'height' => $height));
    }

}
