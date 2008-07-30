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
 * YUI Javascript Helper.
 *
 * @package    ysymfony
 * @subpackage yui
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Dustin Whittle <dustin.whittle@symfony-project.com>
 * @author     David Heinemeier Hansson
 * @version    SVN: $Id$
 *
 * @link       http://developer.yahoo.com/
 */

sfLoader::loadHelpers(array('Tag', 'Javascript', 'Url'));

ysfYUI::addComponents('dom', 'event', 'button', 'connection');

/**
 * Inserts JavaScript code unobtrusively
 *
 * <b>Example:</b>
 * <code>
 *  <?php yui("alert('foobar');") ?>
 * </code>
 *
 * @param  string JavaScript code
 */
function yui($script)
{
  ysfYUI::addEvent('document', 'ready', $script);
}

/**
 * Starts a JavaScript code block for unobtrusive insertion
 *
 * <b>Example:</b>
 * <code>
 *  <?php yui_block() ?>
 *    alert('foobar');
 *  <?php yui_end_block() ?>
 * </code>
 *
 * @see yui_end_block
 */
function yui_block()
{
  ob_start();
  ob_implicit_flush(0);
}

/**
 * Ends a JavaScript code block and inserts the JavaScript code unobtrusively
 *
 * @see yui_block
 */
function yui_end_block()
{
  $content = ob_get_clean();
  yui($content);
}

/**
 * Adds an event listener to an existing DOM element unobtrusively
 *
 * <b>Example:</b>
 * <code>
 *  <?php yui_add_event('id', 'click', "alert('foobar')") ?>
 * </code>
 *
 * @param string $id the Id
 * @param string $event  The event name (without leading 'on')
 * @param string $script JavaScript code
 */
function yui_add_event($id, $event, $script)
{
  ysfYUI::addEvent($id, $event, $script);
}

/**
 * Returns a link that'll trigger a javascript function using the
 * onclick handler and return false after the fact.
 *
 * Examples:
 *   <?php echo yui_link_to_function('Greeting', "alert('Hello world!')") ?>
 *   <?php echo yui_link_to_function(image_tag('delete'), "if confirm('Really?'){ do_delete(); }") ?>
 */
function yui_link_to_function($name, $function, $html_options = array())
{

  $html_options = _parse_attributes($html_options);

  $html_options['id'] = isset($html_options['id']) ? $html_options['id'] : generate_id($name);
  $html_options['href'] = isset($html_options['href']) ? $html_options['href'] : '#';

  ysfYUI::addEvent($html_options['id'], 'click', 'YAHOO.util.Event.preventDefault(e); '.$function);

  return content_tag('a', $name, $html_options);
}


/**
 * Returns a yui button.
 *
 * Examples:
 *   <?php echo yui_button('Greeting') ?>
 */
function yui_button($name, $html_options = array())
{
  $html_options = _parse_attributes($html_options);

  $html_options['type'] = 'button';
  $html_options['value'] = $name;
  $html_options['id'] = isset($html_options['id']) ? $html_options['id'] : generate_id($name);

  ysfYUI::addEvent('document', 'ready', "var button".$html_options['id']." = new YAHOO.widget.Button('".$html_options['id']."-container');");

  return '<span id="'.$html_options['id'].'-container" class="yui-button yui-push-button"><span class="first-child">'.tag('input', $html_options).'</span></span>';
}

/**
 * Creates an <input> button tag of the given name pointing to a routed URL
 * based on the module/action passed as argument and the routing configuration.
 * The syntax is similar to the one of link_to.
 *
 * <b>Options:</b>
 * - 'absolute' - if set to true, the helper outputs an absolute URL
 * - 'query_string' - to append a query string (starting by ?) to the routed url
 * - 'anchor' - to append an anchor (starting by #) to the routed url
 * - 'confirm' - displays a javascript confirmation alert when the button is clicked
 * - 'popup' - if set to true, the button opens a new browser window
 * - 'post' - if set to true, the button submits a POST request instead of GET (caution: do not use inside a form)
 *
 * <b>Examples:</b>
 * <code>
 *  echo yui_button_to('Delete this page', 'my_module/my_action');
 * </code>
 *
 * @param  string $name          name of the button
 * @param  string $internal_uri  'module/action' or '@rule' of the action
 * @param  array  $options       additional HTML compliant <input> tag parameters
 * @return string XHTML compliant <input> tag
 * @see    url_for, link_to
 */
function yui_button_to($name, $internal_uri ='', $options = array())
{
  $html_options = _parse_attributes($options);

  $html_options['id'] = isset($html_options['id']) ? $html_options['id'] : generate_id($name);
  $html_options['value'] = $name;

  if (isset($html_options['post']) && $html_options['post'])
  {
    if (isset($html_options['popup']))
    {
      throw new sfConfigurationException('You can\'t use "popup" and "post" together.');
    }
    $html_options['type'] = 'submit';
    unset($html_options['post']);
    $html_options = _convert_options_to_javascript($html_options);

    return form_tag($internal_uri, array('method' => 'post', 'class' => 'yui_button_to')).content_tag('div', tag('input', $html_options)).'</form>';
  }

  $url = url_for($internal_uri);
  if (isset($html_options['query_string']))
  {
    $url = $url.'?'.$html_options['query_string'];
    unset($html_options['query_string']);
  }
  if (isset($html_options['anchor']))
  {
    $url = $url.'#'.$html_options['anchor'];
    unset($html_options['anchor']);
  }
  $url = "'".$url."'";
  $html_options['type'] = 'button';

  if (isset($html_options['popup']))
  {
    $html_options = _convert_options_to_javascript($html_options, $url);
    unset($html_options['popup']);
  }
  else
  {
    ysfYUI::addEvent($html_options['id'].'-container', 'click', 'YAHOO.util.Event.preventDefault(e); '."document.location.href=".$url.";");
    $html_options = _convert_options_to_javascript($html_options);
  }


  ysfYUI::addEvent('document', 'ready', "var button".$html_options['id']." = new YAHOO.widget.Button('".$html_options['id']."-container');");

  return '<span id="'.$html_options['id'].'-container" class="yui-button yui-push-button"><span class="first-child">'.tag('input', $html_options).'</span></span>';
}


/**
 * If the condition passed as first argument is true,
 * creates a button input tag of the given name using a routed URL
 * based on the module/action passed as argument and the routing configuration.
 * If the condition is false, the given name is returned between <span> tags
 *
 * <b>Options:</b>
 * - 'tag' - the HTML tag that must enclose the name if the condition is false, defaults to <span>
 * - 'absolute' - if set to true, the helper outputs an absolute URL
 * - 'query_string' - to append a query string (starting by ?) to the routed url
 * - 'anchor' - to append an anchor (starting by #) to the routed url
 * - 'confirm' - displays a javascript confirmation alert when the link is clicked
 * - 'popup' - if set to true, the link opens a new browser window
 * - 'post' - if set to true, the link submits a POST request instead of GET (caution: do not use inside a form)
 *
 * <b>Examples:</b>
 * <code>
 *  echo yui_button_to_if($user->isAdministrator(), 'Delete this page', 'my_module/my_action');
 *    => <a href="/path/to/my/action">Delete this page</a>
 *  echo yui_button_to_if(!$user->isAdministrator(), 'Delete this page', 'my_module/my_action');
 *    => <span>Delete this page</span>
 * </code>
 *
 * @param  bool   $condition     condition
 * @param  string $name          name of the link, i.e. string to appear between the <a> tags
 * @param  string $internal_uri  'module/action' or '@rule' of the action
 * @param  array  $options       additional HTML compliant <a> tag parameters
 * @return string XHTML compliant <a href> tag or name
 * @see    link_to
 */
function yui_button_to_if($condition, $name = '', $internal_uri = '', $options = array())
{
  $html_options = _parse_attributes($options);
  if ($condition)
  {
    unset($html_options['tag']);
    return yui_button_to($name, $internal_uri, $html_options);
  }
  else
  {
    unset($html_options['query_string']);
    unset($html_options['absolute_url']);
    unset($html_options['absolute']);

    $tag = _get_option($html_options, 'tag', 'span');

    return content_tag($tag, $name, $html_options);
  }
}

/**
 * If the condition passed as first argument is false,
 * creates a button input tag of the given name using a routed URL
 * based on the module/action passed as argument and the routing configuration.
 * If the condition is true, the given name is returned between <span> tags
 *
 * <b>Options:</b>
 * - 'tag' - the HTML tag that must enclose the name if the condition is true, defaults to <span>
 * - 'absolute' - if set to true, the helper outputs an absolute URL
 * - 'query_string' - to append a query string (starting by ?) to the routed url
 * - 'anchor' - to append an anchor (starting by #) to the routed url
 * - 'confirm' - displays a javascript confirmation alert when the link is clicked
 * - 'popup' - if set to true, the link opens a new browser window
 * - 'post' - if set to true, the link submits a POST request instead of GET (caution: do not use inside a form)
 *
 * <b>Examples:</b>
 * <code>
 *  echo yui_button_to_unless($user->isAdministrator(), 'Delete this page', 'my_module/my_action');
 *    => <span>Delete this page</span>
 *  echo yui_button_to_unless(!$user->isAdministrator(), 'Delete this page', 'my_module/my_action');
 *    => <a href="/path/to/my/action">Delete this page</a>
 * </code>
 *
 * @param  bool   $condition     condition
 * @param  string $name          name of the link, i.e. string to appear between the <a> tags
 * @param  string $internal_uri  'module/action' or '@rule' of the action
 * @param  array  $options       additional HTML compliant <a> tag parameters
 * @return string XHTML compliant <a href> tag or name
 * @see    link_to
 */
function yui_button_to_unless($condition, $name = '', $internal_uri = '', $options = array())
{
  return yui_button_to_if(!$condition, $name, $internal_uri, $options);
}

/**
 * Returns a button that'll trigger a javascript function using the
 * onclick handler and return false after the fact.
 *
 * Examples:
 *   <?php echo button_to_function('Greeting', "alert('Hello world!')") ?>
 */
function yui_button_to_function($name, $function, $html_options = array())
{

  $html_options = _parse_attributes($html_options);

  $html_options['type'] = 'button';
  $html_options['value'] = $name;
  $html_options['id'] = isset($html_options['id']) ? $html_options['id'] : generate_id($name);

  ysfYUI::addEvent('document', 'ready', "var button".$html_options['id']." = new YAHOO.widget.Button('".$html_options['id']."-container');");

  ysfYUI::addEvent($html_options['id'].'-container', 'click', 'YAHOO.util.Event.preventDefault(e); '.$function);

  return '<span id="'.$html_options['id'].'-container" class="yui-button yui-push-button"><span class="first-child">'.tag('input', $html_options).'</span></span>';
}

/**
 * Returns an html button to a remote action defined by 'url' (using the
 * 'url_for()' format) that's called in the background using XMLHttpRequest.
 *
 * See link_to_remote() for details.
 *
 */
function yui_button_to_remote($name, $options = array(), $html_options = array())
{
  return yui_button_to_function($name, yui_remote_function($options), $html_options);
}

/**
 * Returns a link to a remote action defined by 'url'
 * (using the 'url_for()' format) that's called in the background using
 * XMLHttpRequest. The result of that request can then be inserted into a
 * DOM object whose id can be specified with 'update'.
 * Usually, the result would be a partial prepared by the controller with
 * either 'render_partial()'.
 *
 * Examples:
 *  <?php echo yui_link_to_remote('Delete this post'), array(
 *    'update' => 'posts',
 *    'url'    => 'destroy?id='.$post.id,
 *  )) ?>
 *  <?php echo yui_link_to_remote(image_tag('refresh'), array(
 *    'update' => 'emails',
 *    'url'    => '@list_emails',
 *  )) ?>
 *
 * You can also specify a hash for 'update' to allow for
 * easy redirection of output to an other DOM element if a server-side error occurs:
 *
 * Example:
 *  <?php echo yui_link_to_remote('Delete this post', array(
 *      'update' => array('success' => 'posts', 'failure' => 'error'),
 *      'url'    => 'destroy?id='.$post.id,
 *  )) ?>
 *
 * Optionally, you can use the 'position' parameter to influence
 * how the target DOM element is updated. It must be one of
 * 'before', 'top', 'bottom', or 'after'.
 *
 * By default, these remote requests are processed asynchronous during
 * which various JavaScript callbacks can be triggered (for progress indicators and
 * the likes). All callbacks get access to the 'request' object,
 * which holds the underlying XMLHttpRequest.
 *
 * To access the server response, use 'request.responseText', to
 * find out the HTTP status, use 'request.status'.
 *
 * Example:
 *  <?php echo yui_link_to_remote($word, array(
 *    'url'      => '@undo?n='.$word_counter,
 *    'complete' => 'undoRequestCompleted(request)'
 *  )) ?>
 *
 * The callbacks that may be specified are (in order):
 *
 * 'loading'                 Called when the remote document is being
 *                           loaded with data by the browser.
 * 'loaded'                  Called when the browser has finished loading
 *                           the remote document.
 * 'interactive'             Called when the user can interact with the
 *                           remote document, even though it has not
 *                           finished loading.
 * 'success'                 Called when the XMLHttpRequest is completed,
 *                           and the HTTP status code is in the 2XX range.
 * 'failure'                 Called when the XMLHttpRequest is completed,
 *                           and the HTTP status code is not in the 2XX
 *                           range.
 * 'complete'                Called when the XMLHttpRequest is complete
 *                           (fires after success/failure if they are present).,
 *
 * You can further refine 'success' and 'failure' by adding additional
 * callbacks for specific status codes:
 *
 * Example:
 *  <?php echo yui_link_to_remote($word, array(
 *       'url'     => '@rule',
 *       '404'     => "alert('Not found...? Wrong URL...?')",
 *       'failure' => "alert('HTTP Error ' + request.status + '!')",
 *  )) ?>
 *
 * A status code callback overrides the success/failure handlers if present.
 *
 * If you for some reason or another need synchronous processing (that'll
 * block the browser while the request is happening), you can specify
 * 'type' => 'synchronous'.
 *
 * You can customize further browser side call logic by passing
 * in JavaScript code snippets via some optional parameters. In
 * their order of use these are:
 *
 * 'confirm'             Adds confirmation dialog.
 * 'condition'           Perform remote request conditionally
 *                       by this expression. Use this to
 *                       describe browser-side conditions when
 *                       request should not be initiated.
 * 'before'              Called before request is initiated.
 * 'after'               Called immediately after request was
 *                       initiated and before 'loading'.
 * 'submit'              Specifies the DOM element ID that's used
 *                       as the parent of the form elements. By
 *                       default this is the current form, but
 *                       it could just as well be the ID of a
 *                       table row or any other DOM element.
 */
function yui_link_to_remote($name, $options = array(), $html_options = array())
{
  $html_options['href'] = $options['url'];

  return yui_link_to_function($name, yui_remote_function($options), $html_options);
}


/**
 * Periodically calls the specified function every 'frequency' seconds (default is 10).
 */
function yui_periodically_call_function($function, $options = array())
{
  $frequency = isset($options['frequency']) ? $options['frequency'] : 10; // every ten seconds by default
  $frequency = $frequency * 1000;

  ysfYUI::addEvent('window', 'load', "YAHOO.lang.later(".$frequency.", this, $function, [] , true);");
}

/**
 * Periodically calls the specified url ('url') every 'frequency' seconds (default is 10).
 * Usually used to update a specified div ('update') with the results of the remote call.
 * The options for specifying the target with 'url' and defining callbacks is the same as 'yui_link_to_remote()'.
 */
function yui_periodically_call_remote($options = array())
{

  $frequency = isset($options['frequency']) ? $options['frequency'] : 10; // every ten seconds by default
  $frequency = $frequency * 1000;

  ysfYUI::addEvent('window', 'load', "YAHOO.lang.later(".$frequency.", this, function() {".yui_remote_function($options)."}, [] , true);");
}

/**
 * Returns a form tag that will submit using XMLHttpRequest in the background instead of the regular
 * reloading POST arrangement. Even though it's using JavaScript to serialize the form elements, the form submission
 * will work just like a regular submission as viewed by the receiving side (all elements available in 'params').
 * The options for specifying the target with 'url' and defining callbacks are the same as 'yui_link_to_remote()'.
 *
 * A "fall-through" target for browsers that don't do JavaScript can be specified
 * with the 'action'/'method' options on '$html_options'
 *
 * Example:
 *  <?php echo yui_form_remote_tag(array(
 *    'url'      => '@tag_add',
 *    'update'   => 'question_tags',
 *    'complete' => yui_visual_effect('highlight', 'question_tags'),
 *  )) ?>
 *
 * The hash passed as a second argument is equivalent to the options (2nd) argument in the form_tag() helper.
 *
 * By default the fall-through action is the same as the one specified in the 'url'
 * (and the default method is 'post').
 */
function yui_form_remote_tag($options = array(), $html_options = array())
{
  ysfYUI::addComponents('dom', 'event');

  $options = _parse_attributes($options);
  $html_options = _parse_attributes($html_options);

  $url = '';
  if(isset($options['url']))
  {
    $url = url_for($options['url']);
    unset($options['url']);
  }

  $update = '';
  if(isset($options['update']))
  {
    $update = 'YAHOO.util.Dom.get("'.$options['update'].'").innerHTML=o.responseText;';
    unset($options['update']);
  }

  $success = '';
  if(isset($options['success']))
  {
    $success = $options['success'];
    unset($options['success']);
  }

  $id = isset($html_options['id']) ? $html_options['id'] : generate_id();
  $html_options['id'] = $id;

  $js = 'var c = YAHOO.util.Connect;';
  $js .= 'c.setForm(YAHOO.util.Dom.get("'.$id.'"));';
  $js .= 'c.initHeader("X-Requested-With", "XMLHttpRequest");';
  $js .= 'c.asyncRequest("POST", "'.$url.'",{success:function(o){'.$update.$success.'}});';

  ysfYUI::addEvent($id, 'submit', 'YAHOO.util.Event.preventDefault(e); '.$js);

  $html_options['action'] = isset($html_options['action']) ? $html_options['action'] : $url;
  $html_options['method'] = isset($html_options['method']) ? $html_options['method'] : 'post';

  return tag('form', $html_options, true);
}

/**
 *  Returns a button input tag that will submit form using XMLHttpRequest in the background instead of regular
 *  reloading POST arrangement. The '$options' argument is the same as in 'form_remote_tag()'.
 */
function yui_submit_to_remote($name, $value, $options = array(), $html_options = array())
{

  ysfYUI::addComponents('button', 'dom', 'event', 'connection');

  $options = _parse_attributes($options);
  $html_options = _parse_attributes($html_options);

  $id = isset($html_options['id']) ? $html_options['id'] : generate_id($name);
  $html_options['id'] = $id;

  $url = '';
  if(isset($options['url']))
  {
    $url = url_for($options['url']);
    unset($options['url']);
  }

  $update = '';
  if(isset($options['update']))
  {
    $update = 'YAHOO.util.Dom.get("'.$options['update'].'").innerHTML=o.responseText;';
    unset($options['update']);
  }

  $success = '';
  if(isset($options['success']))
  {
    $success = $options['success'];
    unset($options['success']);
  }

  $js = 'var c = YAHOO.util.Connect;';
  $js .= 'c.setForm(YAHOO.util.Dom.get("'.$id.'-container").parentNode);';
  $js .= 'c.initHeader("X-Requested-With", "XMLHttpRequest");';
  $js .= 'c.asyncRequest("POST", "'.$url.'",{ success: function(o) {'.$update.$success.'}});';

  $html_options['name'] = $name;
  $html_options['type'] = 'button';
  $html_options['value'] = $value;

  ysfYUI::addEvent('document', 'ready', "var button".$html_options['id']." = new YAHOO.widget.Button('".$html_options['id']."-container');");

  ysfYUI::addEvent($html_options['id'].'-container', 'click', 'YAHOO.util.Event.preventDefault(e); '.$js);

  return '<span id="'.$html_options['id'].'-container" class="yui-button yui-push-button"><span class="first-child">'.tag('input', $html_options).'</span></span>';
}

/**
 *  Returns a image submit tag that will submit form using XMLHttpRequest in the background instead of regular
 *  reloading POST arrangement. The '$options' argument is the same as in 'form_remote_tag()'.
 */
function yui_submit_image_to_remote($name, $source, $options = array(), $html_options = array())
{
  ysfYUI::addComponents('dom', 'event', 'connection');

  $options = _parse_attributes($options);
  $html_options = _parse_attributes($html_options);

  $html_options['type'] = 'image';
  $html_options['name'] = $name;
  $html_options['src'] = image_path($source);

  if (!isset($html_options['alt']))
  {
    $path_pos = strrpos($source, '/');
    $dot_pos = strrpos($source, '.');
    $begin = $path_pos ? $path_pos + 1 : 0;
    $nb_str = ($dot_pos ? $dot_pos : strlen($source)) - $begin;
    $html_options['alt'] = ucfirst(substr($source, $begin, $nb_str));
  }

  $id = isset($html_options['id']) ? $html_options['id'] : generate_id($name);
  $html_options['id'] = $id;

  $url = '';
  if(isset($options['url']))
  {
    $url = url_for($options['url']);
    unset($options['url']);
  }

  $update = '';
  if(isset($options['update']))
  {
    $update = 'YAHOO.util.Dom.get("'.$options['update'].'").innerHTML=o.responseText;';
    unset($options['update']);
  }

  $success = '';
  if(isset($options['success']))
  {
    $success = $options['success'];
    unset($options['success']);
  }

  $js = 'var c = YAHOO.util.Connect;';
  $js .= 'c.setForm(YAHOO.util.Dom.get("'.$id.'").parentNode);';
  $js .= 'c.initHeader("X-Requested-With", "XMLHttpRequest");';
  $js .= 'c.asyncRequest("POST", "'.$url.'",{ success: function(o) {'.$update.$success.'}});';

  ysfYUI::addEvent($html_options['id'], 'click', 'YAHOO.util.Event.preventDefault(e); '.$js);

  return tag('input', $html_options, false);
}

/**
 * Returns a javascript function (or expression) that will update a DOM element '$element_id'
 * according to the '$options' passed.
 *
 * Possible '$options' are:
 * 'content'            The content to use for updating. Can be left out if using block, see example.
 * 'action'             Valid options are 'update' (assumed by default), 'empty', 'remove'
 * 'position'           If the 'action' is 'update', you can optionally specify one of the following positions:
 *                      'before', 'after'.
 *
 * Example:
 *   <?php echo javascript_tag(
 *      yui_update_element_function('products', array(
 *            'position' => 'bottom',
 *            'content'  => "<p>New product!</p>",
 *      ))
 *   ) ?>
 *
 *
 * This method can also be used in combination with remote method call
 * where the result is evaluated afterwards to cause multiple updates on a page.
 *
 * Example:
 *
 *  # Calling view
 *  <?php echo yui_form_remote_tag(array(
 *      'url'      => '@buy',
 *      'complete' => evaluate_remote_response()
 *  )) ?>
 *  all the inputs here...
 *
 *  # Target action
 *  public function executeBuy()
 *  {
 *     $this->product = ProductPeer::retrieveByPk(1);
 *  }
 *
 *  # Returning view
 *  <php echo yui_update_element_function('cart', array(
 *      'action'   => 'update',
 *      'position' => 'bottom',
 *      'content'  => '<p>New Product: '.$product->getName().'</p>',
 *  )) ?>
 */
function yui_update_element_function($element_id, $options = array())
{
  ysfYUI::addComponents('dom', 'event');

  $content = escape_javascript(isset($options['content']) ? $options['content'] : '');

  $value = isset($options['action']) ? $options['action'] : 'update';
  switch($value)
  {
    case 'update':
      if(isset($options['position']) && $options['position'])
      {
        $js = "YAHOO.util.Dom.insert".sfInflector::camelize($options['position'])."('$content', YAHOO.util.Dom.get('$element_id')); ";
      }
      else
      {
        $js = "YAHOO.util.Dom.get('$element_id').innerHTML = '$content'";
      }
    break;

    case 'empty':
      $js = "YAHOO.util.Dom.get('$element_id').innerHTML = ''";
    break;

    case 'remove':
      $js = "var el = YAHOO.util.Dom.get('$element_id'); el.parentNode.removeChild(el); ";
    break;

    default:
      throw new sfException('Invalid action, choose one of update, remove, empty');
  }

  $js .= ";\n";
  $js = (isset($options['binding']) ? $js.$options['binding'] : $js);

  ysfYUI::addEvent($element_id, 'available', $js);
}

/**
 * Returns 'eval(request.responseText)', which is the javascript function that
 * 'yui_form_remote_tag()' can call in 'complete' to evaluate a multiple update return document
 * using 'yui_update_element_function()' calls.
 */
function yui_evaluate_remote_response()
{

  return 'eval(request.responseText)';
}

/**
 * Returns the javascript needed for a remote function.
 * Takes the same arguments as 'yui_link_to_remote()'.
 *
 * Example:
 *   <select id="options" onchange="<?php echo yui_remote_function(array('update' => 'options', 'url' => '@update_options')) ?>">
 *     <option value="0">Hello</option>
 *     <option value="1">World</option>
 *   </select>
 */
function yui_remote_function($options)
{
  ysfYUI::addComponents('dom', 'event', 'connection');

  $events = array();
  if(isset($options['update']) && is_array($options['update']))
  {
    if(isset($options['update']['success']))
    {
      $events = array('success' => "function(o) { YAHOO.util.Dom.get('".$options['update']['success']."').innerHTML = o.responseText; }");
    }
    if(isset($options['update']['failure']))
    {
      $events = array('failure' => "function(o) { YAHOO.util.Dom.get('".$options['update']['failure']."').innerHTML = o.responseText; }");
    }
  }
  elseif(isset($options['update']))
  {
    $events = array('success' => "function(o) { YAHOO.util.Dom.get('".$options['update']."').innerHTML = o.responseText; }",
                    'failure' => "function(o) { YAHOO.util.Dom.get('".$options['update']."').innerHTML = o.responseText; }");
  }

  if(isset($options['success']))
  {
    $events['success'] = 'function(o) { '.$options['success'].'}';
  }

  if(isset($options['failure']))
  {
    $events['failure'] = 'function(o) { '.$options['failure'].'}';
  }

  // handle $javascript_options + post data ?name=value&name2=value2
  $function = ysfYUI::connection('GET', $options['url'], $events);

  if(isset($options['before']))
  {
    $function = $options['before'].'; '.$function;
  }
  if(isset($options['after']))
  {
    $function = $function.'; '.$options['after'];
  }
  if(isset($options['condition']))
  {
    $function = 'if('.$options['condition'].') { '.$function.'; }';
  }
  if(isset($options['confirm']))
  {
    $function = "if(confirm('".escape_javascript($options['confirm'])."')) { $function; }";
    if(isset($options['cancel']))
    {
      $function = $function.' else { '.$options['cancel'].' }';
    }
  }

  return $function;
}

/**
 * Observes the field with the DOM ID specified by '$field_id' and makes
 * an AJAX call when its contents have changed.
 *
 * Required '$options' are:
 * 'url'                 'url_for()'-style options for the action to call
 *                       when the field has changed.
 *
 * Additional options are:
 * 'frequency'           The frequency (in seconds) at which changes to
 *                       this field will be detected. Not setting this
 *                       option at all or to a value equal to or less than
 *                       zero will use event based observation instead of
 *                       time based observation.
 * 'update'              Specifies the DOM ID of the element whose
 *                       innerHTML should be updated with the
 *                       XMLHttpRequest response text.
 * 'with'                A JavaScript expression specifying the
 *                       parameters for the XMLHttpRequest. This defaults
 *                       to 'value', which in the evaluated context
 *                       refers to the new field value.
 *
 * Additionally, you may specify any of the options documented in
 * yui_link_to_remote().
 */
function yui_observe_field($field_id, $options = array())
{

  if(isset($options['frequency']) && $options['frequency'] > 0)
  {
    return _build_observer('Form.Element.Observer', $field_id, $options);
  }
  else
  {
    return _build_observer('Form.Element.EventObserver', $field_id, $options);
  }
}

/**
 * Like 'yui_observe_field()', but operates on an entire form identified by the
 * DOM ID '$form_id'. '$options' are the same as 'yui_observe_field()', except
 * the default value of the 'with' option evaluates to the
 * serialized (request string) value of the form.
 */
function yui_observe_form($form_id, $options = array())
{

  if(isset($options['frequency']) && $options['frequency'] > 0)
  {
    return _build_observer('Form.Observer', $form_id, $options);
  }
  else
  {
    return _build_observer('Form.EventObserver', $form_id, $options);
  }
}

/**
 * Returns a JavaScript snippet to be used on the AJAX callbacks for starting
 * visual effects.
 *
 * Example:
 *  <?php echo yui_link_to_remote('Reload', array(
 *        'update'  => 'posts',
 *        'url'     => '@reload',
 *        'complete => yui_visual_effect('highlight', 'posts', array('duration' => 0.5 )),
 *  )) ?>
 *
 * If no '$element_id' is given, it assumes "element" which should be a local
 * variable in the generated JavaScript execution context. This can be used
 * for example with drop_receiving_element():
 *
 *  <?php echo drop_receving_element( ..., array(
 *        ...
 *        'loading' => yui_visual_effect('fade'),
 *  )) ?>
 *
 * This would fade the element that was dropped on the drop receiving element.
 *
 * You can change the behaviour with various options, see
 * http://script.aculo.us for more documentation.
 */
function yui_visual_effect($name, $element_id = false, $js_options = array())
{

  ysfYUI::addComponent('animation');

  $element = $element_id ? "'$element_id'" : 'element';

  if(in_array($name, array('toggle_appear', 'toggle_blind', 'toggle_slide')))
  {
    return "new Effect.toggle($element, '".substr($name, 7)."', "._options_for_javascript($js_options).");";
  }
  else
  {
    return "new Effect.".sfInflector::camelize($name)."($element, "._options_for_javascript($js_options).");";
  }
}

/**
 * Makes the elements with the DOM ID specified by '$element_id' sortable
 * by drag-and-drop and make an AJAX call whenever the sort order has
 * changed. By default, the action called gets the serialized sortable
 * element as parameters.
 *
 * Example:
 *   <php echo yui_sortable_element($my_list, array(
 *      'url' => '@order',
 *   )) ?>
 *
 * In the example, the action gets a '$my_list' array parameter
 * containing the values of the ids of elements the sortable consists
 * of, in the current order.
 *
 * You can change the behaviour with various options, see
 * http://script.aculo.us for more documentation.
 */
function yui_sortable_element($element_id, $options = array())
{

  if(! isset($options['with']))
  {
    $options['with'] = "Sortable.serialize('$element_id')";
  }

  if(! isset($options['onUpdate']))
  {
    $options['onUpdate'] = "function(){".yui_remote_function($options)."}";
  }


  foreach(array('tag', 'overlap', 'constraint', 'handle') as $option)
  {
    if(isset($options[$option]))
    {
      $options[$option] = "'{$options[$option]}'";
    }
  }

  if(isset($options['containment']))
  {
    $options['containment'] = _array_or_string_for_javascript($options['containment']);
  }

  if(isset($options['hoverclass']))
  {
    $options['hoverclass'] = "'{$options['hoverclass']}'";
  }

  if(isset($options['only']))
  {
    $options['only'] = _array_or_string_for_javascript($options['only']);
  }

  return javascript_tag("Sortable.create('$element_id', "._options_for_javascript($options).")");
}

/**
 * Makes the element with the DOM ID specified by '$element_id' draggable.
 *
 * Example:
 *   <?php echo draggable_element('my_image') ?>
 *
 * You can change the behaviour with various options.
 */
function yui_draggable_element($element_id, $options = array())
{
  ysfYUI::addComponent('dragdrop');

  ysfYUI::addEvent('document', 'ready', "var dd_{$element_id} = new YAHOO.util.DD('{$element_id}');"); // , "._options_for_javascript($options)."
}

/**
 * Makes the element with the DOM ID specified by '$element_id' receive
 * dropped draggable elements (created by 'draggable_element()') and make an AJAX call.
 * By default, the action called gets the DOM ID of the element as parameter.
 *
 * Example:
 *   <?php drop_receiving_element('my_cart', array(
 *      'url' => 'cart/add',
 *   )) ?>
 *
 * You can change the behaviour with various options.
 */
function yui_drop_receiving_element($element_id, $options = array())
{

  ysfYUI::addComponent('dragdrop');

  if(!isset($options['with']))
  {
    $options['with'] = "'id=' + encodeURIComponent(element.id)";
  }
  if(!isset($options['onDrop']))
  {
    $options['onDrop'] = yui_remote_function($options);
  }

  if(isset($options['accept']))
  {
    $options['accept'] = _array_or_string_for_javascript($options['accept']);
  }

  if(isset($options['hoverclass']))
  {
    $options['hoverclass'] = "'{$options['hoverclass']}'";
  }

  ysfYUI::addEvent('document', 'ready', "var ddt_{$element_id} = new YAHOO.util.DDTarget('{$element_id}'); ddt_{$element_id}.on('dragDropEvent', function(ev, id) { {$options['onDrop']} }, ddt_{$element_id}, true);");
}

/**
 * Autocomplete helper.
 *
 * @param string name value of input field
 * @param string default value for input field
 * @param array input tag options. (size, autocomplete, etc...)
 * @param array completion options. (use_style, etc...)
 *
 * @return string input field tag, div for completion results, and
 *                 auto complete javascript tags
 */
function yui_input_auto_complete_tag($name, $value, $url, $tag_options = array(), $completion_options = array())
{

  ysfYUI::addComponent('autocomplete');

  $tag_options = _convert_options($tag_options);
  $tag_options['id'] = get_id_from_name(isset($tag_options['id']) ? $tag_options['id'] : $name);

  $js_options = array();
  if(isset($options['tokens']))
  {
    $js_options['tokens'] = _array_or_string_for_javascript($options['tokens']);
  }
  if(isset($options['on_show']))
  {
    $js_options['onShow'] = $options['on_show'];
  }
  if(isset($options['on_hide']))
  {
    $js_options['onHide'] = $options['on_hide'];
  }
  if(isset($options['min_chars']))
  {
    $js_options['minChars'] = $options['min_chars'];
  }
  if(isset($options['frequency']))
  {
    $js_options['frequency'] = $options['frequency'];
  }
  if(isset($options['update_element']))
  {
    $js_options['updateElement'] = $options['update_element'];
  }
  if(isset($options['after_update_element']))
  {
    $js_options['afterUpdateElement'] = $options['after_update_element'];
  }

  $javascript_options = _options_for_javascript($js_options);

  $javascript  = '<div id="'.$tag_options['id'].'_container">';
  $javascript .= tag('input', array_merge(array('type' => 'text', 'name' => $name, 'value' => $value), _convert_options($tag_options)));
  $javascript .= content_tag('div', '', array('id' => $tag_options['id'].'_autocomplete', 'class' => 'auto_complete'));
  $javascript .= "</div>";

  ysfYUI::addEvent('window', 'load', "
  var datasource{$tag_options['id']} = new YAHOO.widget.DS_XHR('{$url}', ['ResultSet.Result','Title']);
  datasource{$tag_options['id']}.maxCacheEntries = 60;
  datasource{$tag_options['id']}.queryMatchSubset = true;
  datasource{$tag_options['id']}.queryMatchContains = true;
  datasource{$tag_options['id']}.scriptQueryAppend = 'output=json&results=100';

  var autocomplete{$tag_options['id']} = new YAHOO.widget.AutoComplete('{$tag_options['id']}','{$tag_options['id']}_container', datasource{$tag_options['id']});
  autocomplete{$tag_options['id']}.useShadow = true;
  autocomplete{$tag_options['id']}.queryDelay = 1;
  autocomplete{$tag_options['id']}.doBeforeExpandContainer = function(oTextbox, oContainer, sQuery, aResults) { var pos = YAHOO.util.Dom.getXY(oTextbox); pos[1] += YAHOO.util.Dom.get(oTextbox).offsetHeight + 2; YAHOO.util.Dom.setXY(oContainer,pos); return true; };
  ");

  return $javascript;
}

/**
 * Makes an HTML element specified by the DOM ID '$field_id' become an in-place
 * editor of a property.
 *
 * A form is automatically created and displayed when the user clicks the element,
 * something like this:
 * <form id="myElement-in-place-edit-form" target="specified url">
 *   <input name="value" text="The content of myElement"/>
 *   <input type="submit" value="ok"/>
 *   <a onclick="javascript to cancel the editing">cancel</a>
 * </form>
 *
 * The form is serialized and sent to the server using an AJAX call, the action on
 * the server should process the value and return the updated value in the body of
 * the reponse. The element will automatically be updated with the changed value
 * (as returned from the server).
 *
 * Required '$options' are:
 * 'url'                 Specifies the url where the updated value should
 *                       be sent after the user presses "ok".
 *
 * Addtional '$options' are:
 * 'rows'                Number of rows (more than 1 will use a TEXTAREA)
 * 'cancel_text'         The text on the cancel link. (default: "cancel")
 * 'save_text'           The text on the save link. (default: "ok")
 * 'external_control'    The id of an external control used to enter edit mode.
 * 'options'             Pass through options to the AJAX call
 * 'with'                JavaScript snippet that should return what is to be sent
 *                       in the AJAX call, 'form' is an implicit parameter
 *
 * @param string name id of field that can be edited
 * @param string url of module/action to be called when ok is clicked
 * @param array editor tag options. (rows, cols, highlightcolor, highlightendcolor, etc...)
 *
 * @return string javascript to manipulate the id field to allow click and edit functionality
 */
function yui_input_in_place_editor_tag($name, $url, $editor_options = array())
{
  ysfYUI::addComponents('dom', 'event');

  $editor_options = _convert_options($editor_options);
  $default_options = array('tag' => 'span', 'id' => '\''.$name.'_in_place_editor', 'class' => 'in_place_editor_field');

  $options = array_merge($default_options, $editor_options);

  $javascript = "new Ajax.InPlaceEditor(";

  $javascript .= "'$field_id', ";
  $javascript .= "'".url_for($url)."'";

  $js_options = array();

  if(isset($options['tokens']))
    $js_options['tokens'] = _array_or_string_for_javascript($options['tokens']);

  if(isset($options['cancel_text']))
  {
    $js_options['cancelText'] = "'".$options['cancel_text']."'";
  }
  if(isset($options['save_text']))
  {
    $js_options['okText'] = "'".$options['save_text']."'";
  }
  if(isset($options['cols']))
  {
    $js_options['cols'] = $options['cols'];
  }
  if(isset($options['rows']))
  {
    $js_options['rows'] = $options['rows'];
  }
  if(isset($options['external_control']))
  {
    $js_options['externalControl'] = "'".$options['external_control']."'";
  }
  if(isset($options['options']))
  {
    $js_options['ajaxOptions'] = $options['options'];
  }
  if(isset($options['with']))
  {
    $js_options['callback'] = "function(form, value) { return ".$options['with']." }";
  }
  if(isset($options['highlightcolor']))
  {
    $js_options['highlightcolor'] = "'".$options['highlightcolor']."'";
  }
  if(isset($options['highlightendcolor']))
  {
    $js_options['highlightendcolor'] = "'".$options['highlightendcolor']."'";
  }
  if(isset($options['loadTextURL']))
  {
    $js_options['loadTextURL'] = "'".$options['loadTextURL']."'";
  }

  $javascript .= ', '._options_for_javascript($js_options);
  $javascript .= ');';

  return javascript_tag($javascript);
}


function yui_link_to_dialog($name, $html_options = array())
{
  $html_options = _parse_attributes($html_options);

  $html_options['id'] = isset($html_options['id']) ? $html_options['id'] : generate_id($name);
  $html_options['href'] = isset($html_options['href']) ? $html_options['href'] : '#';

  ysfYUI::addEvent($html_options['id'], 'click', "
  var panel".$html_options['id']." = new YAHOO.widget.Panel('".$html_options['id']."-dialog',
  																			 { width: '240px',
  																			   fixedcenter: true,
  																			   visible: false,
  																			   close: true,
  																			   zindex: 10000,
  																			   modal: true,
  																			   constraintoviewport: true,
  																			 } );
  panel".$html_options['id'].".setHeader('Loading, please wait...');
  panel".$html_options['id'].".setBody('<img src=\"http://us.i1.yimg.com/us.yimg.com/i/us/per/gr/gp/rel_interstitial_loading.gif\" />');
  panel".$html_options['id'].".render(document.body);
  panel".$html_options['id'].".show();
");

  return content_tag('a', $name, $html_options);
}

function yui_link_to_confirm_dialog($name, $html_options = array())
{

  $html_options = _parse_attributes($html_options);

  $html_options['id'] = isset($html_options['id']) ? $html_options['id'] : generate_id($name);
  $html_options['href'] = isset($html_options['href']) ? $html_options['href'] : '#';

  ysfYUI::addEvent($html_options['id'], 'click', "
  var panel".$html_options['id']." = new YAHOO.widget.SimpleDialog('".$html_options['id']."-dialog',
  																			 { width: '300px',
  																			   fixedcenter: true,
  																			   visible: false,
  																			   close: true,
  																			   zindex: 10000,
  																			   modal: true,
  																			   text: 'Do you want to continue?',
  																			   icon: YAHOO.widget.SimpleDialog.ICON_HELP,
  																			   constraintoviewport: true,
  																			   buttons: [ { text: 'Yes', handler: function() { this.hide(); }, isDefault:true }, { text: 'No',  handler: function() { this.hide(); } } ]
  																			 } );
  panel".$html_options['id'].".setHeader('Are you sure?');
  panel".$html_options['id'].".render(document.body);
  panel".$html_options['id'].".show();
");

  return content_tag('a', $name, $html_options);
}

function yui_tooltip($id, $text = null, $options = array())
{
  ysfYUI::addComponents('container');

  $options['context'] = "'".$id."'";

  $id = 'ytooltip'.generate_id($id);

  if(!is_null($text))
  {
    $options['text'] = "'".escape_once($text)."'";
  }

  ysfYUI::addEvent('document', 'ready', "var ".$id." = new YAHOO.widget.Tooltip('".$id."', "._options_for_javascript($options).");");
}
