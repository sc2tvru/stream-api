<?php
require_once dirname(__FILE__).'/../StreamService.php';

class YouTube extends StreamService {
    
	protected function loadChunkInfo($channels) {
		return null;
	}
	
	protected function decodeChunkInfo($raw) {
		return null;
	}
	
	public function checkChannel($channel) {
		return true;
	}
	
    public function getThumbnail($channel) {
        return null;
    }

    public function getEmbedPlayerCode($channel, $width, $height) {
        return $this->renderTemplate('player/youtube', array('channelId' => $channel['id'], 'width' => $width, 'height' => $height));
    }

}


