{* <!-- load CSS files --> *}
{combine_css id="tfa" path=$TFA_PATH|cat:"template/style.css"}

{* <!-- load JS files --> *}
{* {combine_script id="skeleton" require="jquery" path=$SKELETON_PATH|cat:"template/script.js"} *}

{include file='infos_errors.tpl'}

<form action="{$TFA_ACTION}" method="post" name="tfa_form" class="properties">
  <fieldset>

    <p> 
    {if $TFA_METHOD == 0}
      {'A code has been sent to the email address %s, please enter it below to finalize the connection.'|translate:$USER_MAIL}
    {else}
      
    {/if}
    </p>

    <ul>
      <li>
        <span class="property">
          <label for="code">{'Code'|@translate}</label>
        </span>
        <input tabindex="1" class="login" type="text" name="code" id="code" size="6">
      </li>
    </ul>
  </fieldset>

  <p>
    <input tabindex="4" type="submit" name="login" value="{'Submit'|@translate}">
  </p>

	{* <p>
		<a href="{$TFA_DEMAND}" title="{'Lost access to the mailbox ? Make a request to the webmaster'|@translate}" class="pwg-state-default pwg-button">
			<span class="pwg-icon pwg-icon-register">&nbsp;</span><span>{'Lost access to the mailbox ? Make a request to the webmaster'|@translate}</span>
		</a>
	</p> *}

</form>

<script type="text/javascript"><!--
document.querySelector('#code').focus();
//--></script>