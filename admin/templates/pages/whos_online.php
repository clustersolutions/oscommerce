<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  require('../includes/classes/currencies.php');
  $osC_Currencies = new osC_Currencies();

  require('includes/classes/tax.php');
  $osC_Tax = new osC_Tax_Admin();

  $osC_Weight = new osC_Weight();

  require('../includes/classes/customer.php');
  require('../includes/classes/navigation_history.php');
  require('../includes/classes/shopping_cart.php');

  require('includes/classes/ip_locator.php');

  $xx_mins_ago = (time() - 900);

// remove entries that have expired
  $Qdelete = $osC_Database->query('delete from :table_whos_online where time_last_click < :time_last_click');
  $Qdelete->bindTable(':table_whos_online', TABLE_WHOS_ONLINE);
  $Qdelete->bindValue(':time_last_click', $xx_mins_ago);
  $Qdelete->execute();
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<div id="infoBox_wDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_ONLINE; ?></th>
        <th><?php echo TABLE_HEADING_CUSTOMER_ID; ?></th>
        <th><?php echo TABLE_HEADING_FULL_NAME; ?></th>
        <th><?php echo TABLE_HEADING_IP_ADDRESS; ?></th>
        <th><?php echo TABLE_HEADING_LAST_CLICK; ?></th>
        <th><?php echo TABLE_HEADING_LAST_PAGE_URL; ?></th>
        <th><?php echo TABLE_HEADING_SHOPPING_CART_TOTAL; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  $Qwho = $osC_Database->query('select customer_id, full_name, ip_address, time_entry, time_last_click, session_id from :table_whos_online order by time_last_click desc');
  $Qwho->bindTable(':table_whos_online', TABLE_WHOS_ONLINE);
  $Qwho->execute();

  while ($Qwho->next()) {
    if (STORE_SESSIONS == 'mysql') {
      $Qsession = $osC_Database->query('select value from :table_sessions where sesskey = :sesskey');
      $Qsession->bindTable(':table_sessions', TABLE_SESSIONS);
      $Qsession->bindValue(':sesskey', $Qwho->value('session_id'));
      $Qsession->execute();

      $session_data = trim($Qsession->value('value'));
    } else {
      if ( (file_exists($osC_Session->getSavePath() . '/sess_' . $Qwho->value('session_id'))) && (filesize($osC_Session->getSavePath() . '/sess_' . $Qwho->value('session_id')) > 0) ) {
        $session_data = trim(file_get_contents($osC_Session->getSavePath() . '/sess_' . $Qwho->value('session_id')));
      }
    }

    $navigation = unserialize(osc_get_serialized_variable($session_data, 'osC_NavigationHistory_data', 'array'));
    $last_page = end($navigation);

    $currency = unserialize(osc_get_serialized_variable($session_data, 'currency', 'string'));

    $cart = unserialize(osc_get_serialized_variable($session_data, 'osC_ShoppingCart_data', 'array'));

    if (!isset($wInfo) && (!isset($_GET['info']) || (isset($_GET['info']) && ($_GET['info'] == $Qwho->value('session_id'))))) {
      $wInfo = new objectInfo(array_merge($Qwho->toArray(), array('last_page' => $last_page)));
    }

    if (isset($wInfo) && ($Qwho->value('session_id') == $wInfo->session_id)) {
      echo '      <tr class="selected">' . "\n";
    } else {
      echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_WHOS_ONLINE, 'info=' . $Qwho->value('session_id')) . '\';">' . "\n";
    }
?>
        <td><?php echo gmdate('H:i:s', time() - $Qwho->value('time_entry')); ?></td>
        <td><?php echo $Qwho->value('customer_id'); ?></td>
        <td><?php echo $Qwho->value('full_name'); ?></td>
        <td><?php echo $Qwho->value('ip_address'); ?></td>
        <td><?php echo date('H:i:s', $Qwho->value('time_last_click')); ?></td>
        <td><?php echo $last_page['page']; ?></td>
        <td><?php echo $osC_Currencies->format($cart['total_cost'], true, $currency); ?></td>
        <td align="right">
<?php
    if (isset($wInfo) && ($Qwho->value('session_id') == $wInfo->session_id)) {
      echo '<a href="#" onclick="toggleInfoBox(\'wInfo\');">' . osc_icon('info.png', IMAGE_ICON_INFO) . '</a>';
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_WHOS_ONLINE, 'info=' . $Qwho->value('session_id') . '&action=wInfo'), osc_icon('info.png', IMAGE_ICON_INFO));
    }
?>
        </td>
      </tr>
<?php
  }
?>
    </tbody>
  </table>

  <p><?php echo sprintf(TEXT_DISPLAY_NUMBER_OF_WHOS_ONLINE, $Qwho->numberOfRows()); ?></p>
</div>

<?php
  if (isset($wInfo)) {
    $osC_IP_Locator = new osC_IP_Locator();

    $last_page_url = $wInfo->last_page['page'];

    if (isset($wInfo->last_page['get']['osCsid'])) {
      unset($wInfo->last_page['get']['osCsid']);
    }

    if (sizeof($wInfo->last_page['get']) > 0) {
      $last_page_url .= '?' . osc_array_to_string($wInfo->last_page['get']);
    }
?>

<div id="infoBox_wInfo" <?php if ($action != 'wInfo') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('info.png', IMAGE_ICON_INFO) . ' ' . $wInfo->full_name; ?></div>
  <div class="infoBoxContent">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_SESSION_ID . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo $wInfo->session_id; ?></td>
      </tr>
      <tr>
        <td class="smallText" colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_TIME_ONLINE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo gmdate('H:i:s', time() - $wInfo->time_entry); ?></td>
      </tr>
      <tr>
        <td class="smallText" colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_CUSTOMER_ID . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo $wInfo->customer_id; ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_CUSTOMER_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo $wInfo->full_name; ?></td>
      </tr>
      <tr>
        <td class="smallText" colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_IP_ADDRESS . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo $wInfo->ip_address; ?></td>
      </tr>
<?php
    if ($osC_IP_Locator->isLoaded()) {
      if (($data = $osC_IP_Locator->getData($wInfo->ip_address)) !== false) {
        foreach ($data as $entry) {
          echo '      <tr>' . "\n" .
               '        <td class="smallText" width="40%"><b>' . $entry['key'] . '</b></td>' . "\n" .
               '        <td class="smallText" width="60%">' . $entry['value'] . '</td>' . "\n" .
               '      </tr>' . "\n";
        }
      }

      $osC_IP_Locator->unload();
    }
?>
      <tr>
        <td class="smallText" colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_ENTRY_TIME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo date('H:i:s', $wInfo->time_entry); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_LAST_CLICK . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo date('H:i:s', $wInfo->time_last_click); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_LAST_PAGE_URL . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo $last_page_url; ?></td>
      </tr>
<?php
    if (!empty($cart['contents'])) {
      echo '      <tr>' . "\n" .
           '        <td class="smallText" colspan="2">&nbsp;</td>' . "\n" .
           '      </tr>' . "\n" .
           '      <tr>' . "\n" .
           '        <td class="smallText" width="40%" valign="top"><b>' . TEXT_SHOPPING_CART_PRODUCTS . '</b></td>' . "\n" .
           '        <td class="smallText" width="60%"><table border="0" cellspacing="0" cellpadding="2">' . "\n";

      foreach ($cart['contents'] as $product) {
        echo '          <tr>' . "\n" .
             '            <td class="smallText" align="right">' . $product['quantity'] . ' x</td>' . "\n" .
             '            <td class="smallText">' . $product['name'] . '</td>' . "\n" .
             '          </tr>' . "\n";
      }

      echo '        </table></td>' . "\n" .
           '      </tr>' . "\n" .
           '      <tr>' . "\n" .
           '        <td class="smallText" width="40%"><b>' . TEXT_SHOPPING_CART_TOTAL . '</b></td>' . "\n" .
           '        <td class="smallText" width="60%">' . $osC_Currencies->format($cart['total_cost'], true, $currency) . '</td>' . "\n" .
           '      </tr>' . "\n";
    }
?>
    </table>

    <p align="center"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="toggleInfoBox(\'wDefault\');" class="operationButton">'; ?></p>
  </div>
</div>

<?php
  }
?>
