<?php
  require_once('Apache/Solr/Service.php');
  if(isset($_REQUEST['q'])){
  	  $query=$_REQUEST['q'];
  }
  else{
  	 $query=false;
  }
  $url="http://10.120.36.136:8983/solr/csci572/suggest?q=".$query;
  //http://192.168.0.31:8983/solr/csci572/suggest?q=ca
  $opt=array(
       'http'=>array(
            'method'=>'GET',
            'timeout'=>60
       	)
  	);
  $context=stream_context_create($opt);
  $result=file_get_contents($url,false,$context);
  echo $result;

?>