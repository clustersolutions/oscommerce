<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\OSCOM;
  use osCommerce\OM\Registry;
?>

<?php echo '<?xml version="1.0" encoding="utf-8"?>'; // short_open_tag compatibility ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $OSCOM_Language->getTextDirection(); ?>" xml:lang="<?php echo $OSCOM_Language->getCode(); ?>">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $OSCOM_Language->getCharacterSet(); ?>" />

<title><?php echo STORE_NAME . ($OSCOM_Template->hasPageTitle() ? ': ' . $OSCOM_Template->getPageTitle() : ''); ?></title>

<link rel="icon" type="image/png" href="<?php echo OSCOM::getPublicSiteLink('images/store_icon.png'); ?>" />

<meta name="generator" value="osCommerce Online Merchant" />

<script type="text/javascript" src="public/external/jquery/jquery-1.4.2.min.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo OSCOM::getPublicSiteLink('templates/oscom/stylesheets/general.css'); ?>" />

<?php
  if ( $OSCOM_Template->hasPageTags() ) {
    echo $OSCOM_Template->getPageTags();
  }

  if ($OSCOM_Template->hasJavascript()) {
    $OSCOM_Template->getJavascript();
  }
?>

</head>

<body>

<div id="pageBlockLeft">
  <div id="pageContent">

<?php
  if ( $OSCOM_MessageStack->exists('header') ) {
    echo $OSCOM_MessageStack->get('header');
  }

  if ( $OSCOM_Template->hasPageContentModules() ) {
    foreach ( $OSCOM_Service->getCallBeforePageContent() as $service ) {
      Registry::get($service[0])->$service[1]();
    }

    foreach ( $OSCOM_Template->getContentModules('before') as $content_module ) {
      $OSCOM_ContentModule = new $content_module();
      $OSCOM_ContentModule->initialize();

      if ( $OSCOM_ContentModule->hasContent() ) { // HPDL move logic elsewhere
        if ( $OSCOM_Template->getCode() == DEFAULT_TEMPLATE ) {
          include(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/Module/Content/' . $OSCOM_ContentModule->getCode() . '/pages/main.php');
        } else { //HPDL old
          if (file_exists('templates/' . $osC_Template->getCode() . '/modules/content/' . $osC_Box->getCode() . '.php')) {
            include('templates/' . $osC_Template->getCode() . '/modules/content/' . $osC_Box->getCode() . '.php');
          } else {
            include('templates/' . DEFAULT_TEMPLATE . '/modules/content/' . $osC_Box->getCode() . '.php');
          }
        }
      }

      unset($OSCOM_ContentModule);
    }
  }

  if ( $OSCOM_Template->getCode() == DEFAULT_TEMPLATE ) {
    include(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/applications/' . OSCOM::getSiteApplication() . '/pages/' . $OSCOM_Template->getPageContentsFilename());
  } else { // HPDL old
    if (file_exists('templates/' . $osC_Template->getCode() . '/content/' . $osC_Template->getGroup() . '/' . $osC_Template->getPageContentsFilename())) {
      include('templates/' . $osC_Template->getCode() . '/content/' . $osC_Template->getGroup() . '/' . $osC_Template->getPageContentsFilename());
    } else {
      include('templates/' . DEFAULT_TEMPLATE . '/content/' . $osC_Template->getGroup() . '/' . $osC_Template->getPageContentsFilename());
    }
  }
?>

<div style="clear: both;"></div>

<?php
  if ( $OSCOM_Template->hasPageContentModules() ) {
    foreach ( $OSCOM_Service->getCallAfterPageContent() as $service ) {
      Registry::get($service[0])->$service[1]();
    }

    foreach ( $OSCOM_Template->getContentModules('after') as $content_module ) {
      $OSCOM_ContentModule = new $content_module();
      $OSCOM_ContentModule->initialize();

      if ( $OSCOM_ContentModule->hasContent() ) { // HPDL move logic elsewhere
        if ( $OSCOM_Template->getCode() == DEFAULT_TEMPLATE ) {
          include(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/Module/Content/' . $OSCOM_ContentModule->getCode() . '/pages/main.php');
        } else { //HPDL old
          if (file_exists('templates/' . $osC_Template->getCode() . '/modules/content/' . $osC_Box->getCode() . '.php')) {
            include('templates/' . $osC_Template->getCode() . '/modules/content/' . $osC_Box->getCode() . '.php');
          } else {
            include('templates/' . DEFAULT_TEMPLATE . '/modules/content/' . $osC_Box->getCode() . '.php');
          }
        }
      }

      unset($OSCOM_ContentModule);
    }
  }
?>

  </div>

<?php
  $content_left = '';

  if ( $OSCOM_Template->hasPageBoxModules() ) {
    ob_start();

    foreach ( $OSCOM_Template->getBoxModules('left') as $box ) {
      $OSCOM_Box = new $box();
      $OSCOM_Box->initialize();

      if ( $OSCOM_Box->hasContent() ) { // HPDL move logic elsewhere
        if ( $OSCOM_Template->getCode() == DEFAULT_TEMPLATE ) {
          include(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/Module/Box/' . $OSCOM_Box->getCode() . '/pages/main.php');
        } else { //HPDL old
          if (file_exists('templates/' . $osC_Template->getCode() . '/modules/boxes/' . $osC_Box->getCode() . '.php')) {
            include('templates/' . $osC_Template->getCode() . '/modules/boxes/' . $osC_Box->getCode() . '.php');
          } else {
            include('templates/' . DEFAULT_TEMPLATE . '/modules/boxes/' . $osC_Box->getCode() . '.php');
          }
        }
      }

      unset($OSCOM_Box);
    }

    $content_left = ob_get_contents();
    ob_end_clean();
  }

  if ( !empty($content_left) ) {
?>

  <div id="pageColumnLeft">
    <div class="boxGroup">

<?php
    echo $content_left;
?>

    </div>
  </div>

<?php
  } else {
?>

<style type="text/css">
#pageContent {
  width: 99%;
  padding-left: 5px;
}
</style>

<?php
  }
?>

</div>

<?php
  $content_right = '';

  if ( $OSCOM_Template->hasPageBoxModules() ) {
    ob_start();

    foreach ( $OSCOM_Template->getBoxModules('right') as $box ) {
      $OSCOM_Box = new $box();
      $OSCOM_Box->initialize();

      if ( $OSCOM_Box->hasContent() ) { // HPDL move logic elsewhere
        if ( $OSCOM_Template->getCode() == DEFAULT_TEMPLATE ) {
          include(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/Module/Box/' . $OSCOM_Box->getCode() . '/pages/main.php');
        } else { //HPDL old
          if (file_exists('templates/' . $osC_Template->getCode() . '/modules/boxes/' . $osC_Box->getCode() . '.php')) {
            include('templates/' . $osC_Template->getCode() . '/modules/boxes/' . $osC_Box->getCode() . '.php');
          } else {
            include('templates/' . DEFAULT_TEMPLATE . '/modules/boxes/' . $osC_Box->getCode() . '.php');
          }
        }
      }

      unset($OSCOM_Box);
    }

    $content_right = ob_get_contents();
    ob_end_clean();
  }

  if (!empty($content_right)) {
?>

<div id="pageColumnRight">
  <div class="boxGroup">

<?php
    echo $content_right;
?>

  </div>
</div>

<?php
  } elseif (empty($content_left)) {
?>

<style type="text/css"><!--
#pageBlockLeft {
  width: 99%;
}
//--></style>

<?php
  } else {
?>

<style type="text/css"><!--
#pageContent {
  width: 82%;
  padding-right: 5px;
}

#pageBlockLeft {
  width: 99%;
}

#pageColumnLeft {
  width: 16%;
}
//--></style>

<?php
  }

  unset($content_left);
  unset($content_right);

  if ( $OSCOM_Template->hasPageHeader() ) {
?>

<div id="pageHeader">

<?php
    echo osc_link_object(OSCOM::getLink(OSCOM::getDefaultSite(), OSCOM::getDefaultSiteApplication()), osc_image(DIR_WS_IMAGES . 'store_logo.jpg', STORE_NAME), 'id="siteLogo"');
?>

  <ul id="navigationIcons">

<?php
    echo '<li>' . osc_link_object(OSCOM::getLink(null, 'Account', null, 'SSL'), osc_image(DIR_WS_IMAGES . 'header_account.gif', OSCOM::getDef('my_account'))) . '</li>' .
         '<li>' . osc_link_object(OSCOM::getLink(null, 'Checkout', null, 'SSL'), osc_image(DIR_WS_IMAGES . 'header_cart.gif', OSCOM::getDef('cart_contents'))) . '</li>' .
         '<li>' . osc_link_object(OSCOM::getLink(null, 'Checkout', 'Shipping', 'SSL'), osc_image(DIR_WS_IMAGES . 'header_checkout.gif', OSCOM::getDef('checkout'))) . '</li>';
?>

  </ul>

  <div id="navigationBar">

<?php
    if ( $OSCOM_Service->isStarted('Breadcrumb') ) {
?>

    <div id="breadcrumbPath">

<?php
      echo $OSCOM_Breadcrumb->getPath();
?>

    </div>

<?php
    }

    if ( $OSCOM_Customer->isLoggedOn() ) {
      echo osc_link_object(OSCOM::getLink(null, 'Account', 'LogOff', 'SSL'), OSCOM::getDef('sign_out')) . ' &nbsp;|&nbsp; ';
    }

    echo osc_link_object(OSCOM::getLink(null, 'Account', null, 'SSL'), OSCOM::getDef('my_account')) . ' &nbsp;|&nbsp; ' . osc_link_object(OSCOM::getLink(null, 'Checkout', null, 'SSL'), OSCOM::getDef('cart_contents')) . ' &nbsp;|&nbsp; ' . osc_link_object(OSCOM::getLink(null, 'Checkout', 'Shipping', 'SSL'), OSCOM::getDef('checkout'));
?>

  </div>
</div>

<?php
  } // ($osC_Template->hasPageHeader())

  if ( $OSCOM_Template->hasPageFooter() ) {
?>

<div id="pageFooter">

<?php
    echo sprintf(OSCOM::getDef('footer'), date('Y'), OSCOM::getLink(), STORE_NAME);
?>

</div>

<?php
    if ( $OSCOM_Service->isStarted('banner') && $OSCOM_Banner->exists('468x60') ) {
      echo '<p align="center">' . $OSCOM_Banner->display() . '</p>';
    }
  }
?>

</body>

</html>
