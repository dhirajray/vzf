<?php
	$openinviter_settings=array(
		"username"=>"OpenInviter.com username",
		"private_key"=>"OpenInviter.com API key",
		"cookie_path"=>".",
		"message_body"=>"You are invited to http://example.com",
		"message_subject"=>" is inviting you to http://example.com",
		"transport"=>"curl", //Replace "curl" with "wget" if you would like to use wget instead
		"local_debug"=>"on_error", //Available options: on_error => log only requests containing errors; always => log all requests; false => don`t log anything
		"remote_debug"=>FALSE //When set to TRUE OpenInviter sends debug information to our servers. Set it to FALSE to disable this feature
	);
	?>