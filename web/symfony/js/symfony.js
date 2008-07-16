
/**
 * symfony.js - Contains javascript calls needed globally across symfony applications including configuration.
 *
 * @namespace YAHOO.symfony
 * @author    dustin.whittle@symfony-project.com
 * @version   0.1
 */

/**
 * Set namespace for project
 */
YAHOO.namespace('symfony'); 

/**
 * Version Number
 */
YAHOO.symfony.version = 0.1;

/**
 * Configuration
 */
YAHOO.symfony.config = { 
  debug: true
};

/**
 * I18n labels
 */
YAHOO.symfony.i18n = {
  symfony: 'symfony'
};

/**
 * Custom events for symfony web apps
 */
YAHOO.symfony.event = {
  ready: new YAHOO.util.CustomEvent('ready')
}

/**
 * Fire custom event when interface is ready
 */
YAHOO.util.Event.onDOMReady(function() {
  YAHOO.symfony.event.ready.fire();
});
