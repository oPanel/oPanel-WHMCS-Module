<form action="clientarea.php" method="post" target="_blank">
	<input type="hidden" name="action" value="productdetails" />
	<input type="hidden" name="id" value="{$serviceid}" />
	<input type="hidden" name="dosinglesignon" value="1" />
	<input type="submit" value="{$LANG.login}" class="btn btn-primary modulebutton" />
	<input type="button" value="{$LANG.login}" onClick="window.open('http{if $serversecure}s{/if}://{if $serverhostname}{$serverhostname}{else}{$serverip}{/if}:{if $serversecure}2083{else}2082{/if}/')" class="btn btn-default modulebutton" />
</form>