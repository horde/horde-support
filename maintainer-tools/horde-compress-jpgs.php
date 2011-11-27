#!@php_bin@
<?php
/**
 * Script to recursively find all JPG's in a directory and attempt to optimize
 * them. (Does not remove EXIF metadata; tool only optimizes Huffman coding).
 *
 * Jpegtran binary from the standard libjpeg library (http://ijg.org).
 *
 * Usage: horde-compress-jpgs.php -d [directory]
 *                                -j [jpegtran binary]
 *
 * Copyright 2011 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author   Michael Slusarz <slusarz@horde.org>
 * @category Horde
 * @license  http://www.fsf.org/copyleft/lgpl.html LGPL
 * @package  maintainer_tools
 */

require 'Console/Getopt.php';

$c = new Console_Getopt();
$argv = $c->readPHPArgv();
array_shift($argv);
$options = $c->getopt2($argv, 'd:j:');
if (PEAR::isError($options)) {
    print "Invalid arguments.\n";
    exit;
}

$directory = getcwd();

foreach ($options[0] as $val) {
    switch ($val[0]) {
    case 'd':
        $directory = $val[1];
        break;

    case 'j':
        if (is_executable($val[1])) {
            $jpegtran_binary = $val[1];
        }
        break;
    }
}

if (empty($jpegtran_binary)) {
    exit("Invalid arguments.\n");
}

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, RecursiveIteratorIterator::SELF_FIRST));
while ($it->valid()) {
    if ($it->isFile() &&
        (strcasecmp('.jpg', substr($it->key(), -4)) === 0)) {
        $file = realpath($it->key());
        $jpeg = '';
        $start_size = $it->getSize();

        $fp = popen($jpegtran_binary . ' -optimize ' . escapeshellarg($file), 'r');
        while (!feof($fp)) {
            $jpeg .= fread($fp, 8192);
        }
        fclose($fp);

        $end_size = strlen($jpeg);
        if ($end_size < $start_size) {
            file_put_contents($file, $jpeg);
            print $file . ":\n" .
                  '    ' . $start_size . ' => ' . $end_size . ' (' . round((($start_size - $end_size) / $start_size) * 100, 1) . "%)\n";
        }
    }
    $it->next();
}
