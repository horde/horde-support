#!@php_bin@
<?php
/**
 * Script to recursively find all PNG's in a directory and attempt to max
 * compress them.
 *
 * Usage: horde-compress-pngs.php -a [advpng binary]
 *                                -d [directory]
 *                                -o [optipng binary]
 *                                -p [pngout binary]
 *                                -z [zopflipng binary -- OPTIONAL]
 *
 * ZopfliPNG = https://code.google.com/p/zopfli/
 *
 *
 * Copyright 2011-2013 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2011-2013 Horde LLC
 * @license   http://www.fsf.org/copyleft/lgpl.html LGPL
 * @package   maintainer_tools
 */

require 'Console/Getopt.php';

$c = new Console_Getopt();
$argv = $c->readPHPArgv();
array_shift($argv);
$options = $c->getopt2($argv, 'a:d:o:p:z:');
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

    case 'p':
        $pngout_binary = $val[1];
        break;

    case 'z':
        $zopfli_binary = $val[1];
        break;
    }
}

if (empty($advpng_binary) || empty($optipng_binary) || empty($pngout_binary)) {
    exit("Invalid arguments.\n");
}

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, RecursiveIteratorIterator::SELF_FIRST));
while ($it->valid()) {
    if ($it->isFile() &&
        (strcasecmp('.png', substr($it->key(), -4)) === 0)) {
        print "FILE: {$it->key()}\n";

        $start_size = $it->getSize();
        copy($it->key(), $it->key() . '.bak');

        system($advpng_binary . ' -z4 -q ' . $it->key());
        system($pngout_binary . ' ' . $it->key() . ' -y -q');

        if (!empty($zopfli_binary)) {
            system($zopfli_binary . ' -m ' . $it->key() . ' ' . $it->key() . '.out');
            unlink($it->key());
            rename($it->key() . '.out', $it->key());
        }

        system($advpng_binary . ' -z4 -q ' . $it->key());
        system($optipng_binary . ' -zc1-9 -zm1-9 -zs0-3 -f0-5 -quiet ' . $it->key());
        system($pngout_binary . ' ' . $it->key() . ' -y -q');

        clearstatcache();
        $end_size = $it->getSize();

        if ($start_size > $end_size) {
            print $it->key() . ":\n" .
                  '    ' . $start_size . ' => ' . $end_size . ' (' . round((($start_size - $end_size) / $start_size) * 100, 1) . "%)\n";
            unlink($it->key() . '.bak');
        } else {
            unlink($it->key());
            rename($it->key() . '.bak', $it->key());
        }
    }
    $it->next();
}
