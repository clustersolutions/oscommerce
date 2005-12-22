<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Boxes_tell_a_friend extends osC_Modules {
    var $_title = 'Tell a Friend',
        $_code = 'tell_a_friend',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'boxes';

    function osC_Boxes_tell_a_friend() {
//      $this->_title = BOX_HEADING_TELL_A_FRIEND;
    }

    function initialize() {
      global $osC_Template, $osC_Product;

      if (isset($osC_Product) && is_a($osC_Product, 'osC_Product') && ($osC_Template->getModule() != 'tell_a_friend')) {
        $this->_content = '<form name="tell_a_friend" action="' . tep_href_link(FILENAME_PRODUCTS, 'tell_a_friend&' . $osC_Product->getKeyword()) . '" method="post">' . "\n" .
                          osc_draw_input_field('to_email_address') . '&nbsp;' . tep_image_submit('button_tell_a_friend.gif', BOX_HEADING_TELL_A_FRIEND) . '<br />' . BOX_TELL_A_FRIEND_TEXT . "n" .
                          '</form>' . "\n";
      }
    }
  }
?>
