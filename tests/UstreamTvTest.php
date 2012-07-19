<?php

require "StreamChannelMock.php";
require "../StreamService.php";
require "../services/UstreamTv.php";

class UstreamTvTest extends PHPUnit_Framework_TestCase {
    public function testGetInfo() {
        $json_string = file_get_contents(strtr(UstreamTv::CHECK_STREAM_STATUS_URL, array(':channel_name' => 'api-test-show',
            ':dev_key' => UstreamTv::DEV_KEY)));
        $data = json_decode($json_string);

        //Validate json structure
        $this->assertTrue(is_object($data));
        $this->assertTrue(property_exists($data, 'results'));
        $this->assertTrue(property_exists($data->results, 'status'));
        $this->assertTrue(property_exists($data->results, 'imageUrl'));
        $this->assertTrue(property_exists($data->results, 'title'));
        $this->assertTrue(property_exists($data->results, 'description'));

        $streamService = new UstreamTv();
        $streamChannels = array();
        $streamChannels[] = new StreamChannelMock("api-test-show");
        $streamChannels[] = new StreamChannelMock("test");

        $info = $streamService->getInfo($streamChannels[0]);
        $this->assertTrue(is_bool($info['live']));
        $this->assertTrue(array_key_exists('title', $info));
        $this->assertTrue(array_key_exists('description', $info));

        $info = $streamService->getInfoBatch($streamChannels);
        foreach($info as $value) {
            $this->assertTrue(is_bool($value['live']));
            $this->assertTrue(array_key_exists('title', $value));
            $this->assertTrue(array_key_exists('description', $value));
        }
    }
}
