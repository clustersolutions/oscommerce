<?php
/*
  $Id: checkout_trail.php,v 1.3 2004/04/25 17:50:15 mattice Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  switch (substr(strrchr($_SERVER['PHP_SELF'], "/"), 1)) {
    case FILENAME_CHECKOUT_SHIPPING:
    case FILENAME_CHECKOUT_SHIPPING_ADDRESS:
      $step = 1;
      break;
    case FILENAME_CHECKOUT_PAYMENT:
    case FILENAME_CHECKOUT_PAYMENT_ADDRESS:
      $step = 2;
      break;
    case FILENAME_CHECKOUT_CONFIRMATION:
      $step = 3;
      break;
    case FILENAME_CHECKOUT_SUCCESS:
      $step = 4;
      break;
  }
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="15" valign="top"></td>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="0" background="<?php echo DIR_WS_IMAGES . 'checkout_trail/ct_spacer.gif'; ?>">
      <tr>
<?php
  if ($step == 1) {
    echo '        <td align="center" valign="top" class="checkoutBarCurrent">' . tep_image(DIR_WS_IMAGES . 'checkout_trail/ct_step_1_active.gif', CHECKOUT_BAR_DELIVERY) . '<br>' . CHECKOUT_BAR_DELIVERY . '</td>';
  } elseif ( ($step > 1)  && ($step != 4) ) {
    echo '        <td align="center" valign="top" class="checkoutBarTo"><a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'checkout_trail/ct_step_1.gif', CHECKOUT_BAR_DELIVERY,  'border="0"') . '<br>' . CHECKOUT_BAR_DELIVERY  . '</a></td>';
  } else {
    echo '        <td align="center" valign="top" class="checkoutBarFrom">' . tep_image(DIR_WS_IMAGES . 'checkout_trail/ct_step_1.gif', CHECKOUT_BAR_DELIVERY, 'border="0"') . '<br>' . CHECKOUT_BAR_DELIVERY . '</td>';
  }

  if ($step == 2) {
    echo '        <td align="center" valign="top" class="checkoutBarCurrent">' . tep_image(DIR_WS_IMAGES . 'checkout_trail/ct_step_2_active.gif', CHECKOUT_BAR_PAYMENT) . '<br>' . CHECKOUT_BAR_PAYMENT . '</td>';
  } elseif ( ($step > 2)  && ($step != 4) ) {
    echo '        <td align="center" valign="top" class="checkoutBarTo"><a href="' . tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'checkout_trail/ct_step_2.gif', CHECKOUT_BAR_PAYMENT, 'border="0"') . '<br>' . CHECKOUT_BAR_PAYMENT . '</a></td>';
  } else {
    echo '        <td align="center" valign="top" class="checkoutBarFrom">' . tep_image(DIR_WS_IMAGES . 'checkout_trail/ct_step_2.gif', CHECKOUT_BAR_PAYMENT) . '<br>' . CHECKOUT_BAR_PAYMENT . '</td>';
  }

  if ($step == 3) {
    echo '        <td align="center" valign="top" class="checkoutBarCurrent">' . tep_image(DIR_WS_IMAGES . 'checkout_trail/ct_step_3_active.gif', CHECKOUT_BAR_CONFIRMATION) . '<br>' . CHECKOUT_BAR_CONFIRMATION . '</td>';
  } elseif ( ($step > 3) && ($step != 4) ) {
    echo '        <td align="center" valign="top" class="checkoutBarTo"><a href="' . tep_href_link(FILENAME_CHECKOUT_CONFIRMATION, '', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'checkout_trail/ct_step_3.gif', CHECKOUT_BAR_CONFIRMATION) . '<br>' . CHECKOUT_BAR_CONFIRMATION . '</a></td>';
  } else {
    echo '        <td align="center" valign="top" class="checkoutBarFrom">' . tep_image(DIR_WS_IMAGES . 'checkout_trail/ct_step_3.gif', CHECKOUT_BAR_CONFIRMATION) . '<br>' . CHECKOUT_BAR_CONFIRMATION . '</td>';
  }

  if ($step == 4) {
    echo '        <td align="center" valign="top" class="checkoutBarCurrent">' . tep_image(DIR_WS_IMAGES . 'checkout_trail/ct_step_4_active.gif', CHECKOUT_BAR_FINISHED) . '<br>' . CHECKOUT_BAR_FINISHED . '</td>';
  } else {
    echo '        <td align="center" valign="top" class="checkoutBarTo">' . tep_image(DIR_WS_IMAGES . 'checkout_trail/ct_step_4.gif', CHECKOUT_BAR_FINISHED) . '<br>' . CHECKOUT_BAR_FINISHED . '</td>';
  }
?>
      </tr>
    </table></td>
    <td width="15" align="right" valign="top"></td>
  </tr>
</table>
