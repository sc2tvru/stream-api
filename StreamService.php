<?php

interface StreamService {
    public function getVideos($userName, $userId, $lastVideoId = -1);

    /**
        * Get stream information. 
        * @param StreamChannel $streamChannel channel information
        * @return Array 
        */
    public function getInfo($streamChannel);

    /**
        * Get stream information.
        * @param StreamChannel $streamChannels channel information
        * @return Array
        */
    public function getInfoBatch($streamChannels);
}
