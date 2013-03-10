<?php

require "../StreamService.php";
require "../services/JustinTv.php";

class JustinTvTest extends PHPUnit_Framework_TestCase {
    public function testGetInfo() {
        @$xml = simplexml_load_file('http://api.justin.tv/api/stream/list.xml?limit=5');

        $streamService = new JustinTv();
        $channels = array();

        foreach ($xml->children() as $stream) {
            $channels[] = array('name' => (string) $stream->channel->login);
        }
        $channels[] = array('name' => 'testtesttest');

        $info = $streamService->checkChannel(array('name' => 'testtesttest'));
        $this->assertNull($info);

        $info = $streamService->checkChannel($channels[0]);
        $this->assertTrue($info['name'] === $channels[0]['name']);

        $info = $streamService->getInfo($channels);
        foreach($info as $value) {
            $this->assertTrue($value['live']);
            $this->assertTrue(filter_var($value['thumbnail'], FILTER_VALIDATE_URL) != false);
            $this->assertTrue(array_key_exists('title', $value));
            $this->assertTrue(is_int($value['viewers']));
        }
    }
}
