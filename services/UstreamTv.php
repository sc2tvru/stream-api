<?php

class UstreamTv extends StreamService {
    const MAX_INFO_BATCH_CHANNELS = 10;
    const DEV_KEY = 'C1ADCAB5BC8A3A81710D1D3EF66F81F6';
    const CHECK_STREAM_STATUS_URL = 'http://api.ustream.tv/json/channel/:channel_name/getInfo?key=:dev_key';

    public function checkChannel($channelName) {
        $json_string = file_get_contents(strtr(self::CHECK_STREAM_STATUS_URL, array(':channel_name' => $channelName,
            ':dev_key' => self::DEV_KEY)));
        $data = json_decode($json_string);

        $channelId = null;

        if($data->results != null) {
            $channelId = $data->results->id;
        }

        return $channelId;
    }

    public function getInfo($streamChannel) {
        $json_string = file_get_contents(strtr(self::CHECK_STREAM_STATUS_URL, array(':channel_name' => $streamChannel->getChannelName(),
            ':dev_key' => self::DEV_KEY)));
        $data = json_decode($json_string);

        $info = $this->fetchStreamInfo(array($streamChannel->getChannelName()), $data);
        return $info[$streamChannel->getChannelName()];
    }

    public function getInfoBatch($streamChannels) {
        $channels = array_map(function($streamChannel) {
            return $streamChannel->getChannelName();
        }, $streamChannels);

        $json_string = file_get_contents(strtr(self::CHECK_STREAM_STATUS_URL, array(':channel_name' => join(';', $channels),
            ':dev_key' => self::DEV_KEY)));
        $data = json_decode($json_string);

        return $this->fetchStreamInfo($channels, $data);
    }

    private function fetchStreamInfo($channels, $data) {
        $info = array();
        $results = $data->results;

        if($results != null) {
            if(!is_array($results)) {
                $channelName = $results->urlTitleName;
                $info[$channelName] = $this->fillInfo($results->status == 'live' ? true : false,
                    is_object($results->imageUrl) ? $results->imageUrl->medium : null,
                    $results->title, $results->description);
            } else {
                foreach ($results as $stream) {
                    $channelName = $stream->result->urlTitleName;
                    $info[$channelName] = $this->fillInfo($stream->result->status == 'live' ? true : false,
                        is_object($stream->result->imageUrl) ? $stream->result->imageUrl->medium : null,
                        $stream->result->title, $stream->result->description);
                }
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
        return $this->renderTemplate('ustream', array('channelName' => $streamChannel->getChannelName(),
            'channelId' => $streamChannel->getChannelId(), 'width' => $width, 'height' => $height));
    }

    public function getVideos($userName, $userId, $lastVideoId = -1) {
        return null;
    }
}
