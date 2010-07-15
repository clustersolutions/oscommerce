<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\Site\Admin\Application\TaxClasses\TaxClasses;
  use osCommerce\OM\Core\OSCOM;
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . osc_link_object(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo osc_icon('trash.png') . ' ' . OSCOM::getDef('action_heading_batch_delete_tax_classes'); ?></h3>

  <form name="tcDeleteBatch" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'action=BatchDelete'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_batch_delete_tax_classes'); ?></p>

<?php
  $check_tax_classes_flag = array();

  $Qclasses = $OSCOM_Database->query('select tax_class_id, tax_class_title from :table_tax_class where tax_class_id in (":tax_class_id") order by tax_class_title');
  $Qclasses->bindRaw(':tax_class_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qclasses->execute();

  $names_string = '';

  while ( $Qclasses->next() ) {
    if ( TaxClasses::hasProducts($Qclasses->valueInt('tax_class_id')) ) {
      $check_tax_classes_flag[] = $Qclasses->value('tax_class_title');
    }

    $names_string .= osc_draw_hidden_field('batch[]', $Qclasses->valueInt('tax_class_id')) . '<b>' . $Qclasses->value('tax_class_title') . ' (' . sprintf(OSCOM::getDef('total_entries'), TaxClasses::getNumberOfTaxRates($Qclasses->valueInt('tax_class_id'))) . ')</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
  }

  echo '<p>' . $names_string . '</p>';

  if ( empty($check_tax_classes_flag) ) {
    echo '<p>' . osc_draw_button(array('priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::getDef('button_delete'))) . ' ' . osc_draw_button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))) . '</p>';
  } else {
    echo '<p><b>' . OSCOM::getDef('batch_delete_warning_tax_class_in_use') . '</b></p>' .
         '<p>' . implode(', ', $check_tax_classes_flag) . '</p>';

    echo '<p>' . osc_draw_button(array('href' => OSCOM::getLink(), 'icon' => 'triangle-1-w', 'title' => OSCOM::getDef('button_back'))) . '</p>';
  }
?>

  </form>
</div>
