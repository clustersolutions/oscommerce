<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<div class="infoBoxHeading"><?php echo osc_icon('people.png') . ' ' . $osC_Language->get('action_heading_login'); ?></div>
<div class="infoBoxContent">
  <form name="login" class="dataForm" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=process'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction'); ?></p>

  <fieldset>
    <div><label for="user_name"><?php echo $osC_Language->get('field_username'); ?></label><?php echo osc_draw_input_field('user_name'); ?></div>
    <div><label for="user_password"><?php echo $osC_Language->get('field_password'); ?></label><?php echo osc_draw_password_field('user_password'); ?></div>
  </fieldset>

  <p align="center"><?php echo '<input type="submit" value="' . $osC_Language->get('button_login') . '" class="operationButton" />'; ?></p>

  </form>
</div>

<script language="javascript" type="text/javascript"><!--
  $('#user_name').focus();
//--></script>
