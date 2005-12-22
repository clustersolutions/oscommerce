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
    <td class="smallText" align="right"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . tep_href_link(FILENAME_ORDERS, (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $_GET['oID']) . '\';" class="operationButton">'; ?></td>
  </tr>
</table>

<?php
  if ($osC_Order->isValid() === false) {
    $osC_MessageStack->add('orders_edit', sprintf(ERROR_ORDER_DOES_NOT_EXIST, $_GET['oID']), 'error');
    echo $osC_MessageStack->output('orders_edit');
  } else {
?>

<p>
  <input type="button" value="Summary" class="sectionButton" onclick="toggleDivBlocks('section', 'sectionSummary');"> <input type="button" value="Products" class="sectionButton" onclick="toggleDivBlocks('section', 'sectionProducts');"> <input type="button" value="Status History" class="sectionButton" onclick="toggleDivBlocks('section', 'sectionStatusHistory');">
  <input type="button" value="<?php echo IMAGE_ORDERS_INVOICE; ?>" onclick="window.open('<?php echo tep_href_link(FILENAME_ORDERS_INVOICE, 'oID=' . $_GET['oID']); ?>');" class="infoBoxButton"> <input type="button" value="<?php echo IMAGE_ORDERS_PACKINGSLIP; ?>" onclick="window.open('<?php echo tep_href_link(FILENAME_ORDERS_PACKINGSLIP, 'oID=' . $_GET['oID']); ?>');" class="infoBoxButton">
</p>

<div id="sectionSummary" <?php if (!empty($section)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="0" class="infoBoxContent">
    <tr>
      <td width="33%" valign="top">
        <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/personal.png', ENTRY_CUSTOMER, '16', '16') . ' ' . ENTRY_CUSTOMER; ?></div>
        <div class="infoBoxContent">
          <p><?php echo tep_address_format($osC_Order->getCustomer('format_id'), $osC_Order->getCustomer(), 1, '', '<br />'); ?></p>
          <p><?php echo tep_image('templates/' . $template . '/images/icons/16x16/telephone.png', ENTRY_TELEPHONE_NUMBER, '16', '16') . ' ' . $osC_Order->getCustomer('telephone') . '<br />' . tep_image('templates/' . $template . '/images/icons/16x16/write.png', ENTRY_EMAIL_ADDRESS, '16', '16') . ' ' . $osC_Order->getCustomer('email_address'); ?></p>
        </div>
      </td>
      <td width="33%" valign="top">
        <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/home.png', ENTRY_SHIPPING_ADDRESS, '16', '16') . ' ' . ENTRY_SHIPPING_ADDRESS; ?></div>
        <div class="infoBoxContent">
          <p><?php echo tep_address_format($osC_Order->getDelivery('format_id'), $osC_Order->getDelivery(), 1, '', '<br />'); ?></p>
        </div>
      </td>
      <td width="33%" valign="top">
        <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/bill.png', ENTRY_BILLING_ADDRESS, '16', '16') . ' ' . ENTRY_BILLING_ADDRESS; ?></div>
        <div class="infoBoxContent">
          <p><?php echo tep_address_format($osC_Order->getBilling('format_id'), $osC_Order->getBilling(), 1, '', '<br />'); ?></p>
        </div>
      </td>
    </tr>
  </table>

  <br />

  <table border="0" width="100%" cellspacing="0" cellpadding="0" class="infoBoxContent">
    <tr>
      <td width="33%" valign="top">
        <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/payment.png', ENTRY_PAYMENT_METHOD, '16', '16') . ' ' . ENTRY_PAYMENT_METHOD; ?></div>
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
        <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/history.png', ENTRY_STATUS, '16', '16') . ' ' . ENTRY_STATUS; ?></div>
        <div class="infoBoxContent">
          <p><?php echo $osC_Order->getStatus() . '<br />' . ($osC_Order->getDateLastModified() > $osC_Order->getDateCreated() ? tep_datetime_short($osC_Order->getDateLastModified()) : tep_datetime_short($osC_Order->getDateCreated())); ?></p>
          <p><?php echo 'Comments: ' . $osC_Order->getNumberOfComments(); ?></p>
        </div>
      </td>
      <td width="33%" valign="top">
        <div class="infoBoxHeading"><?php echo tep_image('templates/' . $template . '/images/icons/16x16/calculator.png', ENTRY_TOTAL, '16', '16') . ' ' . ENTRY_TOTAL; ?></div>
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
      <td class="dataTableContent" align="right" valign="top"><?php echo tep_image('templates/' . $template . '/images/icons/' . (($status_history['customer_notified'] === 1) ? 'checkbox_ticked.gif' : 'checkbox_crossed.gif')); ?></td>
    </tr>
<?php
    }
?>
  </table>

  <br />

  <?php echo tep_draw_form('status', FILENAME_ORDERS, (isset($_GET['search']) ? 'search=' . $_GET['search'] . '&' : '') . (isset($_GET['status']) ? 'status=' . $_GET['status'] . '&' : '') . (isset($_GET['cID']) ? 'cID=' . $_GET['cID'] . '&' : '') . 'page=' . $_GET['page'] . '&oID=' . $_GET['oID'] . '&action=update_order'); ?>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td class="main"><?php echo ENTRY_STATUS; ?></td>
      <td class="main"><?php echo osc_draw_pull_down_menu('status', $orders_statuses, $osC_Order->getStatusID()); ?></td>
    </tr>
    <tr>
      <td class="main" valign="top"><?php echo ENTRY_NEW_COMMENT; ?></td>
      <td class="main"><?php echo tep_draw_textarea_field('comment', 'soft', '60', '5', '', 'style="width: 100%"'); ?></td>
    </tr>
    <tr>
      <td class="main"><?php echo ENTRY_NOTIFY_CUSTOMER; ?></td>
      <td class="main"><?php echo osc_draw_checkbox_field('notify_customer', '', true); ?></td>
    </tr>
      <td class="main"><?php echo ENTRY_NOTIFY_COMMENTS; ?></td>
      <td class="main"><?php echo osc_draw_checkbox_field('append_comment', '', true); ?></td>
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
