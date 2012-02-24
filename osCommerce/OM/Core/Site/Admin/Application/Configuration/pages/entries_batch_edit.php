<h1>{value}page_icon{value}<a href="{link}{link}">{value}page_title{value}</a></h1>

{widget}message_stack{widget}

<div class="infoBox">
  <h3>{icon}edit.png{icon} {lang}action_heading_batch_edit_configuration_parameters{lang}</h3>

  <form name="cEditBatch" class="dataForm" action="{link}||BatchSaveEntries&Process&id={value}group_id{value}{link}" method="post">

  <p>{lang}introduction_batch_edit_configuration_parameters{lang}</p>

  <fieldset>
    {loop cfg_input_fields}
      <p><label for="configuration[#key#]">#title#</label>#input_field#</p>
      <p>#description#</p>
    {loop}
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
