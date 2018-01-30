<?php
 error_reporting(E_ALL ^ E_WARNING); 
 ini_set('max_execution_time',3600);
 ini_set('memory_limit',-1);
 header('Content-Type:text/html;charset=utf-8');



 include 'initialize.php';
 include 'SpellCorrector.php';
 include 'GetSnippets.php';
 
 $limit=10;
 $query=isset($_REQUEST['q'])?$_REQUEST['q']:false;
 $results=false;
  
 $input=null;
 if($query){
 	
 	require_once('Apache/Solr/Service.php');
 	$solr=new Apache_Solr_Service('10.120.36.136',8983,'/solr/csci572/');
 	$query=strtolower($query);
 	$input=explode(" ",$query);
 	if(get_magic_quotes_gpc()==1){
 		$query=stripslashes($query);
 	}
 	try{
 		if(isset($_GET['way'])==false){
 			$choice="Lucence";
 		}
 		else{
 			$choice=$_GET['way'];
 		}
		if(strcmp($choice,"Lucence")==0){
			$param=array('hl'=>'on','hl.fl'=>'*');
			$results=$solr->search($query,0,$limit,$param);
		}
 		if(strcmp($choice,"PageRank")==0){
 			$param=array('sort'=>'pageRankFile desc','hl'=>'on','hl.fl'=>'*');
 			$results=$solr->search($query,0,$limit,$param);
 		}
 		
 	}
 	catch(Exception $e){
 		die("<html><head><title>SEARCH ERROR</title></head><body><pre>{$e->__toString()}</pre></body></html>");
 	}
 }
?>
<html>
    <head>
        <title>csci572 homework4</title>
          
		  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css"/>
		  <script  src="https://code.jquery.com/jquery-1.12.4.js"
		  integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU="
		  crossorigin="anonymous"></script>
		  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" 
		  integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" 
		  crossorigin="anonymous"></script>
		<script type="text/javascript">
		      function getSuggest(term,e){
                    if(e.keyCode==38 || e.keyCode==40){
                    	return;
                    }
                    if(term.length==0 ||term[term.length-1]==" "){
                    	return;
                    }
                    arr=term.split(" ");
                    input=arr[arr.length-1].toLowerCase();
                    var req=new XMLHttpRequest();
                    req.onreadystatechange=function(){
                    	if(this.readyState==4 || this.readyState==200){
                    		if(document.getElementById("q").value!=term){
                    			return;
                    		}
                    		var response=JSON.parse(this.responseText);
                    		var suggestArr=response['suggest']['suggest'][input]['suggestions'];
                    		var limit=Math.min(suggestArr.length,10);
                            var list=[];
                            for(i=0;i<limit;i++){
                            	var prefix=term.substr(0,term.lastIndexOf(" ")+1);
                            	list.push(prefix+suggestArr[i]['term']);
                            //	alert(suggestArr[i]['term']);
                            }
                            $("#q").autocomplete({
                            	 source:function(request,response){
                            	 	response(list);
                            	 }
                            });
                    	}
                    };
                   req.open("GET","suggest.php?q="+input,true);
                   req.send();
		      }
		</script>

		<style>
		      #header{
		           background-color:#F5F5F5;
		           width:100%;
		           height:10%;

		      }
		      em {
		      	 color:red;
		      }
		</style>
    </head>
    <body class="ui-widget">
	  <div id="header" align="center">
	    
	         <form accept-charset="utf-8" method="get">
	              <label for="q">SEARCH:</label>
	              <input id="q" name="q" type="text" value="<?php  echo htmlspecialchars($query,ENT_QUOTES,'utf-8');?>" 
	              onkeyup="getSuggest(this.value,event)" autocomplete="off"/>
	              <input type="submit" value="SEARCH"/><br/>
	              <input type="radio" name="way" value="PageRank"/>pageRank&nbsp;&nbsp;
	              <input type="radio" name="way" value="Lucence"/>Lucene<br/>
	              
	         </form>
		
      </div>
      <?php
            $correct_ones=[];
            $isCorrect=true;
            for($index=0;$index<count($input);$index++){
            	 $temp=strtolower(SpellCorrector::correct($input[$index]));
            	 if($input[$index]!=$temp){
            	 	 $isCorrect=false;
            	 }
            	 array_push($correct_ones,$temp);
            }
            if($isCorrect==false){
            	 $correct_ones=implode(" ",$correct_ones);
            	 echo  "Do you mean:";
            	 echo  "<a href='index.php?q=".$correct_ones."'>".$correct_ones."</a>";
            }



      ?>
<?php // display results 
  
	if ($results) { 
	 
		$total = (int) $results->response->numFound; 
		
		$start = min(1, $total); 
		$end = min($limit, $total); 
?> 
<div>
	Results <?php echo $start; ?> - <?php echo $end;?> of <?php echo $total; ?>:
</div> 
<ol> 
	<?php 
		// iterate result documents 
		foreach ($results->response->docs as $doc) { 

	?> 
	<li> 
	<table style="text-align: left"> 
	<?php // iterate document fields / values 
	   
	   $DES="abc";
		foreach ($doc as $field => $value) { 
		
		  //   echo $field."=>".$value."<br/>";
		     $flag1=strcmp($field,'id');
			 $flag2=strcmp($field,'description');
			 $flag3=strcmp($field,'title');
			
		//	 $flag4=strcmp($field,'og_url');
			 if($flag1!=0 && $flag3!=0 && $flag2!=0){
				  continue;
			 }
			 else if($flag1==0){
				 $ID=$value;
				 $url=getURL($ID);
			 }
			 else if($flag2==0){
				 $DES=$value;
			 }
			else if($flag3==0){
				  $title=$value;
			 }
			/* else if($flag4==0){
				 $url=$value;
			 }*/
			
	   }
	   // echo $DES;
             echo '<tr>';
             echo '<td  width="100%"><strong><font size=4><a href='.$url.'>'.htmlspecialchars($title,ENT_QUOTES,'utf-8').'</a></font></strong></td>';
			 echo '</tr>';

			 echo '<tr>';
			// echo $query;
			 if(stristr($DES,$query)){
			 	// echo "find it"."<br/>";
			 	 echo '<td width="100%">'.emphasize($DES,$query).'</td>';
			 }
			 else{
			 	echo  '<td width="100%">'.getSnippets($ID,$input).'</td>';
			 }
          //   
			 echo '</tr>';
			 
		/*	 echo '<tr>';
			 echo   '<th>URL:</th>';
             echo '<td  width="100%"><a href='.$url.'>'.htmlspecialchars($url,ENT_QUOTES,'utf-8').'</a></td>';
			 echo '</tr>';	

			 
			 echo '<tr>';
			 echo   '<th>ID:</th>';
             echo '<td  width="100%">'.htmlspecialchars($ID,ENT_QUOTES,'utf-8').'</td>';
			 echo '</tr>';	

			 
			 echo '<tr>';
			 echo   '<th>Description:</th>';
             echo '<td  width="100%">'.htmlspecialchars($DES,ENT_QUOTES,'utf-8').'</td>';
			 echo '</tr>';	*/   
	  ?>
	</table>
	</li> 
	<?php 
	   } 
	?> 
</ol> 
	<?php 
	   } 
     ?>

</body>
</html>