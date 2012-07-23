<?php

class MotionCreds extends StreamService {
    const MAX_INFO_BATCH_CHANNELS = 30;
    const CHECK_STREAM_STATUS_URL = 'http://www.gamecreds.com/api/liveInfo?channels=:channel_name&includeOffline=1';

    public function checkChannel($channelName) {
        $json_string = file_get_contents(strtr(self::CHECK_STREAM_STATUS_URL, array(':channel_name' => $channelName)));
        $data = json_decode($json_string);

        $channelId = null;
        $info = $data->result->$channelName;

        if(!property_exists($info, 'valid') && !$info->valid) {
            $channelId = $channelName;
        }

        return $channelId;
    }

    public function getInfo($streamChannel) {
        $json_string = file_get_contents(strtr(self::CHECK_STREAM_STATUS_URL, array(':channel_name' => $streamChannel->getChannelName())));
        $data = json_decode($json_string);

        $info = $this->fetchStreamInfo(array($streamChannel->getChannelName()), $data);
        return $info[$streamChannel->getChannelName()];
    }

    public function getInfoBatch($streamChannels) {
        $channels = array_map(function($streamChannel) {
            return $streamChannel->getChannelName();
        }, $streamChannels);

        $json_string = file_get_contents(strtr(self::CHECK_STREAM_STATUS_URL, array(':channel_name' => join(',', $channels))));
        $data = json_decode($json_string);

        return $this->fetchStreamInfo($channels, $data);
    }

    private function fetchStreamInfo($channels, $data) {
        $info = array();
        $results = $data->result;

        if($results != null) {
            foreach ($results as $channelName => $stream) {
                $info[$channelName] = $this->fillInfo($stream->online, $stream->img, $stream->name, null, $stream->nbViewers);
            }
        }

        foreach($channels as $channel) {
            if(!array_key_exists($channel, $info)) {
                $info[$channel] = $this->fillInfo(false);
            }
        }

        return $info;
    }

    public function getEmbedCode($streamChannel, $width, $height) {
        return $this->renderTemplate('motioncreds', array('channelName' => $streamChannel->getChannelName(),
            'channelId' => $streamChannel->getChannelId(), 'width' => $width, 'height' => $height));
    }

    public function getVideos($userName, $userId, $lastVideoId = -1) {
        return null;
    }
}
