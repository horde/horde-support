#!@php_bin@
<?php
/**
 * Compress JavaScript file(s).
 *
 * Usage: horde-js-compress.php [--file=filename] [--noyui] [--noshrinksafe]
 *                              [--nojsmin] [--noeolstrip]
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
if (!function_exists('file_put_contents')) {
    require 'PHP/Compat.php';
    PHP_Compat::loadFunction('file_put_contents');
}
require 'Console/Getopt.php';

$c = new Console_Getopt();
$argv = $c->readPHPArgv();
array_shift($argv);
$options = $c->getopt2($argv, '', array('file=', 'noyui', 'noshrinksafe', 'nojsmin', 'noeolstrip', 'debug'));
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

$debug = $file = $noeolstrip = false;
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

    case '--debug':
        $debug = true;
        break;
    }
}

$files = array();
if (empty($file)) {
    if (!empty($options[1])) {
        $files = $options[1];
    } else {
        $d = dir(getcwd());
        while (($entry = $d->read()) !== false) {
            if (preg_match("/(\.js|\.js\.php)$/", $entry)) {
                $files[] = $entry;
            }
        }
    }
} else {
    if (file_exists(getcwd() . '/' . $file)) {
        $files[] = $file;
    }
}

$mode = $jsmin_source = '';
if (!empty($yui) && file_exists($yui)) {
    print "[Using YUI.]\n";
    $mode = 'yui';
    $jsmin_source = '../';
} elseif (!empty($rhino) && file_exists($rhino)) {
    print "[Using ShrinkSafe.]\n";
    $mode = 'rhino';
    $jsmin_source = '../';
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

foreach ($files as $file) {
    print "$file: ";

    switch ($mode) {
    case 'yui':
        if (preg_match("/\.js$/", $file)) {
            system("java -jar " . $yui . ' ' . (($debug) ? '-v' : '') . ' -o ../' . $file . ' ' . $file);
            print ' YUI';
        } elseif (!file_exists('../' . $file)) {
            copy($file, '../' . $file);
        }
        break;

    case 'rhino':
        if (preg_match("/\.js$/", $file)) {
            system("java -jar " . $rhino . ' -c ' . $file . ' > ../' . $file);
            print ' ShrinkSafe';
        } elseif (!file_exists('../' . $file)) {
            copy($file, '../' . $file);
        }
    }

    if (!empty($jsmin)) {
        system($jsmin . ' < ' . $jsmin_source . $file . ' > ../' . $file . '.new');
        rename('../' . $file . '.new', '../' . $file);
        print ' jsmin';
    }

    if (!$noeolstrip) {
        $f = file_get_contents('../' . $file);
        $f = strtr($f, array("\n" => '', "\r" => ''));
        $f = file_put_contents('../' . $file, trim($f));
        print ' EOL-strip';
    }

    print " (done).\n";
}
