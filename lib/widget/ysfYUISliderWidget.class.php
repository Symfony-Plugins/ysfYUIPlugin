<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * ysfYUISliderWidget represents a YUI Color Picker widget.
 *
 * @package    ysymfony
 * @subpackage yui
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Dustin Whittle <dustin.whittle@symfony-project.com>
 * @version    SVN: $Id$
 *
 * @link       http://developer.yahoo.com/
 * @link       http://developer.yahoo.com/yui/examples/button/btn_example14.html
 */
class ysfYUISliderWidget extends sfWidgetForm
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
    $this->addOption('type', 'hidden');

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
    $attributes = $this->fixFormId(array_merge(array('name' => $name), $attributes));

    ysfYUI::addComponent('slider');

    ysfYUI::addEvent($attributes['id'], 'available', "

    var slider_".$attributes['id']." = new YAHOO.widget.Slider.getHorizSlider('".$attributes['id']."-bg', '".$attributes['id']."-thumb', 0, 200, 10);
    slider_".$attributes['id'].".subscribe('change', function(offsetFromStart) { YAHOO.util.Dom.get('".$attributes['id']."').value = Math.round(slider_".$attributes['id'].".getValue()); });

    ");

    $style = '
<style type="text/css">
#'.$attributes['id'].'-bg { position: relative; background:url(/static/ysf/symfony/images/bg-h.gif) 5px 0 no-repeat; height:28px; width:228px; }
#'.$attributes['id'].'-thumb { position: absolute; top: 4px; }
#img

</style>
';

    return $style.'<div id="'.$attributes['id'].'-bg"><div id="'.$attributes['id'].'-thumb"><img src="/static/ysf/symfony/images/thumb-n.gif" /></div></div>'.
           $this->renderTag('input', array_merge(array('type' => $this->getOption('type'), 'name' => $name, 'value' => $value), $attributes));

  }
}
