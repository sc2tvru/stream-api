<?php

class YaTv extends StreamService {
    const CHECK_STREAM_STATUS_URL = 'http://api.yatv.ru/channel,scheme?shortname=:channel_name&latest';
    const THUMBNAIL_IMAGE_URL = 'http://yatv.ru/storage/tvsnapshots/mini/:channel_id.jpg';

    public function checkChannel($channel) {
        return $this->fetchStreamInfo($channel);
    }

    public function getInfo($channels) {
        $info = array();

        foreach($channels as $channel) {
            $info[] = $this->fetchStreamInfo($channel);
        }

        return $info;
    }

    public function getThumbnail($channel) {
        return strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_id' => $channel['id']));
    }

    public function getEmbedPlayerCode($channel, $width, $height) {
        return $this->renderTemplate('player/yatv', array('channelId' => $channel['id'],
            'width' => $width, 'height' => $height));
    }

    private function fetchStreamInfo($channel) {
        $json_string = file_get_contents(strtr(self::CHECK_STREAM_STATUS_URL, array(':channel_name' => $channel['name'])));
        $info = json_decode($json_string);

        if($info->code == 200) {
            $channelId = (int) $info->data->cid;
            $channelName = (int) $info->data->shortname;

            return array(
                'name' => $channelName,
                'id' => $channelId,
                'service' => 'YaTV',
                'live' => $info->data->type == 'live' ? true : false,
                'thumbnail' => strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_id' => $channelId)),
                'title' => $info->data->attributes->title,
                'description' => $info->data->attributes->description,
            );
        }

        return null;
    }
}
