<?php
   include 'simple_html_dom.php';
  
   
   function getURL($id){
       $index=strrpos($id,"/");
       $realid=substr($id,$index+1);
       //echo $realid;
       $conn=mysqli_connect("localhost","root","","hw5");
       if(!$conn){
       	   die("Connected failed:".mysqli_connect_error());
       }
       $sql="select url from id_url where id='".$realid."'";
       $result=$conn->query($sql);
       $row=mysqli_fetch_assoc($result);
       $conn->close();
       return $row['url'];
   }

   function getSnippets($id,$input){
    
   	    $index=strrpos($id,"/");
        $realid=substr($id,$index+1);
        $file=file_get_contents('USA/'.$realid);

        $str=preg_replace("/<script[^>]*?>.*?<\/script>/si","",$file);
        $str=preg_replace("/<([a-z]+)[^>]*>/i","#",$str);
        $str=preg_replace("/<\/([a-z]+)[^>]*>/i","#",$str);
      //  $dom=new DOMDocument('1.0','utf-8');
      //  $dom->loadHTML($file);
      //  $divs=$dom->getElementsByTagName('div');
       
      //  $plaintext="";
      //  foreach($divs AS $div)
      //      $plaintext.=$div->nodeValue;
      //  }
  

        explode(" ",$input);
 

        $pattern=$input[0];
        for($i=1;$i<count($input);$i++){
              $pattern.="|".$input[$i];
        }
        $pattern="/[^:#.?!]*\\b(".$pattern.")\\b[^#.?!|]*[.?!]/i";
        $snippet="";
        if(preg_match($pattern,$str,$matches,PREG_OFFSET_CAPTURE)){
        
            $index=$matches[0][1];
            $index += 1;//adjust for one whitespace
            $start_index = max(0,$index - 100);
            $snippet = substr($str,$start_index,200);
        }
        foreach($input as $term){
            $snippet = preg_replace('/'.$term.'/i',"<b><em>\$0</em></b>",$snippet);
         }
         $snippet=preg_replace('/#/',"",$snippet);
        return $snippet;

    }

    function emphasize($content,$query){
       //  $content=strtolower($content);
         $query=strtolower($query);
         $query=explode(" ",$query);
      //   print_r($query);
     //    echo $content."<br/>";
    
         $snippet=substr($content,0,300);
         foreach($query as $term){
          //    echo $term;
              $snippet=preg_replace('/'.$term.'/i',"<b><em>\$0</em></b>",$snippet);
         }
        // echo $content;
         return $snippet;
    }

?>