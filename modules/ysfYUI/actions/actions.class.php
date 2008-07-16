<?php

/**
 * ysfYUI Actions.
 *
 * @package    symfony
 * @subpackage actions
 * @author     Dustin Whittle <dustin.whittle@gmail.com>
 * @version    SVN: $Id: actions.class.php 2 2008-04-28 06:07:19Z dwhittle $
 */
class ysfYUIActions extends sfActions
{
  /**
   * Executes studio action
   */
  public function executeStudio($request)
  {
    $this->form = new ysfDemoForm();
  }

  /**
   * Executes ajax content action
   */
  public function executeAjaxContent($request)
  {
    return $this->renderText('Ajax Content! - '.time()."\n\n".var_export($request->getParameterHolder()->getAll(), true));
  }

  /**
   * Executes auto complete
   *
   * @param sfRequest $request
   */
  public function executeAutocomplete($request)
  {
    $json = new stdclass();
    $json->ResultSet = new stdclass();

    for($i=0; $i<rand(2, 12); $i++)
    {
      $json->ResultSet->Result[$i] = new stdclass();
      $json->ResultSet->Result[$i]->Title = $request->getParameter('query').' - '.$i;
    }


    return $this->renderText(json_encode($json));
  }

  /**
   * Executes validate rich text editor action
   *
   */
  public function executeRichTextEditor($request)
  {

    $form = new CommentForm();
    $form->bind($request->getParameter('editor'));

    // setup json response
    $this->getResponse()->setContentType('application/json');

    $json = new stdclass();
    $json->Results = new stdclass();

    // filter + validate
    if($form->isValid())
    {
      $values = $form->getValues();

      $json->Results->status = 'OK';

      $json->Results->filter = $values['filter'];
      $json->Results->raw_data = $values['data'];

      // filter any bad tags - use ext/filter html
      $json->Results->data = strip_tags($values['data'], '<'.implode('><', sfConfig::get('yui_rich_text_editor_allowed_tags', array('b','strong','i','em','u','a','p','sup','sub','div','img','span','font','br','ul','ol','li'))).'>');
    }
    else
    {
      $json->Results->status = 'FAILURE';

      $json->Results->filter = false;

      $json->Results->raw_data = '';
      $json->Results->data = '';
    }

    return $this->renderText(json_encode($data));
  }

}
