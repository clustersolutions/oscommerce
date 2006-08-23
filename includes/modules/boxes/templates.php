<?php
/*
  $Id: currencies.php 345 2005-12-10 10:58:35Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Boxes_templates extends osC_Modules {
    var $_title,
        $_code = 'templates',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'boxes';

    function osC_Boxes_templates() {
      global $osC_Language;

      $this->_title = $osC_Language->get('box_templates_heading');
    }

    function initialize() {
      global $osC_Session;

      $data = array();

      foreach (osC_Template::getTemplates() as $template) {
        $data[] = array('id' => $template['code'], 'text' => $template['title']);
      }

      if (sizeof($data) > 1) {
        $hidden_get_variables = '';

        foreach ($_GET as $key => $value) {
          if ( ($key != 'template') && ($key != $osC_Session->getName()) && ($key != 'x') && ($key != 'y') ) {
            $hidden_get_variables .= osc_draw_hidden_field($key, $value);
          }
        }

        $this->_content = '<form name="templates" action="' . osc_href_link(basename($_SERVER['PHP_SELF']), null, 'AUTO', false) . '" method="get">' .
                          $hidden_get_variables . osc_draw_pull_down_menu('template', $data, $_SESSION['template']['code'], 'onchange="this.form.submit();" style="width: 100%"') . osc_draw_hidden_session_id_field() .
                          '</form>';
      }
    }
  }
?>
