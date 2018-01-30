<?php
   $conn=mysqli_connect("localhost","root","","hw5");
  if(!$conn){
  	   die("Connection failed:".mysqli_connect_error());
  }
  echo "connect success!";
  $dataset=fopen("USA_Today_Map.csv","r");
  while(($line=fgets($dataset))!==false){
  	   $strgroup=explode(",",$line);
  	   $id=$strgroup[0];
  	   $url=$strgroup[1];
  	   $sql="insert into id_url(id,url) values('".$id."','".$url."')";
  	   $conn->query($sql);

  }
  echo "initialize success";
  $conn->close();
 
?>