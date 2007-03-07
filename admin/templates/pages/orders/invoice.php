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
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td class="pageHeading"><?php echo nl2br(STORE_NAME_ADDRESS); ?></td>
        <td class="pageHeading" align="right"><?php echo osc_image('../images/store_logo.jpg', STORE_NAME); ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><b><?php echo ENTRY_SOLD_TO; ?></b></td>
          </tr>
          <tr>
            <td class="main"><?php echo osC_Address::format($osC_Order->getBilling(), '<br />'); ?></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td class="main"><?php echo $osC_Order->getCustomer('telephone'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo '<a href="mailto:' . $osC_Order->getCustomer('email_address') . '"><u>' . $osC_Order->getCustomer('email_address') . '</u></a>'; ?></td>
          </tr>
        </table></td>
        <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><b><?php echo ENTRY_SHIP_TO; ?></b></td>
          </tr>
          <tr>
            <td class="main"><?php echo osC_Address::format($osC_Order->getDelivery(), '<br />'); ?></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td class="main"><b><?php echo ENTRY_PAYMENT_METHOD; ?></b></td>
        <td class="main"><?php echo $osC_Order->getPaymentMethod(); ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
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
    foreach ($osC_Order->getProducts() as $product) {
      echo '      <tr class="dataTableRow">' . "\n" .
           '        <td class="dataTableContent" valign="top" align="right">' . $product['quantity'] . '&nbsp;x</td>' . "\n" .
           '        <td class="dataTableContent" valign="top">' . $product['name'];

      if (isset($product['attributes']) && (sizeof($product['attributes']) > 0)) {
        foreach ($product['attributes'] as $attribute) {
          echo '<br /><nobr><small>&nbsp;<i> - ' . $attribute['option'] . ': ' . $attribute['value'];
          if ($attribute['price'] != '0') echo ' (' . $attribute['prefix'] . $osC_Currencies->format($attribute['price'] * $product['quantity'], true, $osC_Order->getCurrency(), $osC_Order->getCurrencyValue()) . ')';
          echo '</i></small></nobr>';
        }
      }

      echo '        </td>' . "\n" .
           '        <td class="dataTableContent" valign="top">' . $product['model'] . '</td>' . "\n";
      echo '        <td class="dataTableContent" align="right" valign="top">' . $osC_Tax->displayTaxRateValue($product['tax']) . '</td>' . "\n" .
           '        <td class="dataTableContent" align="right" valign="top"><b>' . $osC_Currencies->format($product['final_price'], true, $osC_Order->getCurrency(), $osC_Order->getCurrencyValue()) . '</b></td>' . "\n" .
           '        <td class="dataTableContent" align="right" valign="top"><b>' . $osC_Currencies->displayPriceWithTaxRate($product['final_price'], $product['tax'], 1, $osC_Order->getCurrency(), $osC_Order->getCurrencyValue()) . '</b></td>' . "\n" .
           '        <td class="dataTableContent" align="right" valign="top"><b>' . $osC_Currencies->format($product['final_price'] * $product['quantity'], true, $osC_Order->getCurrency(), $osC_Order->getCurrencyValue()) . '</b></td>' . "\n" .
           '        <td class="dataTableContent" align="right" valign="top"><b>' . $osC_Currencies->displayPriceWithTaxRate($product['final_price'], $product['tax'], $product['quantity'], $osC_Order->getCurrency(), $osC_Order->getCurrencyValue()) . '</b></td>' . "\n";
      echo '      </tr>' . "\n";
    }
?>
      <tr>
        <td align="right" colspan="8"><table border="0" cellspacing="0" cellpadding="2">
<?php
  foreach ($osC_Order->getTotals() as $total) {
    echo '          <tr>' . "\n" .
         '            <td align="right" class="smallText">' . $total['title'] . '</td>' . "\n" .
         '            <td align="right" class="smallText">' . $total['text'] . '</td>' . "\n" .
         '          </tr>' . "\n";
  }
?>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
