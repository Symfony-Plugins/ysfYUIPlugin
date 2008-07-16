<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * ysfYUIWebDebug creates debug information for easy debugging in the browser.
 *
 * @package    symfony
 * @subpackage debug
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Dustin Whittle <dustin.whittle@symfony-project.com>
 * @version    SVN: $Id: ysfYUIWebDebug.class.php 2 2008-04-28 06:07:19Z dwhittle $
 */
class ysfYUIWebDebug extends sfWebDebug
{
  /**
   * Returns the web debug toolbar as HTML.
   *
   * @return string The web debug toolbar HTML
   */
  public function getResults()
  {
    if (!sfConfig::get('sf_web_debug'))
    {
      return '';
    }

    ysfYUI::addComponent('debug');

    sfLoader::loadHelpers(array('Helper', 'Url', 'Asset', 'Tag'));

    $result = '';

    // max priority
    $maxPriority = '';
    if (sfConfig::get('sf_logging_enabled'))
    {
      $maxPriority = $this->getPriority($this->maxPriority);
    }

    $logs = '';
    $sqlLogs = array();
    if (sfConfig::get('sf_logging_enabled'))
    {
      $logs = '<table class="sfWebDebugLogs">
        <tr>
          <th class="sfWebDebugLogNumberHeader">#</th>
          <th class="sfWebDebugLogTypeHeader">type</th>
          <th class="sfWebDebugLogMessageHeader">message</th>
        </tr>'."\n";
      $line_nb = 0;
      foreach ($this->log as $logEntry)
      {
        $log = $logEntry['message'];

        $priority = $this->getPriority($logEntry['priority']);

        if (strpos($type = $logEntry['type'], 'sf') === 0)
        {
          $type = substr($type, 2);
        }

        // xdebug information
        $debug_info = '';
        if ($logEntry['debugStack'])
        {
          $debug_info .= '&nbsp;<a href="#debug_'.$line_nb.'" class="toggleUriElement">'.image_tag(sfConfig::get('yui_sf_web_dir').'/images/toggle.gif').'</a><div class="sfWebDebugDebugInfo" id="debug_'.$line_nb.'">';
          foreach ($logEntry['debugStack'] as $i => $logLine)
          {
            $debug_info .= '#'.$i.' &raquo; '.$this->formatLogLine($logLine).'<br/>';
          }
          $debug_info .= "</div>\n";
        }

        // format log
        $log = $this->formatLogLine($log);

        if(sfConfig::get('sf_orm') == 'propel')
        {
          // sql queries log
          if (preg_match('/\b(prepare|query|exec):/', $log, $match))
          {
            list($type, $log) = explode(': ', $log);

            $sqlLogs[] .= $log;
          }
        }

        ++$line_nb;
        $logs .= sprintf('<tr class="sfWebDebugLogLine sfWebDebug%s %s"><td class="sfWebDebugLogNumber">%s</td><td class="sfWebDebugLogType">%s&nbsp;%s</td><td class="sfWebDebugLogMessage">%s%s</td></tr>'."\n",
          ucfirst($priority),
          $logEntry['type'],
          $line_nb,
          image_tag(sfConfig::get('yui_sf_web_dir').'/images/'.$priority.'.png', array('alt' => ucfirst($priority))),
          $type,
          $log,
          $debug_info
        );
      }
      $logs .= '</table>';

      ksort($this->types);
      $types = array();
      foreach ($this->types as $type => $nb)
      {
        $types[] = '<a href="#'.$type.'" class="sfWebDebugTypeDetail toggleLogLevel">'.$type.'</a>';
      }
    }

    // ignore cache link
    $cacheLink = '';
    if (sfConfig::get('sf_debug') && sfConfig::get('sf_cache'))
    {
      $selfUrl = $_SERVER['REQUEST_URI'].((strpos($_SERVER['REQUEST_URI'], '_sf_ignore_cache') === false) ? '?_sf_ignore_cache=1' : '');
      $cacheLink = '<li><a href="'.$selfUrl.'" title="reload &amp; ignore cache">'.image_tag(sfConfig::get('yui_sf_web_dir').'/images/reload.png', array('alt' => 'reload &amp; ignore cache')).'</a></li>';
    }

    // logging information
    $logLink = '';
    if (sfConfig::get('sf_logging_enabled'))
    {
      $logLink = '<li><a href="#sfWebDebugLog" class="toggleDetailLevel">'.image_tag(sfConfig::get('yui_sf_web_dir').'/images/comment.png', array('alt' => 'logs &amp; messages')).' logs &amp; msgs</a></li>';
    }

    // database information
    $dbInfo = '';
    $dbInfoDetails = '';
    if ($sqlLogs)
    {
      $dbInfo = '<li><a href="#sfWebDebugDatabaseDetails" class="toggleDetailLevel">'.image_tag(sfConfig::get('yui_sf_web_dir').'/images/database.png', array('alt' => 'SQL Log')).' '.count($sqlLogs).'</a></li>';

      $dbInfoDetails = '
        <div id="sfWebDebugDatabaseLogs">
        <ol><li>'.implode("</li>\n<li>", $sqlLogs).'</li></ol>
        </div>
      ';
    }

    // memory used
    $memoryInfo = '';
    if (sfConfig::get('sf_debug') && function_exists('memory_get_usage'))
    {
      $totalMemory = sprintf('%.1f', (memory_get_usage() / 1024));
      $memoryInfo = '<li>'.image_tag(sfConfig::get('yui_sf_web_dir').'/images/memory.png').' '.$totalMemory.' KB</li>';
    }

    // total time elapsed
    $timeInfo = '';
    if (sfConfig::get('sf_debug'))
    {
      $totalTime = (microtime(true) - sfConfig::get('sf_timer_start')) * 1000;
      $totalTime = sprintf(($totalTime <= 1) ? '%.2f' : '%.0f', $totalTime);
      $timeInfo = '<li class="last"><a href="#sfWebDebugTimeDetails" class="toggleDetailLevel">'.image_tag(sfConfig::get('yui_sf_web_dir').'/images/time.png', array('alt' => 'Timer')).' '.$totalTime.' ms</a></li>';
    }

    // timers
    $timeInfoDetails = '<table class="sfWebDebugLogs"><tr><th>type</th><th>calls</th><th>time (ms)</th><th>time (%)</th></tr>';
    foreach (sfTimerManager::getTimers() as $name => $timer)
    {
      $timeInfoDetails .= sprintf('<tr><td class="sfWebDebugLogType">%s</td><td class="sfWebDebugLogNumber">%d</td><td>%.2fms</td><td>%d</td></tr>', $name, $timer->getCalls(), $timer->getElapsedTime() * 1000, $timer->getElapsedTime() * 1000 * 100 / $totalTime);
    }
    $timeInfoDetails .= '</table>';

    // logs
    $logInfo = '';
    if (sfConfig::get('sf_logging_enabled'))
    {
      $logInfo .= '
        <ul id="sfWebDebugLogMenu">
          <li><a href="#sfWebDebugLog" id="sfWebDebugToggleAllLogLinesTrigger">[all]</a></li>
          <li><a href="#sfWebDebugLog" id="sfWebDebugToggleNoLogLinesTrigger">[none]</a></li>
          <li><a href="#sfWebDebugInfo" class="toggleLogLevel">'.image_tag(sfConfig::get('yui_sf_web_dir').'/images/info.png', array('alt' => 'Info')).'</a></li>
          <li><a href="#sfWebDebugWarning" class="toggleLogLevel">'.image_tag(sfConfig::get('yui_sf_web_dir').'/images/warning.png', array('alt' => 'Warning')).'</a></li>
          <li><a href="#sfWebDebugError" class="toggleLogLevel">'.image_tag(sfConfig::get('yui_sf_web_dir').'/images/error.png', array('alt' => 'Error')).'</a></li>
          <li>'.implode("</li>\n<li>", $types).'</li>
        </ul>
        <div id="sfWebDebugLogLines">'.$logs.'</div>
      ';
    }

    $result .= '
    <div id="sfWebDebug">
      <div id="sfWebDebugBar" class="sfWebDebug'.ucfirst($maxPriority).'">
        <a href="#sfWebDebug" id="sfWebDebugTrigger">'.image_tag(sfConfig::get('yui_sf_web_dir').'/images/sf.png', array('alt' => 'symfony')).'</a>
        <ul id="sfWebDebugDetails" class="menu">
          <li>'.SYMFONY_VERSION.'</li>
          <li><a href="#sfWebDebugConfig" id="sfWebDebugConfigTrigger">'.image_tag(sfConfig::get('yui_sf_web_dir').'/images/config.png', array('alt' => 'vars & config')).' vars &amp; config</a></li>
          '.$cacheLink.'
          '.$logLink.'
          '.$dbInfo.'
          '.$memoryInfo.'
          '.$timeInfo.'
        </ul>
        <a href="#sfWebDebug" id="sfWebDebugClose">'.image_tag(sfConfig::get('yui_sf_web_dir').'/images/close.png').'</a>
      </div>

      <div id="sfWebDebugLog" class="sfWebDebugTop"><h1>Log and debug messages</h1>'.$logInfo.'</div>
      <div id="sfWebDebugConfig" class="sfWebDebugTop"><h1>Configuration and request variables</h1>'.$this->getCurrentConfigAsHtml().'</div>
      <div id="sfWebDebugDatabaseDetails" class="sfWebDebugTop"><h1>SQL queries</h1>'.$dbInfoDetails.'</div>
      <div id="sfWebDebugTimeDetails" class="sfWebDebugTop"><h1>Timers</h1>'.$timeInfoDetails.'</div>
    </div>
    ';

    return $result;
  }

  /**
   * Converts an array to HTML.
   *
   * @param string The identifier to use
   * @param array  The array of values
   *
   * @return string An HTML string
   */
  protected function formatArrayAsHtml($id, $values)
  {
    $id = ucfirst(strtolower($id));
    $content = '
    <h2>'.$id.' <a href="#sfWebDebugConfigSummary'.$id.'" class="toggleUriElement">'.image_tag(sfConfig::get('yui_sf_web_dir').'/images/toggle.gif').'</a></h2>
    <div id="sfWebDebugConfigSummary'.$id.'"><pre>'.htmlentities(sfYaml::dump($values), ENT_QUOTES, sfConfig::get('sf_charset')).'</pre></div>';

    return $content;
  }

  /**
   * Listens to the 'view.cache.filter_content' event to decorate a chunk of HTML with cache information.
   *
   * @param sfEvent A sfEvent instance
   * @param string  The HTML content
   *
   * @return string The decorated HTML string
   */
  public function decorateContentWithDebug(sfEvent $event, $content)
  {
    // don't decorate if not html or if content is null
    if (!sfConfig::get('sf_web_debug') || !$content || false === strpos($event['response']->getContentType(), 'html'))
    {
      return $content;
    }

    sfLoader::loadHelpers(array('Helper', 'Url', 'Asset', 'Tag'));

    $viewCacheManager = $event->getSubject();

    $cssClass      = ($event['new']) ? 'new' : 'old';
    $lastModified = $viewCacheManager->getLastModified($event['uri']);
    $id           = 'cache_info_'.md5($event['uri']);

    return '
      <div id="main_'.$id.'" class="sfWebDebugActionCache">
      <div id="sub_main_'.$id.'" class="sfWebDebugCache '.$cssClass.'">
        <a href="#sub_main_detail_'.$id.'" class="toggleUriElement">cache information</a>&nbsp;<a href="#sub_main_'.$id.'" class="toggleUriElement">'.image_tag(sfConfig::get('yui_sf_web_dir').'/images/close.png').'</a>
        <div id="sub_main_detail_'.$id.'" class="sfWebDebugCacheDetail">
        [uri]&nbsp;'.htmlentities($event['uri'], ENT_QUOTES, sfConfig::get('sf_charset')).'<br />
        [life&nbsp;time]&nbsp;'.$viewCacheManager->getLifeTime($event['uri']).'&nbsp;seconds<br />
        [last&nbsp;modified]&nbsp;'.(time() - $lastModified).'&nbsp;seconds<br />
        &nbsp;<br />&nbsp;
      </div></div>
      '.$content.'
      </div>
    ';
  }
}
