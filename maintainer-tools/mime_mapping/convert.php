<?php
/**
 * Create MIME mapping file from data sources.
 *
 * Copyright 2001-2013 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author    Anil Madhavapeddy <avsm@horde.org>
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2001-2013 Horde LLC
 * @license   http://www.fsf.org/copyleft/gpl.html GPL
 * @package   maintainer_tools
 */

/* Files containing MIME extensions (Apache format).
 * http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types */
$files = array('mime.types', 'mime.types.horde');

/* Files contating MIME extensions (freedesktop.org format).
 * http://www.freedesktop.org/wiki/Specifications/shared-mime-info-spec */
$od_files = array('mime.globs');

$exts = array();
$maxlength = strlen('__MAXPERIOD__');
$maxperiod = 0;

/* Map the mime extensions file(s) into the $exts hash. */
foreach ($files as $val) {
    $data = file($val);
    foreach ($data as $line) {
        /* Remove trailing whitespace. */
        $line = rtrim($line);

        /* Skip comments. */
        if (strpos($line, '#') === 0) {
            continue;
        }

        /* These are tab-delimited files. Skip the entry if there is no
           extension information. */
        $fields = preg_split("/\s+/", $line, 2);
        if (!empty($fields[1])) {
            foreach (preg_split("/\s+/", $fields[1]) as $val2) {
                $exts[$val2] = $fields[0];
                $maxlength = max(strlen($val2), $maxlength);
            }
        }
    }
}

foreach ($od_files as $val) {
    $data = file($val);
    foreach ($data as $line) {
        /* Remove trailing whitespace. */
        $line = rtrim($line);

        /* Skip comments. */
        if (strpos($line, '#') === 0) {
            continue;
        }

        /* These are ':' delimited files. Skip the entry if this is not
           an extension matching glob. */
        $fields = explode(':', $line, 2);
        $pos = strpos($fields[1], '*.');
        if ($pos !== false) {
            $val2 = substr($fields[1], $pos + 2);
            if ((strpos($val2, '*') !== false) ||
                (strpos($val2, '[') !== false) ||
                isset($exts[$val2])) {
                continue;
            }
            $maxperiod = max(substr_count($val2, '.'), $maxperiod);
            $maxlength = max(strlen($val2), $maxlength);
            $exts[$val2] = $fields[0];
        }
    }
}

/* Assemble/sort the extensions into an output array. */
$output = array();
$output[] = "'__MAXPERIOD__'" . str_repeat(' ', $maxlength - strlen('__MAXPERIOD__')) . " => '$maxperiod'";

ksort($exts);
foreach ($exts as $key => $val) {
    $output[] = "'$key'" . str_repeat(' ', $maxlength - strlen($key)) . " => '$val'";
}

/* Generate the PHP output file. */
print <<<HEADER
<?php
/**
 * This file contains a mapping of common file extensions to
 * MIME types. It has been automatically generated from the
 * horde-support/maintainer-tools/mime_mapping directory.
 *
 * ALL changes should be made to
 * horde-support/maintainer-tools/mime_mapping/mime.types.horde or else they
 * they will be lost when this file is regenerated.
 *
 * Any unknown file extensions will automatically be mapped to
 * 'x-extension/<ext>' where <ext> is the unknown file extension.
 *
 * @category Horde
 * @package  Horde_Mime
 *
HEADER;

/* Add the generated information. */
print "\n * Generated: " . strftime('%D %T') . " by " . $_SERVER['USER'] . " on " . $_SERVER['HOST'] . "\n";
print " */\n";
print "\$mime_extension_map = array(\n    " . implode(",\n    ", $output) . "\n);";
