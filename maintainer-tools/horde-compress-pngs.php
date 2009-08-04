#!@php_bin@
<?php
/**
 * Script to find all PNG's in a horde installation, and attempt to max
 * compress them.
 *
 * Usage: horde-compress-pngs.php -h [horde_base] -a [advpng binary]
 *                                -o [optipng binary]
 *
 * @category Horde
 * @package devtools
 */

require 'File/Find.php';
require 'Console/Getopt.php';

$c = new Console_Getopt();
$argv = $c->readPHPArgv();
array_shift($argv);
$options = $c->getopt2($argv, 'h:a:o:');
if (PEAR::isError($options)) {
    print "Invalid arguments.\n";
    exit;
}

foreach ($options[0] as $val) {
    switch ($val[0]) {
    case 'h':
        $horde_base = $val[1];
        break;

    case 'a':
        $advpng_binary = $val[1];
        break;

    case 'o':
        $optipng_binary = $val[1];
        break;
    }
}

if (empty($horde_base) || empty($advpng_binary) || empty($optipng_binary)) {
    print "Invalid arguments.\n";
    exit;
}

$f = new File_Find();
$a = $f->search('/.png$/', $horde_base, 'perl', false, 'files');

foreach ($a as $val) {
    if (strpos($val, '/hordeweb/') === false) {
        system($advpng_binary . ' -z4 ' . $val);
        system($optipng_binary . ' -o7 ' . $val);
    }
}

file_put_contents('png-compress.txt', implode(' ', $a));
