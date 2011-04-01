<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
?>

<div class="boxNew">
  <div class="boxTitle"><?php echo HTML::link($OSCOM_Box->getTitleLink(), $OSCOM_Box->getTitle()); ?></div>

  <div class="boxContents" style="text-align: center;"><?php echo $OSCOM_Box->getContent(); ?></div>
</div>
