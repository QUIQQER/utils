<?php

/**
 * This file contains the QUI\Utils\Security\Orthos
 */

namespace QUI\Utils\Security;

use QUI\Utils\String as QUIString;

/**
 * Orthos - Security class
 *
 * Has different methods in order to examine variables on their correctness
 * Should be used to validate user input
 *
 * @author www.pcsg.de (Henning Leutz)
 * @author www.pcsg.de (Moritz Scholz)
 */

class Orthos
{
    /**
     * Befreit einen String von allem möglichen Schadcode
     *
     * @param String $str
     * @return String
     */
    static function clear($str)
    {
        $str = self::removeHTML( $str );
        $str = self::clearPath( $str );
        $str = self::clearFormRequest( $str );

        $str = htmlspecialchars( $str );

        return $str;
    }

    /**
     * Remove all none characters in the string.
     * none characters are no a-z A-z or 0-9
     *
     * @param String $str
     * @return String
     */
    static function clearNoneCharacters($str='', $allowedList=array())
    {
        $chars = 'a-zA-Z0-9';

        if ( is_array( $allowedList ) ) {
            $chars .= implode( $allowedList );
        }

        return preg_replace( "/[^{$chars}]/", "", $str );
    }

    /**
     * Befreit ein Array von allem möglichen Schadcode
     *
     * @param Array $data
     * @return Array
     */
    static function clearArray($data)
    {
        if ( !is_array( $data ) ) {
            return array();
        }

        $cleanData = array();

        foreach ( $data as $key => $str )
        {
            if ( is_array( $data[ $key ] ) )
            {
                $cleanData[ $key ] = self::clearArray( $data[ $key ] );
                continue;
            }

            $cleanData[ $key ] = self::clear( $str );
        }

        return $cleanData;
    }

    /**
     * Säubert eine Pfadangabe von eventuellen Änderungen des Pfades
     *
     * @param String $path
     * @return String|Bool
     */
    static function clearPath($path)
    {
        return str_replace( array('../', '..') , '', $path );
    }

    /**
     * Enfernt HTML aus dem Text
     *
     * @param String $text
     * @return String
     */
    static function removeHTML($text)
    {
        return strip_tags( $text );
    }

    /**
     * Befreit einen MySQL Command String von Schadcode
     *
     * If you are using this function to build SQL statements,
     * you are strongly recommended to use PDO::prepare() to prepare
     * SQL statements with bound parameters instead of using PDO::quote()
     * to interpolate user input into an SQL statement. Prepared statements with
     * bound parameters are not only more portable, more convenient, immune to SQL injection,
     * but are often much faster to execute than interpolated queries,
     * as both the server and client side can cache a compiled form of the query.
     *
     * @param String $str - Command
     * @param Bool $escape - Escape the String (true or false}
     * @return String
     *
     * @deprecated use PDO::quote (QUI::getPDO()->quote())
     */
    static function clearMySQL($str, $escape=true)
    {
        if ( get_magic_quotes_gpc() ) {
            $str = stripslashes( $str );
        }

        if ( $escape && class_exists( 'QUI' ) ) {
            $str = \QUI::getPDO()->quote( $str );
        }

        return $str;
    }

    /**
     * Befreit einen Shell Command String von Schadcode
     *
     * @param String $str - Command
     * @return String
     */
    static function clearShell($str)
    {
        return escapeshellcmd( $str );
    }

    /**
     * Enter description here...
     *
     * @param String $str
     * @return Integer
     */
    static function parseInt($str)
    {
        return (int)$str;
    }

    /**
     * Säubert "böses" HTML raus
     * Zum Beispiel für Wiki
     *
     * @param String $str
     * @return String
     */
    static function cleanHTML($str)
    {
        $BBCode = new \QUI\Utils\Text\BBCode();

        $str = $BBCode->parseToBBCode( $str );
        $str = $BBCode->parseToHTML( $str );

        return $str;
    }

    /**
     * Prüft Datumsteile nach Korrektheit
     * Bei Korrektheit kommt $val wieder zurück ansonsten 0
     *
     * @param Integer $val
     * @param String $type - DAY | MONTH | YEAR
     *
     * @return Integer
     */
    static function date($val, $type='DAY')
    {
        if ( $type == 'MONTH' )
        {
            $val = self::parseInt( $val );

            // Wenn Monat nicht zwischen 1 und 12 liegt
            if ( $val < 1 || $val > 12 ) {
                return 0;
            }

            return $val;
        }


        if ( $type == 'YEAR' ) {
            return self::parseInt( $val );
        }


        $val = self::parseInt( $val );

        // Wenn Tag nicht zwischen 1 und 31 liegt
        if ( $val < 1 || $val > 31 ) {
            return 0;
        }

        return $val;
    }

    /**
     * Prüft ein Datum auf Korrektheit
     *
     * @param Integer $day
     * @param Integer $month
     * @param Integer $year
     * @return Bool
     */
    static function checkdate($day, $month, $year)
    {
        if ( !is_int( $day ) ) {
            return false;
        }

        if ( !is_int( $month ) ) {
            return false;
        }

        if ( !is_int( $year ) ) {
            return false;
        }

        return checkdate( $month, $day, $year );
    }

    /**
     * use \QUI\Utils\String::removeLineBreaks
     * @see \QUI\Utils\String::removeLineBreaks
     * @deprecated use \QUI\Utils\String::removeLineBreaks
     * @param String $text
     * @return string
     */
    static function removeLineBreaks($text)
    {
        return QUIString::removeLineBreaks($text, '');
    }

    /**
     * Prüft eine Mail Adresse auf Syntax
     *
     * @param String $email - Mail Adresse
     * @return Bool
     */
    static function checkMailSyntax($email)
    {
        return preg_match('/^([A-Za-z0-9\.\!\#\$\%\&\'\*\+\-\/\=\?\^\_\`\{\|\}\~]){1,64}\@{1}([A-Za-z0-9\.\!\#\$\%\&\'\*\+\-\/\=\?\^\_\`\{\|\}\~]){1,248}\.{1}([a-z]){2,6}$/', $email);
    }

    /**
     * Prüft ein MySQL Timestamp auf Syntax
     *
     * @param String $date
     * @return Bool
     */
    static function checkMySqlDatetimeSyntax($date)
    {
        // Nur Zahlen erlaubt
           if ( preg_match( '/[^0-9- :]/i', $date ) ) {
               return false;
        }

        // Syntaxprüfung
        if ( !preg_match( "/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/", $date ) ) {
            return false;
        }

        return true;
    }

    /**
     * Generiert einen Zufallsstring
     *
     * @param $length - Länge des Passwortes
     * @return String
     */
    static function getPassword($length=10)
    {
        $newpass = "";
        $laenge  = $length;
        $string  = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        mt_srand((double)microtime()*1000000);

        for ($i = 1; $i <= $laenge; $i++) {
            $newpass .= substr($string, mt_rand(0, strlen($string)-1), 1);
        }

        return $newpass;
    }

    /**
     * Prüft ob die Mail Adresse eine Spam Wegwerf Mail Adresse ist
     *
     * @param String $mail - E-Mail Adresse
     * @return Bool
     */
    static function isSpamMail($mail)
    {
        $split = explode('@', $mail);

        $adresses = array(
            'anonbox.net',
            'bumpymail.com',
            'centermail.com',
            'centermail.net',
            'discardmail.com',
            'emailias.com',
            'jetable.net',
            'mailexpire.com',
            'mailinator.com',
            'messagebeamer.de',
            'mytrashmail.com',
            'trash-mail.de',
            'trash-mail.com',
            'trashmail.net',
            'pookmail.com',
            'nervmich.net',
            'netzidiot.de',
            'nurfuerspam.de',
            'mail.net',
            'privacy.net',
            'punkass.com',
            'sneakemail.com',
            'sofort-mail.de',
            'spamex.com',
            'spamgourmet.com',
            'spamhole.com',
            'spaminator.de',
            'spammotel.com',
            'spamtrail.com',
            'temporaryinbox.com',
            'put2.net',
            'senseless-entertainment.com',
            'dontsendmespam.de',

            'spam.la',
            'spaml.de',
            'spambob.com',
            'kasmail.com',
            'dumpmail.de',
            'dodgeit.com',

            'fastacura.com',
            'fastchevy.com',
            'fastchrysler.com',
            'fastkawasaki.com',
            'fastmazda.com',
            'fastmitsubishi.com',
            'fastnissan.com',
            'fastsubaru.com',
            'fastsuzuki.com',
            'fasttoyota.com',
            'fastyamaha.com',

            'nospamfor.us',
            'nospam4.us',

            'trashdevil.de',
            'trashdevil.com',

            'spoofmail.de',
            'fivemail.de',
            'giantmail.de'
        );

        if (!isset($split[1])) {
            return false;
        }

        foreach ($adresses as $entry)
        {
            if (strpos($split[1], $entry) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Löscht alle Zeichen aus einem Request welches für ein XSS verwendet werden könnte
     *
     * @param String $value
     * @return String
     */
    static function clearFormRequest($value)
    {
        if ( is_array( $value ) )
        {
            foreach ( $value as $key => $entry ) {
                $value[$key] = self::clearFormRequest( $entry ); // htmlspecialchars_decode($entry);
            }

        } else
        {
            if ( !is_string( $value ) ) {
                return '';
            }

            $value = htmlspecialchars_decode($value);
        }
        // alle zeichen undd HEX codes werden mit leer ersetzt
        $value = str_replace(
            array(
                '<', '%3C',
                '>', '%3E',
                '"', '%22',
                '\\', '%5C',
                '/', '%2F',
                '\'', '%27',
            ),
            '',
            $value
        );

        return $value;
    }
}