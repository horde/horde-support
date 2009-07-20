#!/usr/bin/env php
<?php

if (!isset($argc) || $argc != 4) {
    die('Usage: horde-patch-config.php blob1 blob2 file2');
}

$file1 = tempnam('.', 'patch');
file_put_contents($file1, str_replace('$Id$', '$Id: ' . $argv[1] . ' $', shell_exec('git cat-file blob ' . $argv[1])));

$file2 = tempnam('.', 'patch');
file_put_contents($file2, str_replace('$Id$', '$Id: ' . $argv[2] . ' $', shell_exec('git cat-file blob ' . $argv[2])));

system('diff -u ' . $file1 . ' ' . $file2 . ' | patch ' . $argv[3]);

unlink($file1);
unlink($file2);
