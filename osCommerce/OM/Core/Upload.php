<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core;

  class Upload {
    protected $file,
              $filename,
              $destination,
              $permissions,
              $extensions;

    public function __construct($file = null, $destination = null, $permissions = '777', $extensions = null) {
      $this->setFile($file);
      $this->setDestination($destination);
      $this->setPermissions($permissions);
      $this->setExtensions($extensions);
    }

    public function exists() {
      $file = array();

      if ( !empty($this->file) ) {
        if ( is_array($this->file) ) {
          $file = $this->file;
        } elseif ( isset($_FILES[$this->file]) ) {
          $file = array('name' => $_FILES[$this->file]['name'],
                        'type' => $_FILES[$this->file]['type'],
                        'size' => $_FILES[$this->file]['size'],
                        'tmp_name' => $_FILES[$this->file]['tmp_name']);
        }
      }

      return isset($file['tmp_name']) && !empty($file['tmp_name']) && ($file['tmp_name'] != 'none') && is_uploaded_file($file['tmp_name']);
    }

    public function parse() {
      $file = array();

      if ( !empty($this->file) ) {
        if ( is_array($this->file) ) {
          $file = $this->file;
        } elseif ( isset($_FILES[$this->file]) ) {
          $file = array('name' => $_FILES[$this->file]['name'],
                        'type' => $_FILES[$this->file]['type'],
                        'size' => $_FILES[$this->file]['size'],
                        'tmp_name' => $_FILES[$this->file]['tmp_name']);
        }
      }

      if ( isset($file['tmp_name']) && !empty($file['tmp_name']) && ($file['tmp_name'] != 'none') && is_uploaded_file($file['tmp_name']) ) {
        if ( count($this->extensions) > 0 ) {
          if ( !in_array(strtolower(substr($file['name'], strrpos($file['name'], '.')+1)), $this->extensions) ) {
            return false;
          }
        }

        $this->setFile($file);
        $this->setFilename($file['name']);

        if ( !empty($this->destination) ) {
          return $this->checkDestination();
        } else {
          return true;
        }
      }
    }

    public function save() {
      if ( substr($this->destination, -1) != '/') {
        $this->destination .= '/';
      }

      if ( move_uploaded_file($this->file['tmp_name'], $this->destination . $this->filename) ) {
        chmod($this->destination . $this->filename, $this->permissions);

        return true;
      } else {
        return false;
      }
    }

    public function setFile($file) {
      $this->file = $file;
    }

    public function setDestination($destination) {
      $this->destination = $destination;
    }

    public function setPermissions($permissions) {
      $this->permissions = octdec($permissions);
    }

    public function getFilename() {
      return $this->filename;
    }

    public function setFilename($filename) {
      $this->filename = $filename;
    }

    public function setExtensions($extensions) {
      if ( !empty($extensions) ) {
        if ( is_array($extensions) ) {
          $this->extensions = $extensions;
        } else {
          $this->extensions = array($extensions);
        }
      } else {
        $this->extensions = array();
      }
    }

    public function checkDestination() {
      return is_dir($this->destination) && is_writeable($this->destination);
    }
  }
?>
