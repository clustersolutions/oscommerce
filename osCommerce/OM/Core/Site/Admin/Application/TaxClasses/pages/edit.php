<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\ObjectInfo;
  use osCommerce\OM\Core\Site\Admin\Application\TaxClasses\TaxClasses;
  use osCommerce\OM\Core\OSCOM;

  $OSCOM_ObjectInfo = new ObjectInfo(TaxClasses::get($_GET['id']));
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('edit.png') . ' ' . $OSCOM_ObjectInfo->getProtected('tax_class_title'); ?></h3>

  <form name="tcEdit" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'Save&Process&id=' . $OSCOM_ObjectInfo->getInt('tax_class_id')); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_edit_tax_class'); ?></p>

  <fieldset>
    <p><label for="tax_class_title"><?php echo OSCOM::getDef('field_title'); ?></label><?php echo osc_draw_input_field('tax_class_title', $OSCOM_ObjectInfo->get('tax_class_title')); ?></p>
    <p><label for="tax_class_description"><?php echo OSCOM::getDef('field_description'); ?></label><?php echo osc_draw_input_field('tax_class_description', $OSCOM_ObjectInfo->get('tax_class_description')); ?></p>
  </fieldset>

  <p><?php echo osc_draw_button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
