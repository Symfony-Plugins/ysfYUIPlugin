<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * ysfYUITextWidget represents an HTML text input tag.
 *
 * @package    ysymfony
 * @subpackage yui
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Dustin Whittle <dustin.whittle@symfony-project.com>
 * @version    SVN: $Id$
 *
 * @link       http://developer.yahoo.com/
 */
class ysfYUITextWidget extends sfWidgetForm
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * type: The widget type (text by default)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addOption('type', 'text');
    $this->addOption('datasource', false);

    $this->setOption('is_hidden', false);
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

    if($datasource = $this->getOption('datasource'))
    {
      ysfYUI::addComponents('autocomplete', 'datasource');

      $attributes = $this->fixFormId(array_merge(array('name' => $name), $attributes));

      ysfYUI::addEvent($attributes['id'], 'ready', "
      var datasource{$attributes['id']} = new YAHOO.widget.DS_XHR('{$datasource}', ['ResultSet.Result','Title']);
      datasource{$attributes['id']}.maxCacheEntries = 60;
      datasource{$attributes['id']}.queryMatchSubset = true;
      datasource{$attributes['id']}.queryMatchContains = true;
      datasource{$attributes['id']}.scriptQueryAppend = 'output=json&results=100';

      var autocomplete{$attributes['id']} = new YAHOO.widget.AutoComplete('{$attributes['id']}','{$attributes['id']}_autocomplete', datasource{$attributes['id']});
      autocomplete{$attributes['id']}.useShadow = true;
      autocomplete{$attributes['id']}.queryDelay = 1;
      autocomplete{$attributes['id']}.doBeforeExpandContainer = function(oTextbox, oContainer, sQuery, aResults) { var pos = YAHOO.util.Dom.getXY(oTextbox); pos[1] += YAHOO.util.Dom.get(oTextbox).offsetHeight + 2; YAHOO.util.Dom.setXY(oContainer,pos); return true; };
      ");

      return $this->renderContentTag('div', $this->renderTag('input', array_merge(array('type' => $this->getOption('type'), 'name' => $name, 'value' => $value), $attributes)).$this->renderTag('div', array('id' => $attributes['id'].'_autocomplete')), array('id' => $attributes['id'].'_container'));
    }
    else
    {
      return $this->renderTag('input', array_merge(array('type' => $this->getOption('type'), 'name' => $name, 'value' => $value), $attributes));
    }
  }
}
