<?php

class UstreamTv extends StreamService {
    const DEV_KEY = 'C1ADCAB5BC8A3A81710D1D3EF66F81F6';
    const CHECK_STREAM_STATUS_URL = 'http://api.ustream.tv/json/channel/:channel_name/getInfo?key=:dev_key';

    public function checkChannel($channel) {
        $json_string = file_get_contents(strtr(self::CHECK_STREAM_STATUS_URL, array(':channel_name' => $channel['name'],
            ':dev_key' => self::DEV_KEY)));
        $data = json_decode($json_string);

        if($data->results != null) {
            return array(
                'name' => $channel['name'],
                'id'=> $data->results->id,
            );
        }

        return null;
    }

    public function getInfo($channels) {
        $names = array_map(function($channel) {
            return $channel['name'];
        }, $channels);

        $json_string = file_get_contents(strtr(self::CHECK_STREAM_STATUS_URL, array(':channel_name' => join(';', $names),
            ':dev_key' => self::DEV_KEY)));

        return $this->fetchStreamInfo(json_decode($json_string));
    }

    public function getThumbnail($channel) {
        return null;
    }

    public function getEmbedPlayerCode($channel, $width, $height) {
        return $this->renderTemplate('player/ustream', array('channelId' => $channel['id'], 'width' => $width, 'height' => $height));
    }

    private function fetchStreamInfo($data) {
        $info = array();
        $results = $data->results;

        if($results != null) {
            if(!is_array($results)) {
                $info[] = $this->fillInfo($results);
            } else {
                foreach ($results as $stream) {
                    $info[] = $this->fillInfo($stream->result);
                }
            }
        }

        return $info;
    }

    public function fillInfo($data) {
        return array(
            'name' => $data->urlTitleName,
            'id' => $data->id,
            'service' => 'UstreamTv',
            'live' => $data->status == 'live' ? true : false,
            'thumbnail' => is_object($data->imageUrl) ? $data->imageUrl->medium : null,
            'title' => $data->title,
            'description' => $data->description,
        );
    }
}
