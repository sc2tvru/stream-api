<?php
require_once dirname(__FILE__).'/../StreamService.php';

class UstreamTv extends StreamService {
    
	protected $chunk_size = 20;
	// Ustream support batch processing, but if is non-existent channel passed for check the entire request will return an error 
	const DEV_KEY = 'C1ADCAB5BC8A3A81710D1D3EF66F81F6';
    const CHECK_STREAM_STATUS_URL = 'http://api.ustream.tv/json/channel/:channels/getInfo?key=:dev_key';
    const THUMBNAIL_IMAGE_URL = 'http://static-cdn.jtvnw.net/previews/live_user_:channel_name-320x240.jpg';
    
    protected function loadChunkInfo($channels) {
		$raw = $this->loadResource(strtr(self::CHECK_STREAM_STATUS_URL, array(':channels' => implode(';',$channels), ':dev_key' => self::DEV_KEY)), array(), 'GET');
		return $raw;
	}
	
	protected function mapInfo($v) {
		
		$channel_name = $v['urlTitleName'];
		$info= array(
			'name' => $v['id'] ,
			'id' => $v['id'],
			'service' => StreamApiService::SERVICE_USTREAMTV,
			'live' => $v['status'] == 'live' ? true : false,
			'thumbnail' => ((isset($v['imageUrl'])&&is_array($v['imageUrl'])&&isset($v['imageUrl']['medium']))?$v['imageUrl']['medium']:null),
			'title' => $v['title'],
			'description' => $v['description'],
			'viewers' => (isset($v['viewersNow'])?(int)$v['viewersNow']:0),
		);
		return $info;
	}
	
	protected function decodeChunkInfo($raw) {
		$res = json_decode($raw, true);
		if($res) {
			if(isset($res['results']) && is_array($res['results'])) {
				if(sizeof($res['results'])>0 && isset($res['results']['id'])) {
					// single result
					$info = array();
					$info[] = $this->mapInfo($res['results']);
					return $info;
				}
				else {
					$info = array();
					foreach($res['results'] as $v) {
						$info[] = $this->mapInfo($v['result']);
					}
					return $info;
				}
				
			}
		}
		return null;
	}
    
    public function getThumbnail($channel) {
        return strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => strtolower($channel['name'])));
    }

	public function getEmbedPlayerCode($channel, $width, $height) {
        return $this->renderTemplate('player/ustream', array('channelName' => $channel['name'], 'width' => $width, 'height' => $height));
    }

}
