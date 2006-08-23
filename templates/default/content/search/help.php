<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<style type="text/css">
<!--
#pageContent {
  width: 100%;
  margin: 0;
  padding: 0;
}

div#pageBlockLeft {
  width: 100%;
  margin: 0;
}
//-->
</style>

<div class="moduleBox">
  <h6><?php echo $osC_Language->get('search_help_heading'); ?></h6>

  <div class="content">
    <p><?php echo $osC_Language->get('search_help'); ?></p>

    <p align="right"><?php echo osc_link_object('javascript:window.close();', $osC_Language->get('close_window')); ?></p>
  </div>
</div>
