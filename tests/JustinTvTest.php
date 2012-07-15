<?php

require "StreamChannelMock.php";
require "../StreamService.php";
require "../services/JustinTv.php";

class JustinTvTest extends PHPUnit_Framework_TestCase {
    public function testGetInfo() {
        @$xml = simplexml_load_file('http://api.justin.tv/api/stream/list.xml?limit=5');
        $dom=new DOMDocument;
        $dom->loadXML($xml->asXML());

        //Check xml scheme
        $this->assertTrue($dom->schemaValidate('./schema/justin_tv_list.xsd'));

        //We obtain one live stream. Check it's info with getInfo method.
        $streamService = new JustinTv();
        $streamChannels = array();

        foreach ($xml->children() as $stream) {
            $streamChannels[] = new StreamChannelMock((string) $stream->channel->login);
        }
        $streamChannels[] = new StreamChannelMock('testtesttest');

        $info = $streamService->getInfo($streamChannels[0]);
        $this->assertTrue($info['live']);
        $this->assertTrue(filter_var($info['thumbnail'], FILTER_VALIDATE_URL) != false);
        $this->assertTrue(array_key_exists('title', $info));
        $this->assertTrue(is_int($info['viewers']));

        $info = $streamService->getInfoBatch($streamChannels);
        foreach($info as $key => $value) {
            if($key == 'testtesttest') {
                $this->assertFalse($value['live']);
            } else {
                $this->assertTrue($value['live']);
                $this->assertTrue(filter_var($value['thumbnail'], FILTER_VALIDATE_URL) != false);
                $this->assertTrue(array_key_exists('title', $value));
                $this->assertTrue(is_int($value['viewers']));
            }
        }
    }
}
