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

    public function test_fetch_will_ignore_wrong_structure()
    {
        $userHandler = new UserHandler();
        $response = $userHandler->fetch([
            'test' => ['test'], 
            '@realmadrid'
        ]);

        $this->assertIsArray($response);
    }

    public function test_fetch_has_alphabetic_key()
    {
        $userHandler = new UserHandler();
        $response = $userHandler->fetch(['@realmadrid']);

        $responseArrayKey = key(reset($response));
        $assertTrue = is_numeric($responseArrayKey) ? false : true;
        
        $this->assertTrue($assertTrue);
    }

    public function test_prepare_data_return_safe_empty_array()
    {
        $userHandler = new UserHandler();
        $response = $userHandler->prepareData([]);

        $this->assertEmpty($response);
    }
}