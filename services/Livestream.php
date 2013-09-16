<?php
require_once dirname(__FILE__).'/../StreamService.php';

class Livestream  extends StreamService {
    
	const CHECK_STREAM_V1_0_STATUS_URL = 'http://channel.api.livestream.com/1.0/channelsinfo?channel=:channels';
    //Version 2.0 do not used because of inability for batch requests
    const CHECK_STREAM_V2_0_STATUS_URL = 'http://x:channel_namex.api.channel.livestream.com/2.0/info.xml';
    const THUMBNAIL_IMAGE_URL = 'http://thumbnail.api.livestream.com/thumbnail?name=:channel_name';

    protected function loadChunkInfo($channels) {
		$raw = $this->loadResource(strtr(self::CHECK_STREAM_V1_0_STATUS_URL, array(':channels' => join(',', $channels))), array(), 'GET');
		return $raw;
	}
	
	protected function mapInfo($v) {
		$channelName = (string) $v->attributes()->name;
		$info = array(
				'name' => $channelName,
				'id' => $channelName,
				'service' => StreamApiService::SERVICE_LIVESTREAM,
				'live' => $v->isLive == 'true' ? true : false,
				'thumbnail' => strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => $channelName)),
				'title' => (string) $v->title,
				'description' => (string) $v->description,
				'viewers' => (int) $v->currentViewerCount,
		);
		return $info;
	}
	
	protected function decodeChunkInfo($raw) {
		$xml = simplexml_load_string(str_replace('ls:', '', $raw));
		if($xml) {
			$info = array();
			foreach ($xml as $v) {
				$info[] = $this->mapInfo($v);
			}
			return $info;
		}
		return null;
	}
    
	public function getThumbnail($channel) {
        return strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => $channel['name']));
    }

    public function getEmbedPlayerCode($channel, $width, $height) {
        return $this->renderTemplate('player/livestream', array('channelName' => $channel['name'],
            'width' => $width, 'height' => $height));
    }
	

}
