#!@php_bin@
<?php
/**
 * Script to recursively find all PNG's in a directory and attempt to max
 * compress them.
 *
 * Usage: horde-compress-pngs.php -a [advpng binary]
 *                                -d [directory]
 *                                -o [optipng binary]
 *
 * @category Horde
 * @package  maintainer_tools
 */

require 'Console/Getopt.php';

$c = new Console_Getopt();
$argv = $c->readPHPArgv();
array_shift($argv);
$options = $c->getopt2($argv, 'a:d:o:');
if (PEAR::isError($options)) {
    print "Invalid arguments.\n";
    exit;
}

$directory = getcwd();

foreach ($options[0] as $val) {
    switch ($val[0]) {
    case 'a':
        $advpng_binary = $val[1];
        break;

    case 'd':
        $directory = $val[1];
        break;

    case 'o':
        $optipng_binary = $val[1];
        break;
    }
}

if (empty($advpng_binary) || empty($optipng_binary)) {
    exit("Invalid arguments.\n");
}

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, RecursiveIteratorIterator::SELF_FIRST));
while ($it->valid()) {
    if ($it->isFile() &&
        (strcasecmp('.png', substr($it->key(), -4)) === 0)) {
        system($advpng_binary . ' -z4 ' . $it->key());
        system($optipng_binary . ' -o7 ' . $it->key());
    }
    $it->next();
}
