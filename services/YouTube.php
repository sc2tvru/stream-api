<?php
require_once dirname(__FILE__).'/../StreamService.php';

class YouTube extends StreamService {

	protected $chunk_size = 50;
	
    const DEV_KEY = 'AIzaSyDjvLZLAlFTB8WPETznMV3j6aIzoTM21F4';
    const CHECK_STREAM_STATUS_URL = 'https://www.googleapis.com/youtube/v3/videos?part=liveStreamingDetails,snippet&id=:channels&key=:dev_key';

	protected function loadChunkInfo($channels) {
		$raw = $this->loadResource(strtr(self::CHECK_STREAM_STATUS_URL, array(':channels' => join(',',$channels), ':dev_key' => self::DEV_KEY)), array('maxResults'=>50), 'GET');
		return $raw;
	}

	protected function mapInfo($v) {
		
		$channel_name = $v['id'];
		$info = array(
			'name' => $v['id'] ,
			'id' => $v['id'],
			'service' => StreamApiService::SERVICE_YOUTUBE,
			'live' => $v['snippet']['liveBroadcastContent'] == 'live' ? true : false,
			'thumbnail' => $v['snippet']['thumbnails']['medium']['url'],
			'title' => $v['snippet']['title'],
			'description' => $v['snippet']['description'],
			'viewers' => (isset($v['liveStreamingDetails']['concurrentViewers']) ? (int)$v['liveStreamingDetails']['concurrentViewers'] : 0),
		);
		return $info;
	}
	
	protected function decodeChunkInfo($raw) {
		$res = json_decode($raw, true);
		$info = array();
		if($res) {
			foreach($res['items'] as $v) {
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
        return $this->renderTemplate('player/youtube', array('channelId' => $channel['name'], 'width' => $width, 'height' => $height));
    }

}


