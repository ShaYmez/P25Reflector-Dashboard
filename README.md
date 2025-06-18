# P25Reflector-Dashboard
Dashboard for P25Reflector (by G4KLX) forked by M0VUB from NXDNReflector-Dashboard of IU7IGU
=====================================

About
=====
P25Reflector-Dashboard is a web-dashboard for visualization of different data like
system temperatur, cpu-load ... and it shows a last-heard-list.

Must be running PhP ver 7.4

It relies on P25Reflector by G4KLX (see https://github.com/g4klx/P25Clients). At 
this place a big thank you to Jonathan for his great work he did with this 
software.

Required are
============
* Webserver like 
* lighttpd or apache(2)
* Updated to work with PHP 8.X >= 8.2.28

Installation
============
* Please ensure to not put loglevels at 0 in P25Reflector.ini.
* Copy all files into your webroot and enjoy working with it.
* Create a config/config.php by calling setup.php and giving suitable values
* If Dashboard is working, remove setup.php from your webroot
* PhP 7.4

For detailled installation see `linux-step-by-step.md` within this repository.

Contact
=======
Feel free to contact the author via email: support@gb7nr.co.uk
