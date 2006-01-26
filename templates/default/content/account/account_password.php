<?php
/*
  $Id:account_password.php 187 2005-09-14 14:22:13 +0200 (Mi, 14 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_account.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<?php
  if ($messageStack->size('account_password') > 0) {
    echo $messageStack->output('account_password');
  }
?>

<form name="account_password" action="<?php echo tep_href_link(FILENAME_ACCOUNT, 'password=save', 'SSL'); ?>" method="post" onsubmit="return check_form(account_password);">

<div class="moduleBox">
  <div class="outsideHeading">
    <span class="inputRequirement" style="float: right;"><?php echo $osC_Language->get('form_required_information'); ?></span>

    <?php echo $osC_Language->get('my_password_title'); ?>
  </div>

  <div class="content">
    <table border="0" cellspacing="2" cellpadding="2">
      <tr>
        <td><?php echo $osC_Language->get('field_customer_password_current'); ?></td>
        <td><?php echo osc_draw_password_field('password_current', '', true); ?></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><?php echo $osC_Language->get('field_customer_password_new'); ?></td>
        <td><?php echo osc_draw_password_field('password_new', '', true); ?></td>
      </tr>
      <tr>
        <td><?php echo $osC_Language->get('field_customer_password_confirmation'); ?></td>
        <td><?php echo osc_draw_password_field('password_confirmation', '', true); ?></td>
      </tr>
    </table>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo tep_image_submit('button_continue.gif', $osC_Language->get('button_continue')); ?></span>

  <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_back.gif', $osC_Language->get('button_back')) . '</a>'; ?>
</div>

</form>
