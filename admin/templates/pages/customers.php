<?php
/*
  $Id$

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
  echo '<form name="search" action="' . osc_href_link_admin(FILENAME_CUSTOMERS) . '" method="get">' .
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
  $Qcustomers = $osC_Database->query('select c.customers_id, c.customers_lastname, c.customers_firstname, c.customers_email_address, c.customers_status, c.customers_ip_address, c.date_account_created, c.date_account_last_modified, c.date_last_logon, c.number_of_logons, a.entry_country_id from :table_customers c left join :table_address_book a on (c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id)');
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
    if (!isset($cInfo) && (!isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $Qcustomers->value('customers_id'))))) {
      $Qreviews = $osC_Database->query('select count(*) as number_of_reviews from :table_reviews where customers_id = :customers_id');
      $Qreviews->bindTable(':table_reviews', TABLE_REVIEWS);
      $Qreviews->bindInt(':customers_id', $Qcustomers->valueInt('customers_id'));
      $Qreviews->execute();

      $cInfo_array = array_merge($Qcustomers->toArray(), $Qreviews->toArray());

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
      echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_CUSTOMERS, (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . 'page=' . $_GET['page'] . '&cID=' . $Qcustomers->valueInt('customers_id')) . '\';">' . "\n";
    }
?>
        <td><?php echo $Qcustomers->valueProtected('customers_lastname'); ?></td>
        <td><?php echo $Qcustomers->valueProtected('customers_firstname'); ?></td>
        <td><?php echo osC_DateTime::getShort($Qcustomers->value('date_account_created')); ?></td>
        <td align="center"><?php echo osc_icon(($Qcustomers->valueInt('customers_status') === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif', null, null); ?></td>
        <td align="right">
<?php
    echo '<a href="#" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_CUSTOMERS, (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . 'page=' . $_GET['page'] . '&cID=' . $Qcustomers->valueInt('customers_id') . '&action=cEdit') . '\';">' . osc_icon('configure.png', IMAGE_EDIT) . '</a>&nbsp;';

    if (isset($cInfo) && ($Qcustomers->valueInt('customers_id') == $cInfo->customers_id)) {
      echo '<a href="#" onclick="toggleInfoBox(\'cDelete\');">' . osc_icon('trash.png', IMAGE_DELETE) . '</a>&nbsp;';
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_CUSTOMERS, (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . 'page=' . $_GET['page'] . '&cID=' . $Qcustomers->valueInt('customers_id') . '&action=cDelete'), osc_icon('trash.png', IMAGE_DELETE)) . '&nbsp;';
    }

    echo '<a href="#" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_ORDERS, 'cID=' . $Qcustomers->valueInt('customers_id')) . '\';">' . osc_icon('orders.png', IMAGE_ORDERS) . '</a>';
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
      echo '<input type="button" value="' . IMAGE_RESET . '" class="operationButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_CUSTOMERS) . '\';"> ';
    }

    echo '<input type="button" value="' . IMAGE_INSERT . '" class="infoBoxButton" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_CUSTOMERS, 'page=' . $_GET['page'] . '&action=cNew') . '\';">';
?>
  </p>
</div>

<?php
  if (isset($cInfo)) {
?>

<div id="infoBox_cDelete" <?php if ($action != 'cDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $cInfo->customers_firstname . ' ' . $cInfo->customers_lastname; ?></div>
  <div class="infoBoxContent">
    <form name="cDelete" action="<?php echo osc_href_link_admin(FILENAME_CUSTOMERS, (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . 'page=' . $_GET['page'] . '&cID=' . $cInfo->customers_id . '&action=deleteconfirm'); ?>" method="post">

    <p><?php echo TEXT_DELETE_INTRO; ?></p>
    <p><?php echo '<b>' . $cInfo->customers_firstname . ' ' . $cInfo->customers_lastname . '</b>'; ?></p>

<?php
    if ($cInfo->number_of_reviews > 0) {
      echo '    <p>' . osc_draw_checkbox_field('delete_reviews', null, true) . ' ' . sprintf(TEXT_DELETE_REVIEWS, $cInfo->number_of_reviews) . '</p>';
    }
?>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'cDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  }
?>
