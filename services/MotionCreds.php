<?php
require_once dirname(__FILE__).'/../StreamService.php';

class MotionCreds extends StreamService {
    
	const CHECK_STREAM_STATUS_URL = 'http://www.gamecreds.com/api/liveInfo?channels=:channels&includeOffline=0';
	const CHECK_STREAM_STATUS_WITH_OFFLINE_URL = 'http://www.gamecreds.com/api/liveInfo?channels=:channels&includeOffline=1';
	

    protected function loadChunkInfo($channels) {
		$raw = $this->loadResource(strtr(self::CHECK_STREAM_STATUS_WITH_OFFLINE_URL, array(':channels' => join(',', $channels))), array(), 'GET');
		return $raw;
	}
	
	protected function mapInfo($v) {
		$channel_name = $v['name'];
		$info= array(
			'name' => $channel_name ,
			'id' => $channel_name,
			'service' => StreamApiService::SERVICE_MOTIONCREDS,
			'live' => $v['online'] ? true : false,
			'thumbnail' => $v['img'],
			'title' => $v['name'],
			'viewers' => (int) $v['nbViewers'],
		);
		return $info;
	}
	
	protected function decodeChunkInfo($raw) {
		$res = json_decode($raw, true);
		if($res) {
			if(isset($res['result']) && is_array($res['result'])) {
				$info = array();
				foreach($res['result'] as $k=>$v) {
					if(!isset($v['valid']) || $v['valid']==true) {
						$inf = $this->mapInfo($v);
						$inf['id'] = $k;
						$inf['name'] = $k;
						$info[] = $inf;
					}
				}
				return $info;
			}
		}
		return null;
	}
    
    public function getThumbnail($channel) {
        return null;
    }

    public function getEmbedPlayerCode($channel, $width, $height) {
        return $this->renderTemplate('player/motioncreds', array('channelId' => $channel['id'], 'width' => $width, 'height' => $height));
    }

}
