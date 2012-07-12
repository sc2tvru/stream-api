<?php
 
class RegameTv implements StreamService {
    public function getVideos($userName, $userId, $lastVideoId = -1) {
        $videos = array();

        @$xml = simplexml_load_file('http://www.regame.tv/video_xml.php?caster='.$userName);

        foreach($xml->children() as $child) {
            $id = (integer) $child->internal_id;

            if($id == $lastVideoId)
                break;

            $videos[] = array(
                'id' => $id,
                'title' => (string) $child->title,
                'thumbnail' => (string) $child->thumbnail,
                'date' => (string) $child->date,
            );
        }

        return $videos;
    }
}
