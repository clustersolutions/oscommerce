<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License

  osCommerce Copyright Policy:
  http://www.oscommerce.com/about/copyright

  osCommerce Trademark Policy:
  http://www.oscommerce.com/about/trademark

  GNU General Public License:
  http://www.gnu.org/licenses/gpl.html
*/

  $template = 'default';
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html <?php echo HTML_PARAMS; ?>>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">

<title><?php echo TITLE; ?></title>

<link rel="stylesheet" type="text/css" href="templates/default/stylesheet.css">

<script language="javascript" src="includes/general.js"></script>

</head>

<body>

<div id="pageHeader">
  <div style="height: 50px">
    <div style="float: left;">
      <?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image(DIR_WS_IMAGES . 'oscommerce.gif', 'osCommerce') . '</a>'; ?>
    </div>

    <div style="text-align: right; padding-top: 20px;">
      <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'header_account.gif', HEADER_TITLE_MY_ACCOUNT) . '</a>&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CHECKOUT, '', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'header_cart.gif', HEADER_TITLE_CART_CONTENTS) . '</a>&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'header_checkout.gif', HEADER_TITLE_CHECKOUT) . '</a>'; ?>
    </div>
  </div>

  <div id="headerBar">

<?php
  if ($osC_Services->isStarted('breadcrumb')) {
    echo '<div id="breadcrumb" style="float: left;">' . $breadcrumb->trail(' &raquo; ') . '</div>';
  }
?>

    <div style="text-align: right;">

<?php
  if ($osC_Customer->isLoggedOn()) {
    echo '<a href="' . tep_href_link(FILENAME_LOGOFF, '', 'SSL') . '" class="headerNavigation">' . HEADER_TITLE_LOGOFF . '</a> &nbsp;|&nbsp; ';
  }

  echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '" class="headerNavigation">' . HEADER_TITLE_MY_ACCOUNT . '</a> &nbsp;|&nbsp; <a href="' . tep_href_link(FILENAME_CHECKOUT, '', 'SSL') . '" class="headerNavigation">' . HEADER_TITLE_CART_CONTENTS . '</a> &nbsp;|&nbsp; <a href="' . tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL') . '" class="headerNavigation">' . HEADER_TITLE_CHECKOUT . '</a>';
?>

    </div>
  </div>
</div>

<div id="pageColumnLeft">
  <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
  </table>
</div>

<div id="pageColumnRight">
  <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
  </table>
</div>

<div id="pageContent">
  <?php require('includes/content/pages/' . $page_contents); ?>
</div>

<div id="pageFooter">
  <div id="footerBar">
    <div style="float: left;"><?php echo strftime(DATE_FORMAT_LONG); ?></div>

    <div style="text-align: right;"><?php if ($messageStack->size('counter')) echo $messageStack->outputPlain('counter'); ?></div>
  </div>

<?php
/*
  The following copyright announcement can only be
  appropriately modified or removed if the layout of
  the site theme has been modified to distinguish
  itself from the default osCommerce-copyrighted
  theme.

  For more information please read the osCommerce
  copyright policy found here:

  http://www.oscommerce.com/about/copyright

  Please leave this comment intact together with the
  following copyright announcement.
*/

  echo '<p align="center">' . FOOTER_TEXT_BODY . '</p>';

  if ($osC_Services->isStarted('banner') && $osC_Banner->exists('468x60')) {
    echo '<p align="center">' . $osC_Banner->display() . '</p>';
  }
?>

</div>

</body>

</html>
