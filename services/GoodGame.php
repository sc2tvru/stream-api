<?php

class GoodGame extends StreamService {
    public function checkChannel($channelName) {
        return null;
    }

    public function getInfo($streamChannel) {
        return null;
    }

    public function getInfoBatch($streamChannels) {
        return null;
    }

    public function getEmbedCode($streamChannel, $width, $height) {
        return $this->renderTemplate('goodgame', array('channelName' => $streamChannel->getChannelName(),
            'channelId' => $streamChannel->getChannelId(), 'width' => $width, 'height' => $height));
    }

    public function getVideos($userName, $userId, $lastVideoId = -1) {
        return null;
    }
}
