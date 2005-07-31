<?php
/*
  $Id$

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

<form name="account_password" action="<?php echo tep_href_link(FILENAME_ACCOUNT, 'password=save', 'SSL'); ?>" method="post" onSubmit="return check_form(account_password);">

<div class="moduleBox">
  <div class="outsideHeading">
    <span class="inputRequirement" style="float: right;"><?php echo FORM_REQUIRED_INFORMATION; ?></span>

    <?php echo MY_PASSWORD_TITLE; ?>
  </div>

  <div class="content">
    <table border="0" cellspacing="2" cellpadding="2">
      <tr>
        <td><?php echo ENTRY_PASSWORD_CURRENT; ?></td>
        <td><?php echo osc_draw_password_field('password_current', '', true); ?></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><?php echo ENTRY_PASSWORD_NEW; ?></td>
        <td><?php echo osc_draw_password_field('password_new', '', true); ?></td>
      </tr>
      <tr>
        <td><?php echo ENTRY_PASSWORD_CONFIRMATION; ?></td>
        <td><?php echo osc_draw_password_field('password_confirmation', '', true); ?></td>
      </tr>
    </table>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></span>

  <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
</div>

</form>
