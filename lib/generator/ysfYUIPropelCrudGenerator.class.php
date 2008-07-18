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
 * Propel CRUD generator using Yahoo User Interface libraries.
 *
 * This class generates a basic CRUD module with propel.
 *
 * @package    ysymfony
 * @subpackage generator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Dustin Whittle <dustin.whittle@symfony-project.com>
 * @version    SVN: $Id$
 *
 * @link       http://developer.yahoo.com/
 */
class ysfYUIPropelCrudGenerator extends sfPropelAdminGenerator
{
  /**
   * The theme for templates
   *
   * @var string
   */
  protected $theme = 'ysfYUI';

  /**
   * Initializes the current sfGenerator instance.
   *
   * @param sfGeneratorManager A sfGeneratorManager instance
   */
  public function initialize(sfGeneratorManager $generatorManager)
  {
    parent::initialize($generatorManager);

    $this->setGeneratorClass('ysfYUIPropelCrudGenerator');

    ysfYUI::addComponent('forms');
  }

}
