<?php
/**
 * FTP Lite is an application for Novius OS for managing static files
 *
 * @copyright  2013 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link https://github.com/novius/novius_ftplite
 */
?>
<p><?= __('This application allows you to upload static files and directories without having to use a FTP client. Files are located at the site root.') ?></p>
<p><?= __('Files you’re likely to manage include <b>robots.txt</b>, <b>sitemap.xml</b> and other <b>SEO pages</b>.') ?></p>
<p><?= __('The allowed file types are:') ?></p>
<ul>
<?php
$icons = \Config::load('noviusos_media::icons', true);
foreach ($icons['extensions'] as $ext_list) {
    echo '<li>', str_replace(',', ', ', $ext_list), '</li>';
}

?>
</ul>