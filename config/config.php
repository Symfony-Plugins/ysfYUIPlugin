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
 * Configuration for ysfYUIPlugin.
 *
 * @package    ysymfony
 * @subpackage config
 * @author     Dustin Whittle <dustin.whittle@symfony-project.com>
 */


/**
 * Register yui.yml config handler and load compiled yui.yml
 */
$this->getConfigCache()->registerConfigHandler('config/yui.yml', 'sfDefineEnvironmentConfigHandler', array('prefix' => 'yui_'));
require_once($this->getConfigCache()->checkConfig('config/yui.yml'));

/**
 * Register event for loading routes
 */
if (sfConfig::get('yui_routes_register', false) && in_array('sfYUI', sfConfig::get('sf_enabled_modules')))
{
  $this->dispatcher->connect('routing.load_configuration', array('ysfYUI', 'listenToRoutingLoadConfigurationEvent'));
}
