<?php

namespace TikTok\Handlers;

use TikTok\Http;
use GuzzleHttp\Promise;
use Symfony\Component\DomCrawler\Crawler;

class UserHandler extends Http
{
    /** @var Crawler */
    private $crawler;

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
            if (!is_string($userId)) {
                continue;
            }
            $promises[$userId]= $this->guzzleClient->getAsync("/$userId");
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
            $this->crawler = new Crawler($response['value']->getBody()->getContents());
            
            try {
                $data[]= [
                    'userId'        => $userId,
                    'fullName'      => $this->extractFullName(),
                    'isVerified'    => $this->extractIsVerified(),
                    'description'   => $this->extractDescription(),
                    'thumbnail'     => $this->extractThumbnail(),
                    'following'     => $this->extractFollowing(),
                    'fans'          => $this->extractFans(),
                ];
            } catch(\Exception $e) {
                echo $e->getMessage();
            }
        }

        return $data;
    }

    /**
     * full_name
     */
    private function extractFullName() : string
    {
        return ($node = $this->crawler->filter('#main .share-info .share-title'))->count() > 0 ? $node->text() : '';
    }

    /**
     * verified
     */
    private function extractIsVerified() : bool
    {
        return $this->crawler->filter('#main .share-info .share-sub-title .header-tag .check-icon')->matches('span.check-icon');
    }

    /**
     * description
     */
    private function extractDescription() : string
    {
        return ($node = $this->crawler->filter('#main .share-info .share-desc'))->count() > 0 ? $node->text() : '';
    }

    /**
     * thumbnail
     */
    private function extractThumbnail() : string
    {
        if (($node = $this->crawler->filter('#main .avatar .avatar-wrapper'))->count() == 0) {
            return '';
        }

        preg_match(
            '/background-image:url\((.*?)\)/', 
            $node->extract(['style'])[0], 
            $thumbnailUrl
        );
        
        return end($thumbnailUrl);
    }

    /**
     * following
     */
    private function extractFollowing() : string
    {
        return ($node = $this->crawler->filter('#main .share-info .count-infos')->filterXPath('//span[@title="Following"]'))->count() > 0 ? $node->text() : '';
    }

    /**
     * fans
     */
    private function extractFans() : string
    {
        return ($node = $this->crawler->filter('#main .share-info .count-infos')->filterXPath('//span[@title="Followers"]'))->count() > 0 ? $node->text() : '';
    }
}