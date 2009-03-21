#!/usr/bin/env php
<?php
/**
 * Script to build snapshot tarballs.
 *
 * $Horde: framework/devtools/horde-build-snaps.php,v 1.13 2009/01/18 05:02:58 chuck Exp $
 *
 * @category Horde
 * @package devtools
 */

$modules = array(
    'agora',
    'ansel',
    'forwards',
    'framework',
    'genie',
    'gollem',
    'hermes',
    'horde',
    'ingo',
    'jeta',
    'jonah',
    'juno',
    'klutz',
    'luxor',
    'merk',
    'midas',
    'mnemo',
    'mottle',
    'nag',
    'nic',
    'passwd',
    'sam',
    'scry',
    'sesha',
    'skeleton',
    'trean',
    'turba',
    'ulaform',
    'vacation',
    'vilma',
    'whups',
    'wicked',
);

$framework3 = array(
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
);

$dir = date('Y-m-d');
if (!is_dir($dir)) {
    mkdir($dir);
}

exportCVS();
makeTarballs();
cleanup();
prune(7);

// Update latest/ symlink.
system("ln -sfh $dir latest");


/**
 * Functions
 */
function exportCVS()
{
    global $dir, $modules, $framework3;

    foreach ($modules as $module) {
        system("cd $dir; cvs -Q export -r HEAD $module > /dev/null");
    }
    foreach ($framework3 as $module) {
        system("cd $dir; cvs -Q export -r FRAMEWORK_3 -d ${module}-FRAMEWORK_3 $module");
    }
}

function makeTarballs()
{
    global $dir, $modules, $framework3;

    foreach ($modules as $module) {
        system("cd $dir; tar -zcf ${module}-HEAD-${dir}.tar.gz $module");
        system("cd $dir; /sbin/md5 ${module}-HEAD-${dir}.tar.gz > ${module}-HEAD-${dir}.tar.gz.md5sum");
    }
    foreach ($framework3 as $module) {
        system("cd $dir; tar -zcf ${module}-FRAMEWORK_3-${dir}.tar.gz ${module}-FRAMEWORK_3");
        system("cd $dir; /sbin/md5 ${module}-FRAMEWORK_3-${dir}.tar.gz > ${module}-FRAMEWORK_3-${dir}.tar.gz.md5sum");
    }
}

function cleanup()
{
    global $dir, $modules;

    foreach ($modules as $module) {
        system("rm -rf $dir/$module");
    }
    foreach ($framework3 as $module) {
        system("rm -rf $dir/${module}-FRAMEWORK_3");
    }
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
