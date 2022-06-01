{* <!-- load CSS files --> *}
{combine_css id="tfa" path=$TFA_PATH|cat:"template/style.css"}

{* <!-- load JS files --> *}
{* {combine_script id="skeleton" require="jquery" path=$SKELETON_PATH|cat:"template/script.js"} *}

{include file='infos_errors.tpl'}

<form action="{$TFA_ACTION}" method="post" name="tfa_form" class="properties">
  <fieldset>

    <p> 
    {'Here you can send a derogation demand wich will be review by an Administrator. If the derogation is accepted, a mail will be sent to your mail adress %s and you will be asked to change your 2FA method.'|translate:$USER_MAIL}
    </p>

    <span class="property">
      <label for="reason">{'Message to the administrator (optional)'|@translate}</label>
    </span>
    
    <textarea rows="5" cols="40" class="login" name="reason" id="reason" placeholder="{'Prove here your identity...'|@translate}"></textarea>
  </fieldset>

  <p>
    <input tabindex="4" type="submit" name="submit" value="{'Ask Derogation'|@translate}">
  </p>
</form>

<script type="text/javascript">
  document.querySelector('#reason').focus();
</script>