<?php

/**
 * ysfYUIView automatically adds unobstrusive javascript code to the view.
 *
 * @package    ysfYUIPlugin
 * @subpackage filter
 * @author     Francois Zaninotto <francois.zaninotto@symfony-project.com>
 * @author     Dustin Whittle <dustin.whittle@symfony-project.com>
 */
class ysfYUIView extends sfPHPView
{

  /**
   * Configures template.
   *
   * @return void
   */
  public function configure()
  {
    parent::configure();

    if(sfConfig::get('yui_loader_enabled', true))
    {
      // set default skin
      ysfYUI::setSkin(sfConfig::get('yui_default_skin'));

      // add default components
      foreach(sfConfig::get('yui_default_components', array()) as $component)
      {
        ysfYUI::addComponent($component);
      }
    }

    if(sfConfig::get('yui_output_xhtml_negotiation', false))
    {
      $acceptableContentTypes = $this->context->getRequest()->getAcceptableContentTypes();
      foreach(array('application/xhtml+xml', 'text/html') as $contentType)
      {
       if(in_array($contentType, $acceptableContentTypes))
       {
         $this->context->getResponse()->setContentType($contentType);
         break;
       }
      }
    }

  }

  /**
   * Renders the presentation.
   *
   * @return string A string representing the rendered presentation
   */
  public function render()
  {
    $content = null;
    if (sfConfig::get('sf_cache'))
    {
      $viewCache = $this->context->getViewCacheManager();
      $uri = $this->context->getRouting()->getCurrentInternalUri();

      list($content, $decoratorTemplate) = $viewCache->getActionCache($uri);
      if (!is_null($content))
      {
        $this->setDecoratorTemplate($decoratorTemplate);
      }
    }

    // render template if no cache
    if (is_null($content))
    {
      // execute pre-render check
      $this->preRenderCheck();

      // render template file
      $content = $this->renderFile($this->getDirectory().'/'.$this->getTemplate());

      if (sfConfig::get('yui_loader_enabled'))
      {
        // register any yui libraries used in actions/view
        $stylesheets = ysfYUI::getStylesheets(sfConfig::get('yui_loader_optimize', true));
        foreach ($stylesheets as $stylesheet)
        {
          $this->context->getResponse()->addStylesheet($stylesheet, 'first', array('media' => 'all', 'charset' => 'utf-8'));
        }

        $javascripts = ysfYUI::getJavascripts(sfConfig::get('yui_loader_optimize', true));
        foreach ($javascripts as $javascript)
        {
          $this->context->getResponse()->addJavascript($javascript, 'first', array('charset' => 'utf-8'));
        }
      }

      $events = ysfYUI::getEvents(true); // get events and then remove as they are included with content
      // add yui events
      if(!empty($events))
      {
        $content .= sprintf("<script type=\"text/javascript\" charset=\"utf-8\">\n//  <![CDATA[\n%s\n//  ]]>\n</script>", $events);
      }

      if (sfConfig::get('sf_cache'))
      {
        $content = $viewCache->setActionCache($uri, $content, $this->isDecorator() ? $this->getDecoratorDirectory().'/'.$this->getDecoratorTemplate() : false);
      }
    }

    // now render decorator template, if one exists
    if ($this->isDecorator())
    {
      $content = $this->decorate($content);
    }

    return $content;
  }
}
