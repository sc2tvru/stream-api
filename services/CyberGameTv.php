<?php
require_once dirname(__FILE__).'/../StreamService.php';

class CyberGameTv extends StreamService {

    const CHECK_STREAM_STATUS_URL = 'http://api.cybergame.tv/w/streams2.php';
    const THUMBNAIL_IMAGE_URL = 'http://fileapi.cybergame.tv/thumbnails/channels/:channel_name.jpg';

	protected function loadChunkInfo($channels) {
		$raw = $this->loadResource(self::CHECK_STREAM_STATUS_URL, array('channels'=>$channels), 'GET');
		return $raw;
	}

	protected function mapInfo($v) {
		$channel_name = $v['channel name'];
		$info= array(
				'name' => $channel_name ,
				'id' => $channel_name,
				'service' => StreamApiService::SERVICE_CYBERGAMETV,
				'live' => ($v['online']=="1"),
				'thumbnail' => $v['thumbnail'],
				'title' => $v['channel_game'],
				'viewers' => (int) $v['viewers'],
		);
		return $info;
	}

	protected function decodeChunkInfo($raw) {
		$res = json_decode($raw, true);
		if($res) {
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
        return $this->renderTemplate('player/cybergame', array('channelName' => $channel['name'],
            'width' => $width, 'height' => $height));
    }

}

