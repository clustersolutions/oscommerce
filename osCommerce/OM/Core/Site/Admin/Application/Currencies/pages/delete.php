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
  use osCommerce\OM\Core\Site\Admin\Application\Currencies\Currencies;

  $OSCOM_ObjectInfo = new ObjectInfo(Currencies::get($_GET['id']));
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('trash.png') . ' ' . $OSCOM_ObjectInfo->getProtected('title'); ?></h3>

<?php
  if ( $OSCOM_ObjectInfo->get('code') == DEFAULT_CURRENCY ) {
?>

  <p><?php echo '<b>' . OSCOM::getDef('introduction_delete_currency_invalid') . '</b>'; ?></p>

  <p><?php echo HTML::button(array('href' => OSCOM::getLink(), 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))); ?></p>

<?php
  } else {
?>

  <form name="cDelete" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'Delete&Process&id=' . $_GET['id']); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_delete_currency'); ?></p>

  <p><?php echo '<b>' . $OSCOM_ObjectInfo->getProtected('title') . '</b>'; ?></p>

  <p><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::getDef('button_delete'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>

<?php
  }
?>

</div>
