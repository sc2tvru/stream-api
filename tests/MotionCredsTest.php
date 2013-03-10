<?php

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
        $channels = array();
        $channels[] = array('name' => '1o7lofv9jemne');
        $channels[] = array('name' => 'of14eqp75q8a');

        $info = $streamService->getInfo(array($channels[0]));
        $info = $info[0];
        $this->assertTrue(is_bool($info['live']));
        $this->assertTrue(filter_var($info['thumbnail'], FILTER_VALIDATE_URL) != false);
        $this->assertTrue(array_key_exists('title', $info));
        $this->assertTrue($info['title'] != '');

        $info = $streamService->getInfo($channels);
        foreach($info as $value) {
            $this->assertTrue(is_bool($value['live']));
            $this->assertTrue(filter_var($value['thumbnail'], FILTER_VALIDATE_URL) != false);
            $this->assertTrue(array_key_exists('title', $value));
            $this->assertTrue($value['title'] != '');
        }
    }
}
