<?php
/*
  $Id: shipping.php 5 2005-01-31 01:40:15Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_contact_us.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<?php
  if ($messageStack->size('contact') > 0) {
    echo $messageStack->output('contact');
  }

  if (isset($_GET['contact']) && ($_GET['contact'] == 'success')) {
?>

<p><?php echo TEXT_INFO_CONTACT_TEXT_SUCCESS; ?></p>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo '<a href="' . tep_href_link(FILENAME_INFO) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></span>
</div>

<?php
  } else {
?>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo TITLE_CONTACT_INFORMATION; ?></div>

  <div class="content">
    <div style="float: right; padding: 0px 0px 10px 20px;">
      <?php echo nl2br(STORE_NAME_ADDRESS); ?>
    </div>

    <div style="float: right; padding: 0px 0px 10px 20px; text-align: center;">
      <?php echo '<b>' . TITLE_STORE_ADDRESS . '</b><br />' . tep_image(DIR_WS_IMAGES . 'arrow_south_east.gif'); ?>
    </div>

    <p style="margin-top: 0px;"><?php echo TEXT_INFO_CONTACT; ?></p>

    <p><?php echo nl2br(STORE_OWNER_EMAIL_ADDRESS); ?></p>
  </div>
</div>

<form name="contact" action="<?php echo tep_href_link(FILENAME_INFO, 'contact=process'); ?>" method="post">

<div class="moduleBox">
  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><?php echo TEXT_INFO_CONTACT_ENTRY_NAME; ?></td>
      </tr>
      <tr>
        <td><?php echo osc_draw_input_field('name'); ?></td>
      </tr>
      <tr>
        <td><?php echo TEXT_INFO_CONTACT_ENTRY_EMAIL; ?></td>
      </tr>
      <tr>
        <td><?php echo osc_draw_input_field('email'); ?></td>
      </tr>
      <tr>
        <td><?php echo TEXT_INFO_CONTACT_ENTRY_ENQUIRY; ?></td>
      </tr>
      <tr>
        <td><?php echo osc_draw_textarea_field('enquiry', '', 50, 15); ?></td>
      </tr>
    </table>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></span>
</div>

</form>

<?php
  }
?>
