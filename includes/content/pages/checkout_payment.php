<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_payment.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<?php
  if ($messageStack->size('checkout_payment') > 0) {
    echo $messageStack->output('checkout_payment');
  }
?>

<form name="checkout_payment" action="<?php echo tep_href_link(FILENAME_CHECKOUT, 'confirmation', 'SSL'); ?>" method="post" onsubmit="return check_form();">

<?php
  if (DISPLAY_CONDITIONS_ON_CHECKOUT == 'true') {
?>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo TABLE_HEADING_CONDITIONS; ?></div>

  <div class="content">
    <?php echo TEXT_CONDITIONS_DESCRIPTION . '<br><br>' . osc_draw_checkbox_field('conditions', '1', false, 'id="conditions"') . '<label for="conditions">&nbsp;' . TEXT_CONDITIONS_CONFIRM . '</label>'; ?>
  </div>
</div>

<?php
  }
?>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo TABLE_HEADING_BILLING_ADDRESS; ?></div>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td valign="top"><?php echo TEXT_SELECTED_BILLING_DESTINATION; ?><br><br><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT, 'payment_address', 'SSL') . '">' . tep_image_button('button_change_address.gif', IMAGE_BUTTON_CHANGE_ADDRESS) . '</a>'; ?></td>
        <td valign="top" align="center"><?php echo '<b>' . TITLE_BILLING_ADDRESS . '</b><br>' . tep_image(DIR_WS_IMAGES . 'arrow_south_east.gif'); ?></td>
        <td valign="top"><?php echo tep_address_label($osC_Customer->id, $osC_Session->value('billto'), true, ' ', '<br>'); ?></td>
      </tr>
    </table>
  </div>
</div>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo TABLE_HEADING_PAYMENT_METHOD; ?></div>

  <div class="content">

<?php
  $selection = $payment_modules->selection();

  if (sizeof($selection) > 1) {
?>

    <div style="float: right; padding: 0px 0px 10px 20px; text-align: center;">
      <?php echo '<b>' . TITLE_PLEASE_SELECT . '</b><br>' . tep_image(DIR_WS_IMAGES . 'arrow_east_south.gif'); ?>
    </div>

    <p style="margin-top: 0px;"><?php echo TEXT_SELECT_PAYMENT_METHOD; ?></p>

<?php
  } else {
?>

    <p style="margin-top: 0px;"><?php echo TEXT_ENTER_PAYMENT_INFORMATION; ?></p>

<?php
  }
?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  $radio_buttons = 0;
  for ($i=0, $n=sizeof($selection); $i<$n; $i++) {
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
        <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
    if ( ($n == 1) || ($selection[$i]['id'] == $osC_Session->value('payment')) ) {
      echo '          <tr id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
    } else {
      echo '          <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
    }
?>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
            <td class="main" colspan="3"><b><?php echo $selection[$i]['module']; ?></b></td>
            <td class="main" align="right"><?php echo osc_draw_radio_field('payment_mod_sel', $selection[$i]['id'], $osC_Session->value('payment')); ?></td>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
          </tr>
<?php
    if (isset($selection[$i]['error'])) {
?>
          <tr>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
            <td class="main" colspan="4"><?php echo $selection[$i]['error']; ?></td>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
          </tr>
<?php
    } elseif (isset($selection[$i]['fields']) && is_array($selection[$i]['fields'])) {
?>
          <tr>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
            <td colspan="4"><table border="0" cellspacing="0" cellpadding="2">
<?php
      for ($j=0, $n2=sizeof($selection[$i]['fields']); $j<$n2; $j++) {
?>
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main"><?php echo $selection[$i]['fields'][$j]['title']; ?></td>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main"><?php echo $selection[$i]['fields'][$j]['field']; ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
<?php
      }
?>
            </table></td>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
          </tr>
<?php
    }
?>
        </table></td>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
      </tr>
<?php
    $radio_buttons++;
  }
?>

    </table>
  </div>
</div>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo TABLE_HEADING_COMMENTS; ?></div>

  <div class="content">
    <?php echo osc_draw_textarea_field('comments', ($osC_Session->exists('comments') ? $osC_Session->value('comments') : '')); ?>
  </div>
</div>

<div class="moduleBox">
  <div class="content">
    <div style="float: right;">
      <?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?>
    </div>

    <?php echo '<b>' . TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE_TO_CONFIRMATION; ?>
  </div>
</div>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td>
        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
      </tr>
    </table></td>
    <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
        <td><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td>
        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
      </tr>
    </table></td>
    <td width="25%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
    <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="center" width="25%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_DELIVERY . '</a>'; ?></td>
    <td align="center" width="25%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_PAYMENT; ?></td>
    <td align="center" width="25%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td>
    <td align="center" width="25%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_FINISHED; ?></td>
  </tr>
</table>

</form>
