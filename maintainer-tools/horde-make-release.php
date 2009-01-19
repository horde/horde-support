#!@php_bin@
<?php
/**
 * This is a short script to make an "official" Horde or application release
 *
 * This script relies on a few things.
 *
 * 1) The file containing the version string is:
 *         <module>/lib/version.php
 * 2) The tag to use in the source is:
 *         <module>_<major>_<minor>_<patch>[_<text>]
 * 3) The directory the source should be packaged from is:
 *         <module>-<major>.<minor>.<patch>[-<text>]
 * 4) The version to put into the version file in CVS when done is:
 *         <major>.<minor>.<patch+1>-cvs unless there was [-<text>],
 *         then just <major>.<minor>.<patch>-cvs
 * 5) It expects the version file's version line to look like this:
 *        <?php define('<MODULE>_VERSION', '<version>') ?>
 * 6) It expects that you have CVS all set up in the shell you're using.
 *         This includes all of the password stuff...
 * 7) The changelog file is:
 *         <module>/docs/CHANGES
 * 8) The release notes are in:
 *         <module>/docs/RELEASE_NOTES
 *
 * $Horde: framework/devtools/horde-make-release.php,v 1.9 2009/01/06 17:50:01 jan Exp $
 *
 * Copyright 1999 Mike Hardy
 * Copyright 2004-2009 The Horde Project (http://www.horde.org/)
 *
 * Licensed under LGPL, please see the file COPYING for details on licensing.
 *
 * @category Horde
 * @package  devtools
 */

require dirname(__FILE__) . '/horde-make-release-conf.php';
require_once 'Horde/Util.php';
require_once 'Horde/RPC.php';
require_once 'Horde/MIME/Message.php';
require_once 'Horde/Release.php';

// Create a class instance
$tarball = new Horde_Release($options);

// Get all the arguments from the command-line
$tarball->getArguments();

// Make sure they are sane
$tarball->checkArguments();

// Do testing (development only)
$tarball->test();

// Set umask, etc.
$tarball->checkSetSystem();

// Set all of the version strings we're going to need for tags, source, etc
$tarball->setVersionStrings();

// Check out the source we're going to release
$tarball->checkOutTag($tarball->options['branch'], $tarball->directoryName);

// Check out the framework module if necessary
$tarball->checkOutFramework($tarball->options['branch'], $tarball->directoryName);

// Update the version file with the release version
$tarball->updateVersionFile($tarball->directoryName, $tarball->sourceVersionString);

// Tag the source in the release directory with the correct versioned tag
$tarball->tagSource();

// Get version number of CHANGES file
$tarball->saveChangelog();

// Clean up all the non-tarball-bound directories so the package is clean
$tarball->cleanDirectories($tarball->directoryName);

if ($tarball->oldVersion) {

    // Check out the next-lowest-patch-level
    $tarball->checkOutTag($tarball->oldTagVersionString, $tarball->oldDirectoryName);

    if ($tarball->makeDiff) {
        // Check out the framework module if necessary
        $tarball->checkOutFramework($tarball->oldTagVersionString, $tarball->oldDirectoryName);
    }

    // Get version number of CHANGES file
    $tarball->saveChangelog(true);

}

// If we have a lower patch-level on this tree, make a diff
if ($tarball->makeDiff) {

    // Clean all the non-tarball-bound directories out of it
    $tarball->cleanDirectories($tarball->oldDirectoryName);

    // Make a diff of the two cleaned releasable directories now
    $tarball->diff();

}

if ($tarball->oldVersion) {

    // Clean the directory out
    $tarball->deleteDirectory($tarball->oldDirectoryName);

}

// Make the tarball now
$tarball->makeTarball();

// Clean all the old directories out
$tarball->deleteDirectory($tarball->directoryName);

// Put tarball up on the server
$tarball->upload();

// Check the new source out again so we can change the string post-tarball
$tarball->checkOutTag($tarball->options['branch'], $tarball->directoryName);
$tarball->updateVersionFile($tarball->directoryName, $tarball->newSourceVersionString);
$tarball->updateSentinel();

// Clean this directory up now
$tarball->deleteDirectory($tarball->directoryName);

// Announce release on mailing lists and freshmeat.
$tarball->announce();

// Update our ticket system.
$tarball->addWhupsVersion();
