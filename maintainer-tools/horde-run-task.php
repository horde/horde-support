#!@php_bin@
<?php
/**
 * Script to run a Horde_LoginTasks task and/or system task.
 *
 * ** This script needs to be run from the base Horde directory. **
 *
 * Usage: horde-run-task.php -a [app] -t [taskname] -u [username]
 *   Taskname is the name of the file in the SystemTask/Task directory - this
 *   script will automatically build the full class name.
 *
 * @category Horde
 * @package  maintainer_tools
 */

require_once getcwd() . '/lib/Application.php';

// Make sure no one runs this from the web.
if (!Horde_Cli::runningFromCli()) {
    exit("Must be run from the command line\n");
}

// Load the CLI environment - make sure there's no time limit, init some
// variables, etc.
$cli = Horde_Cli::singleton();
$cli->init();

$c = new Console_Getopt();
$argv = $c->readPHPArgv();
array_shift($argv);
$options = $c->getopt2($argv, 'a:t:u:');
if ($options instanceof PEAR_Error) {
    $cli->fatal($options->getMessage());
    exit;
}

foreach ($options[0] as $val) {
    switch ($val[0]) {
    case 'a':
        $app = $val[1];
        break;

    case 't':
        $taskname = $val[1];
        break;

    case 'u':
        $username = $val[1];
        break;
    }
}

if (empty($app)) {
    $cli->fatal('Missing argument to -a.');
    exit;
}

if (empty($taskname)) {
    $cli->fatal('Missing argument to -t.');
    exit;
}

if (empty($username)) {
    $cli->fatal('Missing argument to -u.');
    exit;
}

$registry = Horde_Registry::singleton();
Horde_Auth::setAuth($username, array());
$registry->pushApp($app, array('check_perms' => false));

$class = $app . '_LoginTasks_SystemTask_' . $taskname;
if (!class_exists($class)) {
    $class = $app . '_LoginTasks_Task_' . $taskname;
    if (!class_exists($class)) {
        $cli->fatal('Could not find task "' . $taskname . '".');
    }
}


$ob = new $class();
$ob->execute();

$cli->message('Completed task.', 'cli.success');
