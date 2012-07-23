<?php

require "StreamChannelMock.php";
require "../StreamService.php";
require "../services/MotionCreds.php";

class MotionCredsTest extends PHPUnit_Framework_TestCase {
    public function testCheckChannel() {
        $notExistingChannel = 'lol';

        $json_string = file_get_contents(strtr(MotionCreds::CHECK_STREAM_STATUS_URL, array(':channel_name' => $notExistingChannel)));
        $data = json_decode($json_string);

        $this->assertFalse($data->result->$notExistingChannel->valid);
    }

    public function testGetInfo() {
        $json_string = file_get_contents(strtr(MotionCreds::CHECK_STREAM_STATUS_URL, array(':channel_name' => '1o7lofv9jemne')));
        $data = json_decode($json_string);

        //Validate json structure
        $this->assertTrue(is_object($data));
        $this->assertTrue(property_exists($data, 'result'));
        foreach($data->result as $res) {
            $this->assertTrue(property_exists($res, 'online'));
            $this->assertTrue(property_exists($res, 'name'));
            $this->assertTrue(property_exists($res, 'nbViewers'));
            $this->assertTrue(property_exists($res, 'img'));
        }

        $streamService = new MotionCreds();
        $streamChannels = array();
        $streamChannels[] = new StreamChannelMock('1o7lofv9jemne');
        $streamChannels[] = new StreamChannelMock('of14eqp75q8a');

        $info = $streamService->getInfo($streamChannels[0]);
        $this->assertTrue(is_bool($info['live']));
        $this->assertTrue(filter_var($info['thumbnail'], FILTER_VALIDATE_URL) != false);
        $this->assertTrue(array_key_exists('title', $info));
        $this->assertTrue($info['title'] != '');
        $this->assertTrue(array_key_exists('description', $info));

        $info = $streamService->getInfoBatch($streamChannels);
        foreach($info as $value) {
            $this->assertTrue(is_bool($value['live']));
            $this->assertTrue(filter_var($value['thumbnail'], FILTER_VALIDATE_URL) != false);
            $this->assertTrue(array_key_exists('title', $value));
            $this->assertTrue($value['title'] != '');
            $this->assertTrue(array_key_exists('description', $value));
        }
    }
}
