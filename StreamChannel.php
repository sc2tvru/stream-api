<?php

interface StreamChannel {
    public function getChannelName();
    public function setChannelName($channelName);

    public function getChannelId();
    public function serChannelId($channelId);
}
