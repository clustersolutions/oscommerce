<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->get($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_Language->get('action_heading_batch_delete_customers'); ?></div>
<div class="infoBoxContent">
  <form name="cDeleteBatch" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&search=' . $_GET['search'] . '&page=' . $_GET['page'] . '&action=batchDelete'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_batch_delete_customers'); ?></p>

<?php
  $check_reviews_flag = false;

  $Qcustomers = $osC_Database->query('select customers_id, customers_firstname, customers_lastname from :table_customers where customers_id in (":customers_id") order by customers_firstname, customers_lastname');
  $Qcustomers->bindTable(':table_customers', TABLE_CUSTOMERS);
  $Qcustomers->bindRaw(':customers_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qcustomers->execute();

  $names_string = '';

  while ( $Qcustomers->next() ) {
    $Qreviews = $osC_Database->query('select count(*) as total from :table_reviews where customers_id = :customers_id');
    $Qreviews->bindTable(':table_reviews', TABLE_REVIEWS);
    $Qreviews->bindInt(':customers_id', $Qcustomers->valueInt('customers_id'));
    $Qreviews->execute();

    $customer_name = $Qcustomers->valueProtected('customers_firstname') . ' ' . $Qcustomers->valueProtected('customers_lastname');

    if ( $Qreviews->valueInt('total') > 0 ) {
      if ( $check_reviews_flag === false ) {
        $check_reviews_flag = true;
      }

      $customer_name .= ' (' . sprintf($osC_Language->get('total_reviews'), $Qreviews->valueInt('total')) . ')';
    }

    $names_string .= osc_draw_hidden_field('batch[]', $Qcustomers->valueInt('customers_id')) . '<b>' . $customer_name . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2);
  }

  echo '  <p>' . $names_string . '</p>';

  if ( $check_reviews_flag === true ) {
    echo '  <p>' . osc_draw_checkbox_field('delete_reviews', null, true) . ' ' . $osC_Language->get('field_delete_reviews') . '</p>';
  }
?>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&search=' . $_GET['search'] . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
