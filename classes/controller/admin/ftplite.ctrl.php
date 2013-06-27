<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Novius\Ftplite;

class Controller_Admin_Ftplite extends \Nos\Controller_Admin_Application
{
    public function action_index()
    {
        return \View::forge('novius_ftplite::admin/index');
    }

    public function action_import()
    {
        try {
            $dir = Ftplite::path();
            !is_dir($dir) && \File::create_dir(dirname($dir), 'ftplite');

            $context = \Input::post('context', \Nos\Tools_Context::defaultContext());
            $contexts = \Nos\Tools_Context::contexts();
            if (!in_array($context, array_keys($contexts))) {
                throw new \Exception('Context inconnu !');
            }

            $dir = Ftplite::path($context);
            !is_dir($dir) && \File::create_dir(dirname($dir), $context);

            $file_info = \File::file_info($_FILES['import']['tmp_name']);
            if ($file_info['mimetype'] === 'application/zip') {
                $unzip = new \Unzip;
                $allow = array();
                $icons = \Config::load('noviusos_media::icons', true);
                foreach ($icons['extensions'] as $ext_list) {
                    $allow = $allow + explode(',', $ext_list);
                }
                $unzip->allow($allow);
                $unzip->extract($_FILES['import']['tmp_name'], $dir);
            } else {
                move_uploaded_file($_FILES['import']['tmp_name'], $dir.DS.$_FILES['import']['name']);
            }

            \Response::json(array(
                'notify' => 'Import terminé.',
            ));
        } catch (\Exception $e) {
            $this->send_error($e);
        }
    }

    public function action_export()
    {
        try {
            $tmp = \Config::get('tmp_dir', '/tmp/');
            if (is_file($tmp.'fichiers_statiques.zip')) {
                unlink($tmp.'fichiers_statiques.zip');
            }
            if (exec('cd '.Ftplite::path().';zip -r '.$tmp.'fichiers_statiques.zip *')) {
                \Nos\Tools_File::send($tmp.'fichiers_statiques.zip');
            } else {
                throw new \Exception('Impossible de zipper le fichier');
            }
        } catch (\Exception $e) {
            $this->send_error($e);
        }
    }

    private static function _dataFiles($files, $path, $depth)
    {
        $data = array();
        foreach ($files as $dir => $file) {
            if (is_array($file)) {
                $open = \Session::get('tree.ftplite|'.$path.$dir, null);
                $name = rtrim($dir, '/');
                $icon = 'static/apps/novius_ftplite/img/icons/folder.png';
                if (empty($path)) {
                    static $contexts = array();
                    if (empty($contexts)) {
                        $contexts = array_keys(\Nos\Tools_Context::contexts());
                    }
                    if (in_array($name, $contexts)) {
                        $icon = \Nos\Tools_Context::flagUrl($name);
                        $name = \Nos\Tools_Context::contextLabel($name, array('template' => '{site}'));
                    }
                }

                $data[] = array(
                    '_model' => 'dir',
                    '_id' => $path.$dir,
                    'file' => $name,
                    'icon' => $icon,
                    'treeChilds' => $open === true || ($depth > 1 && $open !== false) ? static::_dataFiles($file, $path.$dir, $depth - 1) : sizeof($file),
                );
            } else {
                list($context, $url) = explode('/', $path.$file, 2);
                $data[] = array(
                    '_model' => 'file',
                    '_id' => $path.$file,
                    'file' => $file,
                    'icon' => static::_iconFile($file),
                    'url' => \Nos\Tools_Url::context($context).$url,
                    'treeChilds' => 0,
                );
            }
        }
        return $data;
    }

    private static function _iconFile($file)
    {
        static $extensions = array();
        if (empty($extensions)) {
            $icons = \Config::load('noviusos_media::icons', true);
            foreach ($icons['icons'][16] as $image => $ext_list) {
                foreach (explode(',', $ext_list) as $ext) {
                    $extensions[$ext] = $image;
                }
            }
        }
        $pathinfo = pathinfo($file);
        $ext = $pathinfo['extension'];
        return 'static/apps/noviusos_media/icons/16/'.(isset($extensions[$ext]) ? $extensions[$ext] : 'misc.png');
    }

    public function action_files()
    {
        $depth = \Input::get('depth', 2);
        $file = \Input::get('id', '');
        if ($depth == -1) {
            \Session::set('tree.ftplite|'.$file, false);
            \Response::json(array(
                'items' => array(),
                'total' => 1,
            ));
        } else {
            if ($file) {
                \Session::set('tree.ftplite|'.$file, true);
            }

            try {
                $area = \File_Area::forge(array('basedir' => Ftplite::path()));
                $files = \File::read_dir(Ftplite::path($file), -1, null, $area);
            } catch (\Exception $e) {
                $files = array();
            }
            $data = static::_dataFiles($files, $file, $depth);

            $json = array(
                'items' => $data,
                'total' => count($data),
            );

            \Response::json($json);
        }
    }

    public function action_delete()
    {
        try {
            $file = \Input::post('file', '');
            $path = Ftplite::path($file);
            $area = \File_Area::forge(array('basedir' => Ftplite::path()));
            if (empty($file)) {
                \File::delete_dir($path, true, false, $area);
                $notify = 'Tous les fichiers statiques ont été supprimés.';
            } else if (is_dir($path)) {
                \File::delete_dir($path, true, true, $area);
                $notify = 'Suppression du répertoire réussie.';
            } else {
                \File::delete($path, $area);
                $notify = 'Suppression du fichier réussie.';
            }
            \Response::json(array(
                'notify' => $notify,
            ));
        } catch (\Exception $e) {
            $this->send_error($e);
        }
    }
}
