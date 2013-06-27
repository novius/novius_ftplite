<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

return array(
    'name'    => 'FTP Lite',
    'version' => 'chiba.1',
    'provider' => array(
        'name' => 'Novius',
    ),
    'namespace' => 'Novius\Ftplite',
    'permission' => array(
    ),
    'launchers' => array(
        'novius_ftplite' => array(
            'name'    => 'FTP Lite',
            'action' => array(
                'action' => 'nosTabs',
                'tab' => array(
                    'url' => 'admin/novius_ftplite/ftplite',
                ),
            ),
        ),
    ),
    'icons' => array(
        16 => 'static/apps/novius_ftplite/img/icons/ftplite-16.png',
        32 => 'static/apps/novius_ftplite/img/icons/ftplite-32.png',
        64 => 'static/apps/novius_ftplite/img/icons/ftplite-64.png',
    ),
);
