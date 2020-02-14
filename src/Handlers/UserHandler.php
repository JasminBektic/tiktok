<?php

namespace TikTok\Handlers;

use TikTok\Http;
use GuzzleHttp\Promise;

class UserHandler extends Http
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Fetching TikTok 
     */
    public function fetch(array $userIds) : array
    {
        $promises = [];
        foreach ($userIds as $userId) {
            $promises[]= $this->guzzleClient->getAsync("/@$userId");
        }

        return Promise\settle($promises)->wait();
    }

    /**
     * Preparing data for insertion
     */
    public function prepareData(array $responses) : array
    {
        $data = [];
        foreach ($responses as $userId => $response) {
            $user = $this->extractUserData($response['value']->getBody()->getContents());

            if (is_null($user)) {
                continue;
            }
            
            $data[]= [
                'userId'        => $user->userId,
                'fullName'      => $user->nickName,
                'isVerified'    => $user->verified,
                'description'   => $user->signature,
                'thumbnail'     => reset($user->coversMedium),
                'followers'     => $user->fans,
                'hearts'        => $user->heart,
                'following'     => $user->following,
                'videos'        => $user->video,
            ];
        }

        return $data;
    }

    /**
     * Extract user data from encoded string
     */
    public function extractUserData(string $dataEncoded) : ?object
    {
        preg_match(
            '/<script id="__NEXT_DATA__" type="application\/json" crossorigin="anonymous">(.*?)<\/script>/',
            $dataEncoded, 
            $userData
        );
        $userDataDecoded = json_decode(end($userData));

        return ($user = @$userDataDecoded->props->pageProps->userData) ? $user : null;
    }
}