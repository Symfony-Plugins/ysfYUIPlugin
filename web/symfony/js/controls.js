/**
* controls.js - Contains all custom controls
*
* @namespace YAHOO.symfony
* @author    dustin.whittle@symfony-project.com
* @version   0.1
*/

/*
 * Warning: This needs to be totally rewritten.
 */
/**
 * Check for YUI + symfony availability
*/

/**
 * YAHOO.symfony.controls - symfony controls
 */
YAHOO.symfony.controls =
{

  initialize: function()
  {
    YAHOO.symfony.event.ready.subscribe(YAHOO.symfony.control.collapse.initialize);
  },

  /**
   * doublle list widget
   */
  double_list:
  {
    move: function (src, dest)
    {
      for (var i = 0; i < src.options.length; i++)
      {
        if (src.options[i].selected)
        {
          dest.options[dest.length] = new Option(src.options[i].text, src.options[i].value);
          src.options[i] = null;
          --i;
        }
      }
    },

    submit: function()
    {
      var form = YAHOO.util.Dom.get('sf_admin_edit_form');
      var element;

      // find multiple selects with name beginning 'associated_' and select all their options
      for (var i = 0; i < form.elements.length; i++)
      {
        element = form.elements[i];
        if (element.type == 'select-multiple')
        {
          if (element.className == 'sf_admin_multiple-selected')
          {
            for (var j = 0; j < element.options.length; j++)
            {
              element.options[j].selected = true;
            }
          }
        }
      }
    }
  },

  /**
   * Finds all fieldsets with class="collapse", collapses them, and gives each
   * one a "show" link that uncollapses it. The "show" link becomes a "hide"
   * link when the fieldset is visible.
   */
  collapse:
  {
    collapse_re: /\bcollapse\b/,   // Class of fieldsets that should be dealt with.
    collapsed_re: /\bcollapsed\b/, // Class that fieldsets get when they're hidden.
    collapsed_class: 'collapsed',

    initialize: function()
    {
      var fieldsets = document.getElementsByTagName('fieldset');
      var collapsed_seen = false;

      for (var i = 0, fs; fs = fieldsets[i]; i++)
      {
        // Collapse this fieldset if it has the correct class, and if it
        // doesn't have any errors. (Collapsing shouldn't apply in the case
        // of error messages.)

        if (fs.className.match(YAHOO.symfony.control.collapse.collapse_re) && !YAHOO.symfony.control.collapse.fieldset_has_errors(fs))
        {
          collapsed_seen = true;
          // Give it an additional class, used by CSS to hide it.
          fs.className += ' ' + YAHOO.symfony.control.collapse.collapsed_class;

          var collapse_link = document.createElement('a');
          collapse_link.className = 'collapse-toggle';
          collapse_link.id = 'fieldsetcollapser' + i;
          collapse_link.href = '#';
          collapse_link.innerHTML = 'show';

          var h2 = fs.getElementsByTagName('h2')[0];
          h2.appendChild(document.createTextNode(' ['));
          h2.appendChild(collapse_link);
          h2.appendChild(document.createTextNode(']'));

          YAHOO.util.Event.addListener(collapse_link.id, 'click', function(e) { YAHOO.symfony.control.collapse.show('+i+'); YAHOO.util.Event.preventDefault(e); });
        }
      }
      if (collapsed_seen)
      {
        // Expand all collapsed fieldsets when form is submitted.
        YAHOO.util.Event.addListener(YAHOO.symfony.util.find(document.getElementsByTagName('fieldset')[0]), 'submit', function() { YAHOO.symfony.control.collapse.uncollapse_all(); });
      }
    },

    fieldset_has_errors: function(fs)
    {
      // Returns true if any fields in the fieldset have validation errors.
      var divs = fs.getElementsByTagName('div');
      for (var i=0; i<divs.length; i++)
      {
        if (divs[i].className.match(/\bform-error\b/))
        {
          return true;
        }
      }
      return false;
    },

    show: function(fieldset_index)
    {
      var fs = document.getElementsByTagName('fieldset')[fieldset_index];

      // Remove the class name that causes the "display: none".
      fs.className = fs.className.replace(YAHOO.symfony.control.collapse.collapsed_re, '');

      // Toggle the "show" link to a "hide" link
      var collapse_link = YAHOO.util.Dom.get('fieldsetcollapser' + fieldset_index);
      collapse_link.innerHTML = 'hide';

      YAHOO.util.Event.addListener(collapse_link.id, 'click', function(e) { YAHOO.symfony.control.collapse.hide('+fieldset_index+'); YAHOO.util.Event.preventDefault(e); });
    },

    hide: function(fieldset_index)
    {
      var fs = document.getElementsByTagName('fieldset')[fieldset_index];
      // Add the class name that causes the "display: none".
      fs.className += ' ' + YAHOO.symfony.control.collapse.collapsed_class;
      // Toggle the "hide" link to a "show" link
      var collapse_link = YAHOO.util.Dom.get('fieldsetcollapser' + fieldset_index);
      collapse_link.innerHTML = 'show';

      YAHOO.util.Event.addListener(collapse_link.id, 'click', function(e) { YAHOO.symfony.control.collapse.show('+fieldset_index+'); YAHOO.util.Event.preventDefault(e); });
    },

    uncollapse_all: function()
    {
      var fieldsets = document.getElementsByTagName('fieldset');
      for (var i=0; i<fieldsets.length; i++)
      {
        if (fieldsets[i].className.match(YAHOO.symfony.control.collapse.collapsed_re))
        {
          YAHOO.symfony.control.collapse.show(i);
        }
      }
    }
  }

};
