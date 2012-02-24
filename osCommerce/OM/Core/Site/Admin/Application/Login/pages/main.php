<h1>{value}page_icon{value}<a href="{link}{link}">{value}page_title{value}</a></h1>

<div class="infoBox">
  <h3>{icon}people.png{icon} {lang}action_heading_login{lang}</h3>

  <form id="formLogin" name="login" class="dataForm" action="{link}||Process{link}" method="post">

  <p>{lang}introduction{lang}</p>

  <fieldset>
    <p><label for="user_name">{lang}field_username{lang}</label><input type="text" name="user_name" value="{gp}user_name{gp}" tabindex="1" /></p>
    <p><label for="user_password">{lang}field_password{lang}</label><input type="password" name="user_password" tabindex="2" /></p>
  </fieldset>

  <p>{button}key|button_login{button}</p>

  </form>
</div>

<script>
  $('input[name="user_name"]').focus();

  if (typeof webkitNotifications != 'undefined') {
    $('#formLogin').submit(function() {
      if ( webkitNotifications.checkPermission() == 1 ) {
        webkitNotifications.requestPermission();
      }
    });
  }
</script>

{iftrue show_password}
<script src="public/external/jquery/jquery.showPasswordCheckbox.js"></script>
<script>
  var showPasswordText = '{value}lang_field_show_password{value}';
  $('input[name="user_password"]').showPasswordCheckbox().focus();
</script>
{iftrue}
