<?php
if(!defined("WHMCS")){exit("This file cannot be accessed directly");}
function opanel_MetaData(){
	if(isset($_POST['action'],$_POST['type']) and $_POST['action'] == 'getmoduleinfo' and $_POST['type'] == 'opanel'){
		exit('{"cantestconnection":true,"supportsadminsso":true,"defaultsslport":"2087","defaultnonsslport":"2086","apiTokens":true}');
	}
	return [
		"DisplayName"		=> "oPanel",
		"APIVersion"		=> "1.1",
		"DefaultNonSSLPort"	=> "2086",
		"DefaultSSLPort"	=> "2087",
		'ApiTokens'			=> TRUE,
		'TestConnection'	=> TRUE,
		'AdminSingleSignOn'	=> TRUE,
		"ServiceSingleSignOnLabel"	=> "Login to oPanel",
		"AdminSingleSignOnLabel"	=> "Login to WoM",
		"ApplicationLinkDescription"=> "Provides customers with links that utilise Single Sign-On technology to automatically transfer and log your customers into the WHMCS billing &amp; support portal from within the oPanel user interface.",
		"ListAccountsUniqueIdentifierDisplayName" => "Domain",
		"ListAccountsUniqueIdentifierField" => "domain",
		"ListAccountsProductField"	=> "configoption1",
	];
}
function opanel_api($params,$Url,$Post=[],$Method=null){
	$Curl = curl_init();
	curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($Curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($Curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($Curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($Curl, CURLOPT_HEADER, false);
	curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($Curl, CURLOPT_USERAGENT,$_SERVER['SERVER_NAME']);
	curl_setopt($Curl, CURLOPT_HTTPHEADER,[
		'Accept: application/json',
		'Authorization: oPanel '.base64_encode($params["serverusername"].':'.$params["serveraccesshash"]),
	]);
	if(!empty($Post)){
		curl_setopt($Curl, CURLOPT_POSTFIELDS, json_encode($Post));
	}
	if(!empty($Method)){
		curl_setopt($Curl, CURLOPT_CUSTOMREQUEST,$Method);
	}
	curl_setopt($Curl, CURLOPT_URL,$params["serverhttpprefix"].'://'.(!empty($params["serverhostname"])?$params["serverhostname"]:$params["serverip"]).':'.$params["serverport"].'/API/'.$Url);
	$Data	= curl_exec($Curl);
	$Info	= curl_getinfo($Curl);
	curl_close($Curl);
	if($Json=json_decode($Data,true)){
		list($Durum,$Bilgi)	= $Json;
		if($Durum){
			return [$Durum,$Bilgi];
		}else{
			return [$Durum,$Bilgi];
		}
	}elseif(empty($Info['http_code'])){
		return [false,'oPanel server connect error'];
	}elseif($Info['http_code'] != 200){
		return [false,'oPanel API HTTP Status: '.$Info['http_code']];
	}elseif(empty($Data)){
		return [false,'Server response data empty'];
	}else{
		return [false,$Data];
	}
}
function opanel_ListPackages(array $params,$removeUsername=true){
	list($St,$In)=opanel_api($params,"packets");
	if(empty($St) or empty($In)){
		return ['custom'=>'No Packet'];
	}
	if($St){
		$packets=[];
		foreach($In as $packet){
			if(!empty($packet['name'])){
				$packets[$packet['name']]=ucwords($packet['name']);
			}
		}
		return $packets;
	}
	exit($In);
}
function opanel_ConfigOptions(array $params){
	return [
		"Package Name" 		=> array("Type" => "text", "Size" => "25","Default" => "Unlimited", "Loader" => "opanel_ListPackages", "SimpleMode" => false),
		"Disk Space"		=> array("Type" => "text", "Size" => "5", "Default" => "0", "Description" => "MB"),
		"Bandwidth"			=> array("Type" => "text", "Size" => "5", "Default" => "0", "Description" => "MB"),
		"FTP Accounts"		=> array("Type" => "text", "Size" => "5", "Default" => "-1"),
		"Email Accounts"	=> array("Type" => "text", "Size" => "5", "Default" => "-1"),
		"MySQL Databases"	=> array("Type" => "text", "Size" => "5", "Default" => "-1"),
		"Subdomains"		=> array("Type" => "text", "Size" => "5", "Default" => "-1"),
		"Parked Domains"	=> array("Type" => "text", "Size" => "5", "Default" => "-1"),
		"Addon Domains"		=> array("Type" => "text", "Size" => "5", "Default" => "-1"),
		"oPanel Theme"		=> array("Type" => "text", "Size" => "15", "Default" => "default"),
		"Reseller Host"		=> array("Type" => "text", "Size" => "5", "Default" => "0", "Description" => "0:User, 10:Reseller for max 10 host"),
	];
}
function opanel_create_api_token(array $params){
	$response = opanel_api($params,'token/add',['name'=>'WHMCS']);
	list($St,$In)=opanel_api($params,'token/add',['name'=>'WHMCS']);
	if($St){
		return ["success"=>$St,'api_token'=>''];
	}
	return ["success"=>$St,"error"=>$In];
}
function opanel_TestConnection($params){
	return opanel_GetUserCount($params);
}
function opanel_GetUserCount($params){
	list($St,$In)=opanel_api($params,"users");
	if($St){
		$totalCount=count($In);
		return ["success"=>$St,"totalAccounts"=>$totalCount,"ownedAccounts"=>$totalCount];
	}
	return ["success"=>$St,"error"=>$In];
}
function opanel_getUserData(array $params){
	list($St,$In)=opanel_api($params,"users");
	if($St and isset($In[$params["username"]])){
		$In[$params["username"]]['name']=$In[$params["username"]]['user'];
		$In[$params["username"]]['email']=$In[$params["username"]]['contact'];
		$In[$params["username"]]['uniqueIdentifier']=$In[$params["username"]]['domain'];
		$In[$params["username"]]['product']=$In[$params["username"]]['packet'];
		return array("success" => true, "userData" => $In[$params["username"]]);
	}elseif($St){
		return array("success" => false, "userData" => [], "error" => 'User Not Found');
	}
	return array("success" => false, "userData" => [], "error" => $In);
}
function opanel_GetRemoteMetaData($params){
	$Return=["version"=>'-',"load"=>["fifteen"=>"0","five"=>"0","one"=>"0"],"max_accounts"=>0];
	list($St,$In)=opanel_api($params,"cli",["command"=>"oPanel --action=license --json","background"=>"0"]);
	if($St and $In=json_decode($In,true) and isset($In[1]['lic_limit'],$In[1]['product']['product_limit'])){
		$Return["max_accounts"]	= (empty($In[1]['lic_limit'])?$In[1]['product']['product_limit']:$In[1]['lic_limit']);
	}
	list($St,$In)=opanel_api($params,"cli",["command"=>"cat /usr/local/opanel/conf/version","background"=>"0"]);
	if($St){
		$Return["version"]	= trim($In);
	}
	list($St,$In)=opanel_api($params,"cli",["command"=>"uptime","background"=>"0"]);
	if($St){
		$cut=explode(':',$In);
		$cut=end($cut);
		$cut=explode(',',trim($cut));
		$cut=array_map('trim',$cut);
		$Return["load"]	= ["fifteen"=>$cut[0],"five"=>$cut[1],"one"=>$cut[2]];
	}
	return $Return;
}
function opanel_RenderRemoteMetaData($params){
	$remoteData = $params["remoteData"];
	if ($remoteData){
		$metaData = $remoteData->metaData;
		$version = "Unknown";
		$loadOne = $loadFive = $loadFifteen = 0;
		$maxAccounts = "Unlimited";
		if(array_key_exists("version", $metaData)) {
			$version = $metaData["version"];
		}
		if (array_key_exists("load", $metaData)) {
			$loadOne = $metaData["load"]["one"];
			$loadFive = $metaData["load"]["five"];
			$loadFifteen = $metaData["load"]["fifteen"];
		}
		if (array_key_exists("max_accounts", $metaData) && 0 < $metaData["max_accounts"]) {
			$maxAccounts = $metaData["max_accounts"];
		}
		return "oPanel Version: ".$version."<br>\nLoad Averages: ".$loadOne.", ".$loadFive.", ".$loadFifteen."<br>\nLicense Max # of Accounts: ".$maxAccounts;
	}
	return "";
}
function opanel_ClientArea($params){
	return array("overrideDisplayTitle" => ucfirst($params["domain"]), "tabOverviewReplacementTemplate" => "overview.tpl", "tabOverviewModuleOutputTemplate" => "loginbuttons.tpl");
}
function opanel_SingleSignOn($params,$user,$service,$app = ""){
	if(empty($user)){
		return "Username is required for login.";
	}
	$Port=2082;
	if($service == 'admin'){
		$Port=2086;
	}
	if($params["serverhttpprefix"] == 'https'){
		$Port++;
	}
	if($service == 'reseller'){
		$Port.='/reseller';
	}
	$URL=$params["serverhttpprefix"].'://'.(!empty($params["serverhostname"])?$params["serverhostname"]:$params["serverip"]).':'.$Port.'/'.$app.'?username='.$user.'&password='.urlencode($params["password"]);
	if($app == 'login'){
		$URL=$params["serverhttpprefix"].'://'.(!empty($params["serverhostname"])?$params["serverhostname"]:$params["serverip"]).':'.$Port.'/?username='.$user.'&password='.urlencode($params["password"]);
	}
	return array("success" => true, "redirectTo" => $URL);
}
function opanel_ServiceSingleSignOn($params){
	$user = $params["username"];
	$app = App::get_req_var("app");
	if($params["producttype"] == "reselleraccount"){
		if($app){
			$service = "user";
		}else{
			$service = "reseller";
		}
	}else{
		$service = "user";
	}
	return opanel_singlesignon($params, $user, $service, $app);
}
function opanel_AdminSingleSignOn($params){
	$user = $params["serverusername"];
	$service = "admin";
	$params['password'] = $params["serverpassword"];
	return opanel_singlesignon($params, $user, $service);
}
function opanel_ListAccounts($params){
	list($St,$In)=opanel_api($params,"users");
	if(!$St){
		return ["success"=>$St,"accounts"=>[],"error"=>$In];
	}
	$accounts	= [];
	foreach($In as $user=>$data){
		if(!empty($params["serverid"])){
			WHMCS\Database\Capsule::table("tblhosting")->where("domain",$data["domain"])->where("server",$params["serverid"])->update([
				"diskusage"	=> $data["disk"],
				"disklimit"	=> $data["quota"],
				"bwusage"	=> 0,
				"bwlimit"	=> $data["maxbandw"],
				"lastupdate"=> WHMCS\Carbon::now()->toDateTimeString()
			]);
		}
		$accounts[]	= ["name"=>$data["user"],"email"=>$data["contact"],"username"=>$data["user"],"domain"=>$data["domain"],"uniqueIdentifier"=>$data["domain"],"product"=>'Custom',"primaryip"=>$data["ip"],"created"=>WHMCS\Carbon::createFromTimestamp($data['creation'])->toDateTimeString(),"status"=>($data['status']=='1'?WHMCS\Service\Status::ACTIVE:WHMCS\Service\Status::SUSPENDED)];
	}
	return ["success"=>$St,"accounts"=>$accounts];
}
function opanel_ConfirmPackageName($package, $username, array $packages){
	switch ($username) {
		case "":
		case "root":
			if (array_key_exists($package, $packages)) {
				return $package;
			}
			break;
		default:
			if (array_key_exists((string) $username . "_" . $package, $packages)) {
				return (string) $username . "_" . $package;
			}
			if (array_key_exists($package, $packages)) {
				return $package;
			}
	}
	throw new WHMCS\Exception\Module\NotServicable("Product attribute Package Name \"" . $package . "\" not found on server");
}
function opanel_ParamGetPkgInfo($params,$Get){
	if(isset($params["configoptions"][$Get])){
		return $params["configoptions"][$Get];
	}
	$opanel_ConfigOptions=array_search($Get,array_keys(opanel_ConfigOptions($params)));
	if($opanel_ConfigOptions !== FALSE){
		return $params["configoption".(string)($opanel_ConfigOptions+1)];
	}
	if(in_array($Get,['Disk Space','Parked Domains','Subdomains','FTP Accounts','Addon Domains','MySQL Databases','Bandwidth','Email Accounts'])){
		return 0;
	}
	return null;
}
function opanel_CreateAccount($params){
	$oDatas	= [
		'user'		=> $params["username"],
		'domain'	=> $params["domain"],
		'pass'		=> $params["password"],
		'packet'	=> opanel_ParamGetPkgInfo($params,"Package Name"),
		'quota'		=> opanel_ParamGetPkgInfo($params,"Disk Space"),
		'maxpark'	=> opanel_ParamGetPkgInfo($params,"Parked Domains"),
		'maxsub'	=> opanel_ParamGetPkgInfo($params,"Subdomains"),
		'maxftp'	=> opanel_ParamGetPkgInfo($params,"FTP Accounts"),
		'maxaddon'	=> opanel_ParamGetPkgInfo($params,"Addon Domains"),
		'maxmysql'	=> opanel_ParamGetPkgInfo($params,"MySQL Databases"),
		'maxbandw'	=> opanel_ParamGetPkgInfo($params,"Bandwidth"),
		'maxemail'	=> opanel_ParamGetPkgInfo($params,"Email Accounts"),
//		'ip'		=> 0,
		'contact'	=> (isset($params["clientsdetails"]["email"])?$params["clientsdetails"]["email"]:'info@'.$params["domain"]),
		'expiration'=> 0,
		'status'	=> 1,
		'statusmsg'	=> 'oPanel WHMCS API Create',
		'language'	=> (isset($params["configoptions"]["Language"])?$params["configoptions"]["Language"]:null),
		'theme'		=> opanel_ParamGetPkgInfo($params,"oPanel Theme"),
//		'owner'		=> '',
	];
	if(isset($params["model"],$params["configoptions"]["Dedicated IP"])){
		$params["model"]->serviceProperties->save(array("dedicatedip"=>$params["configoptions"]["Dedicated IP"]));
	}
	if(empty(opanel_ParamGetPkgInfo($params,"Reseller Host"))){
		list($St,$In)=opanel_api($params,"user",$oDatas);
	}else{
		list($St,$In)=opanel_api($params,"reseller",$oDatas);
	}
	if($St){
		return "success";
	}
	return $In;
}
function opanel_ChangePackage($params){
	if(empty($params["username"]) or empty($params["domain"])){
		return "Cannot perform action without accounts username, domain";
	}
	$oDatas	= [
		'user'		=> $params["username"],
		'domain'	=> $params["domain"],
		'pass'		=> $params["password"],
		'packet'	=> opanel_ParamGetPkgInfo($params,"Package Name"),
		'quota'		=> opanel_ParamGetPkgInfo($params,"Disk Space"),
		'maxpark'	=> opanel_ParamGetPkgInfo($params,"Parked Domains"),
		'maxsub'	=> opanel_ParamGetPkgInfo($params,"Subdomains"),
		'maxftp'	=> opanel_ParamGetPkgInfo($params,"FTP Accounts"),
		'maxaddon'	=> opanel_ParamGetPkgInfo($params,"Addon Domains"),
		'maxmysql'	=> opanel_ParamGetPkgInfo($params,"MySQL Databases"),
		'maxbandw'	=> opanel_ParamGetPkgInfo($params,"Bandwidth"),
		'maxemail'	=> opanel_ParamGetPkgInfo($params,"Email Accounts"),
		'ip'		=> opanel_ParamGetPkgInfo($params,"Dedicated IP"),
		'contact'	=> (isset($params["clientsdetails"]["email"])?$params["clientsdetails"]["email"]:'info@'.$params["domain"]),
		'expiration'=> 0,
		//'status'	=> 1,
		'statusmsg'	=> 'oPanel WHMCS API Edit',
		'language'	=> (isset($params["configoptions"]["Language"])?$params["configoptions"]["Language"]:null),
		'theme'		=> opanel_ParamGetPkgInfo($params,"oPanel Theme"),
//		'owner'		=> '',
	];
	if(isset($params["model"],$params["configoptions"]["Dedicated IP"])){
		$params["model"]->serviceProperties->save(array("dedicatedip"=>$params["configoptions"]["Dedicated IP"]));
	}
	if(empty(opanel_ParamGetPkgInfo($params,"Reseller Host"))){
		list($St,$In)=opanel_api($params,"user/".$oDatas["user"],$oDatas,'PUT');
	}else{
		list($St,$In)=opanel_api($params,"reseller/".$oDatas["user"],$oDatas,'PUT');
	}
	if($St){
		return "success";
	}
	return $In;
}
function opanel_SuspendAccount($params){
	if(empty($params["username"]) or empty($params["domain"])){
		return "Cannot perform action without accounts username, domain";
	}
	$oDatas	= [
		'user'		=> $params["username"],
		'domain'	=> $params["domain"],
		'status'	=> 8,
	];
	if(!empty($params["suspendreason"])){
		$oDatas['statusmsg']	= $params["suspendreason"];
	}
	if(empty(opanel_ParamGetPkgInfo($params,"Reseller Host"))){
		list($St,$In)=opanel_api($params,"user/".$oDatas["user"],$oDatas,'PUT');
	}else{
		list($St,$In)=opanel_api($params,"reseller/".$oDatas["user"],$oDatas,'PUT');
	}
	if($St){
		return "success";
	}
	return $In;
}
function opanel_UnsuspendAccount($params){
	if(empty($params["username"]) or empty($params["domain"])){
		return "Cannot perform action without accounts username, domain";
	}
	$oDatas	= [
		'user'		=> $params["username"],
		'domain'	=> $params["domain"],
		'status'	=> 1,
	];
	if(!empty($params["suspendreason"])){
		$oDatas['statusmsg']	= $params["suspendreason"];
	}else{
		$oDatas['statusmsg']	= 'oPanel WHMCS API UnSuspend';
	}
	if(empty(opanel_ParamGetPkgInfo($params,"Reseller Host"))){
		list($St,$In)=opanel_api($params,"user/".$oDatas["user"],$oDatas,'PUT');
	}else{
		list($St,$In)=opanel_api($params,"reseller/".$oDatas["user"],$oDatas,'PUT');
	}
	if($St){
		return "success";
	}
	return $In;
}
function opanel_TerminateAccount($params){
	if(empty($params["username"])){
		return "Cannot perform action without accounts username";
	}
	if(empty(opanel_ParamGetPkgInfo($params,"Reseller Host"))){
		list($St,$In)=opanel_api($params,"user/".$params["username"],[],'DELETE');
	}else{
		list($St,$In)=opanel_api($params,"reseller/".$params["username"],[],'DELETE');
	}
	if($St){
		return "success";
	}
	return $In;
}
function opanel_ChangePassword($params){
	if(empty($params["username"]) or empty($params["password"]) or empty($params["domain"])){
		return "Cannot perform action without accounts username, domain, password";
	}
	$oDatas	= [
		'user'		=> $params["username"],
		'domain'	=> $params["domain"],
		'pass'		=> $params["password"],
	];
	if(!empty($params["suspendreason"])){
		$oDatas['statusmsg']	= $params["suspendreason"];
	}else{
		$oDatas['statusmsg']	= 'oPanel WHMCS API Set Password';
	}
	if(empty(opanel_ParamGetPkgInfo($params,"Reseller Host"))){
		list($St,$In)=opanel_api($params,"user/".$oDatas["user"],$oDatas,'PUT');
	}else{
		list($St,$In)=opanel_api($params,"reseller/".$oDatas["user"],$oDatas,'PUT');
	}
	if($St){
		return "success";
	}
	return $In;
}
function opanel_ClientAreaAllowedFunctions(){
	return array("CreateEmailAccount");
}
function opanel_CreateEmailAccount($params){
	$oDatas	= ['ADDMAIL'=>App::get_req_var("email_prefix"),'ADDPASS'=>App::get_req_var("email_pw"),'ADDQUOTA'=>(int) App::get_req_var("email_quota")];
	list($St,$In)=opanel_api($params,"user/".$params["username"].'/email/accounts',$oDatas,'PUT');
	if($St){
		return array("jsonResponse" => array("success" => true));
	}
	return array("jsonResponse" => array("success" => false, "errorMsg" => "An error occurred. Please contact support."));
}
function opanel_UsageUpdate(array $params){
	opanel_api($params,"users");

}
