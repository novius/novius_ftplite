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

/**
 * Class Ftplite
 * @package Novius\Ftplite
 */
class Ftplite
{

    /**
     * @var string Name of the FTP Lite directory in local/data
     */
    public static $DIR = 'ftplite';

    /**
     * Send a file from FTP Lite if match URL parameter and context.
     *
     * @param string $url URL requested
     */
    public static function sendFile($url)
    {
        try {
            foreach (\Nos\Tools_Context::contexts() as $context => $domains) {
                foreach ($domains as $domain) {
                    if (mb_substr(\Uri::base(false).$url.'/', 0, mb_strlen($domain)) === $domain) {
                        $path = mb_substr(\Uri::base(false).$url, mb_strlen($domain));
                        $path = static::path($context.DS.$path);
                        if (is_file($path)) {
                            header('HTTP/1.0 200 Ok');
                            header('HTTP/1.1 200 Ok');
                            \Nos\Tools_File::send($path);
                        }
                    }
                }
            }
        } catch (\RuntimeException $e) {
        }
    }

    /**
     * @param string $relative_path URL A relative path
     * @return string The absolute path
     */
    public static function path($relative_path = '')
    {
        return realpath(APPPATH.'data').DS.static::$DIR.(!empty($relative_path) ? DS.$relative_path : '');
    }
}
