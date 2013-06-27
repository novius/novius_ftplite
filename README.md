# Ftp Lite for Novius OS

FTP Lite is an application for Novius OS for managing static files.

Licensed under [GNU Affero General Public License v3](http://www.gnu.org/licenses/agpl-3.0.html) or (at your option) any later version.

# Documentation

After installed FTP Lite in your Novius OS, you must add this lines to your local/bootstrap.php

    \Event::register('404.start', function($params) {
        \Module::load('novius_pseudoftp');
    });
    \Event::register('front.start', function($params) {
        \Module::load('novius_pseudoftp');
    });
