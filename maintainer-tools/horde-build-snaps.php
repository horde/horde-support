#!/usr/bin/env php
<?php
/**
 * Script to build snapshot tarballs.
 *
 * @category Horde
 * @package devtools
 */

// How many days to keep snapshots for.
$days = 7;

// Location of MD5 binary.
$md5_path = '/sbin/md5';

// Apps to build.
$apps = array(
    // Apps in git:horde-hatchery
    'hatchery' => array(
        'ansel',
        'chora',
        'gollem',
        'imp',
        'ingo',
        'jeta',
        'kronolith',
        'nag',
        'skeleton',
        'turba'
    ),

    // Apps in CVS HEAD
    'cvs' => array(
        'agora',
        'forwards',
        'framework',
        'genie',
        'hermes',
        'horde',
        'jonah',
        'juno',
        'klutz',
        'luxor',
        'merk',
        'midas',
        'mnemo',
        'mottle',
        'nic',
        'passwd',
        'sam',
        'scry',
        'sesha',
        'trean',
        'ulaform',
        'vacation',
        'vilma',
        'whups',
        'wicked',
    ),

    // Apps in FRAMEWORK_3 CVS
    'fw3' => array(
        'agora',
        'ansel',
        'chora',
        'dimp',
        'forwards',
        'framework',
        'gollem',
        'hermes',
        'horde',
        'imp',
        'ingo',
        'klutz',
        'kronolith',
        'mimp',
        'mnemo',
        'nag',
        'passwd',
        'scry',
        'trean',
        'turba',
        'vacation',
        'whups',
        'wicked',
    )
);

// Create directory for current day
$dir = date('Y-m-d');
if (!is_dir($dir)) {
    mkdir($dir);
}

// Prune old snapshots.
prune($days);

// Do git stuff
foreach ($apps['hatchery'] as $val) {
    tarballGit($dir, $val, 'horde-hatchery');
}

// Do CVS stuff
foreach ($apps['cvs'] as $val) {
    tarballCVS($dir, $val, 'HEAD');
}

foreach ($apps['fw3'] as $val) {
    tarballCVS($dir, $val, 'FRAMEWORK_3');
}

// Update latest/ symlink.
system("ln -sfh $dir latest");


/**
 * Functions
 */
function tarballGit($dir, $module, $repo)
{
    // git archive --format=tar --prefix=imp/ HEAD:imp/ | gzip -9 > imp.tar.gz
}

function tarballCVS($dir, $module, $tag)
{
    system('cd ' . $dir . '; cvs -Q export -r ' . $tag . ' -d ' . $module . '-' . $tag . ' ' . $module . ' > /dev/null');
    system('cd ' . $dir . '; tar -zcf ' . $module . '-' . $tag . '-' . $dir . '.tar.gz ' . $module . '-' . $tag);
    system('cd ' . $dir . '; ' . $GLOBALS['md5_path'] . ' ' . $module . '-' . $tag . '-' . $dir . '.tar.gz > ' . $module . '-' . $tag . '-' . $dir . '.tar.gz.md5sum');
    system('rm -rf ' . $dir . '/' . $module . '-' . $tag);
}

function prune($keep)
{
    if ($cwd = opendir(getcwd())) {
        $dirs = array();

        # Build a list of all the YYYY-MM-DD directories in this directory.
        while (false !== ($entry = readdir($cwd))) {
            if (is_dir($entry) && preg_match('/^\d+\-\d+\-\d+/', $entry)) {
                $dirs[] = $entry;
            }
        }
        closedir($cwd);

        # Reverse-sort the list and remove the number of directories that we
        # want to keep (which will be the first $keep number of elements).
        rsort($dirs);
        $dirs = array_slice($dirs, $keep);

        # Prune (recursively delete) the rest of the directories in the list.
        foreach ($dirs as $dir) {
            system("rm -rf $dir");
        }
    }
}
