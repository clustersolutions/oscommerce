<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\HTML;
?>

<div class="boxNew">
  <div class="boxTitle"><?php echo HTML::link($OSCOM_Box->getTitleLink(), $OSCOM_Box->getTitle()); ?></div>

  <div class="boxContents"><?php echo $OSCOM_Box->getContent(); ?></div>
</div>
