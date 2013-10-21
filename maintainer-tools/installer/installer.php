<?php
/**
 * Horde installer script.
 *
 * Copyright 2013 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2013 Horde LLC
 * @license   http://www.fsf.org/copyleft/gpl.html GPL
 */

require 'vendor/autoload.php';
require 'lib/HordeInstaller.php';

$c = new Horde_Cli();

$c->writeln($c->bold('----------------------'));
$c->writeln($c->bold('Horde installer script'));
$c->writeln($c->bold('----------------------'));

$argv = new Horde_Argv_Parser();
$argv->addOption('-i', '--install-dir', array(
    'action' => 'store',
    'dest' => 'horde_dir',
    'help' => 'Horde install directory'
));
$argv->addOption('-l', '--log', array(
    'action' => 'store',
    'dest' => 'log',
    'help' => 'Log filename'
));
list($values,) = $argv->parseArgs();

$version = phpversion();
if (version_compare($version, '5.3.0') === -1) {
    $c->fatal('You need at least PHP version 5.3.0 to run Horde.');
}

$c->writeln();
$c->message('PHP version: ' . $version, 'cli.message');

exec('which pear', $pear_output);
if (empty($pear_output)) {
    $c->fatal('Could not find PEAR script in your include path.');
}

$c->message('PEAR location: ' . $pear_output[0], 'cli.message');

while (!strlen($values->horde_dir)) {
    $values->horde_dir = $c->prompt('The directory to install Horde into:');
}

$hi = new HordeInstaller();
$logfile = isset($values->log)
    ? realpath($values->log)
    : dirname(Phar::running(false)) . '/horde-installer.log';

$c->writeln();
try {
    $hi->initLogger($logfile);
    $c->message('Log file: ' . $logfile, 'cli.message');
} catch (Exception $e) {
    $c->message(sprintf('Cannot write to log file "%s".', $logfile), 'cli.warning');
}

$c->writeln();
$c->message('Upgrading PEAR to the latest version...', 'cli.message');
$hi->pear('pear upgrade PEAR');
$c->message('Success.', 'cli.success');

$c->writeln();
$c->message('Clearing the PEAR cache...', 'cli.message');
$hi->pear('pear clear-cache');
$c->message('Success.', 'cli.success');

$c->writeln();
$c->message('Registering pear.horde.org PEAR channel...', 'cli.message');
$hi->pear('pear channel-discover pear.horde.org');
$c->message('Success.', 'cli.success');

$c->writeln();
$c->message('Set Horde installation directory...', 'cli.message');
$hi->pear('pear install horde/horde_role');
$hi->pear('pear config-set -c pear.horde.org horde_dir ' . escapeshellarg($values->horde_dir));
$c->message('Success.', 'cli.success');

$c->writeln();
$c->message('Installing Horde packages (this may take awhile)...', 'cli.message');
$hi->pear('pear -d auto_discover=1 install -a -B horde/horde');
$c->message('Success.', 'cli.success');

$c->writeln();
$c->message('Installation complete!', 'cli.success');
