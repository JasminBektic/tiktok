<?php

namespace TikTok\Repositories;

use TikTok\Database;

class VideoRepository
{
    /**
     * Table name.
     *
     * @var string
     */
    private $table = 'videos';

    /**
     * Save data
     */
    public function save(array $data) : void
    {
        foreach ($data as $d) {
            $sql = sprintf(
                "INSERT INTO $this->table (video_id, name, url, comments, duration) 
                VALUES ('%s', '%s', '%s', '%s', '%.f') 
                ON DUPLICATE KEY UPDATE 
                    video_id='%s', name='%s', url='%s', comments='%s', duration='%.f'",
                $d['videoId'],
                $d['name'],
                $d['url'],
                $d['comments'],
                $d['duration'],
                $d['videoId'],
                $d['name'],
                $d['url'],
                $d['comments'],
                $d['duration']
            );

            Database::$connection->query($sql);
        }
    }
}