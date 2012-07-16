<?php

class YaTv implements StreamService {
    const MAX_INFO_BATCH_CHANNELS = 5;
    const CHECK_STREAM_STATUS_URL = 'http://api.yatv.ru/channel,scheme?shortname=:channel_name&latest';
    const THUMBNAIL_IMAGE_URL = 'http://yatv.ru/storage/tvsnapshots/mini/:channel_id.jpg';

    public function getInfo($streamChannel) {
        $json_string = file_get_contents(strtr(self::CHECK_STREAM_STATUS_URL, array(':channel_name' => $streamChannel->getChannelName())));
        $info = json_decode($json_string);

        if($info->code == 200) {
            $channelId= $info->data->cid;
            return array('live' => $info->data->type == 'live' ? true : false,
                'thumbnail' => strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_id' => $channelId)),
                'title' => $info->data->attributes->title,
                'description' => $info->data->attributes->description);
        } else {
            return array('live' => false);
        }
    }

    public function getInfoBatch($streamChannels) {
        $info = array();

        foreach($streamChannels as $streamChannel) {
            $info[$streamChannel->getChannelName()] = $this->getInfo($streamChannel);
        }

        return $info;
    }

    public function getVideos($userName, $userId, $lastVideoId = -1){
        //TODO write method
    }
}
