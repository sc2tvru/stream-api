<?php

class JustinTv implements StreamService {
    const MAX_INFO_BATCH_CHANNELS = 50;
    const CHECK_STREAM_STATUS_URL = 'http://api.justin.tv/api/stream/list.xml?channel=:channel_name';
    const THUMBNAIL_IMAGE_URL = 'http://static-cdn.jtvnw.net/previews/live_user_:channel_name-320x240.jpg';

    public function getInfo($streamChannel) {
        @$xml = simplexml_load_file(strtr(self::CHECK_STREAM_STATUS_URL, array(':channel_name' => $streamChannel->getChannelName())));

        $info = $this->fetchStreamInfo(array($streamChannel->getChannelName()), $xml);
        return $info[$streamChannel->getChannelName()];
    }

    public function getInfoBatch($streamChannels) {
        $channels = array_map(function($streamChannel) {
            return $streamChannel->getChannelName();
        }, $streamChannels);

        @$xml = simplexml_load_file(strtr(self::CHECK_STREAM_STATUS_URL, array(':channel_name' => join(',', $channels))));

        return $this->fetchStreamInfo($channels, $xml);
    }

    private function fetchStreamInfo($channels, $xml) {
        $info = array();

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

        return $info;
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
