<?php
$servername = 'localhost';
$username   = 'wwwdbcsp_cspusr';
$password   = 'tT3*$&nFG4QI';
$dbname     = 'wwwdbcsp_csp';
//$username   = 'root';
//$password   = '';
//$dbname     = 'db_revamp';
$conn       = new mysqli($servername, $username, $password, $dbname);

  

$sql="select * from tblUserBiography2 order by ID ASC";
$result=$conn->query($sql);

while($row=$result->fetch_assoc())
{  

  mysqli_query($conn,"INSERT INTO tblUserBiography set clientID='".$row['clientID']."', field_id='3',field_value='".$row['AboutMe']."',UserID='".$row['UserID']."',LastUpdateDate='".$row['LastUpdateDate']."'");
  
  mysqli_query($conn,"INSERT INTO tblUserBiography set clientID='".$row['clientID']."', field_id='4',field_value='".$row['Occupation']."',UserID='".$row['UserID']."',LastUpdateDate='".$row['LastUpdateDate']."'");
 
  mysqli_query($conn,"INSERT INTO tblUserBiography set clientID='".$row['clientID']."', field_id='5',field_value='".$row['PoliticalViews']."',UserID='".$row['UserID']."',LastUpdateDate='".$row['LastUpdateDate']."'");
 
  mysqli_query($conn,"INSERT INTO tblUserBiography set clientID='".$row['clientID']."', field_id='6',field_value='".$row['RelegiousViews']."',UserID='".$row['UserID']."',LastUpdateDate='".$row['LastUpdateDate']."'");
 
  mysqli_query($conn,"INSERT INTO tblUserBiography set clientID='".$row['clientID']."', field_id='11',field_value='".$row['HobbiesInterests']."',UserID='".$row['UserID']."',LastUpdateDate='".$row['LastUpdateDate']."'");
  
  mysqli_query($conn,"INSERT INTO tblUserBiography set clientID='".$row['clientID']."', field_id='12',field_value='".$row['LikesDislikes']."',UserID='".$row['UserID']."',LastUpdateDate='".$row['LastUpdateDate']."'");

}




?>