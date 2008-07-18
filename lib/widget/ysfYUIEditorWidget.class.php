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
 * ysfYUIEditorWidget represents a YUI Rich Text Editor.
 *
 * @package    ysymfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Dustin Whittle <dustin.whittle@symfony-project.com>
 * @version    SVN: $Id$
 *
 * @link       http://developer.yahoo.com/
 * @link       http://developer.yahoo.com/yui/editor/
 */
class ysfYUIEditorWidget extends sfWidgetForm
{
  /**
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    ysfYUI::addComponent('editor');

    $this->setAttribute('rows', 10);
    $this->setAttribute('cols', 50);
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The value displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $attributes = $this->fixFormId(array_merge(array('name' => $name), $attributes));

    ysfYUI::addEvent($attributes['id'], 'ready', "var editor_".$attributes['id']." = new YAHOO.widget.SimpleEditor('{$attributes['id']}', { height: '400px', width: '500px', animate: true, dompath: true, autoHeight: true, markup:'xhtml', handleSubmit: true }); editor_".$attributes['id'].".render();");

    return $this->renderContentTag('textarea', self::escapeOnce($value), $attributes);
  }
}
