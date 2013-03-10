<?php

abstract class StreamService {

    /**
     * @param Array $channel
     * @return Array or null if channel isn't exist
     */
    abstract public function checkChannel($channel);

    /**
     * Get stream information in batch request
     * @param Array $channels
     * @return Array
     */
    abstract public function getInfo($channels);

    /**
     * Get stream thumbnail
     * @param Array $channel channels information
     * @return String with thumbnail url or null if thumbnail can be obtain only by getInfo method
     */
    abstract public function getThumbnail($channel);

    //TODO
    public function getVideos($userName, $userId, $lastVideoId = -1) {
        return null;
    }

    abstract public function getEmbedPlayerCode($channel, $width, $height);

    public function renderTemplate($template, $data) {
        ob_start();
        extract($data);
        require('services/templates/' . $template . '.tpl.php');
        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }
}
