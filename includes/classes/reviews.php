<?php
/*
  $Id: reviews.php,v 1.2 2004/11/01 09:49:01 sparky Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  class osC_Reviews {
     var $is_enabled = false,
         $is_moderated = false;
     
// class constructor
    function osC_Reviews() {
      
    	$this->enableReviews();
    	$this->enableModeration();
    }

    function enableReviews() {
    	global $osC_Database, $osC_Customer;

      switch (SERVICE_REVIEW_ENABLE_REVIEWS) {
        case 0:
          $this->is_enabled = true;
          break;
        case 1:
          if ($osC_Customer->isLoggedOn() == true) {
            $this->is_enabled = true;
          } else {
            $this->is_enabled = false;
          }
          break;
        case 2:
          if ($this->hasPurchased() == true) {
            $this->is_enabled = true;
          } else {
            $this->is_enabled = false;
          }
          break;
        default:
          $this->is_enabled = false;
          break;
        }
      }
      
    function hasPurchased() {
      global $osC_Database, $osC_Customer;
      
      $Qhaspurchased = $osC_Database->query('select count(*) as total from :table_orders o, :table_orders_products op, :table_products p where o.customers_id = :customers_id and o.orders_id = op.orders_id and op.products_id = p.products_id and op.products_id = :products_id');
      $Qhaspurchased->bindRaw(':table_orders', TABLE_ORDERS);
      $Qhaspurchased->bindRaw(':table_orders_products', TABLE_ORDERS_PRODUCTS);
      $Qhaspurchased->bindRaw(':table_products', TABLE_PRODUCTS);
      $Qhaspurchased->bindInt(':customers_id', $osC_Customer->id);
      $Qhaspurchased->bindInt(':products_id', $_GET['products_id']);
      $Qhaspurchased->execute();

      if ($Qhaspurchased->valueInt('total') >= '1') {
      	return true;
      } else {
      	return false;
      }
    }
    
    function enableModeration() {
    	global $osC_Database, $osC_Customer;

      switch (SERVICE_REVIEW_ENABLE_MODERATION) {
      case -1:
        $this->is_moderated = false;
        break;
      case 0:
        if ($osC_Customer->isLoggedOn() === true) {
          $this->is_moderated = false;
        } else {
          $this->is_moderated = true;
        }
        break;
      case 1:
        $this->is_moderated = true;
        break;
      default:
        $this->is_moderated = true;
        break;
      }
    }
  }    
?>