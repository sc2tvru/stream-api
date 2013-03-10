<?php

class JustinTv extends StreamService {
    const CHECK_STREAM_STATUS_URL = 'http://api.justin.tv/api/stream/list.xml?channel=:channel_name';
    const THUMBNAIL_IMAGE_URL = 'http://static-cdn.jtvnw.net/previews/live_user_:channel_name-320x240.jpg';

    public function checkChannel($channel) {
        @$xml = simplexml_load_file(strtr(self::CHECK_STREAM_STATUS_URL, array(':channel_name' => $channel['name'])));
        $info = $this->fetchStreamInfo($xml);

        return count($info) > 0 ? $info[0] : null;
    }

    public function getInfo($channels) {
        $names = array_map(function($channel) {
            return $channel['name'];
        }, $channels);

        @$xml = simplexml_load_file(strtr(self::CHECK_STREAM_STATUS_URL, array(':channel_name' => join(',', $names))));
        return $this->fetchStreamInfo($xml);
    }

    public function getThumbnail($channel) {
        return strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => $channel['name']));
    }

    public function getEmbedPlayerCode($channel, $width, $height) {
        return $this->renderTemplate('player/justintv', array('channelName' => $channel['name'],
            'width' => $width, 'height' => $height));
    }

    private function fetchStreamInfo($xml) {
        $info = array();

        foreach ($xml->children() as $stream) {
            $channelName = (string) $stream->channel->login;

            $info[] = array(
                'name' => $channelName,
                'id' => (int) $stream->channel->id,
                'service' => 'Justin.tv',
                'live' => $stream->stream_type == 'live' ? true : false,
                'thumbnail' => strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => $channelName)),
                'title' => (string) $stream->title,
                'viewers' => (int) $stream->channel_count,
            );
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
