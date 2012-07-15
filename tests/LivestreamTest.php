<?php

require "StreamChannelMock.php";
require "../StreamService.php";
require "../services/Livestream.php";

class LivestreamTest extends PHPUnit_Framework_TestCase {
    public function testGetInfo() {
        @$xml = simplexml_load_file(strtr(Livestream::CHECK_STREAM_V1_0_STATUS_URL, array(':channel_name' => 'proshowcase,livestream')));

        $dom=new DOMDocument;
        //Something wrong with namespaces in this xml
        $dom->loadXML(str_replace('ls:', '', $xml->asXML()));

        //Check xml scheme
        $this->assertTrue($dom->schemaValidate('./schema/livestream_v1.0_info.xsd'));

        $streamService = new Livestream();
        $streamChannels = array();
        $streamChannels[] = new StreamChannelMock('proshowcase');
        $streamChannels[] = new StreamChannelMock('livestream');

        $info = $streamService->getInfo($streamChannels[0]);
        $this->assertTrue(is_bool($info['live']));
        $this->assertTrue(filter_var($info['thumbnail'], FILTER_VALIDATE_URL) != false);
        $this->assertTrue(array_key_exists('title', $info));
        $this->assertTrue(array_key_exists('description', $info));
        $this->assertTrue(is_int($info['viewers']));

        $info = $streamService->getInfoBatch($streamChannels);
        foreach($info as $key => $value) {
            $this->assertTrue(is_bool($value['live']));
            $this->assertTrue(filter_var($value['thumbnail'], FILTER_VALIDATE_URL) != false);
            $this->assertTrue(array_key_exists('title', $value));
            $this->assertTrue(array_key_exists('description', $value));
            $this->assertTrue(is_int($value['viewers']));
        }
    }
}
