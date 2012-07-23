<?php

abstract class StreamService {

    abstract public function checkChannel($channelName);

    /**
        * Get single stream information
        * @param StreamChannel $streamChannel channel information
        * @return Array 
        */
    abstract public function getInfo($streamChannel);

    /**
        * Get stream information in batch request
        * @param StreamChannel[] $streamChannels channels information
        * @return Array
        */
    abstract public function getInfoBatch($streamChannels);

    /**
        * Get stream thumbnail
        * @param StreamChannel $streamChannel channels information
        * @return String with thumbnail url or null if thumbnail can be obtain only by getInfo method
        */
    public function getThumbnail($streamChannel) {
        return null;
    }

    abstract public function getVideos($userName, $userId, $lastVideoId = -1);

    protected function renderTemplate($template, $data) {
        ob_start();
        extract($data);
        require('services/templates/' . $template . '.tpl.php');
        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }

    protected function fillInfo($live, $thumbnail = null, $title = null, $description = null, $viewers = null) {
        return array('live' => $live, 'thumbnail' => $thumbnail, 'title' => $title, 'description' => $description,
            'viewers' => $viewers);
    }
}
