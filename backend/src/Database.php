<?php
namespace Jenniferd\NutritionApi;
use PDO;

class Database{
    private PDO $db;

    // ? so, once i load the .env parser, i can use $_ENV anywhere in src?
    public function __construct(){
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            $_ENV['DB_HOST'],
            $_ENV['DB_PORT'],
            $_ENV['DB_NAME']
        );

        $this->db = new PDO(
            $dsn,
            $_ENV['DB_USER'],
            $_ENV['DB_PASSWORD']
        );
    }

    public function getConnection(): PDO{
        return $this->db;
    }
}
?>