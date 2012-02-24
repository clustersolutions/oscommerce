<h1>{value}page_icon{value}<a href="{link}{link}">{value}page_title{value}</a></h1>

{widget}message_stack{widget}

<div class="infoBox">
  <h3>{icon}edit.png{icon} {value}cfg_title{value}</h3>

  <form name="cEdit" class="dataForm" action="{link}||EntrySave&Process&id={value}group_id{value}&pID={value}cfg_id{value}{link}" method="post">

  <p>{lang}introduction_edit_parameter{lang}</p>

  <fieldset>
    <p><label for="configuration[{value}cfg_key{value}]">{value}cfg_title{value}</label>{value}cfg_input_field{value}</p>
    <p>{value}cfg_description{value}</p>
  </fieldset>

  <p>
    <button type="submit" id="buttonSave" class="ui-priority-primary">{lang}button_save{lang}</button>
    <button type="button" id="buttonCancel" class="ui-priority-secondary" onclick="window.location.href='{link}||id={value}group_id{value}{link}';">{lang}button_cancel{lang}</button>
  </p>

  </form>
</div>

<script>
$('#buttonSave').button({
  icons: {
    primary: 'ui-icon-check'
  }
});

$('#buttonCancel').button({
  icons: {
    primary: 'ui-icon-close'
  }
});
</script>
