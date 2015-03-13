<?php

/**
 * This file contains \QUI\Log
 */

namespace QUI;

/**
 * Writes Logs into the logdir
 *
 * @todo -> überdenken, da in QUI schon ein log manager gibt der auch mit plugins erweitert wird
 *
 * @author www.pcsg.de (Henning Leutz)
 * @package com.pcsg.qutils
 */

class Log
{
    /**
     * Writes an String to a log file
     *
     * @param String $message  - String to write
     * @param String $filename - Filename (eq: messages, error, database)
     */
    static function write($message, $filename='messages')
    {
        if ( !defined( 'VAR_DIR' ) )
        {
            error_log( $message."\n", 3 );
            return;
        }

        $dir  = VAR_DIR .'log/';
        $file = $dir . $filename . date('-Y-m-d').'.log';

        // Log Verzeichnis erstellen
        Utils\System\File::mkdir( $dir );

        error_log( $message."\n", 3, $file );
    }

    /**
     * Writes with print_r the object into a log file
     *
     * @param mixed $object
     * @param String $filename
     */
    static function writeRecursive($object, $filename='messages')
    {
        self::write( print_r($object, true), $filename );
    }

    /**
     * Writes an Exception to a log file
     *
     * @param \Exception|\QUI\Exception $Exception
     * @param String $filename
     */
    static function writeException($Exception, $filename='error')
    {
        $message  = $Exception->getCode() ." :: \n\n";
        $message .= $Exception->getMessage();

        self::write( $message, $filename );
    }
}