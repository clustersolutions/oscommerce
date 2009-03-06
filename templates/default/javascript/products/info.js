/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

function refreshVariants() {
  var price = null;
  var availability = null;
  var model = null;

  for (c in combos) {
    id = null;

    variants_loop:
    for (group_id in combos[c]['values']) {
      for (value_id in combos[c]['values'][group_id]) {
        if (document.getElementById('variants_' + group_id) != undefined) {
          if (document.getElementById('variants_' + group_id).type == 'select-one') {
            if (value_id == document.getElementById('variants_' + group_id).value) {
              id = c;
            } else {
              id = null;

              break variants_loop;
            }
          }
        } else if (document.getElementById('variants_' + group_id + '_1') != undefined) {
          j = 0;

          while (true) {
            j++;

            if (document.getElementById('variants_' + group_id + '_' + j).type == 'radio') {
              if (document.getElementById('variants_' + group_id + '_' + j).checked) {
                if (value_id == document.getElementById('variants_' + group_id + '_' + j).value) {
                  id = c;
                } else {
                  id = null;

                  break variants_loop;
                }
              }
            }

            if (document.getElementById('variants_' + group_id + '_' + (j+1)) == undefined) {
              break;
            }
          }
        }
      }
    }

    if (id != null) {
      break;
    }
  }

  if (id != null) {
    price = combos[id]['price'];
    availability = productInfoAvailability;
    model = combos[id]['model'];
  } else {
    price = originalPrice;
    availability = productInfoNotAvailable;
    model = '';
  }

  document.getElementById('productInfoPrice').innerHTML = price;
  document.getElementById('productInfoAvailability').innerHTML = availability;
  document.getElementById('productInfoModel').innerHTML = model;
}
