<?php

namespace TikTok\Handlers;

use TikTok\Http;
use GuzzleHttp\Promise;

class VideoHandler extends Http
{
    /**
     * @var Object
     */
    private $video;

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
            $promises[]= $this->guzzleClient->getAsync("/@{$parameters['userId']}/video/$videoId");
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
            $this->extractVideoData($response['value']->getBody()->getContents());

            if (is_null($this->video)) {
                continue;
            }

            $data[]= [
                'videoId'       => $this->extractVideoId(),
                'url'           => $this->extractVideoUrl(),
                'name'          => $this->extractVideoName(),
                'description'   => $this->extractVideoDescription(),
                'thumbnail'     => $this->extractVideoThumbnail(),
                'comments'      => $this->extractVideoComments(),
                'interactions'  => $this->extractVideoInteractions(),
                'duration'      => $this->extractVideoDuration(),
                'uploadDate'    => $this->extractVideoUploadDate(),
            ];
        }
        
        return $data;
    }

    /**
     * Extract user data from encoded string
     */
    public function extractVideoData(string $dataEncoded) : ?object
    {
        preg_match(
            '/<script type="application\/ld\+json" id="videoObject">(.*?)<\/script>/',
            $dataEncoded, 
            $videoData
        );
        $videoDataDecoded = json_decode(end($videoData));

        is_object($videoDataDecoded) ? 
            $this->video = $videoDataDecoded : 
            $this->video = null;

        return $this->video;
    }

    /**
     * video_id
     */
    private function extractVideoId() : int
    {
        return array_pad(
            explode('video/', $this->video->mainEntityOfPage->{'@id'}),
            2,
            null
        )[1];
    }

    /**
     * url
     */
    private function extractVideoUrl() : string
    {
        return $this->video->contentUrl;
    }

    /**
     * name
     */
    private function extractVideoName() : string
    {
        return $this->video->name;
    }

    /**
     * description
     */
    private function extractVideoDescription() : string
    {
        return $this->video->description;
    }

    /**
     * thumbnail
     */
    private function extractVideoThumbnail() : string
    {
        return reset($this->video->thumbnailUrl);
    }

    /**
     * comments
     */
    private function extractVideoComments() : int
    {
        return $this->video->commentCount;
    }

    /**
     * interactions
     */
    private function extractVideoInteractions() : int
    {
        return $this->video->interactionCount;
    }

    /**
     * duration
     */
    private function extractVideoDuration() : int
    {
        preg_match(
            '/PT(.*?)S/',
            $this->video->duration,
            $duration
        );

        return end($duration);
    }

    /**
     * upload_date
     */
    private function extractVideoUploadDate() : string
    {
        return date('Y-m-d H:i:s', strtotime($this->video->uploadDate));
    }
}