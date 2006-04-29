<?php
/*
  $Id:checkout_payment.php 187 2005-09-14 14:22:13 +0200 (Mi, 14 Sep 2005) hpdl $

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
  if (DISPLAY_CONDITIONS_ON_CHECKOUT == '1') {
?>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo $osC_Language->get('order_conditions_title'); ?></div>

  <div class="content">
    <?php echo sprintf($osC_Language->get('order_conditions_description'), tep_href_link(FILENAME_INFO, 'conditions', 'AUTO')) . '<br /><br />' . osc_draw_checkbox_field('conditions', '1', false, 'id="conditions"') . '<label for="conditions">&nbsp;' . $osC_Language->get('order_conditions_acknowledge') . '</label>'; ?>
  </div>
</div>

<?php
  }
?>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo $osC_Language->get('billing_address_title'); ?></div>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td valign="top"><?php echo $osC_Language->get('choose_billing_destination'); ?><br /><br /><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT, 'payment_address', 'SSL') . '">' . tep_image_button('button_change_address.gif', $osC_Language->get('button_change_address')) . '</a>'; ?></td>
        <td valign="top" align="center"><?php echo '<b>' . $osC_Language->get('billing_address_title') . '</b><br />' . tep_image(DIR_WS_IMAGES . 'arrow_south_east.gif'); ?></td>
        <td valign="top"><?php echo tep_address_label($osC_Customer->getID(), $_SESSION['billto'], true, ' ', '<br />'); ?></td>
      </tr>
    </table>
  </div>
</div>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo $osC_Language->get('payment_method_title'); ?></div>

  <div class="content">

<?php
  $selection = $payment_modules->selection();

  if (sizeof($selection) > 1) {
?>

    <div style="float: right; padding: 0px 0px 10px 20px; text-align: center;">
      <?php echo '<b>' . $osC_Language->get('please_select') . '</b><br />' . tep_image(DIR_WS_IMAGES . 'arrow_east_south.gif'); ?>
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
        <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
        <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
    if ( ($n == 1) || ($selection[$i]['id'] == $_SESSION['payment']) ) {
      echo '          <tr id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
    } else {
      echo '          <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
    }
?>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
            <td class="main" colspan="3"><b><?php echo $selection[$i]['module']; ?></b></td>
            <td class="main" align="right"><?php echo osc_draw_radio_field('payment_mod_sel', $selection[$i]['id'], $_SESSION['payment']); ?></td>
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
  <div class="outsideHeading"><?php echo $osC_Language->get('add_comment_to_order_title'); ?></div>

  <div class="content">
    <?php echo osc_draw_textarea_field('comments', (isset($_SESSION['comments']) ? $_SESSION['comments'] : '')); ?>
  </div>
</div>

<div class="moduleBox">
  <div class="content">
    <div style="float: right;">
      <?php echo tep_image_submit('button_continue.gif', $osC_Language->get('button_continue')); ?>
    </div>

    <?php echo '<b>' . $osC_Language->get('continue_checkout_procedure_title') . '</b><br />' . $osC_Language->get('continue_checkout_procedure_to_confirmation'); ?>
  </div>
</div>

</form>
