<?php

class CyberGameTv extends StreamService {
    const CHECK_STREAM_STATUS_URL = 'http://api.cybergame.tv/w/streams2.php?channels[]=:channel_name';
    const CHECK_CHANNEL_URL = 'http://api.cybergame.tv/w/streams2.php?channel=:channel_name';
    const THUMBNAIL_IMAGE_URL = 'http://fileapi.cybergame.tv/thumbnails/channels/:channel_name.jpg';

    public function checkChannel($channel) {
        $json_string = file_get_contents(strtr(self::CHECK_CHANNEL_URL, array(':channel_name' => $channel['name'])));
        return count(json_decode($json_string, true)) > 0 ? array('name' => $channel['name'], 'id' => $channel['name']) : null;
    }

    public function getInfo($channels) {
        $names = array_map(function($channel) {
            return $channel['name'];
        }, $channels);

        $json_string = file_get_contents(strtr(self::CHECK_STREAM_STATUS_URL, array(':channel_name' => join('&channels[]=', $names))));
        return $this->fetchStreamInfo(json_decode($json_string, true));
    }

    public function getThumbnail($channel) {
        return strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => strtolower($channel['name'])));
    }

    public function getEmbedPlayerCode($channel, $width, $height) {
        return $this->renderTemplate('player/cybergame', array('channelName' => $channel['name'],
            'width' => $width, 'height' => $height));
    }

    private function fetchStreamInfo($data) {
        $info = array();

        foreach($data as $stream) {
            $info[] = array(
                'name' => $stream['channel name'],
                'id' => $stream['channel name'],
                'service' => 'Cybergame',
                'live' => $stream['online'] === '1' ? true : false,
                'thumbnail' => strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => strtolower($stream['channel name']))),
                'viewers' => (int) $stream['viewers'],
            );
        }

        return $info;
    }
}
