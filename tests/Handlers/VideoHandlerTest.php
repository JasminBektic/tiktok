<?php

use PHPUnit\Framework\TestCase;
use TikTok\Handlers\VideoHandler;

class VideoHandlerTest extends TestCase
{
    public function test_fetch_returns_safe_empty_array()
    {
        $videoHandler = new VideoHandler();
        $response = $videoHandler->fetch([]);
        
        $this->assertEmpty($response);
    }

    public function test_fetch_will_ignore_wrong_structure()
    {
        $videoHandler = new VideoHandler();
        $response = $videoHandler->fetch([
            'user' => ['test'], 
            'videoIds' => 'rere'
        ]);

        $this->assertEmpty($response);
    }

    public function test_fetch_has_alphabetic_key()
    {
        $videoHandler = new VideoHandler();
        $response = $videoHandler->fetch([
            'userId' => '@realmadrid', 
            'videoIds' => [
                '6721977173101579526'
            ]
        ]);

        $responseArrayKey = key(reset($response));
        $assertTrue = is_numeric($responseArrayKey) ? false : true;
        
        $this->assertTrue($assertTrue);
    }

    public function test_prepare_data_return_safe_empty_array()
    {
        $videoHandler = new VideoHandler();
        $response = $videoHandler->prepareData([]);

        $this->assertEmpty($response);
    }
}