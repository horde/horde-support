#!/usr/bin/env php
<?php
/**
 * Script to build snapshot tarballs.
 *
 * @category Horde
 * @package  maintainer_tools
 */

// How many days to keep snapshots for.
$days = 7;

// Location of MD5 binary.
$md5_path = '/sbin/md5';

// Location of git binary
$git_dir = '/usr/local/bin/git';

// Path to git repos (this is the location of the .git data files)
$git_horde = '/home/horde/git/horde/.git';

// Apps to build.
$apps = array(
    // Apps in horde-git
    'git' => array(
        'ansel',
        'chora',
        'framework',
        'gollem',
        'horde',
        'imp'
        'ingo',
        'jeta',
        'kronolith',
        'nag',
        'skeleton',
        'turba'
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
        'sam',
        'scry',
        'skeleton',
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
foreach ($apps['git'] as $val) {
    tarballGit($dir, $val, $git_horde, 'horde-git');
}

// Do CVS stuff
foreach ($apps['fw3'] as $val) {
    tarballCVS($dir, $val, 'FRAMEWORK_3');
}

// Update latest/ symlink.
system("ln -sfh $dir latest");


/**
 * Functions
 */
function tarballGit($dir, $module, $repo, $name)
{
    global $git_dir;
    
    $filename = $module . '-' . $name . '.tar.gz';
    system('cd ' . $dir . '; ' . $git_dir . ' --git-dir=' . escapeshellarg($repo) . ' archive --format=tar --prefix=' . $module . '/ HEAD:' . $module . '/ | gzip -9 > ' . $filename);
    system('cd ' . $dir . '; ' . $GLOBALS['md5_path'] . ' ' . $filename . ' > ' . $filename . '.md5sum');
}

function tarballCVS($dir, $module, $tag)
{
    $filename = $module . '-' . $tag . '-' . $dir . '.tar.gz';
    system('cd ' . $dir . '; cvs -Q export -r ' . $tag . ' -d ' . $module . '-' . $tag . ' ' . $module . ' > /dev/null');
    system('cd ' . $dir . '; tar -zcf ' . $filename . ' ' . $module . '-' . $tag);
    system('cd ' . $dir . '; ' . $GLOBALS['md5_path'] . ' ' . $filename . ' > ' . $filename . '.md5sum');
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
