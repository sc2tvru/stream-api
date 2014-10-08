<?php
require_once dirname(__FILE__).'/../StreamService.php';

class ReallTv extends StreamService {

    const CHECK_STREAM_STATUS_URL = 'http://reall.tv/api/client/status/c';

	protected function loadChunkInfo($channels) {
		$raw = $this->loadResource(self::CHECK_STREAM_STATUS_URL, array('ids'=>$channels), 'GET');
		return $raw;
	}

	protected function mapInfo($v) {
		$channel_name = $v['cid'];
		$info= array(
				'name' => $channel_name ,
				'id' => $channel_name,
				'service' => StreamApiService::SERVICE_REALLTV,
				'live' => ($v['online']=="1"),
				'thumbnail' => $v['thumb'],
				'title' => "",
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
        return $this->renderTemplate('player/realltv', array('channelName' => $channel['name'], 'width' => $width, 'height' => $height));
    }

}

