<?php

require "StreamChannelMock.php";
require "../StreamService.php";
require "../services/Own3DTv.php";

class Own3DTvTest extends PHPUnit_Framework_TestCase {
    public function testGetInfo() {
        @$xml = simplexml_load_file(strtr(Own3DTv::CHECK_STREAM_STATUS_URL, array(':channel_id' => '113318')));

        $dom=new DOMDocument;
        $dom->loadXML($xml->asXML());

        //Check xml scheme
        $this->assertTrue($dom->schemaValidate('./schema/own3d_status.xsd'));

        $streamService = new Own3DTv();

        $info = $streamService->getInfo(new StreamChannelMock('iCanFly', 113318));

        $this->assertTrue(array_key_exists('live', $info));
        if($info['live']) {
            $this->assertTrue(is_bool($info['live']));
            $this->assertTrue(filter_var($info['thumbnail'], FILTER_VALIDATE_URL) != false);
            $this->assertTrue(array_key_exists('viewers', $info));
            $this->assertTrue(is_int($info['viewers']));
        }
    }
}
