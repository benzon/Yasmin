<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\Utils;

use React\EventLoop\LoopInterface;
use React\Filesystem\Filesystem;
use React\Filesystem\FilesystemInterface;
use React\Promise\ExtendedPromiseInterface;
use React\Promise\PromiseInterface;
use RuntimeException;
use function file_get_contents;
use function filter_var;
use function React\Promise\reject;
use function React\Promise\resolve;
use function realpath;

/**
 * File Helper methods.
 */
class FileHelpers {
    /**
     * @var LoopInterface
     */
    protected static $loop;
    
    /**
     * @var FilesystemInterface|null
     */
    protected static $filesystem;
    
    /**
     * Sets the Event Loop.
     * @param LoopInterface  $loop
     * @return void
     * @internal
     */
    static function setLoop(LoopInterface $loop) {
        self::$loop = $loop;
        
        if(self::$filesystem === null) {
            $adapters = Filesystem::getSupportedAdapters();
            if(!empty($adapters)) {
                self::$filesystem = Filesystem::create($loop);
            }
        }
    }
    
    /**
     * Returns the stored React Filesystem instance, or null.
     * @return FilesystemInterface|false|null
     */
    static function getFilesystem() {
        return self::$filesystem;
    }
    
    /**
     * Sets the React Filesystem instance, or disables it.
     * @param FilesystemInterface|null  $filesystem
     * @return void
     */
    static function setFilesystem(?FilesystemInterface $filesystem) {
        if($filesystem === null) {
            $filesystem = false;
        }
        
        self::$filesystem = $filesystem;
    }
    
    /**
     * Resolves filepath and URL into file data - returns it if it's neither. Resolves with a string.
     * @param string  $file
     * @return ExtendedPromiseInterface|PromiseInterface
     */
    static function resolveFileResolvable(string $file) {
        $rfile = @realpath($file);
        if($rfile) {
            if(self::$filesystem) {
                return self::$filesystem->getContents($file);
            }
            
            return resolve(file_get_contents($rfile));
        } elseif(filter_var($file, FILTER_VALIDATE_URL)) {
            return URLHelpers::resolveURLToData($file);
        }
        
        return reject(new RuntimeException('Given file is not resolvable'));
    }
}
