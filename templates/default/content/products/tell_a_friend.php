<?php
/*
  $Id: tell_a_friend.php 210 2005-10-04 07:47:42 +0200 (Di, 04 Okt 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<h1 style="float: right;"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS, $osC_Product->getKeyword()) . '">' . tep_image(DIR_WS_IMAGES . $osC_Product->getImage(), $osC_Product->getTitle()) . '</a>'; ?></h1>

<h1><?php echo $osC_Template->getPageTitle() . ($osC_Product->hasModel() ? '<br /><span class="smallText">' . $osC_Product->getModel() . '</span>' : ''); ?></h1>

<?php
  if ($messageStack->size('tell_a_friend') > 0) {
    echo $messageStack->output('tell_a_friend');
  }
?>

<form name="tell_a_friend" action="<?php echo tep_href_link(FILENAME_PRODUCTS, 'tell_a_friend&amp;' . $osC_Product->getKeyword() . '&amp;action=process'); ?>" method="post">

<div class="moduleBox">
  <div class="outsideHeading">
    <span class="inputRequirement" style="float: right;"><?php echo FORM_REQUIRED_INFORMATION; ?></span>

    <?php echo FORM_TITLE_CUSTOMER_DETAILS; ?>
  </div>

  <div class="content">
    <table border="0" cellspacing="2" cellpadding="2">
      <tr>
        <td><?php echo FORM_FIELD_CUSTOMER_NAME; ?></td>
        <td><?php echo osc_draw_input_field('from_name', ($osC_Customer->isLoggedOn() ? $osC_Customer->getName() : ''), '', true); ?></td>
      </tr>
      <tr>
        <td><?php echo FORM_FIELD_CUSTOMER_EMAIL; ?></td>
        <td><?php echo osc_draw_input_field('from_email_address', ($osC_Customer->isLoggedOn() ? $osC_Customer->getEmailAddress() : ''), '', true); ?></td>
      </tr>
    </table>
  </div>
</div>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo FORM_TITLE_FRIEND_DETAILS; ?></div>

  <div class="content">
    <table border="0" cellspacing="2" cellpadding="2">
      <tr>
        <td><?php echo FORM_FIELD_FRIEND_NAME; ?></td>
        <td><?php echo osc_draw_input_field('to_name', '', '', true); ?></td>
      </tr>
      <tr>
        <td><?php echo FORM_FIELD_FRIEND_EMAIL; ?></td>
        <td><?php echo osc_draw_input_field('to_email_address', '', '', true); ?></td>
      </tr>
    </table>
  </div>
</div>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo FORM_TITLE_FRIEND_MESSAGE; ?></div>

  <div class="content">
    <table border="0" width="100%" cellspacing="2" cellpadding="2">
      <tr>
        <td><?php echo osc_draw_textarea_field('message', '', 40, 8); ?></td>
      </tr>
    </table>
  </div>
</div>


<div class="submitFormButtons">
  <span style="float: right;"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></span>

  <?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS, $osC_Product->getKeyword()) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
</div>

</form>
