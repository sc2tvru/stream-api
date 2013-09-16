<?php
require_once dirname(__FILE__).'/../StreamService.php';

class YaTv extends StreamService {
	
	protected $chunk_size = 1;
	const CHECK_STREAM_STATUS_URL = 'http://api.yatv.ru/channel,scheme?shortname=:channels&latest';
    const THUMBNAIL_IMAGE_URL = 'http://yatv.ru/storage/tvsnapshots/mini/:channel_id.jpg';

	protected function loadChunkInfo($channels) {
		$raw = $this->loadResource(strtr(self::CHECK_STREAM_STATUS_URL, array(':channels' => implode(';',$channels))), array(), 'GET');
		return $raw;
	}

	protected function mapInfo($v) {

		$channel_name = $v['shortname'];
		$channel_id = $v['cid'];
		$info= array(
				'name' => $channel_name,
				'id' => $channel_id,
				'service' => StreamApiService::SERVICE_YATV,
				'live' => $v['type'] == 'live' ? true : false,
				'thumbnail' => strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_id' => $channel_id)),
				'title' => $v['attributes']['title'],
				'description' => $v['attributes']['description'],
				'viewers' => 0,
		);
		return $info;
	}

	protected function decodeChunkInfo($raw) {
		$res = json_decode($raw, true);
		if($res) {
			//print_r($res);
			if(isset($res['code']) && $res['code']==200 && is_array($res['data'])) {
				// single result
				$info = array();
				$info[] = $this->mapInfo($res['data']);
				return $info;
			}
		}
		return null;
	}

    public function getThumbnail($channel) {
        return strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_id' => $channel['id']));
    }

    public function getEmbedPlayerCode($channel, $width, $height) {
        return $this->renderTemplate('player/yatv', array('channelId' => $channel['id'],
            'width' => $width, 'height' => $height));
    }

}

