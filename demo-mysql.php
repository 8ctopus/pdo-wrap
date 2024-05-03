<?php

declare(strict_types=1);

use NunoMaduro\Collision\Provider;
use Oct8pus\PDOWrap\PDOWrap;

require_once __DIR__ . '/vendor/autoload.php';

(new Provider())
    ->register();

$host = 'localhost';
$name = 'test';
$user = 'root';
$pass = '123';

$db = PDOWrap::factory("mysql:host={$host};dbname={$name};charset=utf8", $user, $pass, [
    // use exceptions
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    // get arrays
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // better prevention against SQL injections
    PDO::ATTR_EMULATE_PREPARES => false,
]);

$sql = <<<'SQL'
DROP TABLE IF EXISTS `test`
SQL;

$db->exec($sql);

$sql = <<<'SQL'
CREATE TABLE `test` (
    `id` INTEGER PRIMARY KEY AUTO_INCREMENT,
    `birthday` DATE NOT NULL,
    `name` VARCHAR(40) NOT NULL,
    `salary` INT NOT NULL,
    `boss` BIT NOT NULL
)
SQL;

$query = $db->prepare($sql, [], true);
$query->execute();

$sql = <<<'SQL'
INSERT INTO `test`
    (`birthday`, `name`, `salary`, `boss`)
VALUES
    (:birthday, :name, :salary, :boss)
SQL;

$query = $db->prepare($sql, [], true);

$staff = [
    [
        'birthday' => new DateTime('1995-05-01'),
        'name' => 'Sharon',
        'salary' => 200,
        'boss' => true,
    ],
    [
        'birthday' => new DateTime('2000-01-01'),
        'name' => 'John',
        'salary' => 140,
        'boss' => false,
    ],
    [
        'birthday' => new DateTime('1985-08-01'),
        'name' => 'Oliver',
        'salary' => 120,
        'boss' => false,
    ],
];

foreach ($staff as $member) {
    $query->execute($member);
}

$sql = <<<'SQL'
SELECT
    `id`, `birthday`, `name`, `salary`, `boss`
FROM
    `test`
SQL;

$query = $db->prepare($sql, [], true);
$query->execute();

while ($row = $query->fetch()) {
    $birthday = $row['birthday']->format('Y-m-d');
    echo "{$row['id']} {$birthday} {$row['name']} {$row['salary']} {$row['boss']}\n";
}

$query = $db->prepare($sql, [], true);
$query->execute();

$rows = $query->fetchAll();

foreach ($rows as $row) {
    $birthday = $row['birthday']->format('Y-m-d');
    echo "{$row['id']} {$birthday} {$row['name']} {$row['salary']} {$row['boss']}\n";
}

$query = $db->prepare($sql, [], true);
$query->execute();

$value = $query->fetchColumn(1);

$birthday = $value->format('Y-m-d');
echo "{$birthday}\n";