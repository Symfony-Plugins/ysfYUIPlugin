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
 * TagHelper defines some base helpers to construct html tags.
 *
 * @package    ysymfony
 * @subpackage helper
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     David Heinemeier Hansson
 * @author     Dustin Whittle <dustin.whittle@symfony-project.com>
 * @version    SVN: $Id$
 *
 * @link       http://developer.yahoo.com/
 */

ysfYUI::addComponents('dom', 'event');

/**
 * Constructs an html tag.
 *
 * @param  string $name     tag name
 * @param  array  $options  tag options
 * @param  bool   $open     true to leave tag open
 * @return string
 */

function tag($name, $options = array(), $open = false)
{
  if (!$name)
  {
    return '';
  }

  return '<'.$name._tag_options($options).(($open || (sfConfig::get('sf_use_xhtml_tags', true) === false)) ? '>' : ' />');
}

function content_tag($name, $content = '', $options = array())
{
  if (!$name)
  {
    return '';
  }

  return '<'.$name._tag_options($options).'>'.$content.'</'.$name.'>';
}

function cdata_section($content)
{
  return "<![CDATA[$content]]>";
}

function conditional($condition, $content)
{
  return '<!--[if '.$condition.']>'."\n".$content.'<![endif]-->'."\n";
}

/**
 * Escape carrier returns and single and double quotes for Javascript segments.
 */
function escape_javascript($javascript = '')
{
  $javascript = preg_replace('/\r\n|\n|\r/', "\\n", $javascript);
  $javascript = preg_replace('/(["\'])/', '\\\\\1', $javascript);

  return $javascript;
}

/**
 * Escapes an HTML string.
 *
 * @param  string $html HTML string to escape
 * @return string escaped string
 */
function escape_once($html)
{
  return fix_double_escape(htmlspecialchars($html, ENT_COMPAT, sfConfig::get('sf_charset')));
}

/**
 * Fixes double escaped strings.
 *
 * @param  string $escaped HTML string to fix
 * @return string fixed escaped string
 */
function fix_double_escape($escaped)
{
  return preg_replace('/&amp;([a-z]+|(#\d+)|(#x[\da-f]+));/i', '&$1;', $escaped);
}

function _tag_options($options = array(), $filter = false)
{
  $options = _parse_attributes($options);

  $id = isset($options['id']) ? $options['id'] : false;

  $html = '';
  foreach ($options as $key => $value)
  {
    if(strpos($key, 'on') !== 0 || $filter)
    {
      // regular attribute
      $html .= ' '.$key.'="'.escape_once($value).'"';
    }
    else
    {
      // on* event attribute
      if(!$id)
      {
        $id = md5(rand(0, 100000));
        $html .= ' id="'.$id.'"';
      }

      if(is_array($value))
      {
        $events = array();
        foreach($value as $event)
        {
          $events[] = $event;
        }

        $events = implode(' ,', $events);
      }
      else
      {
        $events = $value;
      }

      ysfYUI::addEvent($id, substr($key, 2, strlen($key) - 2), 'YAHOO.util.Event.preventDefault(e); '.$events);
    }
  }

  return $html;
}

function _parse_attributes($string)
{
  return is_array($string) ? $string : sfToolkit::stringToArray($string);
}

function _get_option(&$options, $name, $default = null)
{
  if (array_key_exists($name, $options))
  {
    $value = $options[$name];
    unset($options[$name]);
  }
  else
  {
    $value = $default;
  }

  return $value;
}

/**
 * Returns a formatted ID based on the <i>$name</i> parameter and optionally the <i>$value</i> parameter.
 *
 * This function determines the proper form field ID name based on the parameters. If a form field has an
 * array value as a name we need to convert them to proper and unique IDs like so:
 * <samp>
 *  name[] => name (if value == null)
 *  name[] => name_value (if value != null)
 *  name[bob] => name_bob
 *  name[item][total] => name_item_total
 * </samp>
 *
 * <b>Examples:</b>
 * <code>
 *  echo get_id_from_name('status[]', '1');
 * </code>
 *
 * @param  string $name   field name
 * @param  string $value  field value
 *
 * @return string <select> tag populated with all the languages in the world.
 */
function get_id_from_name($name, $value = null)
{
  // check to see if we have an array variable for a field name
  if (strstr($name, '['))
  {
    $name = str_replace(array('[]', '][', '[', ']'), array((($value != null) ? '_'.$value : ''), '_', '_', ''), $name);
  }

  return $name;
}

/**
 * Converts specific <i>$options</i> to their correct HTML format
 *
 * @param  array $options
 * @return array returns properly formatted options
 */
function _convert_options($options)
{
  $options = _parse_attributes($options);

  foreach (array('disabled', 'readonly', 'multiple') as $attribute)
  {
    if (array_key_exists($attribute, $options))
    {
      if ($options[$attribute])
      {
        $options[$attribute] = $attribute;
      }
      else
      {
        unset($options[$attribute]);
      }
    }
  }

  return $options;
}


/**
 * Generates short random id for element.
 */
function generate_id($name = null)
{
  static $i;

  $i++;

  return  'y'.$i.'id';
}
