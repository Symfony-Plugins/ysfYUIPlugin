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
 * ysfYUIForm represents a Y! UI enhanced form.
 *
 * @package    ysymfony
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Dustin Whittle <dustin.whittle@symfony-project.com>
 * @version    SVN: $Id$
 *
 * @link       http://developer.yahoo.com/
 */
class ysfYUIForm extends sfForm
{

  /**
   * Configures the Y! UI form.
   *
   * @see sfForm::configure
   */
  public function configure()
  {
    $this->widgetSchema->setFormFormatterName('table');
  }

}