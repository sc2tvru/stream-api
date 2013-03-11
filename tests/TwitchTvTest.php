<?php

require "../StreamService.php";
require "../services/TwitchTv.php";

class TwitchTvTest extends PHPUnit_Framework_TestCase {
    public function testGetInfo() {
        $streamService = new TwitchTv();

        //Validate nonexistent user
        $this->assertNull($streamService->checkChannel(array('name' => 'test_user1')));
    }
}
