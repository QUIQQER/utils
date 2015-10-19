<?php

/**
 * This file contains \QUI\Utils\Translation\PSpell
 */

namespace QUI\Utils\Translation;

use QUI;

/**
 * Easier Access to pspell
 *
 * @author  www.pcsg.de (Henning Leutz)
 * @package com.pcsg.qui.utils.translation
 *
 * @uses    pspell
 * @todo    check it, class is at the moment not in use
 *
 * @example $Trans = new \QUI\Utils\Translation\PSpell(array(
 *        'lang'    => 'en',
 *        'dialect' => 'american'
 * ));
 *
 * $Trans->translate('House');
 */

class PSpell extends QUI\QDOM
{
    /**
     * internal pspell object
     *
     * @var $_Spell
     */
    protected $_Spell;

    /**
     * Constructor
     *
     * @param array $settings - array(
     *                        lang
     *                        dialect
     *                        personal
     *                        );
     */
    public function __construct(array $settings)
    {
        // defaults
        $this->setAttribute('lang', 'en');
        $this->setAttribute('dialect', 'american');

        $this->setAttributes($settings);


        // PSpell Config
        $Config = pspell_config_create(
            $this->getAttribute('lang'),
            $this->getAttribute('dialect')
        );

        pspell_config_mode($Config, "PSPELL_FAST");

        if ($this->getAttribute('personal')) {
            pspell_config_personal($Config, $this->getAttribute('personal'));
        }

        $this->_Spell = pspell_new($Config);
    }

    /**
     * Check if pspell is installed
     *
     * @return Bool
     * @throws \QUI\Exception
     */
    static function check()
    {
        if (!function_exists('pspell_new')) {
            throw new QUI\Exception('PSpell is not installed');
        }

        return true;
    }

    /**
     * Translate a String
     *
     * @param string $word
     *
     * @return string
     */
    public function translate($word)
    {
        return pspell_suggest($this->_Spell, $word);
    }
}