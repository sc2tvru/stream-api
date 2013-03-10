<?php

class MotionCreds extends StreamService {
    const CHECK_STREAM_STATUS_URL = 'http://www.gamecreds.com/api/liveInfo?channels=:channel_id&includeOffline=1';

    public function checkChannel($channel) {
        $json_string = file_get_contents(strtr(self::CHECK_STREAM_STATUS_URL, array(':channel_id' => $channel['name'])));
        $data = json_decode($json_string);

        $info = $data->result->$channel['name'];

        if(!property_exists($info, 'valid') && !$info->valid) {
            return array(
                'name' => $channel['name'],
                'id' => $channel['name'],
            );
        }

        return null;
    }

    public function getInfo($channels) {
        $ids = array_map(function($channel) {
            return $channel['id'];
        }, $channels);

        $json_string = file_get_contents(strtr(self::CHECK_STREAM_STATUS_URL, array(':channel_id' => join(',', $ids))));
        return $this->fetchStreamInfo(json_decode($json_string));
    }

    public function getThumbnail($channel) {
        return null;
    }

    public function getEmbedPlayerCode($channel, $width, $height) {
        return $this->renderTemplate('player/motioncreds', array('channelId' => $channel['id'], 'width' => $width, 'height' => $height));
    }

    private function fetchStreamInfo($data) {
        $info = array();
        $results = $data->result;

        if($results != null) {
            foreach ($results as $channelName => $stream) {
                $info[] = array(
                    'name' => $channelName,
                    'id' => $channelName,
                    'service' => 'MotionCreds',
                    'live' => $stream->online,
                    'thumbnail' => $stream->img,
                    'title' => $stream->name,
                    'viewers' => $stream->nbViewers,
                );
            }
        }

        return $info;
    }
}
