<?php
/*
  $Id: customers.php,v 1.3 2004/08/17 23:30:51 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><h1><?php echo HEADING_TITLE; ?></h1></td>
    <td class="smallText" align="right">
<?php
  echo tep_draw_form('search', FILENAME_CUSTOMERS, '', 'get') .
       HEADING_TITLE_SEARCH . ' ' . osc_draw_input_field('search') .
       '<input type="submit" value="GO" class="operationButton"></form>';
?>
    </td>
  </tr>
</table>

<div id="infoBox_cDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_LASTNAME; ?></th>
        <th><?php echo TABLE_HEADING_FIRSTNAME; ?></th>
        <th><?php echo TABLE_HEADING_ACCOUNT_CREATED; ?></th>
        <th><?php echo TABLE_HEADING_STATUS; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  $Qcustomers = $osC_Database->query('select c.customers_id, c.customers_lastname, c.customers_firstname, c.customers_email_address, c.customers_status, c.customers_ip_address, a.entry_country_id from :table_customers c left join :table_address_book a on (c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id)');
  $Qcustomers->bindTable(':table_customers', TABLE_CUSTOMERS);
  $Qcustomers->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
  if (isset($_GET['search']) && !empty($_GET['search'])) {
    $Qcustomers->appendQuery('where c.customers_lastname like :customers_lastname or c.customers_firstname like :customers_firstname or c.customers_email_address like :customers_email_address');
    $Qcustomers->bindValue(':customers_lastname', '%' . $_GET['search'] . '%');
    $Qcustomers->bindValue(':customers_firstname', '%' . $_GET['search'] . '%');
    $Qcustomers->bindValue(':customers_email_address', '%' . $_GET['search'] . '%');
  }
  $Qcustomers->appendQuery('order by c.customers_lastname, c.customers_firstname');
  $Qcustomers->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qcustomers->execute();

  while ($Qcustomers->next()) {
    $Qinfo = $osC_Database->query('select customers_info_date_account_created as date_account_created, customers_info_date_account_last_modified as date_account_last_modified, customers_info_date_of_last_logon as date_last_logon, customers_info_number_of_logons as number_of_logons from :table_customers_info where customers_info_id = :customers_info_id');
    $Qinfo->bindTable(':table_customers_info', TABLE_CUSTOMERS_INFO);
    $Qinfo->bindInt(':customers_info_id', $Qcustomers->valueInt('customers_id'));
    $Qinfo->execute();

    if (!isset($cInfo) && (!isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $Qcustomers->value('customers_id'))))) {
      $Qreviews = $osC_Database->query('select count(*) as number_of_reviews from :table_reviews where customers_id = :customers_id');
      $Qreviews->bindTable(':table_reviews', TABLE_REVIEWS);
      $Qreviews->bindInt(':customers_id', $Qcustomers->valueInt('customers_id'));
      $Qreviews->execute();

      $cInfo_array = array_merge($Qcustomers->toArray(), $Qinfo->toArray(), $Qreviews->toArray());

      if ($Qcustomers->valueInt('entry_country_id') > 0) {
        $Qcountry = $osC_Database->query('select countries_name from :table_countries where countries_id = :countries_id');
        $Qcountry->bindTable(':table_countries', TABLE_COUNTRIES);
        $Qcountry->bindInt(':countries_id', $Qcustomers->valueInt('entry_country_id'));
        $Qcountry->execute();

        $cInfo_array = array_merge($cInfo_array, $Qcountry->toArray());
      }

      $cInfo = new objectInfo($cInfo_array);
    }

    if (isset($cInfo) && ($Qcustomers->valueInt('customers_id') == $cInfo->customers_id)) {
      echo '      <tr class="selected">' . "\n";
    } else {
      echo '      <tr onMouseOver="rowOverEffect(this);" onMouseOut="rowOutEffect(this);" onClick="document.location.href=\'' . tep_href_link(FILENAME_CUSTOMERS, (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . 'page=' . $_GET['page'] . '&cID=' . $Qcustomers->valueInt('customers_id')) . '\';">' . "\n";
    }
?>
        <td><?php echo $Qcustomers->valueProtected('customers_lastname'); ?></td>
        <td><?php echo $Qcustomers->valueProtected('customers_firstname'); ?></td>
        <td><?php echo tep_date_short($Qinfo->value('date_account_created')); ?></td>
        <td align="center"><?php echo tep_image('templates/' . $template . '/images/icons/' . (($Qcustomers->valueInt('customers_status') === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif')); ?></td>
        <td align="right">
<?php
    echo '<a href="#" onClick="document.location.href=\'' . tep_href_link(FILENAME_CUSTOMERS, (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . 'page=' . $_GET['page'] . '&cID=' . $Qcustomers->valueInt('customers_id') . '&action=cEdit') . '\';">' . tep_image('templates/' . $template . '/images/icons/16x16/configure.png', IMAGE_EDIT, '16', '16') . '</a>&nbsp;';

    if (isset($cInfo) && ($Qcustomers->valueInt('customers_id') == $cInfo->customers_id)) {
      echo '<a href="#" onClick="toggleInfoBox(\'cDelete\');">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>&nbsp;';
    } else {
      echo '<a href="' . tep_href_link(FILENAME_CUSTOMERS, (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . 'page=' . $_GET['page'] . '&cID=' . $Qcustomers->valueInt('customers_id') . '&action=cDelete') . '">' . tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . '</a>&nbsp;';
    }

    echo '<a href="#" onClick="document.location.href=\'' . tep_href_link(FILENAME_ORDERS, 'cID=' . $Qcustomers->valueInt('customers_id')) . '\';">' . tep_image('templates/' . $template . '/images/icons/16x16/orders.png', IMAGE_ORDERS, '16', '16') . '</a>';
?>
        </td>
      </tr>
<?php
    }
?>
    </tbody>
  </table>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td class="smallText"><?php echo $Qcustomers->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
      <td class="smallText" align="right"><?php echo $Qcustomers->displayBatchLinksPullDown(); ?></td>
    </tr>
  </table>

  <p align="right">
<?php
    if (isset($_GET['search']) && !empty($_GET['search'])) {
      echo '<input type="button" value="' . IMAGE_RESET . '" class="operationButton" onClick="document.location.href=\'' . tep_href_link(FILENAME_CUSTOMERS) . '\';"> ';
    }

    echo '<input type="button" value="' . IMAGE_INSERT . '" class="infoBoxButton" onClick="document.location.href=\'' . tep_href_link(FILENAME_CUSTOMERS, 'page=' . $_GET['page'] . '&action=cNew') . '\';">';
?>
  </p>
</div>

<?php
  if (isset($cInfo)) {
?>

<div id="infoBox_cDelete" <?php if ($action != 'cDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/trash.png', IMAGE_DELETE, '16', '16') . ' ' . $cInfo->customers_firstname . ' ' . $cInfo->customers_lastname; ?></div>
  <div class="infoBoxContent">
    <?php echo tep_draw_form('cDelete', FILENAME_CUSTOMERS, (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . 'page=' . $_GET['page'] . '&cID=' . $cInfo->customers_id . '&action=deleteconfirm'); ?>

    <p><?php echo TEXT_DELETE_INTRO; ?></p>
    <p><?php echo '<b>' . $cInfo->customers_firstname . ' ' . $cInfo->customers_lastname . '</b>'; ?></p>

<?php
    if ($cInfo->number_of_reviews > 0) {
      echo '    <p>' . osc_draw_checkbox_field('delete_reviews', '', true) . ' ' . sprintf(TEXT_DELETE_REVIEWS, $cInfo->number_of_reviews) . '</p>';
    }
?>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onClick="toggleInfoBox(\'cDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  }
?>
