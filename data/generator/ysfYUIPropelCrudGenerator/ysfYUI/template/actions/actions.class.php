[?php

/**
 * <?php echo $this->getGeneratedModuleName() ?> actions.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage <?php echo $this->getGeneratedModuleName() ?>

 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: actions.class.php 2 2008-04-28 06:07:19Z dwhittle $
 */
class <?php echo $this->getGeneratedModuleName() ?>Actions extends sfActions
{
  public function executeIndex()
  {
    return $this->forward('<?php echo $this->getModuleName() ?>', 'list');
  }

  public function executeList()
  {
    $this-><?php echo $this->getPluralName() ?> = <?php echo $this->getClassName() ?>Peer::doSelect(new Criteria());
  }

  public function executeShow()
  {
    $this-><?php echo $this->getSingularName() ?> = <?php echo $this->getClassName() ?>Peer::retrieveByPk(<?php echo $this->getRetrieveByPkParamsForAction(49) ?>);
    $this->forward404Unless($this-><?php echo $this->getSingularName() ?>);
  }

  public function executeCreate()
  {
    $this-><?php echo $this->getSingularName() ?> = new <?php echo $this->getClassName() ?>();

    $this->setTemplate('edit');
  }

  public function executeEdit()
  {
    $this-><?php echo $this->getSingularName() ?> = <?php echo $this->getClassName() ?>Peer::retrieveByPk(<?php echo $this->getRetrieveByPkParamsForAction(49) ?>);
    $this->forward404Unless($this-><?php echo $this->getSingularName() ?>);
  }

  public function executeUpdate()
  {
    if (<?php echo $this->getTestPksForGetOrCreate(false) ?>)
    {
      $<?php echo $this->getSingularName() ?> = new <?php echo $this->getClassName() ?>();
    }
    else
    {
      $<?php echo $this->getSingularName() ?> = <?php echo $this->getClassName() ?>Peer::retrieveByPk(<?php echo $this->getRetrieveByPkParamsForAction(45) ?>);
      $this->forward404Unless($<?php echo $this->getSingularName() ?>);
    }

<?php foreach ($this->getTableMap()->getColumns() as $name => $column): $type = $column->getType(); ?>
<?php if ($name == 'CREATED_AT' || $name == 'UPDATED_AT') continue ?>
<?php $name = sfInflector::underscore($column->getPhpName()) ?>
<?php if ($type == PropelColumnTypes::DATE || $type == PropelColumnTypes::TIMESTAMP): ?>
    if ($this->getRequestParameter('<?php echo $name ?>'))
    {
      list($d, $m, $y) = $this->getContext()->getI18N()->getDateForCulture($this->getRequestParameter('<?php echo $name ?>'), $this->getUser()->getCulture());
      $<?php echo $this->getSingularName() ?>->set<?php echo $column->getPhpName() ?>("$y-$m-$d");
    }
<?php elseif ($type == PropelColumnTypes::BOOLEAN): ?>
    $<?php echo $this->getSingularName() ?>->set<?php echo $column->getPhpName() ?>($this->getRequestParameter('<?php echo $name ?>', 0));
<?php elseif ($column->isForeignKey()): ?>
    $<?php echo $this->getSingularName() ?>->set<?php echo $column->getPhpName() ?>($this->getRequestParameter('<?php echo $name ?>') ? $this->getRequestParameter('<?php echo $name ?>') : null);
<?php else: ?>
    $<?php echo $this->getSingularName() ?>->set<?php echo $column->getPhpName() ?>($this->getRequestParameter('<?php echo $name ?>'));
<?php endif; ?>
<?php endforeach; ?>

    $<?php echo $this->getSingularName() ?>->save();

    return $this->redirect('<?php echo $this->getModuleName() ?>/show?<?php echo $this->getPrimaryKeyUrlParams() ?>);
<?php //' ?>
  }

  public function executeDelete()
  {
    $<?php echo $this->getSingularName() ?> = <?php echo $this->getClassName() ?>Peer::retrieveByPk(<?php echo $this->getRetrieveByPkParamsForAction(43) ?>);

    $this->forward404Unless($<?php echo $this->getSingularName() ?>);

    $<?php echo $this->getSingularName() ?>->delete();

    return $this->redirect('<?php echo $this->getModuleName() ?>/list');
  }
}