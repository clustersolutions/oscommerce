<?php
/*
  $Id: product_reviews_write.php 213 2005-10-05 12:37:33 +0200 (Mi, 05 Okt 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<h1 style="float: right;"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS, $osC_Product->getID()) . '">' . tep_image(DIR_WS_IMAGES . $osC_Product->getImage(), $osC_Product->getTitle()) . '</a>'; ?></h1>

<h1><?php echo $osC_Template->getPageTitle() . ($osC_Product->hasModel() ? '<br /><span class="smallText">' . $osC_Product->getModel() . '</span>' : ''); ?></h1>

<?php
  if ($messageStack->size('reviews') > 0) {
    echo $messageStack->output('reviews');
  }
?>

<form name="reviews_new" action="<?php echo tep_href_link(FILENAME_PRODUCTS, 'reviews=new&amp;' . $osC_Product->getID() . '&amp;action=process'); ?>" method="post" onsubmit="return checkForm(this);">

<div class="moduleBox">
  <div class="outsideHeading">
    <?php echo TEXT_NEW_REVIEW; ?>
  </div>

  <div class="content">
    <table border="0" cellspacing="2" cellpadding="2">

<?php
  if ($osC_Customer->isLoggedOn()) {
?>

      <tr>
        <td><?php echo TEXT_REVIEW_FROM; ?></td>
        <td><?php echo tep_output_string_protected($osC_Customer->getName()); ?></td>
      </tr>

<?php
  } else {
?>

      <tr>
        <td><?php echo ENTRY_NAME; ?></td>
        <td><?php echo osc_draw_input_field('customer_name'); ?></td>
      </tr>
      <tr>
        <td><?php echo ENTRY_EMAIL_ADDRESS; ?></td>
        <td><?php echo osc_draw_input_field('customer_email_address'); ?></td>
      </tr>

<?php
  }
?>

    </table>

    <table border="0" width="100%" cellspacing="2" cellpadding="2">
      <tr>
        <td><?php echo osc_draw_textarea_field('review', '', 60, 15); ?></td>
      </tr>
      <tr>
        <td><?php echo TEXT_REVIEW_SET_RATING . ' ' . TEXT_REVIEW_BAD . ' ' . osc_draw_radio_field('rating', array('1', '2', '3', '4', '5')) . ' ' . TEXT_REVIEW_GOOD; ?></td>
      </tr>
    </table>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></span>

  <?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS, 'reviews&amp;' . $osC_Product->getID()) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
</div>

</form>
