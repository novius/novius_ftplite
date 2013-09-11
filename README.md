# Ftp Lite for Novius OS

FTP Lite is an application for Novius OS for managing static files.

## Requirements

* The FTP Lite applications run on Novius OS Chiba and upper.

## Installation

* [How to install a Novius OS application](http://community.novius-os.org/how-to-install-a-nos-app.html)

After installed FTP Lite in your Novius OS, you must add this lines to your local/bootstrap.php

    \Event::register('404.start', function($params) {
        \Module::load('novius_ftplite');
    });
    \Event::register('front.start', function($params) {
        \Module::load('novius_ftplite');
    });

## License

Licensed under [GNU Affero General Public License v3](http://www.gnu.org/licenses/agpl-3.0.html) or (at your option) any later version.