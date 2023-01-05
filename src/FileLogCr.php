<?php
/**
 * CakePHP(tm) :  Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @since         1.3.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App;

use Cake\Core\Configure;
use Cake\Utility\Text;
use Cake\Log\Engine\FileLog;

/**
 * File Storage stream for Logging. Writes logs to different files
 * based on the level of log it is.
 */
class FileLogCr extends FileLog
{
    /**
     * Implements writing to log files.
     *
     * @param string $level The severity level of the message being written.
     *    See Cake\Log\Log::$_levels for list of possible levels.
     * @param string $message The message you want to log.
     * @param array $context Additional information about the logged message
     * @return bool success of write.
     */
    public function log($level, $message, array $context = [])
    {
        $message = $this->_format($message, $context);
        $message = preg_replace("/\n/i", "\r\n", $message);
        $output = date('Y-m-d H:i:s') . ' ' . ucfirst($level) . ': ' . $message . "\r\n";
        $filename = $this->_getFilename($level);
        if (!empty($this->_size)) {
            $this->_rotateFile($filename);
        }

        $pathname = $this->_path . $filename;
        $mask = $this->_config['mask'];
        if (empty($mask)) {
            return file_put_contents($pathname, $output, FILE_APPEND);
        }

        $exists = file_exists($pathname);
        $result = file_put_contents($pathname, $output, FILE_APPEND);
        static $selfError = false;

        if (!$selfError && !$exists && !chmod($pathname, (int)$mask)) {
            $selfError = true;
            trigger_error(vsprintf(
                'Could not apply permission mask "%s" on log file "%s"',
                [$mask, $pathname]
            ), E_USER_WARNING);
            $selfError = false;
        }

        return $result;
    }
}
