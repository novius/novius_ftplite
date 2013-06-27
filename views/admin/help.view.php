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
<p><?= __('This interface allows you to upload files and directories directly accessible to the root of the site.') ?></p>
<p><?= __('This allows the autonomous management of <b>robots.txt</b>, <b>sitemap.xml</b> and <b>smart pages</b>.') ?></p>
<p><?= __('List of allowed extensions:') ?></p>
<ul>
<?php
$icons = \Config::load('noviusos_media::icons', true);
foreach ($icons['extensions'] as $ext_list) {
    echo '<li>', str_replace(',', ', ', $ext_list), '</li>';
}

?>
</ul>