<?php
/**
 * FTP Lite is an application for Novius OS for managing static files
 *
 * @copyright  2013 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link https://github.com/novius/novius_ftplite
 */

\Event::register('404.end', function($params) {
    \Novius\Ftplite\Ftplite::sendFile($params['url']);
});
\Event::register('front.404NotFound', function($params) {
    \Novius\Ftplite\Ftplite::sendFile($params['url'].'.html');
});
