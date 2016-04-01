INSTALLING KIMAI
================

Kimai is an open source PHP based time tracking system released
under the GNU GPL.

This document describes:

* System requirements for Kimai
* Installation routine
* Upgrade routine

Client browser support
----------------------

Kimai is accessed through a web browser. Kimai supports the following web browsers:

* Internet Explorer 11 and later
* Microsoft Edge
* Google Chrome (Windows, MacOS X, Linux)
* Firefox (Windows, MacOS X, Linux)
* Safari on MacOS X
* and other compatible modern browsers

Server system requirements
--------------------------

Kimai requires a web server with a PHP environment and a database. The minimum
system requirements for running Kimai are:

* Webserver capable of running PHP applications (Apache, Nginx, IIS or other)
* PHP 5.4
* MySQL 5.5 up to 5.7 or compatible
* more than 100 MB of disk space

### MySQL environment

Kimai works with MySQL in the above mentioned versions. It will also work on
compatible "drop-in" replacements like MariaDB or Percona.

### MySQL required privileges

The MySQL user needs a least the following privileges on the Kimai database:

* SELECT, INSERT, UPDATE, DELETE
* CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES

### PHP environment

* memory_limit set to at least 64M
* max_execution_time set to at least 30s (60s recommended)
* register_globals disabled
* AllowOverride in the Apache configuration includes "Indexes" and "FileInfo"
  (see FAQ below)

### PHP required extensions

Your PHP needs to support the following extensions. Install will
check if these are available.

* These are usually part of the standard PHP package on most distributions:
  * filter
  * hash
  * pcre >= 8.30
  * session
  * soap
  * SPL
  * standard
  * xml
  * zip
  * zlib

* These might have to be installed separately:
  * json
  * mysqli

### Recommended setup

This is a basic recommended setup for best performance and increased
functionality:

* Apache with mod_expires enabled

* MySQL 5.5 or newer

* PHP
  * version 7.0 or later
  * memory_limit set to at least 128M
  * max_execution_time set to at least 60s
  * max_input_vars set to at least 1500

* Additional PHP extensions:
  * PHP opcode cache, i.e.: apc, xcache, eaccelerator, Zend Optimizer, wincache (in case of an IIS installation)
  * apcu caching (with at least 100 MB of memory available)
  * mbstring

* PHP access to /dev/urandom or /dev/random on Unix-like platforms for
  increased security. Make sure to add "/dev/random:/dev/urandom" to
  open_basedir settings if you use it. If these paths are unavailable, Kimai
  will attempt to simulate random number generation. This is less secure,
  reduces performance and throws out warnings in the Kimai log.

Installation
------------

If you have SSH access to your webserver,
this is the recommended way of setting up Kimai:

* Uncompress the zip file on the Document
  Root of your Web server:
```
/var/www/html/kimai $ unzip kimai.zip
```

* Important: If you use GIT to fetch the sources, use the following command:
```
git fetch
git checkout <TAG>
```
where TAG is something like ```1.0```

### No SSH possible (not recommended)

In case you only have FTP or SFTP access to your hosting environment, you
can still install Kimai, but you won't easily be able to upgrade your
installation once a new patch-level release is out.

Please note that this is not a recommended setup!

* Uncompress `kimai.zip` locally
* Delete all files except:
  * ```includes/autoconf.php```
  * any self made invoice templates under ```extensions/ki_invoice/invoices/```
  * any self written extensions under ```extensions/```
* Upload all files and subdirectories directly in your Document Root
  (where files that are served by your webserver are located).

Installation: further steps
---------------------------

Now access the web server using a web browser. You will be redirected to the
Installer which will walk you through the steps for setting up Kimai for
the first time.

It will check if your environment conforms to the minimum system requirements
and gives you some suggestions on what to change in case there are any
discrepancies.


Installation FAQ
----------------

### 1
Q:  Why do I get "500 Server error" when I navigate to Kimai?

A:  If you are using Apache web server, check the Apache error log for specifics
    on the error. The cause might be some missing module, or some syntax error
    in your .htaccess file. The error log is usually located in /var/log/apache2
    or /var/log/httpd. Check with your hosting provider if you are in doubt
    where the logs are located.
