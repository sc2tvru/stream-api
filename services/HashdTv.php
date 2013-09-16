<?php
require_once dirname(__FILE__).'/../StreamService.php';

class HashdTv extends StreamService {
    
	const CHECK_STREAM_STATUS_URL = 'http://api.hashd.tv/v1/streams/:channels';
    const THUMBNAIL_IMAGE_URL = 'http://cdn.hashd.tv/live/:channel_name_210x130.jpg';

    protected function loadChunkInfo($channels) {
		$raw = $this->loadResource(strtr(self::CHECK_STREAM_STATUS_URL, array(':channels' => strtolower(join(',', $channels)))), array(), 'GET');
		return $raw;
	}
	
	protected function mapInfo($v) {
		$channel_name = $v['name_seo'];
		$info= array(
			'name' => $channel_name,
			'id' => $v['id'],
			'service' => StreamApiService::SERVICE_HASHDTV,
			'live' => $v['live']?true:false,
			'thumbnail' => isset($v['thumbnails'][2])?$v['thumbnails'][2]:strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => $channel_name)),
			'title' => $v['title'],
			'viewers' => (int) $v['current_viewers'],
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
        return strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => $channel['name']));
    }

    public function getEmbedPlayerCode($channel, $width, $height) {
        return $this->renderTemplate('player/hashdtv', array('channelName' => $channel['name'],
            'width' => $width, 'height' => $height));
    }

}
