<?php
require_once dirname(__FILE__).'/../StreamService.php';

class YouTube extends StreamService {
    const THUMBNAIL_IMAGE_URL = 'http://img.youtube.com/vi/:channel_id/default.jpg';

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
        return strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_id' => $channel['name']));
    }

    public function getEmbedPlayerCode($channel, $width, $height) {
        return $this->renderTemplate('player/youtube', array('channelId' => $channel['name'], 'width' => $width, 'height' => $height));
    }

}


