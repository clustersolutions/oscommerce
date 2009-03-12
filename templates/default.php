<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

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

<script language="javascript" src="ext/jquery/jquery-1.3.2.min.js"></script>

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

<div id="pageBlockLeft">
  <div id="pageContent">

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
?>

<div style="clear: both;"></div>

<?php
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

  </div>

<?php
  $content_left = '';

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

    $content_left = ob_get_contents();
    ob_end_clean();
  }

  if (!empty($content_left)) {
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

<style type="text/css"><!--
#pageContent {
  width: 99%;
  padding-left: 5px;
}
//--></style>

<?php
  }
?>

</div>

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

  if ($osC_Template->hasPageHeader()) {
?>

<div id="pageHeader">

<?php
    echo osc_link_object(osc_href_link(FILENAME_DEFAULT), osc_image(DIR_WS_IMAGES . 'store_logo.jpg', STORE_NAME), 'id="siteLogo"');
?>

  <ul id="navigationIcons">

<?php
    echo '<li>' . osc_link_object(osc_href_link(FILENAME_ACCOUNT, null, 'SSL'), osc_image(DIR_WS_IMAGES . 'header_account.gif', $osC_Language->get('my_account'))) . '</li>' .
         '<li>' . osc_link_object(osc_href_link(FILENAME_CHECKOUT, null, 'SSL'), osc_image(DIR_WS_IMAGES . 'header_cart.gif', $osC_Language->get('cart_contents'))) . '</li>' .
         '<li>' . osc_link_object(osc_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'), osc_image(DIR_WS_IMAGES . 'header_checkout.gif', $osC_Language->get('checkout'))) . '</li>';
?>

  </ul>

  <div id="navigationBar">

<?php
    if ($osC_Services->isStarted('breadcrumb')) {
?>

    <div id="breadcrumbPath">

<?php
      echo $osC_Breadcrumb->getPath();
?>

    </div>

<?php
    }

    if ($osC_Customer->isLoggedOn()) {
      echo osc_link_object(osc_href_link(FILENAME_ACCOUNT, 'logoff', 'SSL'), $osC_Language->get('sign_out')) . ' &nbsp;|&nbsp; ';
    }

    echo osc_link_object(osc_href_link(FILENAME_ACCOUNT, null, 'SSL'), $osC_Language->get('my_account')) . ' &nbsp;|&nbsp; ' . osc_link_object(osc_href_link(FILENAME_CHECKOUT, null, 'SSL'), $osC_Language->get('cart_contents')) . ' &nbsp;|&nbsp; ' . osc_link_object(osc_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL'), $osC_Language->get('checkout'));
?>

  </div>
</div>

<?php
  } // ($osC_Template->hasPageHeader())

  if ($osC_Template->hasPageFooter()) {
?>

<div id="pageFooter">

<?php
    echo sprintf($osC_Language->get('footer'), date('Y'), osc_href_link(FILENAME_DEFAULT), STORE_NAME);
?>

</div>

<?php
    if ($osC_Services->isStarted('banner') && $osC_Banner->exists('468x60')) {
      echo '<p align="center">' . $osC_Banner->display() . '</p>';
    }
  }
?>

</body>

</html>
