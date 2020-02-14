<?php

namespace TikTok\Repositories;

use TikTok\Database;

class UserRepository
{
    /**
     * Table name.
     *
     * @var string
     */
    private $table = 'users';

    /**
     * Save data
     */
    public function save(array $data) : void
    {
        foreach ($data as $d) {
            $sql = sprintf(
                "INSERT INTO $this->table (user_id, full_name, description, thumbnail, verified, followers, hearts, following, videos) 
                VALUES ('%d', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d') 
                ON DUPLICATE KEY UPDATE 
                    user_id='%d', full_name='%s', description='%s', thumbnail='%s', verified='%d', followers='%d', hearts='%d', following='%d', videos='%d'",
                $d['userId'],
                $d['fullName'],
                $d['description'],
                $d['thumbnail'],
                $d['isVerified'],
                $d['followers'],
                $d['hearts'],
                $d['following'],
                $d['videos'],
                $d['userId'],
                $d['fullName'],
                $d['description'],
                $d['thumbnail'],
                $d['isVerified'],
                $d['followers'],
                $d['hearts'],
                $d['following'],
                $d['videos']
            );

            Database::$connection->query($sql);
        }
    }
}