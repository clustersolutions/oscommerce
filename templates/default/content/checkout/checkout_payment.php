<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<?php echo osc_image(DIR_WS_IMAGES . $osC_Template->getPageImage(), $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<?php
  if ($osC_MessageStack->size('checkout_payment') > 0) {
    echo $osC_MessageStack->get('checkout_payment');
  }
?>

<form name="checkout_payment" action="<?php echo osc_href_link(FILENAME_CHECKOUT, 'confirmation', 'SSL'); ?>" method="post" onsubmit="return check_form();">

<?php
  if (DISPLAY_CONDITIONS_ON_CHECKOUT == '1') {
?>

<div class="moduleBox">
  <h6><?php echo $osC_Language->get('order_conditions_title'); ?></h6>

  <div class="content">
    <?php echo sprintf($osC_Language->get('order_conditions_description'), osc_href_link(FILENAME_INFO, 'conditions', 'AUTO')) . '<br /><br />' . osc_draw_checkbox_field('conditions', array(array('id' => 1, 'text' => $osC_Language->get('order_conditions_acknowledge'))), false); ?>
  </div>
</div>

<?php
  }
?>

<div class="moduleBox">
  <h6><?php echo $osC_Language->get('billing_address_title'); ?></h6>

  <div class="content">
    <div style="float: right; padding: 0px 0px 10px 20px;">
      <?php echo osC_Address::format($osC_ShoppingCart->getBillingAddress(), '<br />'); ?>
    </div>

    <div style="float: right; padding: 0px 0px 10px 20px; text-align: center;">
      <?php echo '<b>' . $osC_Language->get('billing_address_title') . '</b><br />' . osc_image(DIR_WS_IMAGES . 'arrow_south_east.gif'); ?>
    </div>

    <?php echo $osC_Language->get('choose_billing_destination'). '<br /><br />' . osc_link_object(osc_href_link(FILENAME_CHECKOUT, 'payment_address', 'SSL'), osc_draw_image_button('button_change_address.gif', $osC_Language->get('button_change_address'))); ?>

    <div style="clear: both;"></div>
  </div>
</div>

<div class="moduleBox">
  <h6><?php echo $osC_Language->get('payment_method_title'); ?></h6>

  <div class="content">

<?php
  $selection = $osC_Payment->selection();

  if (sizeof($selection) > 1) {
?>

    <div style="float: right; padding: 0px 0px 10px 20px; text-align: center;">
      <?php echo '<b>' . $osC_Language->get('please_select') . '</b><br />' . osc_image(DIR_WS_IMAGES . 'arrow_east_south.gif'); ?>
    </div>

    <p style="margin-top: 0px;"><?php echo $osC_Language->get('choose_payment_method'); ?></p>

<?php
  } else {
?>

    <p style="margin-top: 0px;"><?php echo $osC_Language->get('only_one_payment_method_available'); ?></p>

<?php
  }
?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  $radio_buttons = 0;
  for ($i=0, $n=sizeof($selection); $i<$n; $i++) {
?>

      <tr>
        <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
    if ( ($n == 1) || ($osC_ShoppingCart->hasBillingMethod() && ($selection[$i]['id'] == $osC_ShoppingCart->getBillingMethod('id'))) ) {
      echo '          <tr id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
    } else {
      echo '          <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
    }
?>

            <td width="10">&nbsp;</td>

<?php
    if ($n > 1) {
?>

            <td colspan="3"><?php echo '<b>' . $selection[$i]['module'] . '</b>'; ?></td>
            <td align="right"><?php echo osc_draw_radio_field('payment_method', $selection[$i]['id'], ($osC_ShoppingCart->hasBillingMethod() ? $osC_ShoppingCart->getBillingMethod('id') : null)); ?></td>

<?php
    } else {
?>

            <td colspan="4"><?php echo '<b>' . $selection[$i]['module'] . '</b>' . osc_draw_hidden_field('payment_method', $selection[$i]['id']); ?></td>

<?php
  }
?>

            <td width="10">&nbsp;</td>
          </tr>

<?php
    if (isset($selection[$i]['error'])) {
?>

          <tr>
            <td width="10">&nbsp;</td>
            <td colspan="4"><?php echo $selection[$i]['error']; ?></td>
            <td width="10">&nbsp;</td>
          </tr>

<?php
    } elseif (isset($selection[$i]['fields']) && is_array($selection[$i]['fields'])) {
?>

          <tr>
            <td width="10">&nbsp;</td>
            <td colspan="4"><table border="0" cellspacing="0" cellpadding="2">

<?php
      for ($j=0, $n2=sizeof($selection[$i]['fields']); $j<$n2; $j++) {
?>

              <tr>
                <td width="10">&nbsp;</td>
                <td><?php echo $selection[$i]['fields'][$j]['title']; ?></td>
                <td width="10">&nbsp;</td>
                <td><?php echo $selection[$i]['fields'][$j]['field']; ?></td>
                <td width="10">&nbsp;</td>
              </tr>

<?php
      }
?>

            </table></td>
            <td width="10">&nbsp;</td>
          </tr>

<?php
    }
?>

        </table></td>
      </tr>

<?php
    $radio_buttons++;
  }
?>

    </table>
  </div>
</div>

<div class="moduleBox">
  <h6><?php echo $osC_Language->get('add_comment_to_order_title'); ?></h6>

  <div class="content">
    <?php echo osc_draw_textarea_field('comments', (isset($_SESSION['comments']) ? $_SESSION['comments'] : null), null, null, 'style="width: 98%;"'); ?>
  </div>
</div>

<br />

<div class="moduleBox">
  <div class="content">
    <div style="float: right;">
      <?php echo osc_draw_image_submit_button('button_continue.gif', $osC_Language->get('button_continue')); ?>
    </div>

    <?php echo '<b>' . $osC_Language->get('continue_checkout_procedure_title') . '</b><br />' . $osC_Language->get('continue_checkout_procedure_to_confirmation'); ?>
  </div>
</div>

</form>
