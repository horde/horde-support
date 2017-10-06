<?php

$recipients = 'jan@localhost';
$usersfile = '/horde/cvs/CVSROOT/cvsusers';
$rpc_endpoint = $rpc_method = null;
$rpc_options = array();

require dirname(__FILE__) . '/push-conf.php';

if (is_null($rpc_endpoint) || is_null($rpc_method)) {
    exit('Required RPC configuration is missing or incomplete.');
}

set_include_path('/var/www/html/headhorde/libs:' . get_include_path());
require_once 'Horde/Autoloader/Default.php';

if (isset($_SERVER['HTTP_X_GITHUB_EVENT'])) {
    if ($_SERVER['HTTP_X_GITHUB_EVENT'] != 'push') {
        exit('Unhandled event: ' . $_SERVER['HTTP_X_GITHUB_EVENT']);
    }
} else {
    exit('Not a GitHub event');
}
if (!isset($_POST['payload'])) {
    exit('No payload');
}

$payload = json_decode($_POST['payload'], true);
if (!$payload) {
    exit('Invalid payload');
}

define('LOGBEGIN', "\n-----------------------------------------------------------------------\n");

if ($payload['created']) {
    $change_type = 'create';
    $describe = $payload['after'];
} elseif ($payload['deleted']) {
    $change_type = 'delete';
    $describe = $payload['before'];
} else {
    $change_type = 'update';
    $describe = $payload['after'];
}

if (preg_match('|^refs/tags/(.*)|', $payload['ref'], $match)) {
    // un-annotated tag
    $refname_type = 'tag';
    $short_refname = $match[1];
} elseif (preg_match('|^refs/heads/(.*)|', $payload['ref'], $match)) {
    // branch
    $refname_type = 'branch';
    $short_refname = $match[1];
} elseif (preg_match('|^refs/remotes/(.*)|', $payload['ref'], $match)) {
    // tracking branch
    $refname_type = 'tracking branch';
    $short_refname = $match[1];
    exit('Push-update of tracking branch, ' . $payload['ref'] . ' - no email generated.');
} else {
    // Anything else (is there anything else?)
    exit('Unknown type of update to ' . $payload['ref'] . ' - no email generated');
}

$mailfrom = $payload['pusher']['email'];
$file = @file($usersfile, FILE_IGNORE_NEW_LINES);
if ($file) {
    foreach ($file as $line) {
        if (preg_match('/^(\w+)\s+(.+\w)\s+(.*@[-\w\.]+)\s+(.*)$/', $line, $m) &&
            $m[1] == $payload['pusher']['name']) {
            $mailfrom = $m[3];
            if ($m[2]) {
                $mailfrom = $m[2] . " <$mailfrom>";
            }
            break;
        }
    }
}

$projectdesc = $payload['repository']['name'];
$refname = $payload['ref'];
$oldrev = $payload['before'];
$newrev = $payload['after'];

// Generate header
$body = <<<EOF
To: $recipients
From: $mailfrom
Subject: $projectdesc $refname_type $short_refname ${change_type}d. $describe
X-Git-Refname: $refname
X-Git-Reftype: $refname_type
X-Git-Oldrev: $oldrev
X-Git-Newrev: $newrev

The $refname_type "$short_refname" has been ${change_type}d.
EOF;

if ($change_type == 'update' &&
    ($refname_type == 'branch' ||
     $refname_type == 'tracking branch')) {
    $body .= sprintf(
        "\nThe following is a summary of the commits.\n\nfrom: %s\n\n",
        $oldrev
    );

    foreach ($payload['commits'] as $commit) {
        $body .= substr($commit['id'], 0, 7) . ' '
            . substr(
                $commit['message'],
                0,
                strpos($commit['message'], "\n") ?: strlen($commit['message'])
            )
            . "\n";
    }

    $body .= sprintf("\nSummary: %s\n", $payload['compare']);

    foreach ($payload['commits'] as $commit) {
        $date = new DateTime($commit['timestamp']);
        $files = array();
        foreach ($commit['added'] as $file) {
            $files[$file] = 'A';
        }
        foreach ($commit['removed'] as $file) {
            $files[$file] = 'R';
        }
        foreach ($commit['modified'] as $file) {
            $files[$file] = 'M';
        }
        ksort($files);
        $body .= LOGBEGIN;
        $commits = sprintf(
            "commit %s\nAuthor: %s <%s>\nDate:   %s\n\n%s\n\n",
            $commit['id'],
            $commit['author']['name'],
            $commit['author']['email'],
            $date->format('r'),
            $commit['message']
        );
        foreach ($files as $file => $change) {
            $commits .= ' ' . $change . ' ' . $file . "\n";
        }
        $commits .= "\n" . $commit['url'];
        $body .= "\n" . $commits . "\n";

        $tickets = find_tickets($commits);
        $kolab_tickets = find_kolab_tickets($commits);

        foreach ($kolab_tickets as $ticket) {
            $commits .= "\nThis ticket also references kolab issue: http://issues.kolab.org/issue$ticket\n\n";
        }

        if (count($tickets)) {
            $commits = sprintf(
                "Changes have been made in Git (%s):\n\n%s",
                $short_refname,
                $commits
            );
            foreach ($tickets as $ticket) {
                echo "Updating Ticket #" . $ticket . "...\n";
                post_comment($ticket, $commits);
            }
        }
    }

}

$proc = popen(
    '/usr/sbin/sendmail -t -f ' . escapeshellarg($payload['pusher']['email']),
    'w'
);
fwrite($proc, $body);
pclose($proc);

function find_tickets($log_message)
{
    preg_match_all('/(?:(?<!kolab\/)(?:bug|ticket|request|enhancement|issue):?\s*#?|#)(\d+)/i', $log_message, $matches);
    return array_unique($matches[1]);
}

function find_kolab_tickets($log_message)
{
    preg_match_all('/(?:(?:kolab\/issue))(\d+)/i', $log_message, $matches);
    return array_unique($matches[1]);
}

function post_comment($ticket, $log_message)
{
    $http = new Horde_Http_Client($GLOBALS['http_options']);
    try {
        Horde_Rpc::request(
            'jsonrpc',
            $GLOBALS['rpc_endpoint'],
            $GLOBALS['rpc_method'],
            $http,
            array((int)$ticket, $log_message)
        );
    } catch (Horde_Rpc_Exception $e) {
        echo $e . "\n";
    }
}
