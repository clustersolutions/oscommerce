<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_Currencies = new osC_Currencies();

  $osC_Tax = new osC_Tax_Admin();

  $osC_Weight = new osC_Weight();

  $osC_GeoIP = osC_GeoIP_Admin::load();

  if ( $osC_GeoIP->isInstalled() ) {
    $osC_GeoIP->activate();
  }

  $osC_ObjectInfo = new osC_ObjectInfo(osC_WhosOnline_Admin::getData($_GET['info']));

  if ( STORE_SESSIONS == 'mysql' ) {
    $Qsession = $osC_Database->query('select value from :table_sessions where sesskey = :sesskey');
    $Qsession->bindTable(':table_sessions', TABLE_SESSIONS);
    $Qsession->bindValue(':sesskey', $osC_ObjectInfo->get('session_id'));
    $Qsession->execute();

    $session_data = trim($Qsession->value('value'));
  } else {
    if ( file_exists($osC_Session->getSavePath() . '/sess_' . $osC_ObjectInfo->get('session_id')) && ( filesize($osC_Session->getSavePath() . '/sess_' . $osC_ObjectInfo->get('session_id')) > 0 ) ) {
      $session_data = trim(file_get_contents($osC_Session->getSavePath() . '/sess_' . $osC_ObjectInfo->get('session_id')));
    }
  }

  $navigation = unserialize(osc_get_serialized_variable($session_data, 'osC_NavigationHistory_data', 'array'));
  $last_page = end($navigation);
  $last_page_url = $last_page['page'];

  if ( isset($last_page['get']['osCsid']) ) {
    unset($last_page['get']['osCsid']);
  }

  if ( sizeof($last_page['get']) > 0 ) {
    $last_page_url .= '?' . osc_array_to_string($last_page['get']);
  }

  $currency = unserialize(osc_get_serialized_variable($session_data, 'currency', 'string'));

  $cart = unserialize(osc_get_serialized_variable($session_data, 'osC_ShoppingCart_data', 'array'));

?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('info.png', IMAGE_INFO) . ' ' . $osC_ObjectInfo->get('full_name'); ?></div>
<div class="infoBoxContent">
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_SESSION_ID . '</b>'; ?></td>
      <td width="60%"><?php echo $osC_ObjectInfo->get('session_id'); ?></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_TIME_ONLINE . '</b>'; ?></td>
      <td width="60%"><?php echo gmdate('H:i:s', time() - $osC_ObjectInfo->get('time_entry')); ?></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_CUSTOMER_ID . '</b>'; ?></td>
      <td width="60%"><?php echo $osC_ObjectInfo->get('customer_id'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_CUSTOMER_NAME . '</b>'; ?></td>
      <td width="60%"><?php echo $osC_ObjectInfo->get('full_name'); ?></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_IP_ADDRESS . '</b>'; ?></td>
      <td width="60%">

<?php
  echo $osC_ObjectInfo->get('ip_address');

  if ( $osC_GeoIP->isActive() && $osC_GeoIP->isValid($osC_ObjectInfo->get('ip_address')) ) {
    echo '<p>' . implode('<br />', $osC_GeoIP->getData($osC_ObjectInfo->get('ip_address'))) . '</p>';
  }
?>

      </td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_ENTRY_TIME . '</b>'; ?></td>
      <td width="60%"><?php echo date('H:i:s', $osC_ObjectInfo->get('time_entry')); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_LAST_CLICK . '</b>'; ?></td>
      <td width="60%"><?php echo date('H:i:s', $osC_ObjectInfo->get('time_last_click')); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_LAST_PAGE_URL . '</b>'; ?></td>
      <td width="60%"><?php echo $last_page_url; ?></td>
    </tr>

<?php
  if ( !empty($cart['contents']) ) {
    echo '    <tr>' . "\n" .
         '      <td colspan="2">&nbsp;</td>' . "\n" .
         '    </tr>' . "\n" .
         '    <tr>' . "\n" .
         '      <td width="40%" valign="top"><b>' . TEXT_SHOPPING_CART_PRODUCTS . '</b></td>' . "\n" .
         '      <td width="60%"><table border="0" cellspacing="0" cellpadding="2">' . "\n";

    foreach ($cart['contents'] as $product) {
      echo '        <tr>' . "\n" .
           '          <td align="right">' . $product['quantity'] . ' x</td>' . "\n" .
           '          <td>' . $product['name'] . '</td>' . "\n" .
           '        </tr>' . "\n";
    }

    echo '      </table></td>' . "\n" .
         '    </tr>' . "\n" .
         '    <tr>' . "\n" .
         '      <td width="40%"><b>' . TEXT_SHOPPING_CART_TOTAL . '</b></td>' . "\n" .
         '      <td width="60%">' . $osC_Currencies->format($cart['total_cost'], true, $currency) . '</td>' . "\n" .
         '    </tr>' . "\n";
  }
?>

  </table>

  <p align="center"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>
</div>

<?php
  if ( $osC_GeoIP->isActive() ) {
    $osC_GeoIP->deactivate();
  }
?>
