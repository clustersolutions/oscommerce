<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2014 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin;

  use osCommerce\OM\Core\DirectoryListing;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;

  class Template extends \osCommerce\OM\Core\Template {
    protected $_templates = array();

    protected $_default_template = 'Sail';

    protected $_javascript_filenames = [];

    public function __construct() {
      $templates = array();

      $OSCOM_DL = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'Custom/Site/' . OSCOM::getSite() . '/Template');
      $OSCOM_DL->setIncludeFiles(false);
      $OSCOM_DL->setIncludeDirectories(true);

      foreach ( $OSCOM_DL->getFiles() as $file ) {
        if ( !in_array($file['name'], $templates) ) {
          $templates[] = $file['name'];
        }
      }

      $OSCOM_DL = new DirectoryListing(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::getSite() . '/Template');
      $OSCOM_DL->setIncludeFiles(false);
      $OSCOM_DL->setIncludeDirectories(true);

      foreach ( $OSCOM_DL->getFiles() as $file ) {
        if ( !in_array($file['name'], $templates) ) {
          $templates[] = $file['name'];
        }
      }

      sort($templates);

      $counter = 1;

      foreach ( $templates as $t ) {
        $this->_templates[] = array('id' => $counter,
                                    'code' => $t,
                                    'title' => $t);

        $counter++;
      }

      $this->set();
    }

    public static function getTemplates() {
      return $this->_templates;
    }

    public function getTemplateFile($file = null, $template = null) {
      $reset_base_file = false;

      if ( !isset($template) ) {
        $template = $this->_template;
      }

      if ( !isset($file) ) {
        $reset_base_file = true;

        $file = call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Template\\' . $template . '\\Controller', 'getBaseFilename'));
      }

      if ( file_exists(OSCOM::BASE_DIRECTORY . 'Custom/Site/' . OSCOM::getSite() . '/Template/' . $template . '/Content/' . $file) ) {
        return OSCOM::BASE_DIRECTORY . 'Custom/Site/' . OSCOM::getSite() . '/Template/' . $template . '/Content/' . $file;
      }

      if ( file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::getSite() . '/Template/' . $template . '/Content/' . $file) ) {
        return OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::getSite() . '/Template/' . $template . '/Content/' . $file;
      }

      if ( call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Template\\' . $template . '\\Controller', 'hasParent')) ) {
        $template = call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Template\\' . $template . '\\Controller', 'getParent'));

        if ( $reset_base_file === true ) {
          $file = null;
        }

        return $this->getTemplateFile($file, $template);
      }

      trigger_error('Template::getTemplateFile() $file does not exist: ' . $file);

      return false;
    }

    public function getPageContentsFile($file = null, $template = null, $application = null, $site = null) {
      if ( !isset($file) ) {
        $file = $this->getPageContentsFilename();
      }

      if (!isset($application)) {
        $application = OSCOM::getSiteApplication();
      }

      if (!isset($site)) {
        $site = OSCOM::getSite();
      }

      if (!isset($template)) {
        $template = $this->_template;
      }

      if ( file_exists(OSCOM::BASE_DIRECTORY . 'Custom/Site/' . $site . '/Template/' . $template . '/Application/' . $application . '/pages/' . $file) ) {
        return OSCOM::BASE_DIRECTORY . 'Custom/Site/' . $site . '/Template/' . $template . '/Application/' . $application . '/pages/' . $file;
      }

      if ( file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/' . $site . '/Template/' . $template . '/Application/' . $application . '/pages/' . $file) ) {
        return OSCOM::BASE_DIRECTORY . 'Core/Site/' . $site . '/Template/' . $template . '/Application/' . $application . '/pages/' . $file;
      }

      if ( call_user_func(array('osCommerce\\OM\\Core\\Site\\' . $site . '\\Template\\' . $template . '\\Controller', 'hasParent')) ) {
        while ( true ) {
          $template = call_user_func(array('osCommerce\\OM\\Core\\Site\\' . $site . '\\Template\\' . $template . '\\Controller', 'getParent'));

          if ( file_exists(OSCOM::BASE_DIRECTORY . 'Custom/Site/' . $site . '/Template/' . $template . '/Application/' . $application . '/pages/' . $file) ) {
            return OSCOM::BASE_DIRECTORY . 'Custom/Site/' . $site . '/Template/' . $template . '/Application/' . $application . '/pages/' . $file;
          }

          if ( file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/' . $site . '/Template/' . $template . '/Application/' . $application . '/pages/' . $file) ) {
            return OSCOM::BASE_DIRECTORY . 'Core/Site/' . $site . '/Template/' . $template . '/Application/' . $application . '/pages/' . $file;
          }

          if ( !call_user_func(array('osCommerce\\OM\\Core\\Site\\' . $site . '\\Template\\' . $template . '\\Controller', 'hasParent')) ) {
            break;
          }
        }
      }

      if ( file_exists(OSCOM::BASE_DIRECTORY . 'Custom/Site/' . $site . '/Application/' . $application . '/pages/' . $file) ) {
        return OSCOM::BASE_DIRECTORY . 'Custom/Site/' . $site . '/Application/' . $application . '/pages/' . $file;
      }

      if ( file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/' . $site . '/Application/' . $application . '/pages/' . $file) ) {
        return OSCOM::BASE_DIRECTORY . 'Core/Site/' . $site . '/Application/' . $application . '/pages/' . $file;
      }

      trigger_error('Template::getPageContentsFile() $file does not exist: ' . $file);

      return false;
    }

    public function set($code = null) {
      $template = $this->_default_template;

      if ( isset($code) ) {
        $template = $code;
      } elseif ( isset($_GET['template']) && !empty($_GET['template']) && $this->exists($_GET['template']) ) {
        $template = $_GET['template'];
      } elseif ( isset($_SESSION[OSCOM::getSite()]['template']) ) {
        $template = $_SESSION[OSCOM::getSite()]['template'];
      }

      if ( $template == $this->_default_template ) {
        if ( isset($_SESSION[OSCOM::getSite()]['template']) ) {
          unset($_SESSION[OSCOM::getSite()]['template']);
        }
      } else {
        if ( !isset($_SESSION[OSCOM::getSite()]['template']) || ($_SESSION[OSCOM::getSite()]['template'] != $template) ) {
          $_SESSION[OSCOM::getSite()]['template'] = $template;
        }
      }

      $this->_template = $template;
    }

    public function exists($code) {
      foreach ( $this->_templates as $t ) {
        if ( $t['code'] == $code ) {
          return true;
        }
      }

      return false;
    }

    public function getIcon($size = 16, $icon = null, $title = null) {
      if ( !isset($icon) ) {
        $icon = $this->_application->getIcon();
      }

      return HTML::image(OSCOM::getPublicSiteLink('images/applications/' . $size . '/' . $icon), $title, $size, $size);
    }

    public function hasExternalJavascript()
    {
        return !empty($this->_javascript_filenames);
    }

    public function hasJavascriptBlock() {
        return !empty($this->_javascript_blocks);
    }

    public function getExternalJavascript()
    {
        $output = '';

        foreach ($this->_javascript_filenames as $js) {
            $output .= '<script src="' . HTML::outputProtected($js) . '"></script>' . "\n";
        }

        return $output;
    }

    public function getJavascriptBlock()
    {
        return '<script>' . "\n" . implode("\n", $this->_javascript_blocks) . "\n" . '</script>' . "\n";
    }

    public function addExternalJavascript($filename)
    {
        $this->_javascript_filenames[] = $filename;
    }

    public function addJavascriptBlock($js)
    {
        $this->_javascript_blocks[] = $js;
    }
  }
?>
