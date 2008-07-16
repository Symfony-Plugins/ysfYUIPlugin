<?php

// register yui.yml config handler and load compiled yui.yml
$this->getConfigCache()->registerConfigHandler('config/yui.yml', 'sfDefineEnvironmentConfigHandler', array('prefix' => 'yui_'));
require_once($this->getConfigCache()->checkConfig('config/yui.yml'));

// register event for loading routes
if (sfConfig::get('yui_routes_register', false) && in_array('sfYUI', sfConfig::get('sf_enabled_modules')))
{
  $this->dispatcher->connect('routing.load_configuration', array('ysfYUI', 'listenToRoutingLoadConfigurationEvent'));
}
