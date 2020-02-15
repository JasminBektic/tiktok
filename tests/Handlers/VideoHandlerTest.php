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

    public function test_video_data_extraction_returns_object()
    {
        $encodedData = '
            <script type="application/ld+json" id="videoObject">'
            .json_encode([
                'data' => 'testData'
            ]).
            '</script>
        ';
        $videoHandler = new VideoHandler();
        $videoData = $videoHandler->extractVideoData($encodedData);
        
        $this->assertInstanceOf(stdClass::class, $videoData);
    }

    public function test_video_data_extraction_returns_null()
    {
        $encodedData = '
            <script>{"data":"falseData"}</script>
        ';
        $videoHandler = new VideoHandler();
        $videoData = $videoHandler->extractVideoData($encodedData);
        
        $this->assertNull($videoData);
    }

    public function test_prepare_data_return_safe_empty_array()
    {
        $videoHandler = new VideoHandler();
        $response = $videoHandler->prepareData([]);

        $this->assertEmpty($response);
    }
}