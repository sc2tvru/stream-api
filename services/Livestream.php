<?php

class Livestream extends StreamService {
    const CHECK_STREAM_V1_0_STATUS_URL = 'http://channel.api.livestream.com/1.0/channelsinfo?channel=:channel_name';
    //Version 2.0 do not used because of inability for batch requests
    const CHECK_STREAM_V2_0_STATUS_URL = 'http://x:channel_namex.api.channel.livestream.com/2.0/info.xml';
    const THUMBNAIL_IMAGE_URL = 'http://thumbnail.api.livestream.com/thumbnail?name=:channel_name';

    public function checkChannel($channel) {
        @$xml = simplexml_load_file(strtr(self::CHECK_STREAM_V1_0_STATUS_URL, array(':channel_name' => $channel['name'])));
        $info = $this->fetchStreamInfo(simplexml_load_string(str_replace('ls:', '', $xml->asXML())));

        return count($info) > 0 ? $info[0] : null;
    }

    public function getInfo($channels) {
        $names = array_map(function($channel) {
            return $channel['name'];
        }, $channels);

        @$xml = simplexml_load_file(strtr(self::CHECK_STREAM_V1_0_STATUS_URL, array(':channel_name' => join(',', $names))));
        $info = $this->fetchStreamInfo(simplexml_load_string(str_replace('ls:', '', $xml->asXML())));

        return $info;
    }

    public function getThumbnail($channel) {
        return strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => $channel['name']));
    }

    public function getEmbedPlayerCode($channel, $width, $height) {
        return $this->renderTemplate('player/livestream', array('channelName' => $channel['name'],
            'width' => $width, 'height' => $height));
    }

    private function fetchStreamInfo($xml) {
        $info = array();

        foreach ($xml->children() as $stream) {
            $channelName = (string) $stream->attributes()->name;

            $info[] = array(
                'name' => $channelName,
                'id' => $channelName,
                'service' => 'Livestream',
                'live' => $stream->isLive == 'true' ? true : false,
                'thumbnail' => strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => $channelName)),
                'title' => (string) $stream->title,
                'description' => (string) $stream->description,
                'viewers' => (int) $stream->currentViewerCount,
            );
        }

        return $info;
    }
}
