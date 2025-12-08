# P25Reflector-Dashboard
Dashboard for P25Reflector (by G4KLX) forked by M0VUB from NXDNReflector-Dashboard of IU7IGU
=====================================

About
=====
P25Reflector-Dashboard is a web-dashboard for visualization of different data like
system temperature, CPU load, and last-heard lists for P25 reflectors.

It relies on P25Reflector by G4KLX (see https://github.com/g4klx/P25Clients). 
A big thank you to Jonathan for his excellent work on this software.

Requirements
============
* Webserver (lighttpd, Apache, or nginx)
* **PHP 8.2.28 or higher** (Fully optimized for PHP 8.X)
* P25Reflector by G4KLX

PHP Compatibility
=================
This dashboard is **fully compatible with PHP 8.X** and has been optimized with:
* Modern PHP 8 syntax and best practices
* Secure shell command execution with proper escaping
* Safe array access with null coalescing operators
* Removal of deprecated functions (extract, backticks)
* Enhanced security and error handling

**Tested and verified on PHP 8.3.6**

Installation
============
1. Ensure P25Reflector.ini has log level set above 0
2. Copy all files to your webserver root directory
3. Navigate to `setup.php` to create your configuration
4. Provide the requested information (paths, timezone, etc.)
5. **Important:** Delete `setup.php` after configuration for security
6. Access your dashboard via `index.php`
7. **Mega Important** You must set proper permissions to the html directory `sudo chown -R www-data:www-data /var/www/html` before running setup.php

For detailed installation instructions, see `linux-step-by-step.md` in this repository.

Contact
=======
Feel free to contact the author by opening a request or issue on this repo.

Changelog
=========
### Latest Updates (v20251208)
* **PHP 8 Compatibility Optimization**
  - Replaced deprecated backtick execution with `shell_exec()`
  - Removed insecure `extract()` function usage
  - Added null coalescing operators for safe array access
  - Improved empty string comparisons with modern PHP syntax
  - Added proper `escapeshellarg()` for shell command security
  - Fixed `exec()` array handling with null checks
  - Enhanced `$_GET` parameter validation
  - All functions tested and verified on PHP 8.3.6
