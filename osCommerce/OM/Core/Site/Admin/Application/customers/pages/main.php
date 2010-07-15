<?php
/*
  $Id$

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

<form name="search" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT); ?>" method="get"><?php echo osc_draw_hidden_field($osC_Template->getModule()); ?>

<p align="right">

<?php
  echo $osC_Language->get('operation_title_search') . ' ' . osc_draw_input_field('search') . '<input type="submit" value="GO" class="operationButton" />' .
       '<input type="button" value="' . $osC_Language->get('button_insert') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&search=' . $_GET['search'] . '&action=save') . '\';" class="infoBoxButton" />';
?>

</p>

</form>

<?php
  $Qcustomers = $osC_Database->query('select c.customers_id, c.customers_gender, c.customers_lastname, c.customers_firstname, c.customers_email_address, c.customers_status, c.customers_ip_address, c.date_account_created, c.date_account_last_modified, c.date_last_logon, c.number_of_logons, a.entry_country_id from :table_customers c left join :table_address_book a on (c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id)');
  $Qcustomers->bindTable(':table_customers', TABLE_CUSTOMERS);
  $Qcustomers->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);

  if ( !empty($_GET['search']) ) {
    $Qcustomers->appendQuery('where c.customers_lastname like :customers_lastname or c.customers_firstname like :customers_firstname or c.customers_email_address like :customers_email_address');
    $Qcustomers->bindValue(':customers_lastname', '%' . $_GET['search'] . '%');
    $Qcustomers->bindValue(':customers_firstname', '%' . $_GET['search'] . '%');
    $Qcustomers->bindValue(':customers_email_address', '%' . $_GET['search'] . '%');
  }

  $Qcustomers->appendQuery('order by c.customers_lastname, c.customers_firstname');
  $Qcustomers->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qcustomers->execute();
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><?php echo $Qcustomers->getBatchTotalPages($osC_Language->get('batch_results_number_of_entries')); ?></td>
    <td align="right"><?php echo $Qcustomers->getBatchPageLinks('page', $osC_Template->getModule(), false); ?></td>
  </tr>
</table>

<form name="batch" action="#" method="post">

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
  <thead>
    <tr>
      <th width="20">&nbsp;</th>
      <th><?php echo $osC_Language->get('table_heading_last_name'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_first_name'); ?></th>
      <th><?php echo $osC_Language->get('table_heading_date_created'); ?></th>
      <th width="150"><?php echo $osC_Language->get('table_heading_action'); ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th align="right" colspan="5"><?php echo '<input type="image" src="' . osc_icon_raw('trash.png') . '" title="' . $osC_Language->get('icon_trash') . '" onclick="document.batch.action=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete') . '\';" />'; ?></th>
      <th align="center" width="20"><?php echo osc_draw_checkbox_field('batchFlag', null, null, 'onclick="flagCheckboxes(this);"'); ?></th>
    </tr>
  </tfoot>
  <tbody>

<?php
  while ( $Qcustomers->next() ) {
    $customer_icon = osc_icon('people.png');

    if ( ACCOUNT_GENDER > -1 ) {
      switch ( $Qcustomers->value('customers_gender') ) {
        case 'm':
          $customer_icon = osc_icon('user_male.png');

          break;

        case 'f':
          $customer_icon = osc_icon('user_female.png');

          break;
      }
    }
?>

    <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" <?php echo (($Qcustomers->valueInt('customers_status') !== 1) ? 'class="deactivatedRow"' : '') ?>>
      <td align="center"><?php echo $customer_icon; ?></td>
      <td onclick="document.getElementById('batch<?php echo $Qcustomers->valueInt('customers_id'); ?>').checked = !document.getElementById('batch<?php echo $Qcustomers->valueInt('customers_id'); ?>').checked;"><?php echo $Qcustomers->valueProtected('customers_lastname'); ?></td>
      <td><?php echo $Qcustomers->valueProtected('customers_firstname'); ?></td>
      <td><?php echo osC_DateTime::getShort($Qcustomers->value('date_account_created')); ?></td>
      <td align="right">

<?php
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&search=' . $_GET['search'] . '&page=' . $_GET['page'] . '&cID=' . $Qcustomers->valueInt('customers_id') . '&action=save'), osc_icon('edit.png')) . '&nbsp;' .
         osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&search=' . $_GET['search'] . '&page=' . $_GET['page'] . '&cID=' . $Qcustomers->valueInt('customers_id') . '&action=delete'), osc_icon('trash.png')) . '&nbsp;' .
         osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, 'orders&cID=' . $Qcustomers->valueInt('customers_id')), osc_icon('orders.png'));
?>

      </td>
      <td align="center"><?php echo osc_draw_checkbox_field('batch[]', $Qcustomers->valueInt('customers_id'), null, 'id="batch' . $Qcustomers->valueInt('customers_id') . '"'); ?></td>
    </tr>

<?php
    }
?>

  </tbody>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td style="opacity: 0.5; filter: alpha(opacity=50);"><?php echo '<b>' . $osC_Language->get('table_action_legend') . '</b> ' . osc_icon('edit.png') . '&nbsp;' . $osC_Language->get('icon_edit') . '&nbsp;&nbsp;' . osc_icon('trash.png') . '&nbsp;' . $osC_Language->get('icon_trash') . '&nbsp;&nbsp;' . osc_icon('orders.png') . '&nbsp;' . $osC_Language->get('icon_orders'); ?></td>
    <td align="right"><?php echo $Qcustomers->getBatchPagesPullDownMenu('page', $osC_Template->getModule()); ?></td>
  </tr>
</table>
