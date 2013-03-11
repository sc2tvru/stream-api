<?php

class TwitchTv extends StreamService {
    const CHECK_STREAM_STATUS_URL = 'https://api.twitch.tv/kraken/streams/:channel_name';
    const CHECK_CHANNEL_URL = 'https://api.twitch.tv/kraken/channels/:channel_name';
    const THUMBNAIL_IMAGE_URL = 'http://static-cdn.jtvnw.net/previews-ttv/live_user_:channel_name-320x200.jpg';

    public function checkChannel($channel) {
        //$json_string = file_get_contents(strtr(self::CHECK_CHANNEL_URL, array(':channel_name' => $channel['name'])));
        //$data = json_decode($json_string);
        //$data->error

        return array(
            'name' => $channel['name'],
            'id' => $channel['name'],
        );
    }

    public function getInfo($channels) {
        $info = array();

        foreach($channels as $channel) {
            $info[] = $this->fetchStreamInfo($channel);
        }

        return $info;
    }

    public function getThumbnail($channel) {
        return strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => strtolower($channel['name'])));
    }

    public function getEmbedPlayerCode($channel, $width, $height) {
        return $this->renderTemplate('player/twitchtv', array('channelName' => $channel['name'],
            'width' => $width, 'height' => $height));
    }

    private function fetchStreamInfo($channel) {
        $json_string = file_get_contents(strtr(self::CHECK_STREAM_STATUS_URL, array(':channel_name' => strtolower($channel['name']))));
        $data = json_decode($json_string);

        if($data->error) {
            return null;
        }

        return array(
            'name' => $channel['name'],
            'id' => $channel['name'],
            'service' => 'TwitchTv',
            'live' => $data->stream ? true : false,
            'thumbnail' => strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => $channel['name'])),
        );
    }
}
