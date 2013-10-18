<?php

require 'vendor/autoload.php';

$c = new Horde_Cli();

$c->writeln($c->bold('----------------------'));
$c->writeln($c->bold('Horde installer script'));
$c->writeln($c->bold('----------------------'));

// TODO: Check for PEAR to exist in PATH

do {
    $horde_dir = $c->prompt('The directory to install Horde into:');
} while (!strlen($horde_dir));

$c->writeln();
$c->message('Upgrading PEAR to the latest version...', 'cli.message');

system('pear upgrade PEAR');

$c->message('Clearing the PEAR cache...', 'cli.message');

system('pear clear-cache');

$c->message('Registering pear.horde.org PEAR channel...', 'cli.message');

system('pear channel-discover pear.horde.org');

$c->message('Set Horde installation directory...', 'cli.message');

system('pear install horde/horde_role');
system('pear config-set -c pear.horde.org horde_dir ' . escapeshellarg($horde_dir));

$c->message('Installing Horde packages...', 'cli.message');

system('pear -d preferred_state=alpha -d auto_discover=1 install -a -B horde/horde');

$c->message('Installation complete!', 'cli.success');
