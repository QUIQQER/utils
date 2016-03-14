<?php

/**
 * This file includes \QUI\Control
 */

namespace QUI;

use QUI;

/**
 * QUI Control
 * PHP counterpart to the \QUI\Control JavaScript class
 *
 * @author www.pcsg.de (Henning Leutz)
 */
class Control extends QDOM
{
    /**
     * list of css classes
     *
     * @var array
     */
    protected $cssClasses = array();

    /**
     * Constructor
     *
     * @param array $attributes
     */
    public function __construct($attributes = array())
    {
        $this->setAttributes($attributes);
    }

    /**
     * Return the DOM Node string
     *
     * @return string
     */
    public function create()
    {
        $body = '';

        try {
            $body = $this->getBody();
        } catch (QUI\Exception $Exception) {
            QUI\System\Log::writeException($Exception);
        }


        $attributes = $this->getAttributes();
        $params     = '';

        foreach ($attributes as $key => $value) {
            if (strpos($key, 'data-') === false
                && $this->isAllowedAttribute($key) === false
            ) {
                continue;
            }

            if (is_object($value)) {
                continue;
            }

            $key = Utils\Security\Orthos::clear($key);
            $params .= ' ' . $key . '="' . htmlentities($value) . '"';
        }

        // qui class
        $quiClass = '';

        if ($this->getAttribute('qui-class')) {
            $quiClass = 'data-qui="' . $this->getAttribute('qui-class') . '" ';
        }

        $cssClasses = array();

        if ($this->getAttribute('class')) {
            $cssClasses[] = $this->getAttribute('class');
        }

        $cssClasses = array_merge(array_keys($this->cssClasses), $cssClasses);

        if (!empty($cssClasses)) {
            $quiClass .= 'class="' . implode($cssClasses, ' ') . '" ';
        }


        // nddes
        $nodeName = 'div';

        if ($this->getAttribute('nodeName')) {
            $nodeName = $this->getAttribute('nodeName');
        }


        // styles
        $styleList = array();
        $style     = '';

        if ($this->getAttribute('height')) {
            $styleList['height'] = $this->cssValueCheck($this->getAttribute('height'));
        }

        if ($this->getAttribute('width')) {
            $styleList['width'] = $this->cssValueCheck($this->getAttribute('width'));
        }

        if (!empty($styleList)) {
            $style = 'style="';

            foreach ($styleList as $key => $val) {
                $style .= "{$key}:{$val};";
            }

            $style .= '" ';
        }

        return "<{$nodeName} {$style}{$quiClass}{$params}>{$body}</{$nodeName}>";
    }

    /**
     * Return the inner body of the element
     * Can be overwritten
     *
     * @return string
     */
    public function getBody()
    {
        return '';
    }

    /**
     * Add a css class
     *
     * @param string $cssClass
     */
    public function addCSSClass($cssClass)
    {
        $this->cssClasses[$cssClass] = true;
    }

    /**
     * Set the binded javascript control
     *
     * @param string $control
     */
    public function setJavaScriptControl($control)
    {
        $this->setAttribute('qui-class', $control);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setJavaScriptControlOption($name, $value)
    {
        $this->setAttribute(
            'data-qui-options-' . $name,
            $value
        );
    }

    /**
     * Remove a css class from the CSS list
     *
     * @param string $cssClass
     */
    public function removeCSSClass($cssClass)
    {
        if (isset($this->cssClasses[$cssClass])) {
            unset($this->cssClasses[$cssClass]);
        }
    }

    /**
     * Add a css file to the control
     *
     * @param string $file
     */
    public function addCSSFile($file)
    {
        Control\Manager::addCSSFile($file);
    }

    /**
     * @param $val
     *
     * @return string
     */
    protected function cssValueCheck($val)
    {
        $val = trim($val);

        if (empty($val)) {
            return '';
        }

        if (is_numeric($val)) {
            return (string)$val . 'px';
        }

        $units = array(
            'px',
            'cm',
            'mm',
            'mozmm',
            'in',
            'pt',
            'pc',
            'vh',
            'vw',
            'vm',
            'vmin',
            'vmax',
            'rem',
            '%',
            'em',
            'ex',
            'ch',
            'fr',
            'deg',
            'grad',
            'rad',
            's',
            'ms',
            'turns',
            'Hz',
            'kHz'
        );

        $no   = (int)$val;
        $unit = str_replace($no, '', $val);

        if (in_array($unit, $units)) {
            return $no . $unit;
        }

        if (!empty($no) && empty($unit)) {
            return $no . 'px';
        }

        return '';
    }

    /**
     * @param $val
     * @return string
     *
     * @deprecated
     */
    protected function _cssValueCheck($val)
    {
        return $this->cssValueCheck($val);
    }

    /**
     * Is the html node attribute allowed
     *
     * @param $attribute
     * @return boolean
     */
    protected function isAllowedAttribute($attribute)
    {
        $list = array(
            'disabled' => true,
            'alt' => true,
            'title' => true
        );

        return isset($list[$attribute]);
    }

    /**
     * Return the Project
     *
     * @return \QUI\Projects\Project
     */
    protected function getProject()
    {
        if ($this->getAttribute('Project')) {
            return $this->getAttribute('Project');
        }

        $Project = QUI::getRewrite()->getProject();

        if (!$Project) {
            $Project = QUI::getProjectManager()->get();
        }

        $this->setAttribute('Project', $Project);

        return $Project;
    }

    /**
     * @return Projects\Project
     *
     * @deprecated
     */
    protected function _getProject()
    {
        return $this->getProject();
    }
}
