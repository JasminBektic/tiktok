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
                "INSERT INTO $this->table (user_id, full_name, description, thumbnail, verified, following, fans) 
                VALUES ('%s', '%s', '%s', '%s', '%d', '%d', '%s') 
                ON DUPLICATE KEY UPDATE 
                    user_id='%s', full_name='%s', description='%s', thumbnail='%s', verified='%d', following='%d', fans='%s'",
                $d['userId'],
                $d['fullName'],
                $d['description'],
                $d['thumbnail'],
                $d['isVerified'],
                $d['following'],
                $d['fans'],
                $d['userId'],
                $d['fullName'],
                $d['description'],
                $d['thumbnail'],
                $d['isVerified'],
                $d['following'],
                $d['fans']
            );

            Database::$connection->query($sql);
        }
    }
}