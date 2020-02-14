<?php

use PHPUnit\Framework\TestCase;
use TikTok\Handlers\UserHandler;

class UserHandlerTest extends TestCase
{
    public function test_fetch_returns_safe_empty_array()
    {
        $userHandler = new UserHandler();
        $response = $userHandler->fetch([]);
        
        $this->assertEmpty($response);
    }

    public function test_user_data_extraction_returns_object() {
        $encodedData = '
            <script id="__NEXT_DATA__" type="application/json" crossorigin="anonymous">'
            .json_encode([
                'props' => [
                    'pageProps' => [
                        'userData' => [
                            'userId' => 1
                        ]
                    ]
                ]
            ]).
            '</script>
        ';
        $userHandler = new UserHandler();
        $userData = $userHandler->extractUserData($encodedData);
        
        $this->assertInstanceOf(stdClass::class, $userData);
    }

    public function test_user_data_extraction_returns_null() {
        $encodedData = '
            <script>{"data":"falseData"}</script>
        ';
        $userHandler = new UserHandler();
        $userData = $userHandler->extractUserData($encodedData);
        
        $this->assertNull($userData);
    }

    public function test_prepare_data_return_safe_empty_array()
    {
        $userHandler = new UserHandler();
        $response = $userHandler->prepareData([]);

        $this->assertEmpty($response);
    }
}