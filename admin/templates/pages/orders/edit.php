<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  require('includes/classes/tax.php');
  $osC_Tax = new osC_Tax_Admin();

  $osC_Order = new osC_Order($_GET['oID']);

  if ( !$osC_Order->isValid() ) {
    $osC_MessageStack->add($osC_Template->getModule(), sprintf(ERROR_ORDER_DOES_NOT_EXIST, $_GET['oID']), 'error');
  }
?>

<link type="text/css" rel="stylesheet" href="external/tabpane/css/luna/tab.css" />
<script type="text/javascript" src="external/tabpane/js/tabpane.js"></script>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<p align="right">
  <?php echo '<input type="button" value="' . IMAGE_ORDERS_INVOICE . '" onclick="window.open(\'' . osc_href_link_admin(FILENAME_ORDERS_INVOICE, 'oID=' . $_GET['oID']) . '\');" class="infoBoxButton"/> <input type="button" value="' . IMAGE_ORDERS_PACKINGSLIP . '" onclick="window.open(\'' . osc_href_link_admin(FILENAME_ORDERS_PACKINGSLIP, 'oID=' . $_GET['oID']) . '\');" class="infoBoxButton" /> <input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page']) . '\';" class="operationButton" />'; ?>
</p>

<?php
  if ( $osC_Order->isValid() ) {
?>

<div class="tab-pane" id="mainTabPane">
  <script type="text/javascript"><!--
    var mainTabPane = new WebFXTabPane( document.getElementById( "mainTabPane" ) );
  //--></script>

  <div class="tab-page" id="tabSummary">
    <h2 class="tab">Summary</h2>

    <script type="text/javascript"><!--
      mainTabPane.addTabPage( document.getElementById( "tabSummary" ) );
    //--></script>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend style="margin-left: -20px; font-weight: bold;"><?php echo osc_icon('personal.png', ENTRY_CUSTOMER) . ' ' . ENTRY_CUSTOMER; ?></legend>

            <p><?php echo osC_Address::format($osC_Order->getCustomer(), '<br />'); ?></p>
            <p><?php echo osc_icon('telephone.png', ENTRY_TELEPHONE_NUMBER) . ' ' . $osC_Order->getCustomer('telephone') . '<br />' . osc_icon('write.png', ENTRY_EMAIL_ADDRESS) . ' ' . $osC_Order->getCustomer('email_address'); ?></p>
          </fieldset>
        </td>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend style="margin-left: -20px; font-weight: bold;"><?php echo osc_icon('home.png', ENTRY_SHIPPING_ADDRESS) . ' ' . ENTRY_SHIPPING_ADDRESS; ?></legend>

            <p><?php echo osC_Address::format($osC_Order->getDelivery(), '<br />'); ?></p>
          </fieldset>
        </td>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend style="margin-left: -20px; font-weight: bold;"><?php echo osc_icon('bill.png', ENTRY_BILLING_ADDRESS) . ' ' . ENTRY_BILLING_ADDRESS; ?></legend>

            <p><?php echo osC_Address::format($osC_Order->getBilling(), '<br />'); ?></p>
          </fieldset>
        </td>
      </tr>
      <tr>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend style="margin-left: -20px; font-weight: bold;"><?php echo osc_icon('payment.png', ENTRY_PAYMENT_METHOD) . ' ' . ENTRY_PAYMENT_METHOD; ?></legend>

            <p><?php echo $osC_Order->getPaymentMethod(); ?></p>

<?php
    if ( $osC_Order->isValidCreditCard() ) {
?>

            <table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><?php echo ENTRY_CREDIT_CARD_TYPE; ?></td>
                <td><?php echo $osC_Order->getCreditCardDetails('type'); ?></td>
              </tr>
              <tr>
                <td><?php echo ENTRY_CREDIT_CARD_OWNER; ?></td>
                <td><?php echo $osC_Order->getCreditCardDetails('owner'); ?></td>
              </tr>
              <tr>
                <td><?php echo ENTRY_CREDIT_CARD_NUMBER; ?></td>
                <td><?php echo $osC_Order->getCreditCardDetails('number'); ?></td>
              </tr>
              <tr>
                <td><?php echo ENTRY_CREDIT_CARD_EXPIRES; ?></td>
                <td><?php echo $osC_Order->getCreditCardDetails('expires'); ?></td>
              </tr>
            </table>

<?php
    }
?>
          </fieldset>
        </td>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend style="margin-left: -20px; font-weight: bold;"><?php echo osc_icon('history.png', ENTRY_STATUS) . ' ' . ENTRY_STATUS; ?></legend>

            <p><?php echo $osC_Order->getStatus() . '<br />' . ($osC_Order->getDateLastModified() > $osC_Order->getDateCreated() ? osC_DateTime::getShort($osC_Order->getDateLastModified(), true) : osC_DateTime::getShort($osC_Order->getDateCreated(), true)); ?></p>
            <p><?php echo 'Comments: ' . $osC_Order->getNumberOfComments(); ?></p>
          </fieldset>
        </td>
        <td width="33%" valign="top">
          <fieldset style="border: 0; height: 100%;">
            <legend style="margin-left: -20px; font-weight: bold;"><?php echo osc_icon('calculator.png', ENTRY_TOTAL) . ' ' . ENTRY_TOTAL; ?></legend>

            <p><?php echo $osC_Order->getTotal(); ?></p>
            <p><?php echo 'Products: ' . $osC_Order->getNumberOfProducts() . '<br />Items: ' . $osC_Order->getNumberOfItems(); ?></p>
          </fieldset>
        </td>
      </tr>
    </table>
  </div>

  <div class="tab-page" id="tabProducts">
    <h2 class="tab">Products</h2>

    <script type="text/javascript"><!--
      mainTabPane.addTabPage( document.getElementById( "tabProducts" ) );

<?php
    if ( isset($_GET['tabIndex']) && ( $_GET['tabIndex'] == 'tabProducts' ) ) {
      echo 'mainTabPane.setSelectedIndex( mainTabPane.pages.length - 1 );';
    }
?>
    //--></script>

    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
      <thead>
        <tr>
          <th colspan="2"><?php echo TABLE_HEADING_PRODUCTS; ?></th>
          <th><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></th>
          <th><?php echo TABLE_HEADING_TAX; ?></th>
          <th><?php echo TABLE_HEADING_PRICE_EXCLUDING_TAX; ?></th>
          <th><?php echo TABLE_HEADING_PRICE_INCLUDING_TAX; ?></th>
          <th><?php echo TABLE_HEADING_TOTAL_EXCLUDING_TAX; ?></th>
          <th><?php echo TABLE_HEADING_TOTAL_INCLUDING_TAX; ?></th>
        </tr>
      </thead>
      <tbody>

<?php
    foreach ( $osC_Order->getProducts() as $products ) {
?>

        <tr>
          <td valign="top" align="right"><?php echo $products['quantity'] . '&nbsp;x'; ?></td>
          <td valign="top">

<?php
      echo $products['name'];

      if ( isset($products['attributes']) && is_array($products['attributes']) && ( sizeof($products['attributes']) > 0 ) ) {
        foreach ( $products['attributes'] as $attributes ) {
          echo '<br /><nobr><small>&nbsp;<i> - ' . $attributes['option'] . ': ' . $attributes['value'];

          if ( $attributes['price'] != '0' ) {
            echo ' (' . $attributes['prefix'] . $osC_Currencies->format($attributes['price'] * $products['quantity'], $osC_Order->getCurrency(), $osC_Order->getCurrencyValue()) . ')';
          }

          echo '</i></small></nobr>';
        }
      }
?>

          </td>
          <td valign="top"><?php echo $products['model']; ?></td>
          <td valign="top" align="right"><?php echo $osC_Tax->displayTaxRateValue($products['tax']); ?></td>
          <td valign="top" align="right"><?php echo $osC_Currencies->format($products['final_price'], $osC_Order->getCurrency(), $osC_Order->getCurrencyValue()); ?></td>
          <td valign="top" align="right"><?php echo $osC_Currencies->displayPriceWithTaxRate($products['final_price'], $products['tax'], 1, $osC_Order->getCurrency(), $osC_Order->getCurrencyValue()); ?></td>
          <td valign="top" align="right"><?php echo $osC_Currencies->format($products['final_price'] * $products['quantity'], $osC_Order->getCurrency(), $osC_Order->getCurrencyValue()); ?></td>
          <td valign="top" align="right"><?php echo $osC_Currencies->displayPriceWithTaxRate($products['final_price'], $products['tax'], $products['quantity'], $osC_Order->getCurrency(), $osC_Order->getCurrencyValue()); ?></td>
        </tr>

<?php
    }
?>

      </tbody>
    </table>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tbody>

<?php
    foreach ( $osC_Order->getTotals() as $totals ) {
?>

        <tr>
          <td align="right"><?php echo $totals['title']; ?></td>
          <td align="right"><?php echo $totals['text']; ?></td>
        </tr>

<?php
    }
?>

      </tbody>
    </table>
  </div>

  <div class="tab-page" id="tabTransactionHistory">
    <h2 class="tab">Transaction History</h2>

    <script type="text/javascript"><!--
      mainTabPane.addTabPage( document.getElementById( "tabTransactionHistory" ) );

<?php
    if ( isset($_GET['tabIndex']) && ( $_GET['tabIndex'] == 'tabTransactionHistory' ) ) {
      echo 'mainTabPane.setSelectedIndex( mainTabPane.pages.length - 1 );';
    }
?>
    //--></script>

    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
      <thead>
        <tr>
          <th width="130"><?php echo TABLE_HEADING_DATE_ADDED; ?></th>
          <th width="50"><?php echo TABLE_HEADING_STATUS; ?></th>
          <th width="20">&nbsp;</th>
          <th><?php echo TABLE_HEADING_COMMENTS; ?></th>
        </tr>
      </thead>
      <tbody>

<?php
    foreach ( $osC_Order->getTransactionHistory() as $history ) {
?>

        <tr>
          <td valign="top"><?php echo osC_DateTime::getShort($history['date_added'], true); ?></td>
          <td valign="top"><?php echo ( !empty($history['status']) ) ? $history['status'] : $history['status_id']; ?></td>
          <td valign="top" align="center"><?php echo osc_icon(($history['return_status'] === 1 ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif'), null, null); ?></td>
          <td valign="top"><?php echo nl2br($history['return_value']); ?></td>
        </tr>

<?php
    }
?>

      </tbody>
    </table>

<?php
    if ( $osC_Order->hasPostTransactionActions() ) {
?>

    <br />

    <form name="transaction" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $_GET['oID'] . '&action=updateTransaction'); ?>" method="post">

    <p><?php echo ENTRY_POST_TRANSACTION_ACTIONS . ' '. osc_draw_pull_down_menu('transaction', $osC_Order->getPostTransactionActions()) . ' ' . osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_EXECUTE . '" class="operationButton" />'; ?></p>

    </form>

<?php
    }
?>

  </div>

  <div class="tab-page" id="tabStatusHistory">
    <h2 class="tab">Status History</h2>

    <script type="text/javascript"><!--
      mainTabPane.addTabPage( document.getElementById( "tabStatusHistory" ) );

<?php
    if ( isset($_GET['tabIndex']) && ( $_GET['tabIndex'] == 'tabStatusHistory' ) ) {
      echo 'mainTabPane.setSelectedIndex( mainTabPane.pages.length - 1 );';
    }
?>
    //--></script>

    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
      <thead>
        <tr>
          <th><?php echo TABLE_HEADING_DATE_ADDED; ?></th>
          <th><?php echo TABLE_HEADING_STATUS; ?></th>
          <th><?php echo TABLE_HEADING_COMMENTS; ?></th>
          <th align="right"><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></th>
        </tr>
      </thead>
      <tbody>

<?php
    foreach ( $osC_Order->getStatusHistory() as $status_history ) {
?>

        <tr>
          <td valign="top"><?php echo osC_DateTime::getShort($status_history['date_added'], true); ?></td>
          <td valign="top"><?php echo $status_history['status']; ?></td>
          <td valign="top"><?php echo nl2br($status_history['comment']); ?></td>
          <td align="right" valign="top"><?php echo osc_icon((($status_history['customer_notified'] === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif'), null, null); ?></td>
        </tr>

<?php
    }
?>
      </tbody>
    </table>

    <br />

    <form name="status" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&' . (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $_GET['oID'] . '&action=updateStatus'); ?>" method="post">

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><?php echo ENTRY_STATUS; ?></td>
        <td><?php echo osc_draw_pull_down_menu('status', $orders_statuses, $osC_Order->getStatusID()); ?></td>
      </tr>
      <tr>
        <td valign="top"><?php echo ENTRY_NEW_COMMENT; ?></td>
        <td><?php echo osc_draw_textarea_field('comment', null, null, null, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td><?php echo ENTRY_NOTIFY_CUSTOMER; ?></td>
        <td><?php echo osc_draw_checkbox_field('notify_customer', null, true); ?></td>
      </tr>
        <td><?php echo ENTRY_NOTIFY_COMMENTS; ?></td>
        <td><?php echo osc_draw_checkbox_field('append_comment', null, true); ?></td>
      </tr>
      <tr>
        <td colspan="2" align="right"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_UPDATE . '" class="operationButton" />'; ?></td>
      </tr>
    </table>

    </form>
  </div>
</div>

<?php
  }
?>
