<div class="passster-form[PASSSTER_HIDE]" id="[PASSSTER_ID]">  
  <form class="password-form" method="post" autocomplete="off" action-xhr="[PASSSTER_CURRENT_URL]" target="_top">
    <h4>[PASSSTER_FORM_HEADLINE]</h4>
    <p>[PASSSTER_FORM_INSTRUCTIONS]</p>
    <fieldset>
      <span class="ps-loader"><img src="[PS_AJAX_LOADER]"/></span>
      <input placeholder="[PASSSTER_PLACEHOLDER]" type="password" tabindex="1" name="[PASSSTER_AUTH]" id="[PASSSTER_AUTH]" class="passster-password" autocomplete="off" data-protection-type="[PASSSTER_TYPE]" data-list="[PASSSTER_LIST]" data-area="[PASSSTER_AREA]" data-protection="[PASSSTER_PROTECTION]">
      [PASSSTER_SHOW_PASSWORD]
      <button name="submit" type="submit" class="passster-submit" data-psid="[PASSSTER_ID]" [PASSSTER_ACF] data-submit="...Checking Password" data-redirect="[PASSSTER_REDIRECT]">[PASSSTER_BUTTON_LABEL]</button>
       <div class="passster-error"></div>
    </fieldset>
  </form>
</div>