<script>
function doEmailCreate() {
	jQuery("#btnCreateLoader").addClass('fa-spinner fa-spin').removeClass('fa-plus');
	jQuery("#emailCreateSuccess").slideUp();
	jQuery("#emailCreateFailed").slideUp();
	WHMCS.http.jqClient.post(
		"clientarea.php",
		"action=productdetails&modop=custom&a=CreateEmailAccount&" + jQuery("#frmCreateEmailAccount").serialize(),
		function( data ) {
			jQuery("#btnCreateLoader").removeClass('fa-spinner fa-spin').addClass('fa-plus');
			if (data.success) {
				jQuery('#opanel-email-prefix').val('');
				jQuery('#opanel-email-password').val('');
				jQuery("#emailCreateSuccess").hide().removeClass('hidden')
					.slideDown();
			} else {
				jQuery("#emailCreateFailedErrorMsg").html(data.errorMsg);
				jQuery("#emailCreateFailed").hide().removeClass('hidden')
					.slideDown();
			}
		}
	);
}
</script>
<div class="row">
	<div class="col-md-6">

		<div class="panel panel-default" id="oPanelPackagePanel">
			<div class="panel-heading">
				<h3 class="panel-title">{$LANG.cPanel.packageDomain}</h3>
			</div>
			<div class="panel-body text-center">
				<div style="margin-bottom:12px">
					<em>{$groupname}</em>
					<h4 style="margin:0;">{$product}</h4>
					<a href="http://{$domain}" target="_blank">www.{$domain}</a>
				</div>
				<hr>
				{if $username}
					<div class="row">
						<div class="col-sm-5 text-right">
							<strong>{$LANG.login}</strong>
						</div>
						<div class="col-sm-7 text-left">
							<a href="clientarea.php?action=productdetails&amp;id={$serviceid}&amp;dosinglesignon=1&amp;app=login" target="_blank">http://{$serverdata.ipaddress}:2082</a>
						</div>
					</div>
				{/if}
				{if $username}
					<div class="row">
						<div class="col-sm-5 text-right">
							<strong>{$LANG.serverusername}</strong>
						</div>
						<div class="col-sm-7 text-left">
							{$username}
						</div>
					</div>
				{/if}
				{if $password}
					<div class="row">
						<div class="col-sm-5 text-right">
							<strong>{$LANG.serverpassword}</strong>
						</div>
						<div class="col-sm-7 text-left">
							{$password}
						</div>
					</div>
				{/if}
				{if $serverdata.nameserver1 || $serverdata.nameserver2 || $serverdata.nameserver3 || $serverdata.nameserver4 || $serverdata.nameserver5}
					<div class="row">
						<div class="col-sm-5 text-right">
							<strong>{$LANG.domainnameservers}</strong>
						</div>
						<div class="col-sm-7 text-left">
							{if $serverdata.nameserver1}{$serverdata.nameserver1} ({$serverdata.nameserver1ip})<br />{/if}
							{if $serverdata.nameserver2}{$serverdata.nameserver2} ({$serverdata.nameserver2ip})<br />{/if}
							{if $serverdata.nameserver3}{$serverdata.nameserver3} ({$serverdata.nameserver3ip})<br />{/if}
							{if $serverdata.nameserver4}{$serverdata.nameserver4} ({$serverdata.nameserver4ip})<br />{/if}
							{if $serverdata.nameserver5}{$serverdata.nameserver5} ({$serverdata.nameserver5ip})<br />{/if}
						</div>
					</div>
				{/if}
				<hr>
				<p>
					<a href="http://{$domain}" class="btn btn-default btn-sm" target="_blank">{$LANG.visitwebsite}</a>
					{if $domainId}
						<a href="clientarea.php?action=domaindetails&id={$domainId}" class="btn btn-success btn-sm" target="_blank">{$LANG.managedomain}</a>
					{/if}
					<input type="button" onclick="popupWindow('whois.php?domain={$domain}','whois',650,420);return false;" value="{$LANG.whoisinfo}" class="btn btn-info btn-sm" />
				</p>

			</div>
		</div>

		{if $availableAddonProducts}
			<div class="panel panel-default" id="oPanelExtrasPurchasePanel">
				<div class="panel-heading">
					<h3 class="panel-title">{$LANG.cPanel.addonsExtras}</h3>
				</div>
				<div class="panel-body text-center">

					<form method="post" action="cart.php?a=add" class="form-inline">
						<input type="hidden" name="serviceid" value="{$serviceid}" />
						<select name="aid" class="form-control input-sm">
						{foreach $availableAddonProducts as $addonId => $addonName}
							<option value="{$addonId}">{$addonName}</option>
						{/foreach}
						</select>
						<button type="submit" class="btn btn-default btn-sm">
							<i class="fas fa-shopping-cart"></i>
							{$LANG.cPanel.purchaseActivate}
						</button>
					</form>

				</div>
			</div>
		{/if}

	</div>
	<div class="col-md-6">

		<div class="panel panel-default" id="oPanelUsagePanel">
			<div class="panel-heading">
				<h3 class="panel-title">{$LANG.cPanel.usageStats}</h3>
			</div>
			<div class="panel-body text-center" style="padding:17px 15px">

				<div class="row">
					<div class="col-sm-5 col-sm-offset-1 col-xs-6" id="diskUsage">
						<strong>{$LANG.cPanel.diskUsage}</strong>
						<br /><br />
						<input type="text" value="{$diskpercent|substr:0:-1}" class="usage-dial" data-fgColor="#444" data-angleOffset="-125" data-angleArc="250" data-min="0" data-max="{if substr($diskpercent, 0, -1) > 100}{$diskpercent|substr:0:-1}{else}100{/if}" data-readOnly="true" data-width="100" data-height="80" />
						<br /><br />
						{$diskusage} M / {$disklimit} M
					</div>
					<div class="col-sm-5 col-xs-6" id="bandwidthUsage">
						<strong>{$LANG.cPanel.bandwidthUsage}</strong>
						<br /><br />
						<input type="text" value="{$bwpercent|substr:0:-1}" class="usage-dial" data-fgColor="#d9534f" data-angleOffset="-125" data-angleArc="250" data-min="0" data-max="{if substr($bwpercent, 0, -1) > 100}{$bwpercent|substr:0:-1}{else}100{/if}" data-readOnly="true" data-width="100" data-height="80" />
						<br /><br />
						{$bwusage} M / {$bwlimit} M
					</div>
				</div>

				{if $bwpercent|substr:0:-1 > 75}
					<div class="text-danger" style="margin:15px 0 5px;font-size:0.8em">
						{if $bwpercent|substr:0:-1 > 100}
							{$LANG.cPanel.usageStatsBwOverLimit}
						{else}
							{$LANG.cPanel.usageStatsBwLimitNear}
						{/if}
						{if $packagesupgrade}
							<a href="upgrade.php?type=package&id={$serviceid}" class="btn btn-xs btn-danger">
								<i class="fas fa-arrow-circle-up"></i>
								{$LANG.cPanel.usageUpgradeNow}
							</a>
						{/if}
					</div>
				{elseif $diskpercent|substr:0:-1 > 75}
					<div class="text-danger" style="margin:15px 0 5px;font-size:0.8em">
						{if $diskpercent|substr:0:-1 > 100}
							{$LANG.cPanel.usageStatsDiskOverLimit}
						{else}
							{$LANG.cPanel.usageStatsDiskLimitNear}
						{/if}
						{if $packagesupgrade}
							<a href="upgrade.php?type=package&id={$serviceid}" class="btn btn-xs btn-danger">
								<i class="fas fa-arrow-circle-up"></i>
								{$LANG.cPanel.usageUpgradeNow}
							</a>
						{/if}
					</div>
				{else}
					<div class="text-info" style="margin:15px 0 5px;font-size:0.8em">
						{$LANG.cPanel.usageLastUpdated} {$lastupdate}
					</div>
				{/if}

				<script src="{$BASE_PATH_JS}/jquery.knob.js"></script>
				<script type="text/javascript">
				jQuery(function() {
					jQuery(".usage-dial").knob({
						'format': function (value) {
							return value + '%';
						}
					});
				});
				</script>

			</div>
		</div>

	</div>
</div>

{foreach $hookOutput as $output}
	<div>
		{$output}
	</div>
{/foreach}

{if $systemStatus == 'Active'}

	<div class="panel panel-default" id="oPanelQuickShortcutsPanel">
		<div class="panel-heading">
			<h3 class="panel-title">{$LANG.cPanel.quickShortcuts}</h3>
		</div>
		<div class="panel-body text-center">

			<div class="row" style="margin-top:10px;margin-bottom:10px">
				<div class="col-sm-3 col-xs-6" id="oPanelEmailAccounts">
					<a href="clientarea.php?action=productdetails&amp;id={$serviceid}&amp;dosinglesignon=1&amp;app=email/accounts" target="_blank">
						<img src="modules/servers/opanel/img/email_accounts.png" style="display:block;margin:0 auto 5px auto"/>
						{$LANG.cPanel.emailAccounts}
					</a>
				</div>
				<div class="col-sm-3 col-xs-6" id="oPanelForwarders">
					<a href="clientarea.php?action=productdetails&amp;id={$serviceid}&amp;dosinglesignon=1&amp;app=email/forwards" target="_blank">
						<img src="modules/servers/opanel/img/forwarders.png" style="display:block;margin:0 auto 5px auto"/>
						{$LANG.cPanel.forwarders}
					</a>
				</div>
				<div class="col-sm-3 col-xs-6" id="oPanelAutoResponders">
					<a href="clientarea.php?action=productdetails&amp;id={$serviceid}&amp;dosinglesignon=1&amp;app=email/auto-reply" target="_blank">
						<img src="modules/servers/opanel/img/autoresponders.png" style="display:block;margin:0 auto 5px auto"/>
						{$LANG.cPanel.autoresponders}
					</a>
				</div>
				<div class="col-sm-3 col-xs-6" id="oPanelFileManager">
					<a href="clientarea.php?action=productdetails&amp;id={$serviceid}&amp;dosinglesignon=1&amp;app=files/manager" target="_blank">
						<img src="modules/servers/opanel/img/file_manager.png" style="display:block;margin:0 auto 5px auto"/>
						{$LANG.cPanel.fileManager}
					</a>
				</div>
			</div>
			<div class="row" style="margin-top:10px;margin-bottom:10px">
				<div class="col-sm-3 col-xs-6" id="oPanelBackup">
					<a href="clientarea.php?action=productdetails&amp;id={$serviceid}&amp;dosinglesignon=1&amp;app=files/backup" target="_blank">
						<img src="modules/servers/opanel/img/backup.png" style="display:block;margin:0 auto 5px auto"/>
						{$LANG.cPanel.backup}
					</a>
				</div>
				<div class="col-sm-3 col-xs-6" id="oPanelSubdomains">
					<a href="clientarea.php?action=productdetails&amp;id={$serviceid}&amp;dosinglesignon=1&amp;app=domains/domains" target="_blank">
						<img src="modules/servers/opanel/img/subdomains.png" style="display:block;margin:0 auto 5px auto"/>
						{$LANG.cPanel.subdomains}
					</a>
				</div>
				<div class="col-sm-3 col-xs-6" id="oPanelAddonDomains">
					<a href="clientarea.php?action=productdetails&amp;id={$serviceid}&amp;dosinglesignon=1&amp;app=domains/domains" target="_blank">
						<img src="modules/servers/opanel/img/addon_domains.png" style="display:block;margin:0 auto 5px auto"/>
						{$LANG.cPanel.addonDomains}
					</a>
				</div>
				<div class="col-sm-3 col-xs-6" id="oPanelCronJobs">
					<a href="clientarea.php?action=productdetails&amp;id={$serviceid}&amp;dosinglesignon=1&amp;app=advanced/cron" target="_blank">
						<img src="modules/servers/opanel/img/cron_jobs.png" style="display:block;margin:0 auto 5px auto"/>
						{$LANG.cPanel.cronJobs}
					</a>
				</div>
			</div>
			<div class="row" style="margin-top:10px;margin-bottom:10px">
				<div class="col-sm-3 col-xs-6" id="oPanelMySQLDatabases">
					<a href="clientarea.php?action=productdetails&amp;id={$serviceid}&amp;dosinglesignon=1&amp;app=databases/mysql-db" target="_blank">
						<img src="modules/servers/opanel/img/mysql_databases.png" style="display:block;margin:0 auto 5px auto"/>
						{$LANG.cPanel.mysqlDatabases}
					</a>
				</div>
				<div class="col-sm-3 col-xs-6" id="oPanelPhpMyAdmin">
					<a href="clientarea.php?action=productdetails&amp;id={$serviceid}&amp;dosinglesignon=1&amp;app=databases/phpmyadmin" target="_blank">
						<img src="modules/servers/opanel/img/php_my_admin.png" style="display:block;margin:0 auto 5px auto"/>
						{$LANG.cPanel.phpMyAdmin}
					</a>
				</div>
				<div class="col-sm-3 col-xs-6" id="oPanelAwstats">
					<a href="clientarea.php?action=productdetails&amp;id={$serviceid}&amp;dosinglesignon=1&amp;app=metrics/webstats" target="_blank">
						<img src="modules/servers/opanel/img/awstats.png" style="display:block;margin:0 auto 5px auto"/>
						{$LANG.cPanel.awstats}
					</a>
				</div>
			</div>

		</div>
	</div>

	<div class="panel panel-default" id="oPanelQuickEmailPanel">
		<div class="panel-heading">
			<h3 class="panel-title">{$LANG.cPanel.createEmailAccount}</h3>
		</div>
		<div class="panel-body">

			{include file="$template/includes/alert.tpl" type="success" msg=$LANG.cPanel.emailAccountCreateSuccess textcenter=true hide=true idname="emailCreateSuccess" additionalClasses="email-create-feedback"}

			{include file="$template/includes/alert.tpl" type="danger" msg=$LANG.cPanel.emailAccountCreateFailed|cat:' <span id="emailCreateFailedErrorMsg"></span>' textcenter=true hide=true idname="emailCreateFailed" additionalClasses="email-create-feedback"}

			<form id="frmCreateEmailAccount" onsubmit="doEmailCreate();return false">
				<input type="hidden" name="id" value="{$serviceid}" />
				<input type="hidden" name="email_quota" value="250" />
				<div class="row">
					<div class="col-sm-6">
						<div class="input-group">
							<input type="text" name="email_prefix" class="form-control" placeholder="{$LANG.cPanel.usernamePlaceholder}">
							<span class="input-group-addon">@{$domain}</span>
						</div>
					</div>
					<div class="col-sm-3">
						<input type="password" name="email_pw" class="form-control" placeholder="{$LANG.cPanel.passwordPlaceholder}">
					</div>
					<div class="col-sm-3">
						<button type="submit" class="btn btn-primary btn-block" />
							<i class="fas fa-plus" id="btnCreateLoader"></i>
							{$LANG.cPanel.create}
						</button>
					</div>
				</div>
			</form>

		</div>
	</div>

{else}

	<div class="alert alert-warning text-center" role="alert" id="oPanelSuspendReasonPanel">
		{if $suspendreason}
			<strong>{$suspendreason}</strong><br />
		{/if}
		{$LANG.cPanel.packageNotActive} {$status}.<br />
		{if $systemStatus eq "Pending"}
			{$LANG.cPanel.statusPendingNotice}
		{elseif $systemStatus eq "Suspended"}
			{$LANG.cPanel.statusSuspendedNotice}
		{/if}
	</div>

{/if}

<div class="panel panel-default" id="oPanelBillingOverviewPanel">
	<div class="panel-heading">
		<h3 class="panel-title">{$LANG.cPanel.billingOverview}</h3>
	</div>
	<div class="panel-body">

		<div class="row">
			<div class="col-md-5">
				{if $firstpaymentamount neq $recurringamount}
					<div class="row" id="firstPaymentAmount">
						<div class="col-xs-6 text-right" >
							{$LANG.firstpaymentamount}
						</div>
						<div class="col-xs-6">
							{$firstpaymentamount}
						</div>
					</div>
				{/if}
				{if $billingcycle != $LANG.orderpaymenttermonetime && $billingcycle != $LANG.orderfree}
					<div class="row" id="recurringAmount">
						<div class="col-xs-6 text-right">
							{$LANG.recurringamount}
						</div>
						<div class="col-xs-6">
							{$recurringamount}
						</div>
					</div>
				{/if}
				<div class="row" id="billingCycle">
					<div class="col-xs-6 text-right">
						{$LANG.orderbillingcycle}
					</div>
					<div class="col-xs-6">
						{$billingcycle}
					</div>
				</div>
				<div class="row" id="paymentMethod">
					<div class="col-xs-6 text-right">
						{$LANG.orderpaymentmethod}
					</div>
					<div class="col-xs-6">
						{$paymentmethod}
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="row" id="registrationDate">
					<div class="col-xs-6 col-md-5 text-right">
						{$LANG.clientareahostingregdate}
					</div>
					<div class="col-xs-6 col-md-7">
						{$regdate}
					</div>
				</div>
				<div class="row" id="nextDueDate">
					<div class="col-xs-6 col-md-5 text-right">
						{$LANG.clientareahostingnextduedate}
					</div>
					<div class="col-xs-6 col-md-7">
						{$nextduedate}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
{if $configurableoptions}
	<div class="panel panel-default" id="oPanelConfigurableOptionsPanel">
		<div class="panel-heading">
			<h3 class="panel-title">{$LANG.orderconfigpackage}</h3>
		</div>
		<div class="panel-body">
			{foreach from=$configurableoptions item=configoption}
				<div class="row">
					<div class="col-md-5 col-xs-6 text-right">
						<strong>{$configoption.optionname}</strong>
					</div>
					<div class="col-md-7 col-xs-6 text-left">
						{if $configoption.optiontype eq 3}{if $configoption.selectedqty}{$LANG.yes}{else}{$LANG.no}{/if}{elseif $configoption.optiontype eq 4}{$configoption.selectedqty} x {$configoption.selectedoption}{else}{$configoption.selectedoption}{/if}
					</div>
				</div>
			{/foreach}
		</div>
	</div>
{/if}
{if $metricStats}
	<div class="panel panel-default" id="oPanelMetricStatsPanel">
		<div class="panel-heading">
			<h3 class="panel-title">{$LANG.metrics.title}</h3>
		</div>
		<div class="panel-body">
			{include file="$template/clientareaproductusagebilling.tpl"}
		</div>
	</div>
{/if}
{if $customfields}
	<div class="panel panel-default" id="oPanelAdditionalInfoPanel">
		<div class="panel-heading">
			<h3 class="panel-title">{$LANG.additionalInfo}</h3>
		</div>
		<div class="panel-body">
			{foreach from=$customfields item=field}
				<div class="row">
					<div class="col-md-5 col-xs-6 text-right">
						<strong>{$field.name}</strong>
					</div>
					<div class="col-md-7 col-xs-6 text-left">
						{if empty($field.value)}
							{$LANG.blankCustomField}
						{else}
							{$field.value}
						{/if}
					</div>
				</div>
			{/foreach}
		</div>
	</div>
{/if}
