<?php

require "../StreamChannel.php";

class StreamChannelMock implements StreamChannel {
    public $channelName;
    public $channelId;

    public function __construct($channelName = "", $channelId = "") {
        $this->channelName = $channelName;
        $this->channelId = $channelId;
    }

    public function getChannelName() {
        return $this->channelName;
    }

    public function setChannelName($channelName) {
        $this->channelName = $channelName;
    }

    public function getChannelId() {
        return $this->channelId;
    }

    public function serChannelId($channelId) {
        $this->channelId = $channelId;
    }
}
