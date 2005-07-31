<?php
/*
  $Id: html_output.php 53 2005-03-09 05:11:10Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

/**
 * The osC_Template class defines or adds elements to the page output such as the page title, page content, and javascript blocks
 */

  class osC_Template {

/**
 * Holds the template name value
 *
 * @var string
 * @access private
 */

    var $_template = 'default';

/**
 * Holds the title of the page
 *
 * @var string
 * @access private
 */

    var $_page_title;

/**
 * Holds the filename of the content to be added to the page
 *
 * @var string
 * @access private
 */

    var $_page_contents_filename;

/**
 * Holds javascript filenames to be included in the page
 *
 * The javascript files must be plain javascript files without any PHP logic, and are linked to from the page
 *
 * @var array
 * @access private
 */

    var $_javascript_filenames = array('includes/general.js');

/**
 * Holds javascript PHP filenames to be included in the page
 *
 * The javascript PHP filenames can consist of PHP logic to produce valid javascript syntax, and is embedded in the page
 *
 * @var array
 * @access private
 */

    var $_javascript_php_filenames = array();

/**
 * Holds blocks of javascript syntax to embedd into the page
 *
 * Each block must contain its relevant <script> and </script> tags
 *
 * @var array
 * @access private
 */

    var $_javascript_blocks = array();

/**
 * Returns the template name
 *
 * @access public
 * @return string
 */

    function getTemplate() {
      return $this->_template;
    }

/**
 * Returns the title of the page
 *
 * @access public
 * @return string
 */

    function getPageTitle() {
      return $this->_page_title;
    }

/**
 * Returns the content filename of the page
 *
 * @access public
 * @return string
 */

    function getPageContentsFilename() {
      return $this->_page_contents_filename;
    }

/**
 * Returns the javascript to link from or embedd to on the page
 *
 * @access public
 * @return string
 */

    function getJavascript() {
      if (!empty($this->_javascript_filenames)) {
        echo $this->_getJavascriptFilenames();
      }

      if (!empty($this->_javascript_php_filenames)) {
        $this->_getJavascriptPhpFilenames();
      }

      if (!empty($this->_javascript_blocks)) {
        echo $this->_getJavascriptBlocks();
      }
    }

/**
 * Checks to see if the page has a title set
 *
 * @access public
 * @return boolean
 */

    function hasPageTitle() {
      return !empty($this->_page_title);
    }

/**
 * Checks to see if the page has javascript to link to or embedd from
 *
 * @access public
 * @return boolean
 */

    function hasJavascript() {
      return (!empty($this->_javascript_filenames) || !empty($this->_javascript_php_filenames) || !empty($this->_javascript_blocks));
    }

/**
 * Sets the name of the template to use
 *
 * @param string $template The name of the template to set
 * @access public
 */

    function setTemplate($template) {
      $this->_template = $template;
    }

/**
 * Sets the title of the page
 *
 * @param string $title The title of the page to set to
 * @access public
 */

    function setPageTitle($title) {
      $this->_page_title = $title;
    }

/**
 * Sets the content of the page
 *
 * @param string $filename The content filename to include on the page
 * @access public
 */

    function setPageContentsFilename($filename) {
      $this->_page_contents_filename = $filename;
    }

/**
 * Adds a javascript file to link to
 *
 * @param string $filename The javascript filename to link to
 * @access public
 */

    function addJavascriptFilename($filename) {
      $this->_javascript_filenames[] = $filename;
    }

/**
 * Adds a PHP based javascript file to embedd on the page
 *
 * @param string $filename The PHP based javascript filename to embedd
 * @access public
 */

    function addJavascriptPhpFilename($filename) {
      $this->_javascript_php_filenames[] = $filename;
    }

/**
 * Adds javascript logic to the page
 *
 * @param string $javascript The javascript block to add on the page
 * @access public
 */

    function addJavascriptBlock($javascript) {
      $this->_javascript_blocks[] = $javascript;
    }

/**
 * Returns the javascript filenames to link to on the page
 *
 * @access private
 * @return string
 */

    function _getJavascriptFilenames() {
      $js_files = '';

      foreach ($this->_javascript_filenames as $filenames) {
        $js_files .= '<script language="javascript" type="text/javascript" src="' . $filenames . '"></script>' . "\n";
      }

      return $js_files;
    }

/**
 * Returns the PHP javascript files to embedd on the page
 *
 * @access private
 */

    function _getJavascriptPhpFilenames() {
      foreach ($this->_javascript_php_filenames as $filenames) {
        include($filenames);
      }
    }

/**
 * Returns javascript blocks to add to the page
 *
 * @access private
 * @return string
 */

    function _getJavascriptBlocks() {
      return implode("\n", $this->_javascript_blocks);
    }
  }
?>
