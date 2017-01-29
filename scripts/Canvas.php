<?php
require_once(realpath(__DIR__) . '/../src/Canvas/File/Model.php');
require_once(realpath(__DIR__) . '/../src/Canvas/File/Entity.php');
require_once(realpath(__DIR__) . '/../src/Canvas/File/Mapper.php');
require_once(realpath(__DIR__) . '/../src/Canvas/File/Query.php');
require_once(realpath(__DIR__) . '/../src/Canvas/File/Named.php');
require_once(realpath(__DIR__) . '/../src/Canvas/File/Collection.php');
require_once(realpath(__DIR__) . '/../src/Canvas/Writer.php');

if (count($argv) < 2) {
    echo "Usage: {$argv[0]} <model> [config]\n";
    exit;
}

if (file_exists('.canvas/config.php')) {
    /* Guess a config path */
    $config = include('.canvas/config.php'); 
}

if (isset($argv[2])) {
    /* A config path was provided */
    $config = include($argv[2]); 
}

if (!isset($config)) {
    echo "Could not find config file, please specify path\n";
    exit;
}

$model = ucfirst($argv[1]);

if (!is_dir($config['path'])) {
    echo "Path specified in config does not exist: {$config['path']}\n";
    exit;
}

echo "Creating {$model} model in directory: {$config['path']}\n";

if (!is_dir("{$config['path']}/{$model}")) {
    echo "- Creating model directory in {$config['path']}\n";
    mkdir("{$config['path']}/{$model}");
}

$files = array(
    new Canvas\File\Model($config['namespace'], $model),
    new Canvas\File\Entity($config['namespace'], $model),
    new Canvas\File\Mapper($config['namespace'], $model),
    new Canvas\File\Collection($config['namespace'], $model),
    new Canvas\File\Query($config['namespace'], $model),
    new Canvas\File\Named($config['namespace'], $model),
);

$writer = new Canvas\Writer($config['path']);

foreach ($files as $file) {
    try {
        $writer->create($file);
        echo "- Created {$file->getRelativePath()}\n";
    } catch (Exception $e) {
        echo "- {$e->getMessage()}\n";
    }
}
