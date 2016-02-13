<?php
// replace with file to your own project bootstrap
require_once "vendor/autoload.php";

use Doctrine\ORM\Tools\Console\ConsoleRunner;

$dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => 'blockchain',
    'password' => 'blockchain',
    'dbname'   => 'blockchain',
    'host'     => 'multichain-mysql.docker',
);
$isDevMode = true;
$paths = array("./yml");
$config = \Doctrine\ORM\Tools\Setup::createYAMLMetadataConfiguration($paths, $isDevMode);
$entityManager = \Doctrine\ORM\EntityManager::create($dbParams, $config);


return ConsoleRunner::createHelperSet($entityManager);
