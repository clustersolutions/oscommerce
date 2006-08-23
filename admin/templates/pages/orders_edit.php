<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  $osC_Order = new osC_Order($_GET['oID']);
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><h1><?php echo HEADING_TITLE; ?></h1></td>
    <td class="smallText" align="right"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_ORDERS, (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $_GET['oID']) . '\';" class="operationButton">'; ?></td>
  </tr>
</table>

<?php
  if ($osC_Order->isValid() === false) {
    $osC_MessageStack->add('orders_edit', sprintf(ERROR_ORDER_DOES_NOT_EXIST, $_GET['oID']), 'error');
    echo $osC_MessageStack->output('orders_edit');
  } else {
?>

<p>
  <input type="button" value="Summary" class="sectionButton" onclick="toggleDivBlocks('section', 'sectionSummary');"> <input type="button" value="Products" class="sectionButton" onclick="toggleDivBlocks('section', 'sectionProducts');"> <input type="button" value="Transaction History" class="sectionButton" onclick="toggleDivBlocks('section', 'sectionTransactionHistory');"> <input type="button" value="Status History" class="sectionButton" onclick="toggleDivBlocks('section', 'sectionStatusHistory');">
  <input type="button" value="<?php echo IMAGE_ORDERS_INVOICE; ?>" onclick="window.open('<?php echo osc_href_link_admin(FILENAME_ORDERS_INVOICE, 'oID=' . $_GET['oID']); ?>');" class="infoBoxButton"> <input type="button" value="<?php echo IMAGE_ORDERS_PACKINGSLIP; ?>" onclick="window.open('<?php echo osc_href_link_admin(FILENAME_ORDERS_PACKINGSLIP, 'oID=' . $_GET['oID']); ?>');" class="infoBoxButton">
</p>

<div id="sectionSummary" <?php if (!empty($section)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="0" class="infoBoxContent">
    <tr>
      <td width="33%" valign="top">
        <div class="infoBoxHeading"><?php echo osc_icon('personal.png', ENTRY_CUSTOMER) . ' ' . ENTRY_CUSTOMER; ?></div>
        <div class="infoBoxContent">
          <p><?php echo osC_Address::format($osC_Order->getCustomer(), '<br />'); ?></p>
          <p><?php echo osc_icon('telephone.png', ENTRY_TELEPHONE_NUMBER) . ' ' . $osC_Order->getCustomer('telephone') . '<br />' . osc_icon('write.png', ENTRY_EMAIL_ADDRESS) . ' ' . $osC_Order->getCustomer('email_address'); ?></p>
        </div>
      </td>
      <td width="33%" valign="top">
        <div class="infoBoxHeading"><?php echo osc_icon('home.png', ENTRY_SHIPPING_ADDRESS) . ' ' . ENTRY_SHIPPING_ADDRESS; ?></div>
        <div class="infoBoxContent">
          <p><?php echo osC_Address::format($osC_Order->getDelivery(), '<br />'); ?></p>
        </div>
      </td>
      <td width="33%" valign="top">
        <div class="infoBoxHeading"><?php echo osc_icon('bill.png', ENTRY_BILLING_ADDRESS) . ' ' . ENTRY_BILLING_ADDRESS; ?></div>
        <div class="infoBoxContent">
          <p><?php echo osC_Address::format($osC_Order->getBilling(), '<br />'); ?></p>
        </div>
      </td>
    </tr>
  </table>

  <br />

  <table border="0" width="100%" cellspacing="0" cellpadding="0" class="infoBoxContent">
    <tr>
      <td width="33%" valign="top">
        <div class="infoBoxHeading"><?php echo osc_icon('payment.png', ENTRY_PAYMENT_METHOD) . ' ' . ENTRY_PAYMENT_METHOD; ?></div>
        <div class="infoBoxContent">
          <p><?php echo $osC_Order->getPaymentMethod(); ?></p>
<?php
    if ($osC_Order->isValidCreditCard()) {
?>
          <table border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="smallText"><?php echo ENTRY_CREDIT_CARD_TYPE; ?></td>
              <td class="smallText"><?php echo $osC_Order->getCreditCardDetails('type'); ?></td>
            </tr>
            <tr>
              <td class="smallText"><?php echo ENTRY_CREDIT_CARD_OWNER; ?></td>
              <td class="smallText"><?php echo $osC_Order->getCreditCardDetails('owner'); ?></td>
            </tr>
            <tr>
              <td class="smallText"><?php echo ENTRY_CREDIT_CARD_NUMBER; ?></td>
              <td class="smallText"><?php echo $osC_Order->getCreditCardDetails('number'); ?></td>
            </tr>
            <tr>
              <td class="smallText"><?php echo ENTRY_CREDIT_CARD_EXPIRES; ?></td>
              <td class="smallText"><?php echo $osC_Order->getCreditCardDetails('expires'); ?></td>
            </tr>
          </table>
<?php
    }
?>
        </div>
      </td>
      <td width="33%" valign="top">
        <div class="infoBoxHeading"><?php echo osc_icon('history.png', ENTRY_STATUS) . ' ' . ENTRY_STATUS; ?></div>
        <div class="infoBoxContent">
          <p><?php echo $osC_Order->getStatus() . '<br />' . ($osC_Order->getDateLastModified() > $osC_Order->getDateCreated() ? tep_datetime_short($osC_Order->getDateLastModified()) : tep_datetime_short($osC_Order->getDateCreated())); ?></p>
          <p><?php echo 'Comments: ' . $osC_Order->getNumberOfComments(); ?></p>
        </div>
      </td>
      <td width="33%" valign="top">
        <div class="infoBoxHeading"><?php echo osc_icon('calculator.png', ENTRY_TOTAL) . ' ' . ENTRY_TOTAL; ?></div>
        <div class="infoBoxContent">
          <p><?php echo $osC_Order->getTotal(); ?></p>
          <p><?php echo 'Products: ' . $osC_Order->getNumberOfProducts() . '<br />Items: ' . $osC_Order->getNumberOfItems(); ?></p>
        </div>
      </td>
    </tr>
  </table>
</div>

<div id="sectionProducts" <?php if ($section != 'products') { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr class="dataTableHeadingRow">
      <td class="dataTableHeadingContent" colspan="2"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
      <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TAX; ?></td>
      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_EXCLUDING_TAX; ?></td>
      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_INCLUDING_TAX; ?></td>
      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_EXCLUDING_TAX; ?></td>
      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_INCLUDING_TAX; ?></td>
    </tr>
<?php
    foreach ($osC_Order->getProducts() as $products) {
?>
    <tr class="dataTableRow">
      <td class="dataTableContent" valign="top" align="right"><?php echo $products['quantity'] . '&nbsp;x'; ?></td>
      <td class="dataTableContent" valign="top">
<?php
      echo $products['name'];

      if (isset($products['attributes']) && is_array($products['attributes']) && (sizeof($products['attributes']) > 0)) {
        foreach ($products['attributes'] as $attributes) {
          echo '<br /><nobr><small>&nbsp;<i> - ' . $attributes['option'] . ': ' . $attributes['value'];

          if ($attributes['price'] != '0') {
            echo ' (' . $attributes['prefix'] . $osC_Currencies->format($attributes['price'] * $products['quantity'], $osC_Order->getCurrency(), $osC_Order->getCurrencyValue()) . ')';
          }

          echo '</i></small></nobr>';
        }
      }
?>
      </td>
      <td class="dataTableContent" valign="top"><?php echo $products['model']; ?></td>
      <td class="dataTableContent" valign="top" align="right"><?php echo tep_display_tax_value($products['tax']) . '%'; ?></td>
      <td class="dataTableContent" valign="top" align="right"><?php echo '<b>' . $osC_Currencies->format($products['final_price'], $osC_Order->getCurrency(), $osC_Order->getCurrencyValue()) . '</b>'; ?></td>
      <td class="dataTableContent" valign="top" align="right"><?php echo '<b>' . $osC_Currencies->format(tep_add_tax($products['final_price'], $products['tax']), $osC_Order->getCurrency(), $osC_Order->getCurrencyValue()) . '</b>'; ?></td>
      <td class="dataTableContent" valign="top" align="right"><?php echo '<b>' . $osC_Currencies->format($products['final_price'] * $products['quantity'], $osC_Order->getCurrency(), $osC_Order->getCurrencyValue()) . '</b>'; ?></td>
      <td class="dataTableContent" valign="top" align="right"><?php echo '<b>' . $osC_Currencies->format(tep_add_tax($products['final_price'], $products['tax']) * $products['quantity'], $osC_Order->getCurrency(), $osC_Order->getCurrencyValue()) . '</b>'; ?></td>
    </tr>
<?php
    }
?>
  </table>

  <table border="0" cellspacing="0" cellpadding="2" align="right">
<?php
    foreach ($osC_Order->getTotals() as $totals) {
?>
    <tr>
      <td class="smallText" align="right"><?php echo $totals['title']; ?></td>
      <td class="smallText" align="right"><?php echo $totals['text']; ?></td>
    </tr>
<?php
    }
?>
  </table>
</div>

<div id="sectionTransactionHistory" <?php if ($section != 'transactionHistory') { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr class="dataTableHeadingRow">
      <td class="dataTableHeadingContent" width="130"><?php echo TABLE_HEADING_DATE_ADDED; ?></td>
      <td class="dataTableHeadingContent" width="50"><?php echo TABLE_HEADING_STATUS; ?></td>
      <td class="dataTableHeadingContent" width="20">&nbsp;</td>
      <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_COMMENTS; ?></td>
    </tr>

<?php
    foreach ($osC_Order->getTransactionHistory() as $history) {
?>

    <tr class="dataTableRow">
      <td class="dataTableContent" valign="top"><?php echo tep_datetime_short($history['date_added']); ?></td>
      <td class="dataTableContent" valign="top"><?php echo (empty($history['status']) === false) ? $history['status'] : $history['status_id']; ?></td>
      <td class="dataTableContent" valign="top" align="center"><?php echo osc_icon(($history['return_status'] === 1 ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif'), null, null); ?></td>
      <td class="dataTableContent" valign="top"><?php echo nl2br($history['return_value']); ?></td>
    </tr>

<?php
    }
?>

  </table>

<?php
    if ($osC_Order->hasPostTransactionActions()) {
?>

  <br />

  <form name="transaction" action="<?php echo osc_href_link_admin(FILENAME_ORDERS, (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $_GET['oID'] . '&action=update_transaction'); ?>" method="post">

  <p><?php echo ENTRY_POST_TRANSACTION_ACTIONS . ' '. osc_draw_pull_down_menu('transaction', $osC_Order->getPostTransactionActions()) . ' <input type="submit" value="' . IMAGE_EXECUTE . '" class="operationButton">'; ?></p>

  </form>

<?php
    }
?>

</div>

<div id="sectionStatusHistory" <?php if ($section != 'statusHistory') { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr class="dataTableHeadingRow">
      <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_DATE_ADDED; ?></td>
      <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_STATUS; ?></td>
      <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_COMMENTS; ?></td>
      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></td>
    </tr>
<?php
    foreach ($osC_Order->getStatusHistory() as $status_history) {
?>
    <tr class="dataTableRow">
      <td class="dataTableContent" valign="top"><?php echo tep_datetime_short($status_history['date_added']); ?></td>
      <td class="dataTableContent" valign="top"><?php echo $status_history['status']; ?></td>
      <td class="dataTableContent" valign="top"><?php echo nl2br($status_history['comment']); ?></td>
      <td class="dataTableContent" align="right" valign="top"><?php echo osc_icon((($status_history['customer_notified'] === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif'), null, null); ?></td>
    </tr>
<?php
    }
?>
  </table>

  <br />

  <form name="status" action="<?php echo osc_href_link_admin(FILENAME_ORDERS, (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $_GET['oID'] . '&action=update_order'); ?>" method="post">

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td class="main"><?php echo ENTRY_STATUS; ?></td>
      <td class="main"><?php echo osc_draw_pull_down_menu('status', $orders_statuses, $osC_Order->getStatusID()); ?></td>
    </tr>
    <tr>
      <td class="main" valign="top"><?php echo ENTRY_NEW_COMMENT; ?></td>
      <td class="main"><?php echo osc_draw_textarea_field('comment', null, null, null, 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td class="main"><?php echo ENTRY_NOTIFY_CUSTOMER; ?></td>
      <td class="main"><?php echo osc_draw_checkbox_field('notify_customer', null, true); ?></td>
    </tr>
      <td class="main"><?php echo ENTRY_NOTIFY_COMMENTS; ?></td>
      <td class="main"><?php echo osc_draw_checkbox_field('append_comment', null, true); ?></td>
    </tr>
    <tr>
      <td colspan="2" class="main" align="right"><?php echo '<input type="submit" value="' . IMAGE_UPDATE . '" class="operationButton">'; ?></td>
    </tr>
  </table>

  </form>
</div>

<?php
  }
?>
