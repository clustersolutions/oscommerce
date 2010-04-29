<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<?php echo '<?xml version="1.0" encoding="utf-8"?>'; // short_open_tag compatibility ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $OSCOM_Language->getTextDirection(); ?>" xml:lang="<?php echo $OSCOM_Language->getCode(); ?>">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $OSCOM_Language->getCharacterSet(); ?>" />

<title>osCommerce Online Merchant</title>

<link rel="icon" type="image/png" href="<?php echo OSCOM::getPublicSiteLink('images/oscommerce_icon.png'); ?>" />

<meta name="generator" value="osCommerce Online Merchant" />
<meta name="robots" content="noindex,nofollow" />

<script type="text/javascript" src="public/external/jquery/jquery-1.4.2.min.js"></script>

<link rel="stylesheet" type="text/css" href="public/external/jquery/ui/themes/smoothness/jquery.ui.all.css" />
<script type="text/javascript" src="public/external/jquery/ui/jquery-ui-1.8.custom.min.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo OSCOM::getPublicSiteLink('templates/default/stylesheets/general.css'); ?>" />

</head>

<body>

<div id="pageHeader" class="round">
  <div>
    <div style="float: right; padding-top: 40px; padding-right: 15px; color: #000000; font-weight: bold;"><a href="http://www.oscommerce.com" target="_blank">osCommerce Website</a> &nbsp;|&nbsp; <a href="http://www.oscommerce.com/support" target="_blank">Support</a></div>

    <a href="<?php echo OSCOM::getLink(null, 'Index'); ?>"><img src="<?php echo OSCOM::getPublicSiteLink('images/oscommerce_logo-silver.jpg'); ?>" border="0" width="250" height="50" alt="" title="osCommerce Online Merchant v3.0" style="margin: 10px 10px 0px 10px;" /></a>
  </div>
</div>

<div id="pageContent">
<?php require(OSCOM::BASE_DIRECTORY . 'sites/' . OSCOM::getSite() . '/applications/' . OSCOM::getSiteApplication() . '/pages/' . $OSCOM_Template->getPageContentsFilename()); ?>
</div>

<div id="pageFooter">
  Copyright &copy; 2000-2010 <a href="http://www.oscommerce.com" target="_blank">osCommerce</a> (<a href="http://www.oscommerce.com/about/copyright" target="_blank">Copyright Policy</a>, <a href="http://www.oscommerce.com/about/trademark" target="_blank">Trademark Policy</a>)<br />osCommerce provides no warranty and is redistributable under the <a href="http://www.fsf.org/licenses/gpl.txt" target="_blank">GNU General Public License v2 (1991)</a>
</div>

</body>

</html>
