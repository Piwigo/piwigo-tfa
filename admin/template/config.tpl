{combine_css path=$SKELETON_PATH|@cat:"admin/template/style.css"}
{combine_script id='common' load='footer' path='admin/themes/default/js/common.js'}

{footer_script}
{/footer_script}


<div class="titrePage">
	<h2>Skeleton</h2>
</div>

<form method="post" action="" class="properties">
<div id="configContent">
  <fieldset class="tfa">
    <legend><span class="icon-lock icon-yellow"></span>{'Two Factor Authentification Parameter'|translate}</legend>
    <ul>
      <li class="on_new_machine">
        <label class="font-checkbox">
          <span class="icon-check"></span>
          <input type="checkbox" name="on_new_machine" {if ($tfa.on_new_machine)}checked="checked"{/if}>
          {'Demand on a new device'|translate}
        </label>
      </li>
      <li class="duration">
        <label class="font-checkbox">{'2FA duration in days (0 = never expire)'|translate}</label>
        <br>
        <input type="number" min="0" name="duration" value="{$tfa.duration}">
      </li>
      <li class="code_tries">
        <label class="font-checkbox">{'Number of tries accorded'|translate}</label>
        <br>
        <input type="number" min="1" max="20" name="code_tries" value="{$tfa.code_tries}">
      </li>
    </ul>
  </fieldset>
</div>

<input type="hidden" name="save_config" value="true"/>

<p class="formButtons"><button class="buttonLike" name="submit" type="submit"><i class="icon-floppy"></i>{'Save Settings'|translate}"</button></p>

</form>