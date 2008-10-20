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
 * ysfYUI adds the components javascript libraries to the current response
 * and takes care of not to include them twice.
 *
 * @package    ysymfony
 * @subpackage yui
 * @author     Dustin Whittle <dustin.whittle@symfony-project.com>
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Nick Winfield <enquiries@superhaggis.com>
 * @author     Pierre Minnieur <pm@pierre-minnieur.de>
 * @version    SVN: $Id: ysfYUI.class.php 2 2008-04-28 06:07:19Z dwhittle $
 *
 * @link http://trac.symfony-project.org/wiki/ysfYUIPlugin
 * @link http://trac.symfony-project.org/browser/plugins/ysfYUIPlugin
 * @link http://developer.yahoo.com/yui/
 */
class ysfYUI
{

  const YUI_VERSION   = '2.5.2';
  const YUI_CDN       = 'http://yui.yahooapis.com/';

  private static $skin = 'sam';

  /**
   * A list of already included components to prevent including the same component twice.
   *
   * @var array
   */
  private static $loadedComponents = array();

  /**
   * A list of optimized rollup components
   *
   * @var array
   */
  private static $optimizedComponents = array();

  /**
   * Javascript components available with their properties:
   *
   * type (yui, ojay, symfony)
   * skinnable (has related css)
   * status (alpha, beta, stable)
   * dependencies (other components)
   *
   * @var array
   */
  private static $javascriptComponents = array('animation'    => array('type'         => 'yui',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('dom', 'event')),

                                               'autocomplete' => array('type'         => 'yui',
                                                                       'skinnable'    => true,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('dom', 'event', 'connection', 'animation')),

                                               'button'       => array('type'         => 'yui',
                                                                       'skinnable'    => true,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('element', 'container', 'menu')),

                                               'calendar'     => array('type'         => 'yui',
                                                                       'skinnable'    => true,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('dom', 'event')),

                                               'charts'       => array('type'         => 'yui',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'experimental',
                                                                       'dependencies' => array('event', 'dom')),

						                                   'carousel'     => array('type'         => 'yui',
                                                                       'skinnable'    => true,
                                                                       'status'       => 'beta',
                                                                       'dependencies' => array('animation', 'dom', 'event')),

                                               'colorpicker'  => array('type'         => 'yui',
                                                                       'skinnable'    => true,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('animation', 'slider', 'element')),

                                               'connection'   => array('type'         => 'yui',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('event')),

                                               'container'    => array('type'         => 'yui',
                                                                       'skinnable'    => true,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('animation', 'dragdrop')),

                                               'cookie'       => array('type'         => 'yui',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('event', 'dom')),

                                               'datasource'   => array('type'         => 'yui',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('event', 'connection')),

                                               'datatable'    => array('type'         => 'yui',
                                                                       'skinnable'    => true,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('element', 'datasource', 'calendar', 'dragdrop', 'connection')),

                                               'dom'          => array('type'         => 'yui',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('yahoo')),

                                               'dragdrop'     => array('type'         => 'yui',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('dom', 'event')),

                                               'editor'       => array('type'         => 'yui',
                                                                       'skinnable'    => true,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('container', 'menu', 'element', 'button', 'animation', 'dragdrop')),

                                               'element'      => array('type'         => 'yui',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'beta',
                                                                       'dependencies' => array('dom', 'event')),

                                               'event'        => array('type'         => 'yui',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('yahoo')),

                                               'get'          => array('type'         => 'yui',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('event', 'dom')),

                                               'history'      => array('type'         => 'yui',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('event')),

                                               'imageloader'  => array('type'         => 'yui',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('event', 'dom')),

                                               'imagecropper' => array('type'         => 'yui',
                                                                       'skinnable'    => true,
                                                                       'status'       => 'beta',
                                                                       'dependencies' => array('event', 'dom')),

                                               'json'         => array('type'         => 'yui',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('event', 'dom')),

                                               'layout'       => array('type'         => 'yui',
                                                                       'skinnable'    => true,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('event', 'dom', 'animation', 'dragdrop', 'element', 'resize')),

                                               'logger'       => array('type'         => 'yui',
                                                                       'skinnable'    => true,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('event', 'dom', 'dragdrop')),

                                               'menu'         => array('type'         => 'yui',
                                                                       'skinnable'    => true,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('container')),

																						 	 'paginator'    => array('type'        => 'yui',
																								                       'skinnable'    => true,
																								                       'status'       => 'stable',
																								                       'dependencies' => array('animation', 'dom', 'event')),

                                               'profiler'     => array('type'         => 'yui',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('event', 'dom')),

                                               'profilerviewer' => array('type'       => 'yui',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'beta',
                                                                       'dependencies' => array('profiler')),

                                               'resize'       => array('type'         => 'yui',
                                                                       'skinnable'    => true,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('dom', 'event', 'animation')),

                                               'selector'     => array('type'         => 'yui',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'beta',
                                                                       'dependencies' => array('dom')),

                                               'slider'       => array('type'         => 'yui',
                                                                       'skinnable'    => true,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('dragdrop', 'animation')),

                                               'tabview'      => array('type'         => 'yui',
                                                                       'skinnable'    => true,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('element', 'connection')),

                                               'treeview'     => array('type'         => 'yui',
                                                                       'skinnable'    => true,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('event')),

                                               'uploader'     => array('type'         => 'yui',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'experimental',
                                                                       'dependencies' => array('dom')),

                                               'yahoo'        => array('type'         => 'yui',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array()),

                                               'yuiloader'    => array('type'         => 'yui',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array()),

                                               'yuitest'      => array('type'         => 'yui',
                                                                       'skinnable'    => true,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('logger')),

                                               'symfony'      => array('type'         => 'symfony',
                                                                       'skinnable'    => true,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('yahoo', 'dom', 'event')),

                                               'debug'        => array('type'         => 'symfony',
                                                                       'skinnable'    => true,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('logger', 'symfony')),

                                               'forms'        => array('type'         => 'symfony',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('symfony')),

                                               'controls'     => array('type'         => 'symfony',
                                                                       'skinnable'    => false,
                                                                       'status'       => 'stable',
                                                                       'dependencies' => array('symfony'))
                                              );


  /**
   * Javascript optimizations (rollup/aggregated files)
   *
   * @todo make by type (yui, ojay, symfony)
   *
   * @var array
   */
  private static $javascriptOptimizations = array('yahoo-dom-event'     => array('yahoo', 'dom', 'event'),
                                                  'yuiloader-dom-event' => array('yuiloader', 'dom', 'event'),
                                                  'utilities'           => array('yahoo', 'dom', 'event', 'element', 'connection', 'dragdrop', 'animation')
                                                 );


  /**
   * Stylesheet components
   *
   * @var array
   */
  private static $stylesheetComponents = array('reset' => array('dependencies' => array()),
                                               'fonts' => array('dependencies' => array('reset')),
                                               'grids' => array('dependencies' => array('reset', 'fonts')),
                                               'base'  => array('dependencies' => array('reset', 'fonts', 'grids'))
                                              );

  /**
   * Stylesheet optimizations (rollup/aggregated files)
   *
   * @todo make by type (yui, ojay, symfony)
   *
   * @var array
   */
  private static $stylesheetOptimizations = array('reset-fonts-grids' => array('reset', 'fonts', 'grids'));

  /**
   * Javascripts for dependencies for events
   *
   * @var array
   */
  private static $javascripts = null;

  /**
   * Stylesheets for dependencies for events
   *
   * @var array
   */
  private static $stylesheets = null;

  /**
   * Events
   *
   * @var array
   */
  private static $events = array();

  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   *
   * @return void
   */
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $sf_routing = $event->getSubject();

    $sf_routing->prependRoute('yui_actions', '/ysf/:action', array('module' => 'sfYUI'));
    $sf_routing->prependRoute('yui_actions_with_params', '/ysf/:action/*', array('module' => 'sfYUI'));
  }

  /**
   * Adds a component to the list of included javascripts.
   * Adds a component stylesheet to the list of included stylesheets.
   *
   * @param string $name The Yahoo! UI components name to include.
   *
   * @return void
   */
  public static function addComponent($component, $dependency = false)
  {
    $component = strtolower($component);

    // check if the component is already included and is valid
    if(sfConfig::get('yui_loader_enabled', true) && !in_array($component, self::$loadedComponents))
    {
      if($dependency)
      {
        $callingComponent = array_pop(self::$loadedComponents);
      }

      // add component to the included list
      array_push(self::$loadedComponents, $component);

      if($dependency)
      {
        array_push(self::$loadedComponents, $callingComponent);
      }

      if(array_key_exists($component, self::$javascriptComponents))
      {
        foreach(array_diff(self::$javascriptComponents[$component]['dependencies'], self::$loadedComponents) as $dependency)
        {
          self::addComponent($dependency, true); // call component dependencies recursively
        }
      }
      elseif(array_key_exists($component, self::$stylesheetComponents))
      {
        foreach(array_diff(self::$stylesheetComponents[$component]['dependencies'], self::$loadedComponents) as $dependency)
        {
          self::addComponent($dependency, true); // call component dependencies recursively
        }
      }
    }

    return true;
  }

  /**
   * Takes a list of components as arguments.
   */
  public static function addComponents()
  {
    if(sfConfig::get('yui_loader_enabled', true))
    {
      foreach(func_get_args() as $component)
      {
        self::addComponent($component);
      }
    }

    return true;
  }

  /**
   * Returns the currently loaded components.
   *
   * @return array The components that are to be loaded
   */
  public static function getLoadedComponents()
  {
    return array_unique(self::$loadedComponents);
  }

  /**
   * Returns all registered components (css + js).
   *
   * @return array The registered components
   */
  public static function getComponents()
  {
    return array_unique(array_merge(self::$javascriptComponents, self::$stylesheetComponents));
  }

  /**
   * Returns the javascripts for loaded components.
   *
   * @param boolean $optimize Return optimized components (aggregated) or expanded
   *
   * @return array The javascripts to be loaded
   */
  public static function getJavascripts($optimize = false)
  {

    self::$javascripts = array();

    if(sfConfig::get('yui_loader_enabled', true))
    {
      // yui
      $yui_lib_web_dir = sfConfig::get('yui_lib_web_dir');
      $yui_suffix = sfConfig::get('yui_suffix');

      $yui_sf_web_dir = sfConfig::get('yui_sf_web_dir');

      if(sfConfig::get('yui_loader_optimize', true) && $optimize)
      {
        foreach(self::$javascriptOptimizations as $javascript => $components)
        {
          $matches = 0;
          $count = count($components);

          foreach($components as $component)
          {
            if(in_array($component, self::$loadedComponents))
            {
              $matches++;
            }
          }

          if($matches == $count)
          {
            // handle combohandler: <script type="text/javascript" src="http://yui.yahooapis.com/combo?2.5.2/build/yahoo-dom-event/yahoo-dom-event.js&2.5.2/build/container/container_core-min.js"></script>
            $optimizedJavascript = sprintf('%s/%s/%s.js', $yui_lib_web_dir, $javascript, $javascript);
            self::$optimizedComponents = array_merge(self::$optimizedComponents, $components);
          }
        }

        if(isset($optimizedJavascript)) // take last optimization as it is most efficient
        {
          array_push(self::$javascripts, $optimizedJavascript);
        }
      }

      foreach(self::$loadedComponents as $component)
      {
        if(!in_array($component, self::$optimizedComponents) && array_key_exists($component, self::$javascriptComponents))
        {
          if(self::$javascriptComponents[$component]['type'] == 'yui')
          {
            $componentsName = (self::$javascriptComponents[$component]['status'] == 'stable') ? $component : $component.'-'.self::$javascriptComponents[$component]['status'];
            array_push(self::$javascripts, sprintf('%s/%s/%s%s.js', $yui_lib_web_dir, $component, $componentsName, $yui_suffix));
          }
          elseif(self::$javascriptComponents[$component]['type'] == 'symfony')
          {
            array_push(self::$javascripts, sprintf('%s/js/%s.js', $yui_sf_web_dir, $component));
          }
        }
      }
    }

    return self::$javascripts;
  }

  /**
   * Returns the stylesheets for loaded components.
   *
   * @param boolean $optimize Return optimized components (aggregated) or expanded
   *
   * @return array The stylesheets to be loaded
   */
  public static function getStylesheets($optimize = false)
  {

    self::$stylesheets = array();

    if(sfConfig::get('yui_loader_enabled', true))
    {
      // yui
      $yui_lib_web_dir = sfConfig::get('yui_lib_web_dir');
      $yui_suffix = sfConfig::get('yui_suffix');

      $yui_sf_web_dir = sfConfig::get('yui_sf_web_dir');

      if(sfConfig::get('yui_loader_optimize', true) && $optimize)
      {
        foreach(self::$stylesheetOptimizations as $stylesheet => $components)
        {
          $matches = 0;
          $count = count($components);

          foreach($components as $component)
          {
            if(in_array($component, self::$loadedComponents))
            {
              $matches++;
            }
          }

          if($matches == $count)
          {
            $optimizedStylesheet = sprintf('%s/%s/%s.css', $yui_lib_web_dir, $stylesheet, $stylesheet);
            self::$optimizedComponents = array_merge(self::$optimizedComponents, $components);
          }
        }

        if(isset($optimizedStylesheet)) // take last optimization as it is most efficient
        {
          array_push(self::$stylesheets, $optimizedStylesheet);
        }
      }

      foreach(self::$loadedComponents as $component)
      {
        if(!in_array($component, self::$optimizedComponents))
        {
          if((array_key_exists($component, self::$javascriptComponents) === true) && (self::$javascriptComponents[$component]['skinnable'] === true))
          {
            if(self::$javascriptComponents[$component]['type'] == 'yui' && $optimize === false)
            {
              array_push(self::$stylesheets, sprintf('%s/%s/assets/skins/%s/%s.css', $yui_lib_web_dir, $component, ysfYUI::getSkin(), $component));
            }
            else if(self::$javascriptComponents[$component]['type'] == 'symfony')
            {
              array_push(self::$stylesheets, sprintf('%s/css/%s.css', $yui_sf_web_dir, $component));
            }
          }
          elseif(array_key_exists($component, self::$stylesheetComponents))
          {
            array_push(self::$stylesheets, sprintf('%s/%s/%s.css', $yui_lib_web_dir, $component, $component, $yui_suffix));
          }
        }
      }

      if($optimize === true)
      {
        array_push(self::$stylesheets, sprintf('%s/assets/skins/%s/skin.css', $yui_lib_web_dir, ysfYUI::getSkin()));
      }
    }

    return self::$stylesheets;
  }

  /**
   * Sets the current skin.
   *
   * @param string $skin The skin to set
   *
   * @return boolean If skin was set (in allowed list)
   */
  public static function setSkin($skin)
  {
    if(in_array($skin, sfConfig::get('yui_allowed_skins', array('sam'))))
    {
      self::$skin = $skin;

      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * Returns the current skin.
   *
   * @return string The current skin
   */
  public static function getSkin()
  {
    return (empty(self::$skin) === true) ? sfConfig::get('yui_default_skin') : self::$skin;
  }

  /**
   * Adds a javascript event.
   *
   * @param string $id The dom element id (window, document, id)
   * @param string $handler The dom event to listen to (ready, available, click, blur, submit)
   * @param string $event The event (javascript)
   *
   * @link http://developer.yahoo.com/yui/event/
   */
  public static function addEvent($id, $handler, $event)
  {
    self::$events[$id][$handler][] = trim($event);
  }

  /**
   * Returns all javascript events.
   *
   * @param boolean $remove To remove all events after returning
   *
   * @return string The javascript events
   */
  public static function getEvents($remove = false)
  {
    self::addComponent('event');

    $script = '';
    if (is_array(self::$events))
    {
      foreach(self::$events as $id => $events)
      {
        $domElement = ($id == 'window' || $id == 'document') ? $id : "'{$id}'";
        foreach($events as $handler => $eventStack)
        {
          if($domElement == 'document' && $handler == 'ready')
          {
            $script .= "YAHOO.util.Event.onDOMReady(function() { " . join(' ', $eventStack) . " });\n";
          }
          elseif($handler == 'ready' || $handler == 'available')
          {
            $handler = ($handler == 'ready') ? 'onContentReady' : 'onAvailable';
            $script .= "YAHOO.util.Event.".$handler."({$domElement}, function() { " . join(' ', $eventStack) . "});\n";
          }
          else
          {
            $script .= "YAHOO.util.Event.addListener({$domElement}, '{$handler}', function(e) { " . join(' ', $eventStack) . "});\n";
          }
        }
      }

      if($remove === true)
      {
        self::$events = array();
      }
    }

    return $script;
  }



  /**
   * Prepares a YAHOO.util.Anim call which can be called via
   * an event handler or callback.
   *
   *   Possible 'Easing' values for $effect:
   *
   *   'backBoth'        => Backtracks slightly, then reverses direction, overshoots end, then reverses and comes back to end.
   *   'backIn'          => Backtracks slightly, then reverses direction and moves to end.
   *   'backOut'         => Overshoots end, then reverses and comes back to end.
   *   'bounceBoth'      => Bounces off start and end.
   *   'bounceIn'        => Bounce off of start.
   *   'bounceOut'       => Bounces off end.
   *   'easeBoth'        => Begins slowly and decelerates towards end. (quadratic)
   *   'easeBothStrong'  => Begins slowly and decelerates towards end. (quartic)
   *   'easeIn'          => Begins slowly and accelerates towards end. (quadratic)
   *   'easeInStrong'    => Begins slowly and accelerates towards end. (quartic)
   *   'easeNone'        => Uniform speed between points.
   *   'easeOut'         => Begins quickly and decelerates towards end. (quadratic) - default effect.
   *   'easeOutStrong'   => Begins quickly and decelerates towards end. (quartic)
   *   'elasticBoth'     => Snap both elastic effect.
   *   'elasticIn'       => Snap in elastic effect.
   *   'elasticOut'      => Snap out elastic effect.
   *
   *   Possible indexes for $options:
   *
   *   'from_width'      => The width that the element should start at.
   *   'from_height'     => The height that the element should start at.
   *   'unit_width'      => Defaults to pixels (px) - unit of measurement for the specified width values.
   *   'unit_height'     => Defaults to pixels (px) - unit of measurement for the specified height values.
   *   'duration'        => The duration of the animation (in seconds; defaults to 1)
   *   'opacity_from'    => The opacity that the element should start at.
   *   'opacity_to'      => The opacity that the element should finish at.
   *   'fontsize_from'   => The size that the element's font should start at.
   *   'fontsize_to'     => The size that the element's font should
   *

    finish at.
   *   'fontsize_unit'   => The unit of measurement that the font should
   *

    change by. (defaults to %)
   *
   *   Choose either of these width-specific options:
   *   'to_width'        => The width that the element should finish at.
   *   'by_width'        => The width that the element should change by.
   *
   *   Choose either of these height-specific options:
   *   'to_height'       => The height that the element should finish at.
   *   'by_height'       => The height that the element should change by.
   *
   * Example usage:
   *
   *   100x50px black box that expands to 400x200px after 2 seconds using
   *   the 'elasticOut' animation effect.  Opacity also changes from 100%
   *   to 25% and font size changes from 100% to 250%.
   *
   *   ...
   *   <div id="foo" style="color: #FFFFFF; background-color: #000000; height: 50px; width: 100px">hello, world!</div>
   *   <?php echo link_to('click me!', '#', array(
   *     'onclick' => ysfYUI::animation('elasticOut', 'foo', array(
   *       'from_height' => '50',
   *       'from_width' => '100',
   *       'to_height' => '200',
   *       'to_width' => '400',
   *       'opacity_from' => '1',
   *       'opacity_to' => '0.25',
   *       'fontsize_from' => '100',
   *       'fontsize_to' => '250',
   *       'fontsize_unit' => '%',
   *       'duration' => '2',
   *     )),
   *   )) ?>
   *
   * @param string $effect The animation
   * @param string $element The dom element id
   * @param array $options The options for animation object
   *
   * @return string The javascript for animation
   *
   * @link http://developer.yahoo.com/yui/animation/
   */
  public static function animation($effect, $element, $options = array())
  {

    self::addComponent('animation');

    $js  = "";

    $js .= "var " . $element . "_anim = new YAHOO.util.Anim('".$element."', { ";

    $dimensions = array();

    if(isset($options['from_width']) || isset($options['to_width']) || isset($options['by_width']))
    {
      $width  = "";
      $width .= "width: { ";

      $width_attributes = array();

      if(isset($options['from_width']))
      {
        $from_width = "from: " . $options['from_width'];
        $width_attributes[] = $from_width;
      }

      if(isset($options['to_width']))
      {
        $to_width = "to: " . $options['to_width'];
        $width_attributes[] = $to_width;
      }
      elseif(isset($options['by_width']))
      {
        $by_width = "by: " . $options['by_width'];
        $width_attributes[] = $by_width;
      }

      if(isset($options['unit_width']))
      {
        $unit_width = "unit: '" . $options['unit_width'];
        $width_attributes[] = $unit_width;
      }

      $width .= join(', ', $width_attributes);

      $width .= " }";

      $dimensions[] = $width;
    }

    if(isset($options['from_height']) || isset($options['to_height']) || isset($options['by_height']))
    {
      $height  = "";
      $height .= "height: { ";

      $height_attributes = array();

      if(isset($options['from_height']))
      {
        $from_height = "from: " . $options['from_height'];
        $height_attributes[] = $from_height;
      }

      if(isset($options['to_height']))
      {
        $to_height = "to: " . $options['to_height'];
        $height_attributes[] = $to_height;
      }
      elseif(isset($options['by_height']))
      {
        $by_height = "by: " . $options['by_height'];
        $height_attributes[] = $by_height;
      }

      if(isset($options['unit_height']))
      {
        $unit_height = "unit: '" . $options['unit_height'];
        $height_attributes[] = $unit_height;
      }

      $height .= join(', ', $height_attributes);

      $height .= " }";

      $dimensions[] = $height;
    }

    if(isset($options['opacity_from']) || isset($options['opacity_to']))
    {
      $opacity  = "";
      $opacity .= "opacity: { ";

      $opacity_attributes = array();

      if(isset($options['opacity_from']))
      {
        $opacity_from = "from: " . $options['opacity_from'];
        $opacity_attributes[] = $opacity_from;
      }

      if(isset($options['opacity_to']))
      {
        $opacity_to = "to: " . $options['opacity_to'];
        $opacity_attributes[] = $opacity_to;
      }

      $opacity .= join(', ', $opacity_attributes);

      $opacity .= " }";

      $dimensions[] = $opacity;
    }

    if(isset($options['fontsize_from']) || isset($options['fontsize_to']))
    {
      $fontsize  = "";
      $fontsize .= "fontSize: { ";

      $fontsize_attributes = array();

      if(isset($options['fontsize_from']))
      {
        $fontsize_from = "from: " . $options['fontsize_from'];
        $fontsize_attributes[] = $fontsize_from;
      }

      if(isset($options['fontsize_to']))
      {
        $fontsize_to = "to: " . $options['fontsize_to'];
        $fontsize_attributes[] = $fontsize_to;
      }

      if(isset($options['fontsize_unit']))
      {
        $fontsize_unit = "unit: " . $options['fontsize_unit'];
        $fontsize_attributes[] = $fontsize_unit;
      }
      else
      {
        $fontsize_unit = "unit: '%'";
        $fontsize_attributes[] = $fontsize_unit;
      }

      $fontsize .= join(', ', $fontsize_attributes);

      $fontsize .= " }";

      $dimensions[] = $fontsize;
    }

    $js .= join(', ', $dimensions);

    $js .= " }, ";
    $js .= (isset($options['duration'])) ? $options['duration'] : 1;
    $js .= ", ";

    $js .= "YAHOO.util.Easing." . $effect ."); " . $element . "_anim.animate();";

    return $js;
  }

  /**
   * Prepares a YAHOO.util.Connect.asyncRequest call which can be called via
   * an event handler or callback.
   *
   * Possible callback cases:
   *
   *   - success
   *   - failure
   *   - scope
   *   - upload
   *   - argument
   *
   * Example usage:
   *
   *   ...
   *   <script language="javascript" type="text/javascript">
   *     var transaction = <?php echo ysfYUI::connection('GET', 'default/index', array(
   *       'success' => "function(o) { alert(o.responseText) }",
   *       'failure' => "myOwnFailureFunction(o)",
   *     )); ?>
   *   </script>
   *
   * @param string $method HTTP transaction method
   * @param string $uri Fully qualified path of resource
   * @param array $callbacks User-defined callback function
   * @param string $postData Optional POST body
   *
   * @return string The javascript for connection
   *
   * @link http://developer.yahoo.com/yui/connection/
   */
  public static function connection($method = 'POST', $uri, $callbacks = array(), $postData = null)
  {

    self::addComponent('connection');

    $allowedCallbacks = array('success' => '', 'failure' => '', 'argument' => '', 'scope' => '', 'upload' => '');

    // is $uri a route?
    if(substr($uri, 0, 1) == '@')
    {
      $uri = url_for($uri);
    }

    // extract post data
    if($method == 'POST' && !$postData and strstr($uri, '?'))
    {
      $postData = substr($uri, strpos($uri, '?') + 1);
      $uri      = substr($uri, 0, strpos($uri, '?'));
    }

    $js = '';

    $js .= "var connection = YAHOO.util.Connect; ";
    $js .= "connection.initHeader('X_REQUESTED_WITH', 'XMLHttpRequest'); ";
    $js .= "connection.asyncRequest('" . $method . "', '" . $uri . "', ";

    // callbacks
    $callbackCount = count($callbacks);

    $i = 0;
    $js .= "{ ";
    foreach($callbacks as $callback => $value )
    {
      // valid callback?
      if(isset($allowedCallbacks[$callback]))
      {
        $js .= $callback . ": ";
        $js .= $value;

        // this maybe has to be completed
        switch ($callback)
        {

          case 'success': break;


          case 'failure': break;


          case 'scope': break;


          case 'upload':

            $js .= $allowedCallbacks[$callback];

            break;


          case 'argument':

            if(is_array($allowedCallbacks[$callback]))

            {

              $js .= "[ ";

              foreach($allowedCallbacks[$callback] as $argument)

              {

                $js .= $argument . ", ";

              }

              $js .= " ]";

            }

            break;
        }

        $js .= ($i < ($callbackCount - 1)) ? ', ' : '';
        $i++;
      }
    }
    $js .= " }";

    // append post data
    $js .= ($postData) ? ", '" . $postData . "'" : ", ''";

    $js .= ");";

    return $js;
  }

}
