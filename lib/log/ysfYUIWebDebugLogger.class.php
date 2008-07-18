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
 * ysfYUIWebDebugLogger logs messages into the yui powered web debug toolbar.
 *
 * @package    ysymfony
 * @subpackage log
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Fabien Potencier <dustin.whittle@symfony-project.com>
 * @version    SVN: $Id: ysfYUIWebDebugLogger.class.php 2 2008-04-28 06:07:19Z dwhittle $
 */
class ysfYUIWebDebugLogger extends sfWebDebugLogger
{
  protected
    $context       = null,
    $dispatcher    = null,
    $webDebug      = null,
    $xdebugLogging = true;

  /**
   * Initializes this logger.
   *
   * @param  sfEventDispatcher A sfEventDispatcher instance
   * @param  array        An array of options.
   *
   * @return Boolean      true, if initialization completes successfully, otherwise false.
   */
  public function initialize(sfEventDispatcher $dispatcher, $options = array())
  {
    parent::initialize($dispatcher, $options);

    // register Y! + symfony components
    ysfYUI::addComponents('logger', 'animation', 'connection', 'element', 'debug', 'symfony');
  }

  /**
   * Listens to the response.filter_content event.
   *
   * @param  sfEvent The sfEvent instance
   * @param  string  The response content
   *
   * @return string  The filtered response content
   */
  public function filterResponseContent(sfEvent $event, $content)
  {
    if (!sfConfig::get('sf_web_debug'))
    {
      return $content;
    }

    /*
    // log timers information
    $messages = array();
    foreach (sfTimerManager::getTimers() as $name => $timer)
    {
      $messages[] = sprintf('%s %.2f ms (%d)', $name, $timer->getElapsedTime() * 1000, $timer->getCalls());
    }
    $this->dispatcher->notify(new sfEvent($this, 'application.log', $messages));
    */

    // don't add debug toolbar:
    // * for XHR requests
    // * if 304
    // * if not rendering to the client
    // * if HTTP headers only
    $response = $event->getSubject();
    if (!$this->context->has('request') || !$this->context->has('response') || !$this->context->has('controller') ||
      $this->context->getRequest()->isXmlHttpRequest() ||
      strpos($response->getContentType(), 'html') === false ||
      $response->getStatusCode() == 304 ||
      $this->context->getController()->getRenderMode() != sfView::RENDER_CLIENT ||
      $response->isHeaderOnly()
    )
    {
      return $content;
    }

    if(false !== ($pos = strpos($content, '</head>')))
    {
      sfLoader::loadHelpers(array('Tag', 'Asset'));

      $html = '';
      if (sfConfig::get('symfony.asset.stylesheets_included', false) === false)
      {
        $stylesheets = ysfYUI::getStylesheets(true);
        foreach($stylesheets as $stylesheet)
        {
          if(!strpos($content, $stylesheet))
          {
           $html .= stylesheet_tag($stylesheet);
          }
        }
      }

      if (sfConfig::get('symfony.asset.javascripts_included', false) === false)
      {
        $javascripts  = ysfYUI::getJavascripts(true);
        foreach($javascripts as $javascript)
        {
          if(!strpos($content, $javascript))
          {
            $html .= javascript_include_tag($javascript);
          }
        }
      }

      if ($html)
      {
        sfConfig::set('symfony.asset.javascripts_included', true);
        sfConfig::set('symfony.asset.stylesheets_included', true);
        $content = substr($content, 0, $pos).$html.substr($content, $pos);
      }
    }

    // add web debug information to content
    $webDebugContent = $this->webDebug->getResults();
    $count = 0;
    $content = str_ireplace('</body>', $webDebugContent.'</body>', $content, $count);
    if (!$count)
    {
      $content .= $webDebugContent;
    }

    return $content;
  }
}
