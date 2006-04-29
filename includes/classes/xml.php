<?php
/*
  $Id: index.php 377 2006-01-09 14:47:49Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_XML {
    var $_xml,
        $_encoding;

    function osC_XML(&$xml, $encoding = '') {
      $this->_xml =& $xml;

      if (empty($encoding) === false) {
        $this->_encoding = $encoding;
      }
    }

    function toArray($fallback = false) {
      if (($fallback === false) && function_exists('simplexml_load_string')) {
        return osc_object2array_recursive(simplexml_load_string($this->_xml));
      } else {
        if (function_exists('XML_unserialize') === false) {
          include(dirname(__FILE__) . '/../../ext/phpxml/xml.php');
        }

        return XML_unserialize($this->_xml);
      }
    }

    function toXML() {
      if (function_exists('XML_serialize') === false) {
        include(dirname(__FILE__) . '/../../ext/phpxml/xml.php');
      }

      return XML_serialize($this->_xml, $this->_encoding);
    }
  }
?>
