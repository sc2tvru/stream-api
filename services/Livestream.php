<?php

class Livestream implements StreamService {
    const MAX_INFO_BATCH_CHANNELS = 50;
    const CHECK_STREAM_V1_0_STATUS_URL = 'http://channel.api.livestream.com/1.0/channelsinfo?channel=:channel_name';
    //Version 2.0 do not used because of inability for batch requests
    const CHECK_STREAM_V2_0_STATUS_URL = 'http://x:channel_namex.api.channel.livestream.com/2.0/info.xml';
    const THUMBNAIL_IMAGE_URL = 'http://thumbnail.api.livestream.com/thumbnail?name=:channel_name';

    public function getInfo($streamChannel) {
        @$xml = simplexml_load_file(strtr(self::CHECK_STREAM_V1_0_STATUS_URL, array(':channel_name' => $streamChannel->getChannelName())));

        $info = $this->fetchStreamInfo(array($streamChannel->getChannelName()), simplexml_load_string(str_replace('ls:', '', $xml->asXML())));
        return $info[$streamChannel->getChannelName()];
    }

    public function getInfoBatch($streamChannels) {
        $channels = array_map(function($streamChannel) {
            return $streamChannel->getChannelName();
        }, $streamChannels);

        @$xml = simplexml_load_file(strtr(self::CHECK_STREAM_V1_0_STATUS_URL, array(':channel_name' => join(',', $channels))));

        return $this->fetchStreamInfo($channels, simplexml_load_string(str_replace('ls:', '', $xml->asXML())));
    }

    private function fetchStreamInfo($channels, $xml) {
        $info = array();

        foreach ($xml->children() as $stream) {
            $channelName = (string) $stream->attributes()->name;
            $info[$channelName] = array('live' => $stream->isLive == 'true' ? true : false,
                'thumbnail' => strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => $channelName)),
                'title' => (string) $stream->title,
                'description' => (string) $stream->description,
                'viewers' => (int) $stream->currentViewerCount);
        }

        foreach($channels as $channel) {
            if(!array_key_exists($channel, $info)) {
                $info[$channel] = array('live' => false);
            }
        }

        return $info;
    }

    public function getVideos($userName, $userId, $lastVideoId = -1){
        //TODO write method
    }
}
