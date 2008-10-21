#!/usr/bin/env php
<?php
/**
 * ====================================================================
 * commit-php-syntax-check.php: check that every changed php file
 * parses. If any file fails this test the user is sent a verbose
 * error message suggesting solutions and the commit is aborted.
 *
 * Usage: commit-php-syntax-check.php REPOS TXN-NAME
 * ====================================================================
 * Much of commit-php-syntax-check.php was adapted from
 * commit-access-control.pl, Revision 9986, 2004-06-14 16:29:22 -0400.
 * ====================================================================
 *
 * @category Horde
 * @package  devtools
 */


/**
 ** Configuration
 **/

$svnlook = '/usr/bin/svnlook';
$php = '/usr/bin/php';


/**
 ** Sanity checks
 **/

if (!is_executable($svnlook)) {
    abort("Required program $svnlook is not executable.");
}

if (!is_executable($php)) {
    abort("Required program $php is not executable.");
}

if (count($_SERVER['argv']) != 3) {
    usage();
}


/**
 ** Command-line parsing
 **/

$repos = $_SERVER['argv'][1];
$txn = $_SERVER['argv'][2];

if (!file_exists($repos)) {
    abort("Repository $repos does not exist.");
}

if (!is_dir($repos)) {
    abort("Repository $repos is not a directory.");
}


/**
 ** Read changed files for this transaction
 **/

// Build PHP lint command
$php_lint = "$php -d display_errors=1 -d html_errors=0 -d error_prepend_string=\" \" -d error_append_string=\" \" -d error_reporting=\"E_ALL & ~E_STRICT\" -l";

// Run svnlook changed to list files in the transaction
exec(implode(' ', array($svnlook,
                        'changed',
                        '--transaction',
                        escapeshellarg($txn),
                        escapeshellarg($repos))),
     $transaction_lines);

$failed = false;
foreach ($transaction_lines as $line) {
    // Only add files (not directories, properties, or anything else)
    // that were changed or added in this transaction.
    if (preg_match('/^(A|U).  (.*[^\/])$/', $line, $matches)) {
        $file = $matches[2];
        // Only lint PHP files.
        if (substr($file, -4) == '.php') {
            $cmd = implode(' ', array($svnlook,
                                      'cat',
                                      '--transaction',
                                      escapeshellarg($txn),
                                      escapeshellarg($repos),
                                      escapeshellarg($file),
                                      '2>&1 |',
                                      $php_lint,
                                      '|',
                                      'grep',
                                      '-v',
                                      '"No syn"'));

            $errors = shell_exec($cmd);
            if (strpos($errors, "No syntax errors") === false) {
                $errors = trim(str_replace(array('in - on', 'Errors parsing -'),
                                           array('in ' . $file . ' on', ''),
                                           $errors));
                warn("\n" . $errors);
                $failed = true;
            }
        }
    }
}

if ($failed) {
    abort("
Every PHP file must pass php -l (syntax check). Please review the above errors
and correct the problem before re-committing.");
}

exit(0);


/**
 ** Functions
 **/

function warn($msg) {
    fputs(STDERR, $msg . "\n");
}

function abort($msg) {
    fputs(STDERR, $msg . "\n");
    exit(1);
}

function usage() {
    abort("usage: commit-php-syntax-check.php REPOS TXN-NAME");
}
