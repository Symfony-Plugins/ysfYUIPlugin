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
 * ysfYUICheckboxWidget represents an HTML checkbox.
 *
 * @package    ysymfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Dustin Whittle <dustin.whittle@symfony-project.com>
 * @version    SVN: $Id$
 *
 * @link       http://developer.yahoo.com/
 * @link       http://developer.yahoo.com/yui/examples/button/btn_example03.html
 */
class ysfYUICheckboxWidget extends sfWidgetFormInput
{
  /**
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormInput
   */
  protected function configure($options = array(), $attributes = array())
  {
    ysfYUI::addComponent('button');

    parent::configure($options, $attributes);

    $this->setOption('type', 'checkbox');
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The this widget is checked if value is not null
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if (!is_null($value) && $value !== false)
    {
      $attributes['checked'] = 'checked';
    }

    $attributes = $this->fixFormId(array_merge(array('name' => $name), $attributes));

    ysfYUI::addEvent($attributes['id'], 'ready', "var checkbox_".$attributes['id']." = new YAHOO.widget.Button('{$attributes['id']}');");

    return parent::render($name, null, $attributes, $errors);
  }
}
