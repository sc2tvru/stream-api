<?php
require_once dirname(__FILE__).'/../StreamService.php';

class TwitchTv extends StreamService {
    
	const CHECK_STREAM_STATUS_URL = 'https://api.twitch.tv/kraken/streams?channel=:channels';
    const THUMBNAIL_IMAGE_URL = 'http://static-cdn.jtvnw.net/previews-ttv/live_user_:channel_name-320x200.jpg';

    protected function loadChunkInfo($channels) {
		$raw = $this->loadResource(strtr(self::CHECK_STREAM_STATUS_URL, array(':channels' => join(',', $channels))), array('limit'=>100), 'GET');
		return $raw;
	}
	
	protected function mapInfo($v) {
		$channel_name = $v['channel']['name'];
		$info= array(
			'name' => $channel_name ,
			'id' => $channel_name,
			'service' => StreamApiService::SERVICE_TWITCHTV,
			'live' => true,
			'thumbnail' => strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => strtolower($channel_name))),
			'title' => $v['channel']['status'],
			'viewers' => (int) $v['viewers'],
		);
		return $info;
	}
	
	protected function decodeChunkInfo($raw) {
		$res = json_decode($raw, true);
		if($res) {
			if(isset($res['streams']) && is_array($res['streams'])) {
				foreach($res['streams'] as $v) {
					$info[] = $this->mapInfo($v);
				}
			}
			return $info;
		}
		return null;
	}
    
    public function getThumbnail($channel) {
        return strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => strtolower($channel['name'])));
    }

    public function getEmbedPlayerCode($channel, $width, $height) {
        return $this->renderTemplate('player/twitchtv', array('channelName' => $channel['name'],
            'width' => $width, 'height' => $height));
    }

}
