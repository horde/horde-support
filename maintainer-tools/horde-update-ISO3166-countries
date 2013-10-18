#!/usr/bin/php
<?php
/**
 * This is a script to fetch the current ISO-3166 country definitions and
 * update Horde's country list file in horde/lib/NLS/countries.php in an
 * interactive way.
 *
 * ** This script needs to be run from the base Horde directory. **
 *
 * The source for the country list is the International Organization for
 * Standardization (http://iso.org).
 *
 * Copyright 2004-2009 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @category Horde
 * @package  maintainer_tools
 * @author   Marko Djukic <marko@oblo.com>
 *
 * @todo  Would be good to expand this to fetch also ISO-3166-2 lists from
 *        somewhere for lists of countries' regions/states.
 */

require_once getcwd() . '/lib/Application.php';

if (!Horde_CLI::runningFromCLI()) {
    exit("Must be run from the command line\n");
}
Horde_CLI::init();

/* Location on ISO website where to fetch the updates from. */
$url = 'http://www.iso.org/iso/en/prods-services/iso3166ma/02iso-3166-code-lists/list-en1-semic.txt';

/* Update Horde's file. */
$countries = new Countries($url);
$updated = $countries->update();
if ($updated == false) {
    /* Nothing found to update. */
    $countries->cli->writeln('Nothing to update. Exiting.');
    exit;
}

/* Save the new file. */
$countries->save();
exit;

class Countries {

    var $old_data = array();
    var $new_data = array();
    var $cli;
    var $_url;

    /**
     * Constructor
     */
    function Countries($url)
    {
        $this->_url = $url;

        $this->cli = Horde_CLI::singleton();

        $this->old_data = $this->_loadOld();
        $this->new_data = $this->_loadNew();
    }

    function update()
    {
        $accept_all = false;
        $updated = false;
        foreach ($this->new_data as $code => $name) {
            if ($accept_all == true) {
                /* Auto accept activated just select all auto names. */
                $this->new_data[$code] = $this->_newName($name);
            } elseif (isset($this->old_data[$code]) &&
                      strtolower($this->old_data[$code]) == strtolower($name)) {
                /* Old name matches new name so assume no changes. */
                $this->new_data[$code] = $this->old_data[$code];
            } else {
                $new_name = $this->_newName($name);
                if (isset($this->old_data[$code])) {
                    /* There is an existing old name, show the two versions. */
                    $old = $this->cli->red($this->old_data[$code]);
                    $this->cli->writeln(sprintf('Old name: %s', $old));
                    $new = $this->cli->green($new_name);
                    $this->cli->writeln(sprintf('New name: %s', $new));
                }
                /* Prompt for a new country name. */
                $accept = $this->cli->prompt(sprintf('New country name \"%s\", convert to: \"%s\"?', $name, $new_name), array('y' => 'Yes', 'n' => 'No', 'a' => 'Accept All'));
                if ($accept == 'n') {
                    /* User does not accept auto name, ask for input. */
                    $new_name = $this->cli->prompt('Enter the new name: ', true);
                } elseif ($accept == 'a') {
                    /* User asked to accept all, flag it. */
                    $accept_all = true;
                }
                $updated = true;
                $this->new_data[$code] = $new_name;
            }
        }
        return $updated;
    }

    function save()
    {
        $filename = HORDE_BASE . '/lib/NLS/countries.php';
        $old_file = file($filename);
        $new_file = '';
        foreach ($old_file as $key => $line) {
            $new_file .= $line;
            if ($line == "\$countries = array(\n") {
                break;
            }
        }
        $new_data = array();
        foreach ($this->new_data as $code => $name) {
            $new_data[] = sprintf('    \'%s\' => \'%s\'', $code, $name);
        }
        $new_file .= implode(", \n", $new_data) . ');';

        /* Save the new file. */
        if (!$fp = @fopen($filename, 'w')) {
            $this->cli->fatal(sprintf('Cannot open file %s.', $filename));
        }

        if (!fwrite($fp, $new_file)) {
            $this->cli->fatal(sprintf('Cannot write file %s.', $filename));
        }
        fclose($fp);
        $this->cli->writeln('Success, saved new country data.');
    }

    function &_loadOld()
    {
        require_once HORDE_BASE . '/lib/NLS/countries.php';
        return $countries;
    }

    function &_loadNew()
    {
        $new_file = Countries::_readURL($this->_url);
        if (is_a($new_file, 'PEAR_Error')) {
            $this->cli->fatal(sprintf('Cannot read source for country data. %s', $new_file->getMessage()));
        }

        $lines = explode("\r\n", $new_file);
        $new_data = array();
        foreach ($lines as $line) {
            if (!preg_match('/^(.*?);(\w\w)$/', $line, $matches)) {
                continue;
            }
            $new_data[$matches[2]] = $matches[1];
        }
        return $new_data;
    }

    function _readURL()
    {
        $options['method'] = 'GET';
        $options['timeout'] = 5;
        $options['allowRedirects'] = true;

        $http = new HTTP_Request($this->_url, $options);
        @$http->sendRequest();
        if ($http->getResponseCode() != 200) {
            return PEAR::raiseError(sprintf('Could not open %s.', $this->_url));
        }

        return $http->getResponseBody();
    }

    function _newName($name)
    {
        $no_caps = array('AND', 'OF', 'THE');
        $parts = explode(' ', $name);
        foreach ($parts as $key => $part) {
            if (in_array($part, $no_caps)) {
                $part = strtolower($part);
            } else {
                $part = preg_replace('/(\w)(\w*(\'S)?)/e', "$1 . strtolower('$2')", $part);
            }
            $parts[$key] = $part;
        }

        return implode(' ', $parts);
    }

}
