#!@php_bin@
<?php
/**
 * Script to update prototype and scriptaculous libraries.
 *
 * $Horde: framework/devtools/horde-update-protaculous.php,v 1.3 2007/06/29 13:43:31 jan Exp $
 *
 * @category Horde
 * @package devtools
 */

if (empty($_SERVER['argv'][1])) {
    exit(wordwrap("No Horde root specified. Run as \"horde-update-protaculous.php /path/to/horde\"\n"));
}

$horde_base = $_SERVER['argv'][1];
if (!is_dir($horde_base)) {
    exit('"' . $horde_base . "\" is not a directory.\n");
}

$protaculous_base = dirname(__FILE__);
$protaculous_files = explode("\n", trim(shell_exec('ls lib/*.js src/*.js')));

$compress = array();
foreach ($protaculous_files as $file) {
    $script_name = basename($file);
    $horde_instances = explode("\n", trim(shell_exec('find ' . $horde_base . ' -name ' . $script_name)));
    foreach ($horde_instances as $horde_file) {
        if (strpos($horde_file, 'js/src/') !== false) {
            $compress[dirname($horde_file)] = true;
        }
        copy($file, $horde_file);
    }
}

foreach (array_keys($compress) as $dir_to_compress) {
    chdir($dir_to_compress);
    shell_exec('horde-js-compress.sh');
}
