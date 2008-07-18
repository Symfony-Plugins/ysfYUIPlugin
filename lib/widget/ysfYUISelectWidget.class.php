<?php

/**
 *
 * Copyright (c) 2008 Yahoo! Inc.  All rights reserved.
 * The copyrights embodied in the content in this file are licensed
 * under the MIT open source license.
 *
 * For the full copyright and license information, please view the LICENSE.yahoo
 * file that was distributed with this source code.
 *
 */


/**
 * ysfYUISelectWidget represents a YUI select menu widget.
 *
 * @package    ysymfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Dustin Whittle <dustin.whittle@symfony-project.com>
 * @version    SVN: $Id$
 *
 * @link       http://developer.yahoo.com/
 * @link       http://developer.yahoo.com/yui/examples/button/btn_example07.html
 */
class ysfYUISelectWidget extends sfWidgetForm
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * choices:  An array of possible choices (required)
   *  * multiple: true if the select tag must allow multiple selections
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    ysfYUI::addComponent('container', 'menu', 'button');

    $this->addRequiredOption('choices');
    $this->addOption('multiple', false);
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The value selected in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if ($this->getOption('multiple'))
    {
      $attributes['multiple'] = 'multiple';

      if ('[]' != substr($name, -2))
      {
        $name .= '[]';
      }
    }

    $choices = $this->getOption('choices');
    if ($choices instanceof sfCallable)
    {
      $choices = $choices->call();
    }

    $attributes = $this->fixFormId(array_merge(array('name' => $name), $attributes));

    ysfYUI::addEvent($attributes['id'], 'ready', "var ".$attributes['id']."_button = new YAHOO.widget.Button('".$attributes['id']."_button', { type: 'menu', menu: '".$attributes['id']."' }); ".$attributes['id']."_button.getMenu().clickEvent.subscribe(function(ev, args) { var opt = args[1].srcElement; ".$attributes['id']."_button.set('label', opt.innerHTML); ".$attributes['id']."_button.set('value', opt.value); });");

    return '<input type="button" id="'.$attributes['id'].'_button" name="'.$attributes['id'].'_button" value="Select Menu" />'
           .$this->renderContentTag('select', "\n".implode("\n", $this->getOptionsForSelect($value, $choices))."\n", $attributes);
  }

  /**
   * Returns an array of option tags for the given choices
   *
   * @param  string $value    The selected value
   * @param  array  $choices  An array of choices
   *
   * @return array  An array of option tags
   */
  protected function getOptionsForSelect($value, $choices)
  {
    $mainAttributes = $this->attributes;
    $this->attributes = array();

    $options = array();
    foreach ($choices as $key => $option)
    {
      if (is_array($option))
      {
        $options[] = $this->renderContentTag('optgroup', implode("\n", $this->getOptionsForSelect($value, $option)), array('label' => self::escapeOnce($key)));
      }
      else
      {
        $attributes = array('value' => self::escapeOnce($key));
        if ((is_array($value) && in_array(strval($key), $value)) || strval($key) == strval($value))
        {
          $attributes['selected'] = 'selected';
        }

        $options[] = $this->renderContentTag('option', self::escapeOnce($option), $attributes);
      }
    }

    $this->attributes = $mainAttributes;

    return $options;
  }

  /**
   * @see sfWidget
   *
   * We always generate an attribute for the value.
   */
  protected function attributesToHtmlCallback($k, $v)
  {
    return is_null($v) || ('' === $v && 'value' != $k) ? '' : sprintf(' %s="%s"', $k, $this->escapeOnce($v));
  }
}
