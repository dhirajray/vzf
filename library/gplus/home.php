<!doctype html>
<html>
<title>Home - Login with Google Plus</title>
<body >
<div style="margin:0px auto; width:800px;text-align:left;font-family:arial">
<a href='http://9lessons.info'>9lessons.info</a> 

<h1>Home - Login with Google Plus</h1>
<?php
session_start();
if (!isset($_SESSION['gplusdata'])) {
    // Redirection to login page twitter or facebook
    header("location: index.php");
}
else
{
$me=$_SESSION['gplusdata'];
?>
<div>
<img src="<?php print $me['image']['url']; ?>?sz=82" /><br/>
<b>Name:</b> <?php print $me['displayName']; ?><br/>
<b>Gplus Id</b> <?php print $me['id']; ?><br/>
<b>Male:</b> <?php print $me['gender']; ?><br/>
<b>Relationship:</b> <?php print $me['relationshipStatus']; ?><br/>
<b>Location:</b> <?php print $me['placesLived'][0]['value']; ?><br/>
<b>Tagline:</b> <?php print $me['tagline']; ?><br/>  
</div>
<?php 
print "<a class='logout' href='index.php?logout'>Logout</a><br/> <br/> "; 
}
?>

<b>SQL Query </b>: insert into users(oauth_provider, username,gplusid, gender, location) values("Google","<span style="color:#cc0000"> <?php print $me['displayName']; ?></span>","<span style="color:#cc0000"> <?php print $me['id']; ?></span>","<span style="color:#cc0000"> <?php print $me['gender']; ?></span>","<span style="color:#cc0000"><?php echo $me['placesLived'][0]['value']; ?></span>")';

</div>

</body>
</html>