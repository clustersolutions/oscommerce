<?php
/*
  $Id: default.php 352 2005-12-19 11:55:30Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $osC_Language->getTextDirection(); ?>" xml:lang="<?php echo $osC_Language->getCode(); ?>" lang="<?php echo $osC_Language->getCode(); ?>">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $osC_Language->getCharacterSet(); ?>" />

<title><?php echo STORE_NAME . ($osC_Template->hasPageTitle() ? ': ' . $osC_Template->getPageTitle() : ''); ?></title>

<base href="<?php echo osc_href_link(null, null, 'AUTO', false); ?>" />

<link rel="stylesheet" type="text/css" href="templates/<?php echo $osC_Template->getCode(); ?>/stylesheet.css" />

<?php
  if ($osC_Template->hasPageTags()) {
    echo $osC_Template->getPageTags();
  }

  if ($osC_Template->hasJavascript()) {
    $osC_Template->getJavascript();
  }
?>

<meta name="Generator" content="osCommerce" />

</head>

<body>

<?php
  if ($osC_Template->hasPageHeader()) {
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2" id="pageHeader">
  <tr>
    <td id="headerLogo">

<?php
    echo osc_link_object(osc_href_link(FILENAME_DEFAULT), osc_image(DIR_WS_IMAGES . 'store_logo.jpg', STORE_NAME), 'id="siteLogo"');
?>

    </td>
    <td id="headerIcons" align="right">

<?php
    echo osc_link_object(osc_href_link(FILENAME_ACCOUNT, null, 'SSL'), osc_image(DIR_WS_IMAGES . 'header_account.gif', $osC_Language->get('my_account'))) . '&nbsp;&nbsp;' .
         osc_link_object(osc_href_link(FILENAME_CHECKOUT, null, 'SSL'), osc_image(DIR_WS_IMAGES . 'header_cart.gif', $osC_Language->get('cart_contents'))) . '&nbsp;&nbsp;' .
         osc_link_object(osc_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'), osc_image(DIR_WS_IMAGES . 'header_checkout.gif', $osC_Language->get('checkout')));
?>

    </td>
  </tr>
  <tr id="headerBar">
    <td id="breadcrumb">

<?php
    if ($osC_Services->isStarted('breadcrumb')) {
      echo $osC_Breadcrumb->getPath();
    }
?>

    </td>
    <td align="right">

<?php
    if ($osC_Customer->isLoggedOn()) {
      echo osc_link_object(osc_href_link(FILENAME_ACCOUNT, 'logoff', 'SSL'), $osC_Language->get('sign_out'), 'class="headerNavigation"') . ' &nbsp;|&nbsp; ';
    }

    echo osc_link_object(osc_href_link(FILENAME_ACCOUNT, null, 'SSL'), $osC_Language->get('my_account'), 'class="headerNavigation"') . ' &nbsp;|&nbsp; ' .
         osc_link_object(osc_href_link(FILENAME_CHECKOUT, null, 'SSL'), $osC_Language->get('cart_contents'), 'class="headerNavigation"') . ' &nbsp;|&nbsp; ' .
         osc_link_object(osc_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'), $osC_Language->get('checkout'), 'class="headerNavigation"');
?>

    </td>
  </tr>
</table>

<?php
  } // if ($osC_Template->hasPageHeader())

  $left_content = '';

  if ($osC_Template->hasPageBoxModules()) {
    ob_start();

    foreach ($osC_Template->getBoxModules('left') as $box) {
      $osC_Box = new $box();
      $osC_Box->initialize();

      if ($osC_Box->hasContent()) {
        if ($osC_Template->getCode() == DEFAULT_TEMPLATE) {
          include('templates/' . $osC_Template->getCode() . '/modules/boxes/' . $osC_Box->getCode() . '.php');
        } else {
          if (file_exists('templates/' . $osC_Template->getCode() . '/modules/boxes/' . $osC_Box->getCode() . '.php')) {
            include('templates/' . $osC_Template->getCode() . '/modules/boxes/' . $osC_Box->getCode() . '.php');
          } else {
            include('templates/' . DEFAULT_TEMPLATE . '/modules/boxes/' . $osC_Box->getCode() . '.php');
          }
        }
      }

      unset($osC_Box);
    }

    $left_content = ob_get_contents();
    ob_end_clean();
  }
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>

<?php
  if (!empty($left_content)) {
?>

    <td id="pageColumnLeft" valign="top">
      <div class="boxGroup">

<?php
    echo $left_content;
?>

      </div>
    </td>

<?php
  }

  unset($left_content);

?>

    <td id="pageContent" valign="top">

<?php
  if ($osC_MessageStack->size('header') > 0) {
    echo $osC_MessageStack->get('header');
  }

  if ($osC_Template->hasPageContentModules()) {
    foreach ($osC_Services->getCallBeforePageContent() as $service) {
      $$service[0]->$service[1]();
    }

    foreach ($osC_Template->getContentModules('before') as $box) {
      $osC_Box = new $box();
      $osC_Box->initialize();

      if ($osC_Box->hasContent()) {
        if ($osC_Template->getCode() == DEFAULT_TEMPLATE) {
          include('templates/' . $osC_Template->getCode() . '/modules/content/' . $osC_Box->getCode() . '.php');
        } else {
          if (file_exists('templates/' . $osC_Template->getCode() . '/modules/content/' . $osC_Box->getCode() . '.php')) {
            include('templates/' . $osC_Template->getCode() . '/modules/content/' . $osC_Box->getCode() . '.php');
          } else {
            include('templates/' . DEFAULT_TEMPLATE . '/modules/content/' . $osC_Box->getCode() . '.php');
          }
        }
      }

      unset($osC_Box);
    }
  }

  if ($osC_Template->getCode() == DEFAULT_TEMPLATE) {
    include('templates/' . $osC_Template->getCode() . '/content/' . $osC_Template->getGroup() . '/' . $osC_Template->getPageContentsFilename());
  } else {
    if (file_exists('templates/' . $osC_Template->getCode() . '/content/' . $osC_Template->getGroup() . '/' . $osC_Template->getPageContentsFilename())) {
      include('templates/' . $osC_Template->getCode() . '/content/' . $osC_Template->getGroup() . '/' . $osC_Template->getPageContentsFilename());
    } else {
      include('templates/' . DEFAULT_TEMPLATE . '/content/' . $osC_Template->getGroup() . '/' . $osC_Template->getPageContentsFilename());
    }
  }

  if ($osC_Template->hasPageContentModules()) {
    foreach ($osC_Services->getCallAfterPageContent() as $service) {
      $$service[0]->$service[1]();
    }

    foreach ($osC_Template->getContentModules('after') as $box) {
      $osC_Box = new $box();
      $osC_Box->initialize();

      if ($osC_Box->hasContent()) {
        if ($osC_Template->getCode() == DEFAULT_TEMPLATE) {
          include('templates/' . $osC_Template->getCode() . '/modules/content/' . $osC_Box->getCode() . '.php');
        } else {
          if (file_exists('templates/' . $osC_Template->getCode() . '/modules/content/' . $osC_Box->getCode() . '.php')) {
            include('templates/' . $osC_Template->getCode() . '/modules/content/' . $osC_Box->getCode() . '.php');
          } else {
            include('templates/' . DEFAULT_TEMPLATE . '/modules/content/' . $osC_Box->getCode() . '.php');
          }
        }
      }

      unset($osC_Box);
    }
  }
?>

    </td>

<?php
  $content_right = '';

  if ($osC_Template->hasPageBoxModules()) {
    ob_start();

    foreach ($osC_Template->getBoxModules('right') as $box) {
      $osC_Box = new $box();
      $osC_Box->initialize();

      if ($osC_Box->hasContent()) {
        if ($osC_Template->getCode() == DEFAULT_TEMPLATE) {
          include('templates/' . $osC_Template->getCode() . '/modules/boxes/' . $osC_Box->getCode() . '.php');
        } else {
          if (file_exists('templates/' . $osC_Template->getCode() . '/modules/boxes/' . $osC_Box->getCode() . '.php')) {
            include('templates/' . $osC_Template->getCode() . '/modules/boxes/' . $osC_Box->getCode() . '.php');
          } else {
            include('templates/' . DEFAULT_TEMPLATE . '/modules/boxes/' . $osC_Box->getCode() . '.php');
          }
        }
      }

      unset($osC_Box);
    }

    $content_right = ob_get_contents();
    ob_end_clean();
  }

  if (!empty($content_right)) {
?>

    <td id="pageColumnRight" valign="top">
      <div class="boxGroup">

<?php
    echo $content_right;
?>

      </div>
    </td>

<?php
  }

  unset($content_right);
?>

  </tr>
</table>

<?php
  if ($osC_Template->hasPageFooter()) {
?>

<table border="0" width="100%" cellspacing="0" cellspacing="2">
  <tr id="footerBar">
    <td>

<?php
    echo osC_DateTime::getLong();
?>

    </td>
  </tr>
  <tr>
    <td>

<?php
    echo '<p align="center">' . sprintf($osC_Language->get('footer'), date('Y'), osc_href_link(FILENAME_DEFAULT), STORE_NAME) . '</p>';
?>

    </td>
  </tr>
</table>

<?php
    if ($osC_Services->isStarted('banner') && $osC_Banner->exists('468x60')) {
      echo '<p align="center">' . $osC_Banner->display() . '</p>';
    }
  }
?>

</body>

</html>
