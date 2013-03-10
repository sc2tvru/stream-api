<?php

require "../StreamService.php";
require "../services/HashdTv.php";

class HashdTvTest extends PHPUnit_Framework_TestCase {
    public function testGetInfo() {
        $streamService = new HashdTv();

        $info = $streamService->getInfo(array(array('name' => 'alkoholikstv')));

        $this->assertTrue(array_key_exists('live', $info[0]));
        $this->assertTrue(is_bool($info[0]['live']));
        $this->assertTrue(filter_var($info[0]['thumbnail'], FILTER_VALIDATE_URL) != false);
        $this->assertTrue(array_key_exists('viewers', $info[0]));
        $this->assertTrue(is_int($info[0]['viewers']));
    }
}
