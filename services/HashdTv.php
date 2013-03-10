<?php

class HashdTv extends StreamService {
    const CHECK_STREAM_STATUS_URL = 'http://api.hashd.tv/v1/stream/:channel_name';
    const THUMBNAIL_IMAGE_URL = 'http://cdn.hashd.tv/live/:channel_name_210x130.jpg';

    public function checkChannel($channel) {
        return $this->fetchStreamInfo($channel);
    }

    public function getInfo($channels) {
        $info = array();

        foreach($channels as $channel) {
            $info[] = $this->fetchStreamInfo($channel);
        }

        return $info;
    }

    public function getThumbnail($channel) {
        return strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => $channel['name']));
    }

    public function getEmbedPlayerCode($channel, $width, $height) {
        return $this->renderTemplate('player/hashdtv', array('channelName' => $channel['name'],
            'width' => $width, 'height' => $height));
    }

    private function fetchStreamInfo($channel) {
        $json_string = file_get_contents(strtr(self::CHECK_STREAM_STATUS_URL, array(':channel_name' => $channel['name'])));
        $data = json_decode($json_string);

        if($data) {
            return array(
                'name' => $data->name_seo,
                'id' => $data->id,
                'service' => 'HashdTv',
                'live' => $data->live,
                'thumbnail' => strtr(self::THUMBNAIL_IMAGE_URL, array(':channel_name' => $data->name_seo)),
                'title' => $data->title,
                'description' => $data->description_long,
                'viewers' => $data->current_viewers,
            );
        }

        return null;
    }

    public function getVideos($userName, $userId, $lastVideoId = -1) {
        $videos = array();

        @$xml = simplexml_load_file('http://api.own3d.tv/api.php?sort=date&limit=100&channel='.$userName, 'SimpleXMLElement', LIBXML_NOCDATA);
        foreach($xml->channel->children() as $item) {
            if($item->getName() == 'item') {
                preg_match("/([0-9]*)$/", $item->guid, $id);

                $id = (integer) $id[1];

                if($id == $lastVideoId)
                    return array_reverse($videos);

                $videos[] = array(
                    'id' => $id,
                    'title' => (string) $item->title,
                    'thumbnail' => (string) $item->thumbnail,
                );
            }
        }

        $ch = curl_init();
        $url = 'http://www.own3d.tv/iCanFly?s=user&user_name='.$userName;

        $offset = 100;

        do {
            $fields = array(
                'xjxfun=get_user_videos',
                'xjxr=1333376121936',
                'xjxargs[]=N'.$userId,
                'xjxargs[]=Suploads',
                'xjxargs[]=S36',
                'xjxargs[]=S',
                'xjxargs[]=Sfalse',
                'xjxargs[]=N'.$offset,
                'xjxargs[]=Sdate',
                'xjxargs[]=Spopular_channel_videos'
            );

            $fieldsString = '';
            foreach($fields as $field) { $fieldsString .= $field.'&'; }
            rtrim($fieldsString,'&');

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

            $result = curl_exec($ch);
            $parsedVideos = $this->parseAjaxResult($result, $lastVideoId);
            $len = count($parsedVideos);
            $offset += $len;
            $videos = array_merge($videos, $parsedVideos);
        } while($len == 36);

        return array_reverse($videos);
    }

    private function parseAjaxResult($data, $lastVideoId) {
        $videos = array();

        preg_match_all("/\<img.*class=\"VIDEOS-thumbnail\".*\/\>/", $data, $matches);

        foreach($matches[0] as $match) {
            preg_match("/rel=\"([0-9]*)\"/", $match, $id);
            preg_match("/src=\"(.*?)\"/", $match, $thumbnail);
            preg_match("/alt=\"(.*?)\"/", $match, $title);

            $id = (integer) $id[1];

            if($id == $lastVideoId)
                break;

            $videos[] = array(
                'id' => $id,
                'title' => (string) $title[1],
                'thumbnail' => (string) $thumbnail[1],
            );
        }

        return $videos;
    }
}
