<?php
declare(strict_types=1);

namespace Blog;

use Exception;
use PDO;

class PostMapper
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getByUrlKey(string $urlKey): ?array
    {
        $statement = $this->connection->prepare('SELECT * FROM post WHERE url_key =:url_key');
        $statement->execute([
            'url_key' => $urlKey
        ]);
        $result = $statement->fetchAll();
        return array_shift($result);
    }

    /**
     * @throws Exception
     */
    public function getList(string $derection): ?array
    {
        if(!in_array($derection, ['DESC', 'ASC'])){
            throw new Exception('The direction is not supported');
        }
        $statement = $this->connection->prepare('SELECT * FROM post ORDER BY published_date  ' .  $derection);
        $statement->execute();
        return $statement->fetchAll();
    }
}
