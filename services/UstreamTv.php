<?php

class UstreamTv implements StreamService {
    const MAX_INFO_BATCH_CHANNELS = 10;
    const DEV_KEY = 'C1ADCAB5BC8A3A81710D1D3EF66F81F6';
    const CHECK_STREAM_STATUS_URL = 'http://api.ustream.tv/json/channel/:channel_name/getInfo?key=:dev_key';

    public function getThumbnail($streamChannel) {
        return null;
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

        $json_string = file_get_contents(strtr(self::CHECK_STREAM_STATUS_URL, array(':channel_name' => join(',', $channels),
            ':dev_key' => self::DEV_KEY)));
        $data = json_decode($json_string);

        return $this->fetchStreamInfo($channels, $data);
    }

    private function fetchStreamInfo($channels, $data) {
        $info = array();
        $result = $data->results;

        if($result != null) {
            if(!is_array($result)) {
                $result = array($result);
            }

            foreach ($result as $stream) {
                $channelName = $stream->urlTitleName;
                $info[$channelName] = $this->fillInfo($stream->status == 'live' ? true : false,
                    is_object($stream->imageUrl) ? $stream->imageUrl->medium : null,
                    $stream->title, $stream->description);
            }
        }

        foreach($channels as $channel) {
            if(!array_key_exists($channel, $info)) {
                $info[$channel] = $this->fillInfo(false);
            }
        }

        return $info;
    }

    private function fillInfo($live, $thumbnail = null, $title = null, $description = null, $viewers = null) {
        return array('live' => $live, 'thumbnail' => $thumbnail, 'title' => $title, 'description' => $description,
            'viewers' => $viewers);
    }

    public function getVideos($userName, $userId, $lastVideoId = -1) {
        $videos = array();

        $offset = 0;

        do {
            @$xml = simplexml_load_file('http://api.justin.tv/api/channel/archives/'.$userName.'.xml?limit=100&offset='.$offset);
            $parsedVideos = array();

            foreach($xml->children() as $child) {
                $id = (integer) $child->id;

                if($id == $lastVideoId)
                    break;

                $parsedVideos[] = array(
                    'id' => $id,
                    'title' => (string) $child->title,
                    'thumbnail' => (string) $child->image_url_medium,
                    'date' => (string) $child->start_time,
                );
            }

            $len = count($parsedVideos);
            $offset += $len;
            $videos = array_merge($videos, $parsedVideos);
            sleep(3);
        } while($len == 100);

        return $videos;
    }
}
