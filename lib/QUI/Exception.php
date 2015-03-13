<?php

/**
 * This file contains the \QUI\Exception
 */

namespace QUI;

/**
 * The Main Exception class for QUIQQER CMS and QUI Utils
 *
 * @author www.pcsg.de (Henning Leutz)
 * @package com.pcsg.qutils
 */

class Exception extends \Exception
{
    /**
     * Internal list of attributes
     * @var array
     */
    protected $_attributes = array();

    /**
     * Constructor
     *
     * @param String $message - Text der Exception
     * @param Integer $code   - Errorcode der Exception
     * @param String $type    - Error Typ (Standard = Exception)
    */
    public function __construct($message=null, $code=0, $type='Exception')
    {
        parent::__construct( (string)$message, (int)$code );
    }

    /**
     * Return the Exception type
     *
     * @return String
     */
    public function getType()
    {
        return get_class( $this );
    }

    /**
     * Return the Exception as an array
     *
     * @return Array
     */
    public function toArray()
    {
        $attributes = $this->_attributes;

        $attributes['code']    = $this->getCode();
        $attributes['message'] = $this->getMessage();
        $attributes['type']    = $this->getType();

        return $attributes;
    }

    /**
     * returns a attribute
     * if the attribute is not set, it returns false
     *
     * @param String $name
     * @return mixed
     */
    public function getAttribute($name)
    {
        if ( isset( $this->_attributes[ $name ] ) ) {
            return $this->_attributes[ $name ];
        }

        return false;
    }

    /**
     * set an attribute
     *
     * @param String $name - name of the attribute
     * @param mixed $val - value of the attribute
     * @return \QUI\Exception this
     */
    public function setAttribute($name, $val)
    {
        $this->_attributes[ $name ] = $val;

        return $this;
    }

    /**
     * If you want to set more than one attribute
     *
     * @param Array $attributes
     * @return \QUI\Exception
     */
    public function setAttributes($attributes)
    {
        if ( !is_array( $attributes ) ) {
            return $this;
        }

        foreach ( $attributes as $key => $value ) {
            $this->setAttribute( $key, $value );
        }

        return $this;
    }
}