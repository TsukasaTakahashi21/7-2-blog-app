<?php

namespace App\Infrastructure\Dao;
require_once __DIR__ . '/../../../vendor/autoload.php';
use App\Domain\ValueObject\User\NewUser;
use App\Domain\ValueObject\Email;
use \PDO;
use PDOException;

final class UserDao
{
    const TABLE_NAME = 'users';
    private $pdo;

    public function __construct()
    {
        try {
            $this->pdo = new PDO(
                'mysql:dbname=blog;host=mysql;charset=utf8',
                'root',
                'password'
            );
        } catch (PDOException $e) {
            exit('DB接続エラー:' . $e->getMessage());
        }
    }

    public function create(NewUser $user): void {
        $hashedPassword = $user->password()->hash();

        $sql = sprintf(
            'INSERT INTO %s (name, email, password) VALUES (:name, :email, :password)',
            self::TABLE_NAME
        );
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':name', $user->name()->value(), PDO::PARAM_STR);
        $statement->bindValue(':email', $user->email()->value(), PDO::PARAM_STR);
        $statement->bindValue(':password', $user->password(), PDO::PARAM_STR);
        $statement->execute();
    }

    public function findByEmail(Email $email): ?array
    {
        $sql = sprintf(
            'SELECT * FROM %s WHERE email = :email',
            self::TABLE_NAME
        );
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':email', $email->value(), PDO::PARAM_STR);
        $statement->execute();
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        return $user === false ? null : $user;
    }
}