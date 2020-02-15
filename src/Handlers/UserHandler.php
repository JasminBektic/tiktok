<?php

namespace TikTok\Handlers;

use TikTok\Http;
use GuzzleHttp\Promise;

class UserHandler extends Http
{
    /**
     * @var Object
     */
    private $user;

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
        foreach ($responses as $response) {
            $this->extractUserData($response['value']->getBody()->getContents());

            if (is_null($this->user)) {
                continue;
            }
            
            $data[]= [
                'userId'        => $this->extractUserId(),
                'fullName'      => $this->extractUserName(),
                'isVerified'    => $this->extractUserVerified(),
                'description'   => $this->extractUserDescription(),
                'thumbnail'     => $this->extractUserThumbnail(),
                'followers'     => $this->extractUserFollowers(),
                'hearts'        => $this->extractUserHearts(),
                'following'     => $this->extractUserFollowing(),
                'videos'        => $this->extractUserVideos(),
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

        isset($userDataDecoded->props->pageProps->userData) ? 
            $this->user = $userDataDecoded->props->pageProps->userData : 
            $this->user = null;

        return $this->user;
    }

    /**
     * user_id
     */
    private function extractUserId() : int
    {
        return $this->user->userId;
    }
    
    /**
     * full_name
     */
    private function extractUserName() : string
    {
        return $this->user->nickName;
    }

    /**
     * verified
     */
    private function extractUserVerified() : int
    {
        return $this->user->verified;
    }

    /**
     * description
     */
    private function extractUserDescription() : string
    {
        return $this->user->signature;
    }

    /**
     * thumbnail
     */
    private function extractUserThumbnail() : string
    {
        return reset($this->user->coversMedium);
    }

    /**
     * followers
     */
    private function extractUserFollowers() : int
    {
        return $this->user->fans;
    }

    /**
     * hearts
     */
    private function extractUserHearts() : int
    {
        return $this->user->heart;
    }

    /**
     * following
     */
    private function extractUserFollowing() : int
    {
        return $this->user->following;
    }

    /**
     * video
     */
    private function extractUserVideos() : int
    {
        return $this->user->video;
    }
}