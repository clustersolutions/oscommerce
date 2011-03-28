<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\ObjectInfo;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Admin\Application\Countries\Countries;

  $OSCOM_ObjectInfo = new ObjectInfo(Countries::get($_GET['id']));
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('edit.png') . ' ' . $OSCOM_ObjectInfo->getProtected('countries_name'); ?></h3>

  <form name="cEdit" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'Save&Process&id=' . $_GET['id']); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_edit_country'); ?></p>

  <fieldset>
    <p><label for="countries_name"><?php echo OSCOM::getDef('field_name'); ?></label><?php echo HTML::inputField('countries_name', $OSCOM_ObjectInfo->get('countries_name')); ?></p>
    <p><label for="countries_iso_code_2"><?php echo OSCOM::getDef('field_iso_code_2'); ?></label><?php echo HTML::inputField('countries_iso_code_2', $OSCOM_ObjectInfo->get('countries_iso_code_2')); ?></p>
    <p><label for="countries_iso_code_3"><?php echo OSCOM::getDef('field_iso_code_3'); ?></label><?php echo HTML::inputField('countries_iso_code_3', $OSCOM_ObjectInfo->get('countries_iso_code_3')); ?></p>
    <p><label for="address_format"><?php echo OSCOM::getDef('field_address_format'); ?></label><?php echo HTML::textareaField('address_format', $OSCOM_ObjectInfo->get('address_format')); ?><br /><i>:name</i>, <i>:street_address</i>, <i>:suburb</i>, <i>:city</i>, <i>:postcode</i>, <i>:state</i>, <i>:state_code</i>, <i>:country</i></p>
  </fieldset>

  <p><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
