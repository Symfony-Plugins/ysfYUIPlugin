/**
 * forms.js - Contains all symfony form related controls for creating new form controls and handling validation.
 *
 * @namespace YAHOO.symfony
 * @author    dustin.whittle@gmail.com
 * @version   0.1
 */

/**
 * Check for YUI + symfony availability
 */

/**
 * symfony-form - symfony form tools
 */
YAHOO.symfony.forms =
{
  initialize: function()
  {
    YAHOO.symfony.forms.validation.initialize()
    // YAHOO.symfony.forms.control.initialize();
  }
};
YAHOO.symfony.event.ready.subscribe(YAHOO.symfony.forms.initialize);

YAHOO.symfony.forms.config =
{
  stopOnFirstError:  true
}


/**
 * symfony-form - symfony form utilities
 */
YAHOO.symfony.forms.util =
{
  find: function(node)
  {
    // returns the node of the form containing the given node
    if (node.tagName.toLowerCase() != 'form')
    {
      return findForm(node.parentNode);
    }

    return node;
  }
};


/**
 * symfony-form - symfony form validation
 */
YAHOO.symfony.forms.validation =
{
  initialize: function()
  {
    // add event to all forms on page triggered on submit
    YAHOO.util.Dom.batch(YAHOO.util.Dom.getElementsByClassName('validate', 'form'), function(element)
    {
      YAHOO.util.Event.addListener(element, "submit", function(element) { YAHOO.symfony.forms.validation.validate(element, {}); });

      if(YAHOO.symfony.forms.config.stopOnFirstError == true)
      {
        YAHOO.util.Dom.batch(YAHOO.util.Dom.getElementsByClassName('required', 'input', element), function(element) { YAHOO.util.Event.addListener(element, "blur", function(event) { YAHOO.symfony.forms.validation.validate(element, {}); } ) } );
        YAHOO.util.Dom.batch(YAHOO.util.Dom.getElementsByClassName('required', 'checkbox', element), function(element) { YAHOO.util.Event.addListener(element, "blur", function(event) { YAHOO.symfony.forms.validation.validate(element, {}); } ) } );
        YAHOO.util.Dom.batch(YAHOO.util.Dom.getElementsByClassName('required', 'radio', element), function(element) { YAHOO.util.Event.addListener(element, "blur", function(event) { YAHOO.symfony.forms.validation.validate(element, {}); } ) } );
      }
    });

    var form = document.getElementsByTagName('form');
    if(form.length > 0)
    {
      for(var i = 0; i < form.length; i++)
      {

      }

      YAHOO.util.Dom.batch(YAHOO.util.Dom.getElementsByClassName('required'), function(e) { YAHOO.symfony.forms.validation.validate } )
    }
    

    YAHOO.log('form validation initialized', 'info', 'symfony');
  },

  required: function(value)
  {
	  return ((value == null) || (value.length == 0));
	},

  configure: function(parameters)
  {
    if(typeof parameters !== 'undefined' && typeof parameters == 'object')
    {

    }
  },

  validate: function(element, config)
  {
    // check test (need to move to validators object and add for dynamic valiations)
    var validated = YAHOO.symfony.forms.validation.required(element.value);
    var advice_id = 'yui-validation-advice-for-' + element.name;

    if(validated === true)
    {
      // YAHOO.util.Event.preventDefault(element);

      YAHOO.util.Dom.addClass(element, 'validation-failed');

      var advice = YAHOO.util.Dom.get(advice_id);
      if(advice)
      {
        advice.style.dislay = 'block';
      }
      else
      {
        var validationMessage = document.createElement('div');
            validationMessage.id = advice_id;
            validationMessage.innerHTML = 'The field is required.';
            validationMessage.style.display = 'block';

        YAHOO.util.Dom.addClass(validationMessage, 'validation-advice');
        YAHOO.util.Dom.insertAfter(validationMessage, element);
      }
    }
    else
    {
     var advice = YAHOO.util.Dom.get(advice_id);
     if(advice)
     {
        advice.style.display = 'none';
     }
    }
  }
}
