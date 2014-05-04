<?php
require_once dirname(__FILE__).'/../StreamService.php';

class DailyMotion extends StreamService {
    
	const CHECK_STREAM_STATUS_URL = 'https://api.dailymotion.com/videos?fields=audience,id,onair,thumbnail_240_url,title&ids=:channels';

    protected function loadChunkInfo($channels) {
		$raw = $this->loadResource(strtr(self::CHECK_STREAM_STATUS_URL, array(':channels' => join(',', $channels))), array('limit'=>100), 'GET');
		return $raw;
	}
	
	protected function mapInfo($v) {
		$channel_name = $v['id'];
		$info= array(
			'name' => $channel_name ,
			'id' => $v['id'],
			'service' => StreamApiService::SERVICE_DAILYMOTION,
			'live' => $v['onair'] ? true : false,
			'thumbnail' => empty($v['thumbnail_240_url'])?null:$v['thumbnail_240_url'],
			'title' => $v['title'],
			'viewers' => (int) $v['audience'],
		);
		return $info;
	}
	
	protected function decodeChunkInfo($raw) {
		$res = json_decode($raw, true);
		$info = array();
		if($res) {
			foreach($res['list'] as $v) {
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
        return $this->renderTemplate('player/dailymotion', array('channelName' => $channel['name'], 'width' => $width, 'height' => $height));
    }

}
