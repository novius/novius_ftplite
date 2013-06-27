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
<p>Cette interface permet d'uploader des fichiers et répertoires directement accessibles à la racine du site.</p>
<p>Cela permet la gestion autonome des <b>robots.txt</b>, <b>sitemap.xml</b> et <b>smart pages</b>.</p>
<p>Liste des extensions autorisées :</p>
<ul>
<?php
$icons = \Config::load('noviusos_media::icons', true);
foreach ($icons['extensions'] as $ext_list) {
    echo '<li>', str_replace(',', ', ', $ext_list), '</li>';
}

?>
</ul>