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
                "INSERT INTO $this->table (video_id, name, description, url, thumbnail, comments, interactions, duration, upload_date) 
                VALUES ('%d', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s') 
                ON DUPLICATE KEY UPDATE 
                    video_id='%d', name='%s', description='%s', url='%s', thumbnail='%s', comments='%d', interactions='%d', duration='%d', upload_date='%s'",
                $d['videoId'],
                $d['name'],
                $d['description'],
                $d['url'],
                $d['thumbnail'],
                $d['comments'],
                $d['interactions'],
                $d['duration'],
                $d['uploadDate'],
                $d['videoId'],
                $d['name'],
                $d['description'],
                $d['url'],
                $d['thumbnail'],
                $d['comments'],
                $d['interactions'],
                $d['duration'],
                $d['uploadDate']
            );

            Database::$connection->query($sql);
        }
    }
}