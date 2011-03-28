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
  use osCommerce\OM\Core\Site\Admin\Application\Configuration\Configuration;
  use osCommerce\OM\Core\Site\Admin\Application\PaymentModules\PaymentModules;

  $OSCOM_ObjectInfo = new ObjectInfo(PaymentModules::get($_GET['code']));
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('edit.png') . ' ' . $OSCOM_ObjectInfo->getProtected('title'); ?></h3>

  <form name="pmEdit" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'Save&Process&code=' . $OSCOM_ObjectInfo->get('code')); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_edit_payment_module'); ?></p>

  <fieldset>

<?php
  $keys = '';

  foreach ( $OSCOM_ObjectInfo->get('keys') as $key ) {
    $Qkey = $OSCOM_PDO->prepare('select configuration_title, configuration_value, configuration_description, use_function, set_function from :table_configuration where configuration_key = :configuration_key');
    $Qkey->bindValue(':configuration_key', $key);
    $Qkey->execute();

    $keys .= '<p><label for="' . $key . '">' . $Qkey->value('configuration_title') . '</label><br />' . $Qkey->value('configuration_description');

    if ( strlen($Qkey->value('set_function')) > 0 ) {
      $keys .= Configuration::callUserFunc($Qkey->value('set_function'), $Qkey->value('configuration_value'), $key);
    } else {
      $keys .= HTML::inputField('configuration[' . $key . ']', $Qkey->value('configuration_value'));
    }

    $keys .= '</p>';
  }

  echo $keys;
?>

  </fieldset>

  <p><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'check', 'title' => OSCOM::getDef('button_save'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
