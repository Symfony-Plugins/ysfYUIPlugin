<?php

/**
 *
 * Copyright (c) 2008 Yahoo! Inc.  All rights reserved.
 * The copyrights embodied in the content in this file are licensed
 * under the MIT open source license.
 *
 * For the full copyright and license information, please view the LICENSE.yahoo
 * file that was distributed with this source code.
 */

/**
 * ysfYUI demo rich form.
 *
 * @package    ysymfony
 * @subpackage yui
 * @version    SVN: $Id$
 */
class ysfDemoForm extends ysfYUIForm
{

  public function configure()
  {
    parent::configure();

    $this->setDefault('radio', 'maybe');

    $this->setWidgets(array(
      'text'         => new ysfYUITextWidget(),
      'select'       => new ysfYUISelectWidget(array('choices' => array('billing', 'tech support', 'sales'))),
      'autocomplete' => new ysfYUITextWidget(array('datasource' => sfContext::getInstance()->getController()->genUrl('ysfYUI/autocomplete'))),
      'editor'       => new ysfYUIEditorWidget(),
      'radio'        => new ysfYUIRadioWidget(array('choices' => array('yes' => 'yes', 'no' => 'no', 'maybe' => 'maybe'))),
      'calendar'     => new ysfYUICalendarWidget(),
      'checkbox'     => new ysfYUICheckboxWidget(array(), array('value' => 'yui')),
      'checkbox2'    => new ysfYUICheckboxWidget(array(), array('value' => 'ojay', 'checked' => true)),
      'checkbox3'    => new ysfYUICheckboxWidget(array(), array('value' => 'dedchain')),
      'colorpicker'  => new ysfYUIColorPickerWidget(),
      'slider'       => new ysfYUISliderWidget(),
      'file'         => new ysfYUIFileUploadWidget(),
    ));

    $this->setValidators(array(
      'text'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'editor'    => new sfValidatorString(array('max_length' => 255, 'required' => false))
    ));

    $this->widgetSchema->setNameFormat('demo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }
}
