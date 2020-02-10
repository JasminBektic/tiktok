<?php

namespace TikTok\Handlers;

use TikTok\Http;
use GuzzleHttp\Promise;
use Symfony\Component\DomCrawler\Crawler;

class VideoHandler extends Http
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
    public function fetch(array $parameters) : array
    {
        if (!isset($parameters['videoIds']) || !is_array($parameters['videoIds']) || !is_string($parameters['userId'])) {
            return [];
        }

        $promises = [];
        foreach ($parameters['videoIds'] as $videoId) {
            $videoPath = "/{$parameters['userId']}/video/$videoId";
            $promises[$_ENV['SCRAPE_TARGET'] . $videoPath]= $this->guzzleClient->getAsync($videoPath);
        }

        return Promise\settle($promises)->wait();
    }

    /**
     * Preparing data for insertion
     */
    public function prepareData(array $responses) : array
    {
        $data = [];
        foreach ($responses as $videoUrl => $response) {
            $this->crawler = new Crawler($response['value']->getBody()->getContents());

            try {
                $data[]= [
                    'videoId'   => explode('/', $videoUrl)[5],
                    'url'       => $videoUrl,
                    'name'      => $this->extractName(),
                    'comments'  => $this->extractComments(),
                    'duration'  => $this->extractDuration(),
                ];
            } catch(\Exception $e) {
                echo $e->getMessage();
            }
        }

        return $data;
    }

    /**
     * name
     */
    private function extractName() : string
    {
        return ($node = $this->crawler->filter('#main .content-container .video-meta-title > strong'))->count() > 0 ? $node->text() : '';
    }

    /**
     * comments
     */
    private function extractComments() : string
    {
        if (($node = $this->crawler->filter('#main .content-container .video-meta'))->count() == 0) {
            return 0;
        }

        preg_match(
            '/· (.*?) /', 
            $node->text(),
            $comments
        );

        return $comments[1];
    }

    /**
     * duration
     */
    private function extractDuration() : float
    {
        if (($node = $this->crawler->filter('#main .image-card video'))->count() == 0) {
            return 0;
        }

        $videoUrl = $node->extract(['src'])[0];

        $cmdOutput = shell_exec("ffprobe -v quiet -print_format json -show_format -show_streams $videoUrl");
        $cmdOutput = json_decode($cmdOutput, true);

        return $cmdOutput['format']['duration'];
    }
}