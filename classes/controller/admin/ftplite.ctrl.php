<?php
/**
 * FTP Lite is an application for Novius OS for managing static files
 *
 * @copyright  2013 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link https://github.com/novius/novius_ftplite
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
            if (\Input::method() === 'POST') {
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
                        $allow = array_merge($allow, explode(',', $ext_list));
                    }
                    $unzip->allow($allow);
                    $unzip->extract($_FILES['import']['tmp_name'], $dir);
                } else {
                    move_uploaded_file($_FILES['import']['tmp_name'], $dir.DS.$_FILES['import']['name']);
                }

                \Response::json(array(
                    'notify' => __('Mission accomplished, your file(s) has(ve) been uploaded.'),
                    'dispatchEvent' => array('name' => 'ftplite'),
                    'closeDialog' => true,
                ));
            } else {
                return \View::forge('novius_ftplite::admin/import');
            }
        } catch (\Exception $e) {
            $this->send_error($e);
        }
    }

    public function action_export()
    {
        try {
            $tmp = \Config::get('tmp_dir', '/tmp/');
            if (is_file($tmp.'ftplite.zip')) {
                unlink($tmp.'ftplite.zip');
            }
            if (exec('cd '.Ftplite::path().';zip -r '.$tmp.'ftplite.zip *')) {
                \Nos\Tools_File::send($tmp.'ftplite.zip');
            } else {
                throw new \Exception(__('We cannot unzip the archive. This is something your developer or system administrator can fix for you.'));
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
            if (\Input::method() === 'POST') {
                $file = \Input::post('file', '');
                $path = Ftplite::path($file);
                $area = \File_Area::forge(array('basedir' => Ftplite::path()));
                if (empty($file)) {
                    \File::delete_dir($path, true, false, $area);
                    $notify = __('All files have been deleted.');
                } else if (is_dir($path)) {
                    \File::delete_dir($path, true, true, $area);
                    $notify = __('The folder has been deleted.');
                } else {
                    \File::delete($path, $area);
                    $notify = __('The file has been deleted.');
                }
                \Response::json(array(
                    'notify' => $notify,
                    'dispatchEvent' => array('name' => 'ftplite'),
                ));
            } else {
                $view_params = array(
                    'file' => \Input::get('file', ''),
                    'crud' => array(
                        'config' => array(
                            'views' => array(
                                'delete' => 'novius_ftplite::admin/delete',
                            ),
                            'i18n' => \Config::load('nos::i18n_common', true),
                            'controller_url' => 'admin/novius_ftplite/ftplite',
                        ),
                    ),
                );
                $view_params['view_params'] = &$view_params;
                return \View::forge('nos::crud/delete_popup_layout', $view_params, false);
            }
        } catch (\Exception $e) {
            $this->send_error($e);
        }
    }
}
