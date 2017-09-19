<?
$details = getRequestDetails($id);
if(count($details) == 0)
	exit(header("Location: https://twitchtokengenerator.com/"));
if($details["enabled"] == "0")
	exit(header("Location: https://twitchtokengenerator.com/"));

fireEmail($details['requester_email'], $details['requester_name'], $token);
disableRequest($id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Twitch Token Generator by swiftyspiffy</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
		
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js"></script>
	<script src="../bootstrap-checkbox.min.js"></script>
	<script src="script.js"></script>
	<link rel="stylesheet" href="../style.css">
	<link rel="icon" type="image/ico" sizes="48x48" href="../favicon-48x48.ico">
</head>
<div class="col-md-2"></div>
<div class="container col-md-8">	
	<br>
	
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title text-center">Request Success</h3>
		</div>
		<div class="panel-body">
			<span>
			Success! Your Twitch token has been emailed to your buddy <b><? echo $details['requester_name']; ?></b>! They will receive it shortly!
			</span>
		</div>
	</div>

    <div class="row text-center">
        <span>
            <i>Website Source: <a href="https://github.com/swiftyspiffy/twitch-token-generator" target="_blank">Repo</a><br>This tool was created and is maintained by swiftyspiffy. <br>
                <a href="https://twitch.tv/swiftyspiffy" target="_blank">
                    <img src="https://twitchtokengenerator.com/img/twitch.png" width="30" height="30">
                </a>
                <a href="https://twitter.com/swiftyspiffy" target="_blank">
                    <img src="https://twitchtokengenerator.com/img/twitter.png" width="45" height="45">
                </a>
                <a href="https://github.com/swiftyspiffy" target="_blank">
                    <img src="https://twitchtokengenerator.com/img/github.png" width="30" height="30">
                </a>
            <i>
        </span>
    </div>
	<br><br>
</div>
<script>
/* --- GA START --- */
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-91354985-1', 'auto');
ga('send', 'pageview');
/* --- GA END --- */

/* --- Runtime PHP Generated JS Vars START -- */
var authSuccessful = <? echo (strlen($access_token) > 1 ? "true" : "false"); ?>;
/* --- Runtime PHP Generated JS Vars START -- */
</script>
</html>


<?
function fireEmail($email, $name, $token) {
	$to      = $email;
	$subject = 'TwitchTokenGenerator.com - Request Successful';
	$message = 'Hello '.$name."!\n\nYour TwitchTokenGenerator request has been completed successfully!\n\nUsername: ".getUsername($token)."\nToken: ".$token."\n\nCheers,\nswiftyspiffy";
	$headers = 'From: requests@twitchtokengenerator.com' . "\r\n" .
		'Reply-To: noreply@twitchtokengenerator.com' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();

	mail($to, $subject, $message, $headers);
}

function getUsername($token) {
    $usernameResult = file_get_contents("https://api.twitch.tv/kraken?oauth_token=" . $token);
    $json_decoded_usernameResult = json_decode($usernameResult, true);
    return $json_decoded_usernameResult['token']['user_name'];
}

function disableRequest($id) {
	mysql_connect(DB_HOST, DB_USER, DB_PASS) or
		die("Could not connect: " . mysql_error());
	mysql_select_db(DB_ANONDATA);

	$ab = mysql_query("UPDATE  `DB_ANONDATA`.`REQUESTS` SET  `enabled` =  '0' WHERE  `REQUESTS`.`unique_string` = '".mysql_real_escape_string($id)."';") or trigger_error(mysql_error());
}

function getRequestDetails($id) {
	mysql_connect(DB_HOST, DB_USER, DB_PASS) or
		die("Could not connect: " . mysql_error());
	mysql_select_db(DB_ANONDATA);

	$ab = mysql_query("SELECT * FROM `REQUESTS`") or trigger_error(mysql_error());
	while ($row = mysql_fetch_array($ab)) {
		if(strtolower($id) == strtolower($row['unique_string'])) {
			$scopeStr = $row['scopes'];
			$scopes;
			if (strpos($scopeStr, '+') !== false)
				$scopes = explode("+", $scopeStr);
			else
				array_push($scopes, $scopeStr);
			return array("enabled" => $row['enabled'], "scopes_str" => $scopeStr, "scopes" => $scopes, "requester_name" => $row['requester_name'], "requester_email" => $row['requester_email']);
		}
		
	}
	return array();
}
?>
