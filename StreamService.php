<?php

interface StreamService {
    public function getVideos($userName, $userId, $lastVideoId = -1);

    /**
        * Get single stream information
        * @param StreamChannel $streamChannel channel information
        * @return Array 
        */
    public function getInfo($streamChannel);

    /**
        * Get stream information in batch request
        * @param StreamChannel[] $streamChannels channels information
        * @return Array
        */
    public function getInfoBatch($streamChannels);

    /**
        * Get stream thumbnail
        * @param StreamChannel $streamChannel channels information
        * @return String with thumbnail url or null if thumbnail can be obtain only by getInfo method
        */
    public function getThumbnail($streamChannel);
}
