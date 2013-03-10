<?php

class GoodGame extends StreamService {
    public function checkChannel($channel) {
        return array(
            'name' => $channel['name'],
            'id' => $channel['name'],
        );
    }

    public function getInfo($channels) {
        return null;
    }

    public function getThumbnail($channel) {
        return null;
    }

    public function getEmbedPlayerCode($channel, $width, $height) {
        return $this->renderTemplate('player/goodgame', array('channelId' => $channel['id'], 'width' => $width, 'height' => $height));
    }
}
