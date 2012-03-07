<div id="adminMenu">
  <ul class="apps">
    <li class="shortcuts"><a href="{link}{value}default_site_application{value}{link}"><img src="{publiclink}images/oscommerce_icon.png{publiclink}" /></a></li>

{iftrue logged_in}
    <li><a href="#">Applications &#9662;</a>
      {value}apps_links{value}
    </li>
{iftrue}

    <li><a href="{link}|Shop{link}" target="_blank">{lang}header_title_online_catalog{lang}</a></li>
    <li><a href="http://www.oscommerce.com" target="_blank">{lang}header_title_help{lang} &#9662;</a>
      <ul>
        <li><a href="http://www.oscommerce.com" target="_blank">osCommerce Website</a></li>
        <li><a href="http://www.oscommerce.info" target="_blank">Online Documentation</a></li>
        <li><a href="http://forums.oscommerce.com" target="_blank">Community Forums</a></li>
        <li><a href="http://www.oscommerce.com/index.php?Services" target="_blank">Support Services</a></li>
        <li><a href="http://addons.oscommerce.com" target="_blank">Add-Ons</a></li>
        <li><a href="http://forums.oscommerce.com/tracker/project-4-oscommerce-online-merchant-v3x/" target="_blank">Bug Reporter</a></li>
      </ul>
    </li>
  </ul>

{iftrue logged_in}
  {value}shortcut_links{value}
{iftrue}

</div>

<script>
$('#adminMenu .apps').droppy({speed: 0});
$('#adminMenu .apps li img').tipsy();
</script>

{iftrue logged_in}
<script>
var totalShortcuts = {value}total_shortcuts{value};
var wkn = new Object;

if ( $.cookie('wkn') ) {
  wkn = $.secureEvalJSON($.cookie('wkn'));
}

function updateShortcutNotifications(resetApplication) {
  $.getJSON('{rpclink}GetShortcutNotifications&reset=RESETAPP|Dashboard|Admin{rpclink}'.replace('RESETAPP', resetApplication), function (data) {
    $.each(data, function(key, val) {
      if ( $('#shortcut-' + key + ' .notBubble').html != val ) {
        if ( val > 0 || val.length > 0 ) {
          $('#shortcut-' + key + ' .notBubble').html(val).show();

          if ( (typeof webkitNotifications != 'undefined') && (webkitNotifications.checkPermission() == 0) ) {
            if ( typeof wkn[key] == 'undefined' ) {
              wkn[key] = new Object;
            }

            if ( wkn[key].value != val ) {
              wkn[key].value = val;
              wkn[key].n = webkitNotifications.createNotification('{publiclink}images/applications/32/APPICON.png{publiclink}'.replace('APPICON', key), key, val);
              wkn[key].n.replaceId = key;
              wkn[key].n.ondisplay = function(event) {
                setTimeout(function() {
                  event.currentTarget.cancel();
                }, 5000);
              };
              wkn[key].n.show();
            }
          }
        } else {
          $('#shortcut-' + key + ' .notBubble').hide();
        }
      }
    });

    $.cookie('wkn', $.toJSON(wkn));
  });
}

$(function() {
  if ( totalShortcuts > 0 ) {
    updateShortcutNotifications(typeof resetShortcutNotification != 'undefined' ? '{value}current_site_application{value}' : null);

    setInterval('updateShortcutNotifications()', 10000);
  }
});

if ( (typeof window.external.msAddSiteMode != 'undefined') && window.external.msIsSiteMode() ) {
  {value}ms_pinned_sites{value}
}
</script>
{iftrue}
