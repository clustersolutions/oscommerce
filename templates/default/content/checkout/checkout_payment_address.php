<?php
/*
  $Id:checkout_payment_address.php 187 2005-09-14 14:22:13 +0200 (Mi, 14 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  $addresses_count = tep_count_customer_address_book_entries();
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_delivery.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<?php
  if ($messageStack->size('checkout_address') > 0) {
    echo $messageStack->output('checkout_address');
  }
?>

<form name="checkout_address" action="<?php echo tep_href_link(FILENAME_CHECKOUT, 'payment_address=process', 'SSL'); ?>" method="post" onsubmit="return check_form_optional(checkout_address);">

<?php
  if ($_GET['payment_address'] != 'process') {
    if ($osC_Customer->hasDefaultAddress()) {
?>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo $osC_Language->get('billing_address_title'); ?></div>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td valign="top"><?php echo $osC_Language->get('selected_billing_destination'); ?></td>
        <td valign="top" align="center"><?php echo '<b>' . $osC_Language->get('current_billing_address_title') . '</b><br />' . tep_image(DIR_WS_IMAGES . 'arrow_south_east.gif'); ?></td>
        <td valign="top"><?php echo tep_address_label($osC_Customer->getID(), $_SESSION['billto'], true, ' ', '<br />'); ?></td>
      </tr>
    </table>
  </div>
</div>

<?php
    }

    if ($addresses_count > 1) {
?>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo $osC_Language->get('address_book_entries_title'); ?></div>

  <div class="content">
    <div style="float: right; padding: 0px 0px 10px 20px; text-align: center;">
      <?php echo '<b>' . $osC_Language->get('please_select') . '</b><br />' . tep_image(DIR_WS_IMAGES . 'arrow_east_south.gif'); ?>
    </div>

    <p style="margin-top: 0px;"><?php echo $osC_Language->get('select_another_billing_destination'); ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
      $radio_buttons = 0;

      $Qaddresses = $osC_Template->getListing();

      while ($Qaddresses->next()) {
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
        <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
       if ($Qaddresses->valueInt('address_book_id') == $_SESSION['billto']) {
          echo '          <tr id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
        } else {
          echo '          <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
        }
?>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
            <td class="main" colspan="2"><b><?php echo $Qaddresses->valueProtected('firstname') . ' ' . $Qaddresses->valueProtected('lastname'); ?></b></td>
            <td class="main" align="right"><?php echo osc_draw_radio_field('address', $Qaddresses->valueInt('address_book_id'), $_SESSION['billto']); ?></td>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
          </tr>
          <tr>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
            <td colspan="3"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main"><?php echo tep_address_format(tep_get_address_format_id($Qaddresses->valueInt('country_id')), $Qaddresses->toArray(), true, ' ', ', '); ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
          </tr>
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

<?php
    }
  }

  if ($addresses_count < MAX_ADDRESS_BOOK_ENTRIES) {
?>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo $osC_Language->get('new_billing_address_title'); ?></div>

  <div class="content">
    <?php echo $osC_Language->get('new_billing_address'); ?>

    <div style="margin: 10px 30px 10px 30px;">
      <?php require('includes/modules/checkout_new_address.php'); ?>
    </div>
  </div>
</div>

<?php
  }
?>

<div class="moduleBox">
  <div class="content">
    <div style="float: right;">
      <?php echo tep_image_submit('button_continue.gif', $osC_Language->get('button_continue')); ?>
    </div>

    <?php echo '<b>' . $osC_Language->get('continue_checkout_procedure_title') . '</b><br />' . $osC_Language->get('continue_checkout_procedure_to_payment'); ?>
  </div>
</div>

</form>
