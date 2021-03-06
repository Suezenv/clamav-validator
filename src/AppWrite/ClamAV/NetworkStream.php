<?php

namespace Suez\ClamAV\AppWrite\ClamAV;

use Appwrite\ClamAV\Network;

class NetworkStream extends Network {

    /** 
     * Inspired by https://fossies.org/linux/www/moodle-latest-310.tgz/moodle/lib/antivirus/clamav/classes/scanner.php
     */
    public function instreamFileScan(string $file): bool 
    {
        $socket = $this->getSocket();
        socket_write($socket, "nINSTREAM\n");
 
        // Open the file for reading.
        $fhandle = fopen($file, 'rb');
        while (!feof($fhandle)) {
            $chunk = fread($fhandle, self::CLAMAV_MAX);
            $size = pack('N', strlen($chunk));
            socket_write($socket, $size);
            socket_write($socket, $chunk);
        }

        socket_write($socket, pack('N', 0));
        
        // Get ClamAV answer.
        socket_recv($socket, $result, self::CLAMAV_MAX, 0);
        
        fclose($fhandle);
        socket_close($socket);

        return (trim($result) === 'stream: OK');
    }
}