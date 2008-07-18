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
 * debug.js - Contains all debug related tasks, for dealing with web debug toolbar and miscellaneous debug.
 *
 * @namespace YAHOO.symfony
 * @author    dustin.whittle@symfony-project.com
 * @version   0.1
 * @todo      Refactor to use custom events and delegation.
 */

/**
 * symfony-debug - symfony client-side development environment
 */
YAHOO.symfony.debug =
{
  /** generic */
  initialize: function()
  {
    YAHOO.symfony.debug.logger.initialize();
    YAHOO.symfony.debug.toolbar.initialize();

    // handle keymapping of 'escape' to hide all debug elements
    var esc = new YAHOO.util.KeyListener(document, { keys: 27 }, { fn: function() { YAHOO.symfony.debug.toolbar.hideMenu(); YAHOO.symfony.debug.logger.console.hide(); }}, "keyup");
    esc.enable();

    // handle keymapping of 'ctrl + y' to open/close debug dialog
    var y = new YAHOO.util.KeyListener(document, { ctrl:true, keys: 89 },  { fn: function () { YAHOO.symfony.debug.logger.toggle(); window.scrollTo(YAHOO.util.Dom.getDocumentScrollTop(document), YAHOO.util.Dom.getY('yui-logger')); } } );
    y.enable();

    // handle keymapping of 'ctrl + s' to open/close debug dialog
    var s = new YAHOO.util.KeyListener(document, { ctrl: true, keys: 83 }, { fn: function() { YAHOO.symfony.debug.toolbar.toggle(); window.scrollTo(YAHOO.util.Dom.getDocumentScrollTop(document), YAHOO.util.Dom.getY('sfWebDebugBar')); } } );
    s.enable();

    YAHOO.log("debug environment initialized", 'info', 'symfony');
  },

  element:
  {
    toggle: function(element)
    {
      if(typeof element == 'string' && element != '')
      {
        var element = YAHOO.util.Dom.get(element);
      }

      if(typeof element == 'object')
      {
        element.style.display = (!element.style.display || element.style.display == 'none') ? 'block' : 'none';
      }
    },

    show: function(element)
    {
      if(typeof element == 'string' && element != '')
      {
        var element = YAHOO.util.Dom.get(element);
      }

      if(typeof element == 'object')
      {
        element.style.display = '';
      }
    },

    hide: function(element)
    {
      if(typeof element == 'string' && element != '')
      {
        var element = YAHOO.util.Dom.get(element);
      }

      if(typeof element == 'object')
      {
        element.style.display = 'none';
      }
    }
  },

  logger:
  {
    initialize: function()
    {
      // Send any YUI log messages to the browser console (firebug!)
      YAHOO.widget.Logger.enableBrowserConsole();

      if(!YAHOO.util.Dom.get('yui-logger'))
      {
        var logger = document.createElement('div');
        logger.id = 'yui-logger';
        document.body.appendChild(logger);
      }

      YAHOO.symfony.debug.logger.console = new YAHOO.widget.LogReader('yui-logger');
      YAHOO.symfony.debug.logger.console.setTitle("YUI Logger");
      YAHOO.symfony.debug.logger.console.collapse();
      YAHOO.symfony.debug.logger.console.hide();

      YAHOO.log("yui logger initialized", 'info', 'symfony');
    },

    toggle: function()
    {
      var element = YAHOO.util.Dom.get('yui-logger');

      if(typeof element == 'object')
      {
        element.style.display = (element.style.display == 'none') ? 'block' : 'none';

        if(element.style.display == 'block')
        {
          YAHOO.symfony.debug.logger.console.show();
          YAHOO.symfony.debug.logger.console.expand();
        }
        else
        {
          YAHOO.symfony.debug.logger.console.collapse();
          YAHOO.symfony.debug.logger.console.hide();
        }
      }
    }
  },

  toolbar: /** widget for symfony debug toolbar  */
  {
    initialize: function()
    {
      // get debug toolbar
      YAHOO.symfony.debug.toolbar.element = YAHOO.util.Dom.get('sfWebDebugDetails');

      // add events for dealing with web debug toolbar

      // handle click on trigger to open/close debug panels
      YAHOO.util.Event.addListener('sfWebDebugTrigger', "click", function(e) { YAHOO.symfony.debug.toolbar.toggle(); YAHOO.util.Event.preventDefault(e); } );
      YAHOO.util.Event.addListener('sfWebDebugClose', "click", function (e) { YAHOO.symfony.debug.toolbar.hideMenu(); YAHOO.util.Event.preventDefault(e); } );

      YAHOO.util.Event.addListener('sfWebDebugConfigTrigger', "click", function(e) { YAHOO.symfony.debug.toolbar.toggleDetailMenu('sfWebDebugConfig'); YAHOO.util.Event.preventDefault(e); });
      YAHOO.util.Event.addListener('sfWebDebugToggleAllLogLinesTrigger', "click", function(e) { YAHOO.symfony.debug.toolbar.showDetailLevel('sfWebDebugLogLine'); YAHOO.util.Event.preventDefault(e); });
      YAHOO.util.Event.addListener('sfWebDebugToggleNoLogLinesTrigger', "click", function(e) { YAHOO.symfony.debug.toolbar.hideDetailLevel('sfWebDebugLogLine'); YAHOO.util.Event.preventDefault(e); });

      YAHOO.util.Dom.batch(YAHOO.util.Dom.getElementsByClassName('toggleDetailLevel', '*', YAHOO.util.Dom.get('sfWebDebugBar')), function(element) { YAHOO.util.Event.addListener(element, "click", function(e) { var i = element.href.indexOf('#'); if(i) { YAHOO.symfony.debug.toolbar.toggleDetailMenu(element.href.substring(i + 1)); YAHOO.util.Event.preventDefault(e); }})});
      YAHOO.util.Dom.batch(YAHOO.util.Dom.getElementsByClassName('toggleLogLevel', '*', YAHOO.util.Dom.get('sfWebDebug')), function(element) { YAHOO.util.Event.addListener(element, "click", function(e) { var i = element.href.indexOf('#'); if(i) { YAHOO.symfony.debug.toolbar.toggleLogLevel(element.href.substring(i + 1)); YAHOO.util.Event.preventDefault(e); }})});
      YAHOO.util.Dom.batch(YAHOO.util.Dom.getElementsByClassName('toggleUriElement'), function(element) { YAHOO.util.Event.addListener(element, "click", function(e) { var i = element.href.indexOf('#'); if(i) { YAHOO.symfony.debug.element.toggle(element.href.substring(i + 1)); YAHOO.util.Event.preventDefault(e); }})});

      // make toolbar visible by default if desired
      YAHOO.symfony.debug.toolbar.show();

      YAHOO.log("web debug toolbar initialized", 'info', 'symfony');
    },

    toggle: function()
    {
    (YAHOO.symfony.debug.toolbar.element.style.display != 'none') ? YAHOO.symfony.debug.toolbar.hide() : YAHOO.symfony.debug.toolbar.show();
    },

    show: function()
    {
      YAHOO.symfony.debug.toolbar.element.style.display = 'inline';

      YAHOO.symfony.debug.element.show('sfWebDebug');
      YAHOO.symfony.debug.element.hide('sfWebDebugLog');
      YAHOO.symfony.debug.element.hide('sfWebDebugConfig');
      YAHOO.symfony.debug.element.hide('sfWebDebugDatabaseDetails');
      YAHOO.symfony.debug.element.hide('sfWebDebugTimeDetails');
      YAHOO.symfony.debug.toolbar.showCache();
      // YAHOO.symfony.debug.element.show('sfWebDebugShowMenu');
      // YAHOO.symfony.debug.element.show('sfWebDebugDetails'); // don't make this a block element, needs to be inline
      // YAHOO.symfony.debug.element.show('sfWebDebugHideMenu');
    },

    hide: function()
    {
      YAHOO.symfony.debug.toolbar.element.style.display = 'none';

      YAHOO.symfony.debug.element.show('sfWebDebug');
      YAHOO.symfony.debug.element.hide('sfWebDebugLog');
      YAHOO.symfony.debug.element.hide('sfWebDebugConfig');
      YAHOO.symfony.debug.element.hide('sfWebDebugDatabaseDetails');
      YAHOO.symfony.debug.element.hide('sfWebDebugTimeDetails');
      YAHOO.symfony.debug.toolbar.hideCache();
      // YAHOO.symfony.debug.element.show('sfWebDebugShowMenu');
      // YAHOO.symfony.debug.element.show('sfWebDebugHideMenu');
      // YAHOO.symfony.debug.element.hide('sfWebDebugDetails');
    },

    hideMenu: function()
    {
      YAHOO.symfony.debug.toolbar.hide();
      YAHOO.symfony.debug.element.hide('sfWebDebug');
    },

    toggleDetailMenu: function(id)
    {
      if (id != 'sfWebDebugLog')
      {
        YAHOO.symfony.debug.element.hide('sfWebDebugLog');
      }

      if (id != 'sfWebDebugConfig')
      {
        YAHOO.symfony.debug.element.hide('sfWebDebugConfig');
      }

      if (id != 'sfWebDebugDatabaseDetails')
      {
        YAHOO.symfony.debug.element.hide('sfWebDebugDatabaseDetails');
      }

      if (id != 'sfWebDebugTimeDetails')
      {
        YAHOO.symfony.debug.element.hide('sfWebDebugTimeDetails');
      }

      YAHOO.symfony.debug.element.toggle(id);
    },

    toggleDetailLevel: function(className)
    {
      YAHOO.util.Dom.batch(YAHOO.util.Dom.getElementsByClassName(className, '*', YAHOO.util.Dom.get('sfWebDebug')), function(element)
      {
        if(element)
        {
          YAHOO.symfony.debug.element.toggle(element);
        }
      });
    },

    showDetailLevel: function(className)
    {
      YAHOO.util.Dom.batch(YAHOO.util.Dom.getElementsByClassName(className, '*', YAHOO.util.Dom.get('sfWebDebug')), function(element)
      {
        if(element)
        {
          YAHOO.symfony.debug.element.show(element);
        }
      });
    },

    hideDetailLevel: function(className)
    {
      YAHOO.util.Dom.batch(YAHOO.util.Dom.getElementsByClassName(className, '*', YAHOO.util.Dom.get('sfWebDebug')), function(element) { if(element) { YAHOO.symfony.debug.element.hide(element)  } });
    },

    toggleLogLevel: function(className)
    {
      YAHOO.util.Dom.batch(YAHOO.util.Dom.getElementsByClassName('sfWebDebugLogLine', 'tr', YAHOO.util.Dom.get('sfWebDebugLogLines')), function(element)
      {
        if(YAHOO.util.Dom.hasClass(element, className))
        {
          element.style.display = '';
        }
        else
        {
          element.style.display = 'none';
        }
      });
    },

    showCache: function()
    {
      YAHOO.util.Dom.batch(YAHOO.util.Dom.getElementsByClassName('sfWebDebugCache', 'div', document.body), function(element) { if(typeof element == 'object') { element.style.display = 'inline'; } });
      YAHOO.util.Dom.batch(YAHOO.util.Dom.getElementsByClassName('sfWebDebugActionCache', 'div', document.body), function(element) { if(typeof element == 'object') { element.style.border = '1px solid #F00'; } });
    },

    hideCache: function()
    {
      YAHOO.util.Dom.batch(YAHOO.util.Dom.getElementsByClassName('sfWebDebugCache', 'div', document.body), function(element) { if(typeof element == 'object') { element.style.display = 'none'; } });
      YAHOO.util.Dom.batch(YAHOO.util.Dom.getElementsByClassName('sfWebDebugActionCache', 'div', document.body), function(element) { if(typeof element == 'object') { element.style.border = 'none'; } });
    }
  }
};

YAHOO.symfony.event.ready.subscribe(YAHOO.symfony.debug.initialize);
