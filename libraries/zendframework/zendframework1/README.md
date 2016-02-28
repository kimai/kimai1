![Logo](http://framework.zend.com/images/logos/ZendFramework-logo.png)

Welcome to the Zend Framework 1.12 Release! 

Master: [![Build Status](https://api.travis-ci.org/zendframework/zf1.png?branch=master)](https://travis-ci.org/zendframework/zf1)

RELEASE INFORMATION
===================

Zend Framework 1.12.17dev Release.
Released on MMM DD, YYYY.

IMPORTANT FIXES FOR 1.12.17
---------------------------

See http://framework.zend.com/changelog for full details.

NEW FEATURES
============

Zend_Loader changes
-------------------

A number of autoloaders and autoloader facilities were back ported from
ZF2 to provide performant alternatives to those already available in the
1.X releases.  These include: Zend_Loader_StandardAutoloader, which
improves on Zend_Loader_Autoloader by allowing the ability to specify a
specific path to associate with a vendor prefix or namespace;
Zend_Loader_ClassMapAutoloader, which provides the ability to use lookup
tables for autoloading (which are typically the fastest possible way to
autoload); and Zend_Loader_AutoloaderFactory, which can both create and
update autoloaders for you, as well as register them with
spl_autoload_register().

The Zend_Loader changes were back ported from ZF2 by Matthew Weier
O’Phinney

Zend_EventManager
-----------------

Zend_EventManager is a component that allows you to attach and detach
listeners to named events, both on a per-instance basis as well as via
shared collections; trigger events; and interrupt execution of
listeners.

Zend_EventManager was back ported from ZF2 by Matthew Weier O’Phinney

Zend_Http_UserAgent_Features_Adapter_Browscap
---------------------------------------------

This class provides a features adapter that calls get_browser() in order
to discover mobile device capabilities to inject into UserAgent device
instances.

Browscap (http://browsers.garykeith.com/) is an open project dedicated
to collecting an disseminating a “database” of browser capabilities. PHP
has built-in support for using these files via the get_browser()
function. This function requires that your php.ini provides a browscap
entry pointing to the PHP-specific php_browscap.ini file which is
available at http://browsers.garykeith.com/stream.asp?PHP_BrowsCapINI.

Zend_Http_UserAgent_Features_Adapter_Browscap was created by Matthew
Weier O’Phinney

Zend_Mobile_Push
----------------

Zend_Mobile_Push is a component for implementing push notifications for
the 3 major push notification platforms (Apple (Apns), Google (C2dm) and
Microsoft (Mpns).

Zend_Mobile_Push was contributed by Mike Willbanks.

Zend_Gdata_Analytics
--------------------

Zend_Gdata_Analytics is an extension to Zend_Gdata to allow interaction
with Google’s Analytics Data Export API. This extension does not
encompass any major changes in the overall operation of Zend_Gdata
components.

Zend_Gdata_Analytics was contributed by Daniel Hartmann.

Removed features
================

Zend_Http_UserAgent_Features_Adapter_WurflApi
---------------------------------------------

Due to the changes in licensing of WURFL, we have removed the WurflApi
adapter. We will be providing the WurflApi adapter to ScientiaMobile so
that users of WURFL will still have that option.

Bug Fixes
=========

In addition,  over 200 reported issues in the tracker have been fixed.
We’d like to particularly thank Adam Lundrigan, Frank Brückner and
Martin Hujer for their efforts in making this happen. Thanks also to the
many people who ran the ZF1 unit tests and reported their results!

For a complete list of closed issues beginning with 1.12.3, visit:

 * https://github.com/zendframework/zf1/issues?labels=&milestone=&page=1&state=closed
 * http://framework.zend.com/changelog/

MIGRATION NOTES
===============

A detailed list of migration notes may be found at:

http://framework.zend.com/manual/en/migration.html

SYSTEM REQUIREMENTS
===================

Zend Framework requires PHP 5.2.11 or later. Please see our reference
guide for more detailed system requirements:

http://framework.zend.com/manual/en/requirements.html

INSTALLATION
============

Please see [INSTALL.md](INSTALL.md).

REPOSITORY HISTORY
==================

This repository was created based on the release-1.12 branch of a Subversion
repository, http://framework.zend.com/svn/framework/standard/. It contains a
subset of the project history, dating from between the 1.5.0 and 1.6.0 releases,
and only contains the tags for the 1.12 series. If you would like an older
version, you may access the subversion repository linked above, or download an
older version from http://framework.zend.com/downloads/archives.

CONTRIBUTING
============

Please see [README-GIT.md](README-GIT.md) and
[DEVELOPMENT_README.md](DEVELOPMENT_README.md).

QUESTIONS AND FEEDBACK
======================

Online documentation can be found at http://framework.zend.com/manual.
Questions that are not addressed in the manual should be directed to the
appropriate mailing list:

- http://framework.zend.com/wiki/display/ZFDEV/Mailing+Lists

If you find code in this release behaving in an unexpected manner or
contrary to its documented behavior, please create an issue in the Zend
Framework issue tracker at:

- https://github.com/zendframework/zf1/issues

If you would like to be notified of new releases, you can subscribe to
the fw-announce mailing list by sending a blank message to:

- fw-announce-subscribe@lists.zend.com.

LICENSE
=======

The files in this archive are released under the Zend Framework license.
You can find a copy of this license in [LICENSE.txt](LICENSE.txt).

ACKNOWLEDGEMENTS
================

The Zend Framework team would like to thank all the contributors to the Zend
Framework project, our corporate sponsor, and you, the Zend Framework user.
Please visit us sometime soon at http://framework.zend.com.
