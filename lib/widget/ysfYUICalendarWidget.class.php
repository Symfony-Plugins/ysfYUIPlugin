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
 * ysfYUICalendarWidget represents a YUI calendar widget.
 *
 * @package    ysymfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Dustin Whittle <dustin.whittle@symfony-project.com>
 * @version    SVN: $Id$
 *
 * @link       http://developer.yahoo.com/
 * @link       http://developer.yahoo.com/yui/examples/button/btn_example09.html
 */
class ysfYUICalendarWidget extends sfWidgetForm
{
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * format:       The date format string (%month%/%day%/%year% by default)
   *  * years:        An array of years for the year select tag (optional)
   *  * months:       An array of months for the month select tag (optional)
   *  * days:         An array of days for the day select tag (optional)
   *  * can_be_empty: Whether the widget accept an empty value (true by default)
   *  * empty_values: An array of values to use for the empty value (empty string for year, month, and date by default)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    ysfYUI::addComponent('button');

    $this->addOption('format', '%month%/%day%/%year%');
    $this->addOption('days', parent::generateTwoCharsRange(1, 31));
    $this->addOption('months', parent::generateTwoCharsRange(1, 12));
    $years = range(date('Y') - 5, date('Y') + 5);
    $this->addOption('years', array_combine($years, $years));

    $this->addOption('can_be_empty', true);
    $this->addOption('empty_values', array('year' => '', 'month' => '', 'day' => ''));
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The date displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    // convert value to an array
    $default = array('year' => null, 'month' => null, 'day' => null);
    if (is_array($value))
    {
      $value = array_merge($default, $value);
    }
    else
    {
      $value = ctype_digit($value) ? (integer) $value : strtotime($value);
      if (false === $value)
      {
        $value = $default;
      }
      else
      {
        $value = array('year' => date('Y', $value), 'month' => date('n', $value), 'day' => date('j', $value));
      }
    }

    $date = array();
    $emptyValues = $this->getOption('empty_values');

    // days
    $widget = new sfWidgetFormSelect(array('choices' => $this->getOption('can_be_empty') ? array('' => $emptyValues['day']) + $this->getOption('days') : $this->getOption('days')), array_merge($this->attributes, $attributes));
    $date['%day%'] = $widget->render($name.'[day]', $value['day']);

    // months
    $widget = new sfWidgetFormSelect(array('choices' => $this->getOption('can_be_empty') ? array('' => $emptyValues['month']) + $this->getOption('months') : $this->getOption('months')), array_merge($this->attributes, $attributes));
    $date['%month%'] = $widget->render($name.'[month]', $value['month']);

    // years
    $widget = new sfWidgetFormSelect(array('choices' => $this->getOption('can_be_empty') ? array('' => $emptyValues['year']) + $this->getOption('years') : $this->getOption('years')), array_merge($this->attributes, $attributes));
    $date['%year%'] = $widget->render($name.'[year]', $value['year']);

    return strtr($this->getOption('format'), $date);
  }
}