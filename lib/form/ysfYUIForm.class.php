<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * ysfYUIForm represents a Y! UI enhanced form.
 *
 * @package    ysymfony
 * @subpackage yui
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Dustin Whittle <dustin.whittle@symfony-project.com>
 * @version    SVN: $Id$
 *
 * @link       http://developer.yahoo.com/
 */
class ysfYUIForm extends sfForm
{

  public function configure()
  {
    $this->widgetSchema->setFormFormatterName('table');
  }

}