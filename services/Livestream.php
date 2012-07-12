<?php

class Livestream implements StreamService {
    const MAX_INFO_BATCH_CHANNELS = 50;
    const CHECK_STREAM_V1_0_STATUS_URL = 'http://channel.api.livestream.com/1.0/channelsinfo?channel=:channel_name';
    const CHECK_STREAM_V2_0_STATUS_URL = 'http://x:channel_namex.api.channel.livestream.com/2.0/info.xml';
    const THUMBNAIL_IMAGE_URL = 'http://thumbnail.api.livestream.com/thumbnail?name=:channel_name';

    public function getInfo($streamChannel) {
        //Use api version 2.0 for single channel info
        /*@$xml = simplexml_load_file(strtr(self::CHECK_STREAM_V2_0_STATUS_URL, array(':channel_name' => $streamChannel->getChannelName())));

        if($xml==null)
            return 0;
        $stream_info = $xml->children("http://api.channel.livestream.com/2.0");
        $is_live = (string) ($stream_info -> isLive[0]);

        if($is_live=="true")
        return 1;

        return 0;*/
    }

    public function getInfoBatch($streamChannels) {
        //Use api version 1.0 for batch request
    }

    private function fetchStreamInfo($channels, $xml) {
        /*$info = array();

        foreach ($xml->children() as $stream) {
            $channelName = (string) $stream->channel->login;
            $info[$channelName] = array('live' => $stream->stream_type == 'live' ? true : false,
                'thumbnail' => strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => $channelName)));
        }

        foreach($channels as $channel) {
            if(!array_key_exists($channel, $info)) {
                $info[$channel] = array('live' => false);
            }
        }

        return $info;*/
    }

    public function getVideos($userName, $userId, $lastVideoId = -1){
        //TODO write method
    }
}
