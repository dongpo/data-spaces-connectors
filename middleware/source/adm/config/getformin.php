<?php
if($_SERVER['QUERY_STRING']<>""){
	$MYSTRING=$_SERVER['QUERY_STRING'];
	parse_str($MYSTRING, $params);
	foreach ($params as $key => $term){
		if (is_array($term)){
			for ($i = 0; $i < count($term); $i++) {
				if (is_array($term[$i])){
					if (is_array($term[$i][$z])){
						for ($h = 0; $h < count($term[$i][$z]); $h++) {
							$term[$i][$z][$h]= str_replace("'", "&#039;",$term[$i][$z][$h]);
							$term[$i][$z][$h]= str_replace("\"", "&#034;",$term[$i][$z][$h]);							
							$term[$i][$z][$h]= str_replace("=", "&#061;",$term[$i][$z][$h]);							
						}
					}else{
						$term[$i][$z]= str_replace("'", "&#039;",$term[$i][$z]);
						$term[$i][$z]= str_replace("\"", "&#034;",$term[$i][$z]);							
						$term[$i][$z]= str_replace("=", "&#061;",$term[$i][$z]);
					}					
					
				}else{
					$term[$i]= str_replace("'", "&#039;",$term[$i]);
					$term[$i]= str_replace("\"", "&#034;",$term[$i]);							
					$term[$i]= str_replace("=", "&#061;",$term[$i]);					
				}
			}
			$newinput=$key;
			$$newinput=$term;			
		}else{			
			$term= str_replace("'", "&#039;",$term);
			$term= str_replace("\"", "&#034;",$term);
			$term= str_replace("=", "&#061;",$term);			
			$newinput=$key;
			$$newinput=$term;
		}		
		//$term= str_replace("'", "&#039;",$term);
		//$term= str_replace("\"", "&#034;",$term);
		//$term= str_replace("=", "&#061;",$term);
		//$newinput=$key;
		//$$newinput=$term;
	}
}
if(count($_POST)>0){
	foreach ($_POST as $key => $term){
		if (is_array($term)){
			for ($i = 0; $i < count($term); $i++) {
				if (is_array($term[$i])){
					if (is_array($term[$i][$z])){
						for ($h = 0; $h < count($term[$i][$z]); $h++) {
							$term[$i][$z][$h]= str_replace("'", "&#039;",$term[$i][$z][$h]);
							$term[$i][$z][$h]= str_replace("\"", "&#034;",$term[$i][$z][$h]);							
							$term[$i][$z][$h]= str_replace("=", "&#061;",$term[$i][$z][$h]);							
						}
					}else{
						$term[$i][$z]= str_replace("'", "&#039;",$term[$i][$z]);
						$term[$i][$z]= str_replace("\"", "&#034;",$term[$i][$z]);							
						$term[$i][$z]= str_replace("=", "&#061;",$term[$i][$z]);
					}					
					
				}else{
					$term[$i]= str_replace("'", "&#039;",$term[$i]);
					$term[$i]= str_replace("\"", "&#034;",$term[$i]);							
					$term[$i]= str_replace("=", "&#061;",$term[$i]);					
				}
			}
			$newinput=$key;
			$$newinput=$term;			
		}else{			
			$term= str_replace("'", "&#039;",$term);
			$newinput=$key;
			$$newinput=$term;
		}
	}

}


?>
