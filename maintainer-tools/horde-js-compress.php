#!@php_bin@
<?php
/**
 * Compress JavaScript file(s).
 *
 * Usage: horde-js-compress.php [--file=filename] [--noyui] [--noshrinksafe]
 *                              [--nojsmin] [--noeolstrip] [--overwrite]
 *                              [--debug] [file1] [file2] [file3] ...
 *
 * By default, all files in the current working directory are compressed.
 *
 * By default, will attempt to use the YUI compressor (version 2.3+),
 * available at:
 *   http://developer.yahoo.com/yui/compressor/
 *
 * Alternatively, will attempt to use the Dojo toolkit's 'ShrinkSafe' java
 * binary. As of 4/07, available at:
 *   http://svn.dojotoolkit.org/dojo/trunk/buildscripts/lib/custom_rhino.jar
 * More information at:
 *   http://dojotoolkit.org/docs/shrinksafe
 *
 * @category Horde
 * @package  maintainer_tools
 */

ob_implicit_flush(1);
require 'Console/Getopt.php';

$c = new Console_Getopt();
$argv = $c->readPHPArgv();
array_shift($argv);
$options = $c->getopt2($argv, '', array('file=', 'noyui', 'noshrinksafe', 'nojsmin', 'noeolstrip', 'overwrite', 'debug'));
if (PEAR::isError($options)) {
    print "Invalid arguments.\n";
    exit;
}

// Get file locations
$rhino = dirname(__FILE__) . '/custom_rhino.jar';
$jsmin = trim(`which jsmin`);
if (empty($jsmin)) {
    $jsmin = dirname(__FILE__) . '/jsmin/jsmin';
}
$yui = dirname(__FILE__) . '/yuicompressor.jar';

$debug = $file = $noeolstrip = $overwrite = false;
foreach ($options[0] as $val) {
    switch ($val[0]) {
    case '--file':
        $file = $val[1];
        break;

    case '--noshrinksafe':
        $rhino = null;
        break;

    case '--noyui':
        $yui = null;
        break;

    case '--nojsmin':
        $jsmin = null;
        break;

    case '--noeolstrip':
        $noeolstrip = true;
        break;

    case '--overwrite':
        // Overwrite the current JS files?
        $overwrite = true;
        break;

    case '--debug':
        $debug = true;
        break;
    }
}

$files = $tmp = array();
if (empty($file)) {
    if (!empty($options[1])) {
        $tmp = $options[1];
    } else {
        $d = dir(getcwd());
        while (($entry = $d->read()) !== false) {
            if (preg_match("/(\.js|\.js\.php)$/", $entry)) {
                $tmp[] = $entry;
            }
        }
    }
} else {
    if (file_exists(getcwd() . '/' . $file)) {
        $tmp[] = $file;
    }
}

foreach ($tmp as $val) {
    if ($overwrite) {
        $files[$val] = $val;
    } else {
        $dir = dirname($val);
        $files[$dir . '/../' . $val] = $val;
    }
}

$mode = '';
if (!empty($yui) && file_exists($yui)) {
    print "[Using YUI.]\n";
    $mode = 'yui';
} elseif (!empty($rhino) && file_exists($rhino)) {
    print "[Using ShrinkSafe.]\n";
    $mode = 'rhino';
}

if (!empty($jsmin) && file_exists($jsmin)) {
    if (empty($mode)) {
        $mode = 'jsmin';
    }
} else {
    $jsmin = null;
}

if (!$noeolstrip && empty($mode)) {
    $mode = 'eolstrip';
}

if (empty($mode)) {
    print "[No valid compression modes available. Exiting.]\n";
    exit;
}

foreach ($files as $dest => $file) {
    print "$file: ";

    switch ($mode) {
    case 'yui':
        if (preg_match("/\.js$/", $file)) {
            system("java -jar " . $yui . ' ' . (($debug) ? '-v' : '') . ' -o ' . $dest . ' ' . $file);
            print ' YUI';
        } elseif (!file_exists($dest)) {
            copy($file, $dest);
        }
        break;

    case 'rhino':
        if (preg_match("/\.js$/", $file)) {
            system("java -jar " . $rhino . ' -c ' . $file . ' > ' . $dest);
            print ' ShrinkSafe';
        } elseif (!file_exists($dest)) {
            copy($file, $dest);
        }
    }

    if (!empty($jsmin)) {
        system($jsmin . ' < ' . (in_array($mode, array('rhino', 'yui')) ? $dest : $file) . ' > ' . $dest . '.new');
        rename($dest . '.new', $dest);
        print ' jsmin';
    }

    if (!$noeolstrip) {
        $f = file_get_contents($dest);
        $f = strtr($f, array("\n" => '', "\r" => ''));
        $f = file_put_contents($dest, trim($f));
        print ' EOL-strip';
    }

    print " (done).\n";
}
