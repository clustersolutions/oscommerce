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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html <?php echo HTML_PARAMS; ?>>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">

<title><?php echo TITLE . ($osC_Template->hasPageTitle() ? ': ' . $osC_Template->getPageTitle() : ''); ?></title>

<base href="<?php echo tep_href_link('', '', 'AUTO', false); ?>">

<link rel="stylesheet" type="text/css" href="templates/<?php echo $osC_Template->getTemplate(); ?>/stylesheet.css">

<?php
  if ($osC_Template->hasJavascript()) {
    $osC_Template->getJavascript();
  }
?>

</head>

<body>

<div id="pageContent">
  <?php require('includes/content/pages/' . $osC_Template->getPageContentsFilename()); ?>
</div>

<div id="pageHeader">
  <div style="float: left;">
    <?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image(DIR_WS_IMAGES . 'oscommerce.gif', 'osCommerce') . '</a>'; ?>
  </div>

  <div style="text-align: right; padding-top: 20px;">
    <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'header_account.gif', HEADER_TITLE_MY_ACCOUNT) . '</a>&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CHECKOUT, '', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'header_cart.gif', HEADER_TITLE_CART_CONTENTS) . '</a>&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'header_checkout.gif', HEADER_TITLE_CHECKOUT) . '</a>'; ?>
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

<div id="pageFooter">
  <div id="footerBar">
    <div style="float: left;"><?php echo strftime(DATE_FORMAT_LONG); ?></div>

    <div style="text-align: right;"><?php if ($messageStack->size('counter')) echo $messageStack->outputPlain('counter'); ?></div>
  </div>

  <?php echo '<p align="center">' . FOOTER_TEXT_BODY . '</p>'; ?>
</div>

<?php
  if ($osC_Services->isStarted('banner') && $osC_Banner->exists('468x60')) {
    echo '<p align="center">' . $osC_Banner->display() . '</p>';
  }
?>

</body>

</html>
