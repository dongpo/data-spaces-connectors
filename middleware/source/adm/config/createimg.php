<?php 
	function addwater($watermark_image,$thumb,$pos){
	   // 載入浮水印圖
	   $w_image = imagecreatefrompng($watermark_image) ;
		// 取出浮水印圖 寬 與 高
		$w_width = imagesx($w_image);
		$w_height = imagesy($w_image);
		//抓取原圖高與寬
		$tw=imagesx($thumb);
		$th=imagesy($thumb);
		$x=0;$y=0;
		if($pos==1){
			$x=0;
			$y=0;
		}else if($pos==2){
			$x=$tw-$w_width;
			$y=0;
		}else if($pos==3){
			$x=0;
			$y=$th-$w_height;
		}else if($pos==4){
			$x=$tw-$w_width;
			$y=$th-$w_height;
		}
		imagecopy($thumb,$w_image,$x,$y,0,0,$w_width,$w_height);
	}
function createimg_sp($tempid,$imgurl,$width,$height,$imgwidth,$imgheight,$imgs,$thumbwidth,$thumbheight,$src,$myext,$wat){//$wat==water mark
	// masks
	$mask = imagecreatefrompng( 'mask.png' );
	if($imgwidth>0 && $imgheight>0){
		$thumb_w = $imgwidth;
		$thumb_h = intval($height / $width * $thumb_w);
		if($imgheight>=$thumb_h ){		  //如果不夠高,要以高為標準
			$thumb_h = $imgheight;
			$thumb_w = intval($width / $height  * $thumb_h);				  
		}
		//計算圖片位置
		if($thumb_w==$imgwidth){
			  $xpos =0;
			  $ypos =-($thumb_h-$imgheight)/2;
		}else{
			   $xpos =-($thumb_w-$imgwidth)/2;
			   $ypos =0;
		}				  
	}else if($imgwidth>0){	//使用一邊為尺寸基礎不切圖
		$thumb_w = $imgwidth;
		$thumb_h = intval($height / $width * $thumb_w); //不切圖
		$xpos =0;
		$ypos =0;
		$imgheight=$thumb_h;
    }else if($imgheight>0){	//使用一邊為尺寸基礎不切圖
		$thumb_h = $imgheight;
		$thumb_w = intval($width / $height  * $thumb_h);	
		$xpos =0;
		$ypos =0;	
		$imgwidth=$thumb_w;
    }else{	//使用原尺寸
		$thumb_h=$height;
		$thumb_w=$width;
		$xpos =0;
		$ypos =0;	
	}
 	$thumb = imagecreatetruecolor($imgwidth, $imgheight);
	if($myext=="png"){
		imagealphablending($thumb,false);
		imagesavealpha($thumb,true);
	}
	imagecopyresampled($thumb, $src,$xpos,$ypos, 0, 0, $thumb_w, $thumb_h, $width, $height);  
	//$source = imagecreatefrompng($src);
	imagealphamask( $thumb, $mask );
	//儲存
	imagepng($thumb, $imgurl.$tempid.".png");	
}
function createimg($tempid,$imgurl,$width,$height,$imgwidth,$imgheight,$imgs,$thumbwidth,$thumbheight,$src,$myext,$wat){//$wat==water mark
	// water mark
	$watermark_image="../img/water.png";
	if(file_exists($watermark_image)){
		$w_image = imagecreatefrompng($watermark_image) ;
	}
	if($imgwidth>0 && $imgheight>0){
		$thumb_w = $imgwidth;
		$thumb_h = intval($height / $width * $thumb_w);
		if($imgheight>=$thumb_h ){		  //如果不夠高,要以高為標準
			$thumb_h = $imgheight;
			$thumb_w = intval($width / $height  * $thumb_h);				  
		}
		//計算圖片位置
		if($thumb_w==$imgwidth){
			  $xpos =0;
			  $ypos =-($thumb_h-$imgheight)/2;
		}else{
			   $xpos =-($thumb_w-$imgwidth)/2;
			   $ypos =0;
		}				  
	}else if($imgwidth>0){	//使用一邊為尺寸基礎不切圖
		$thumb_w = $imgwidth;
		$thumb_h = intval($height / $width * $thumb_w); //不切圖
		$xpos =0;
		$ypos =0;
		$imgheight=$thumb_h;
    }else if($imgheight>0){	//使用一邊為尺寸基礎不切圖
		$thumb_h = $imgheight;
		$thumb_w = intval($width / $height  * $thumb_h);	
		$xpos =0;
		$ypos =0;	
		$imgwidth=$thumb_w;
    }else{	//使用原尺寸
		$thumb_h=$height;
		$thumb_w=$width;
		$xpos =0;
		$ypos =0;	
	}
 	$thumb = imagecreatetruecolor($imgwidth, $imgheight);
	if($myext=="png"){
		imagealphablending($thumb,false);
		imagesavealpha($thumb,true);
	}
	imagecopyresampled($thumb, $src,$xpos,$ypos, 0, 0, $thumb_w, $thumb_h, $width, $height);  
	if($wat==1){
		imagealphablending($thumb, true);
	//	imagesavealpha($thumb,true);
	//	imagecopyresampled($thumb, $src,$xpos,$ypos, 0, 0, $thumb_w, $thumb_h, $width, $height);  
	//	$w_width = imagesx($w_image);
	//	$w_height = imagesy($w_image);
	}
	//儲存
	if($myext=="png"){
		imagepng($thumb, $imgurl.$tempid.".png");	
	}else{
		imagejpeg($thumb, $imgurl.$tempid.".jpg", 100);	
	}
	//作縮圖--邏輯與 大圖相同 分開計算與儲存
	if($imgs==1){
		if($thumbwidth>0 && $thumbheight>0){
			$thumb_w = $thumbwidth;
			$thumb_h = intval($height / $width * $thumb_w);
			if($thumbheight>=$thumb_h ){		  //如果不夠高,要以高為標準
				$thumb_h = $thumbheight;
				$thumb_w = intval($width / $height  * $thumb_h);				  
			}
			//計算圖片位置
			if($thumb_w==$thumbwidth){
				  $xpos =0;
				  $ypos =-($thumb_h-$thumbheight)/2;
			}else{
				   $xpos =-($thumb_w-$thumbwidth)/2;
				   $ypos =0;
			}				  
		}else if($thumbwidth>0){	//使用一邊為尺寸基礎不切圖
			$thumb_w = $thumbwidth;
			$thumb_h = intval($height / $width * $thumb_w); //不切圖
			$xpos =0;
			$ypos =0;
			$thumbheight=$thumb_h;
		}else if($thumbheight>0){	//使用一邊為尺寸基礎不切圖
			$thumb_h = $thumbheight;
			$thumb_w = intval($width / $height  * $thumb_h);	
			$xpos =0;
			$ypos =0;	
			$thumbwidth=$thumb_w;
		}else{	//使用60
			$thumb_h=60;
			$thumb_w=60*($width / $height);
			$xpos =0;
			$ypos =0;	
		}
		$thumb = imagecreatetruecolor($thumbwidth, $thumbheight);
		if($myext=="png"){
			imagealphablending($thumb,false);
			imagesavealpha($thumb,true);
		}
		imagecopyresampled($thumb, $src,$xpos,$ypos, 0, 0, $thumb_w, $thumb_h, $width, $height);  
		//儲存
		if($myext=="png"){
			imagepng($thumb, $imgurl.$tempid."s.png");	
		}else{
			imagejpeg($thumb, $imgurl.$tempid."s.jpg", 100);	
		}
	}
}
	function imagealphamask( &$picture, $mask ) {
		// Get sizes and set up new picture
		$xSize = imagesx( $picture );
		$ySize = imagesy( $picture );
		$newPicture = imagecreatetruecolor( $xSize, $ySize );
		imagesavealpha( $newPicture, true );
		imagefill( $newPicture, 0, 0, imagecolorallocatealpha( $newPicture, 0, 0, 0, 127 ) );
	
		// Resize mask if necessary
		if( $xSize != imagesx( $mask ) || $ySize != imagesy( $mask ) ) {
			$tempPic = imagecreatetruecolor( $xSize, $ySize );
			imagecopyresampled( $tempPic, $mask, 0, 0, 0, 0, $xSize, $ySize, imagesx( $mask ), imagesy( $mask ) );
			imagedestroy( $mask );
			$mask = $tempPic;
		}
	
		// Perform pixel-based alpha map application
		for( $x = 0; $x < $xSize; $x++ ) {
			for( $y = 0; $y < $ySize; $y++ ) {
				$alpha = imagecolorsforindex( $mask, imagecolorat( $mask, $x, $y ) );
				$color = imagecolorsforindex( $picture, imagecolorat( $picture, $x, $y ) );
				$alpha = 127 - floor((127-$color['alpha']) * ($alpha[ 'red' ]/255));
				imagesetpixel( $newPicture, $x, $y, imagecolorallocatealpha( $newPicture, $color[ 'red' ], $color[ 'green' ], $color[ 'blue' ], $alpha ) );
			}
		}
	
		// Copy back to original picture
		imagedestroy( $picture );
		$picture = $newPicture;
	}
  function addtext($txt,$thumb,$pos){
		global $fontpos;
		$pt=25;//上方誤差值
		$fz=16;
		$fh=24;
		$t=explode("\n",$txt);
		$h=count($t)*$fh;//一行20高
		$tw=imagesx($thumb);
		$th=imagesy($thumb);
		$white=imagecolorallocate($thumb, 255, 255, 255);
		$shadow=imagecolorallocatealpha($thumb, 60, 60, 60, 30);
		$shadow2=imagecolorallocatealpha($thumb, 90, 90, 90,80);
		$shadow3=imagecolorallocatealpha($thumb, 90, 90, 90,110);
		$x=$fh;$y=0;
		if($pos==3){
			$y=$th-$h;
		}if($pos==1){
			$y=$pt;
		}
		$a=0;
	//	echo $th."-".$h."<BR>";
	//	putenv('GDFONTPATH=' . realpath('.'));
		foreach($t as $tt){
			//imagettftext($thumb, 14, 0, $x+3, ($y+$a*20)+3, $shadow3,$fontpos,$tt);
			imagettftext($thumb, $fz, 0, $x+3, ($y+$a*$fh)+2, $shadow3,$fontpos,$tt);
			imagettftext($thumb, $fz, 0, $x+2, ($y+$a*$fh)+3, $shadow3,$fontpos,$tt);
			imagettftext($thumb, $fz, 0, $x-3, ($y+$a*$fh)-2, $shadow3,$fontpos,$tt);
			imagettftext($thumb, $fz, 0, $x-2, ($y+$a*$fh)-3, $shadow3,$fontpos,$tt);
			imagettftext($thumb, $fz, 0, $x+2, ($y+$a*$fh), $shadow2,$fontpos,$tt);
			imagettftext($thumb, $fz, 0, $x, ($y+$a*$fh)+2, $shadow2,$fontpos,$tt);
			imagettftext($thumb, $fz, 0, $x+1, ($y+$a*$fh), $shadow,$fontpos,$tt);
			imagettftext($thumb, $fz, 0, $x, ($y+$a*$fh)+1, $shadow,$fontpos,$tt);
			imagettftext($thumb, $fz, 0, $x-2, ($y+$a*$fh), $shadow,$fontpos,$tt);
			imagettftext($thumb, $fz, 0, $x, ($y+$a*$fh)-2, $shadow,$fontpos,$tt);
			imagettftext($thumb, $fz, 0, $x-1, ($y+$a*$fh), $white,$fontpos,$tt);
			imagettftext($thumb, $fz, 0, $x, ($y+$a*$fh)-1, $white,$fontpos,$tt);
			imagettftext($thumb, $fz, 0, $x, ($y+$a*$fh), $white,$fontpos,$tt);
			//圖檔,字大小,角度,x位置,y位置
			$a++;
		}

	}
	function addwaterfix($watermark_image,$thumb,$txt){
		global $fontpos;
		//抓取原圖高與寬
		$tw=imagesx($thumb);
		$th=imagesy($thumb);
		 // 載入浮水印圖
		if($tw>1000){
			$fz=14;
			$fh=21;
			$w_image = imagecreatefrompng($watermark_image."2.png") ;
		}else if($tw>500){
			$fz=10;
			$fh=16;
			$w_image = imagecreatefrompng($watermark_image."3.png") ;
		}else{
			$fz=10;
			$fh=16;
			$w_image = imagecreatefrompng($watermark_image."3.png") ;
		}
		// 取出浮水印圖 寬 與 高
		$w_width = imagesx($w_image);
		$w_height = imagesy($w_image);
		$x=0;
		$y=$th-$w_height;
		imagecopy($thumb,$w_image,$x,$y,0,0,$w_width,$w_height);
		$white=imagecolorallocate($thumb, 255, 255, 255);
		$shadow=imagecolorallocatealpha($thumb, 60, 60, 60, 40);
		$shadow2=imagecolorallocatealpha($thumb, 90, 90, 90,80);
		$shadow3=imagecolorallocatealpha($thumb, 90, 90, 90,100);
		$y=$y+$w_height/2+$fz/2;
		$x=$w_width;
		$t=explode("\n",$txt);
		$a=0;
		foreach($t as $tt){
			//imagettftext($thumb, 14, 0, $x+3, ($y+$a*20)+3, $shadow3,$fontpos,$tt);
		//	imagettftext($thumb, $fz, 0, $x+4, ($y+$a*$fh)+3, $shadow3,$fontpos,$tt);
		//	imagettftext($thumb, $fz, 0, $x+3, ($y+$a*$fh)+4, $shadow3,$fontpos,$tt);
		//	imagettftext($thumb, $fz, 0, $x-3, ($y+$a*$fh)-2, $shadow3,$fontpos,$tt);
		//	imagettftext($thumb, $fz, 0, $x-2, ($y+$a*$fh)-3, $shadow3,$fontpos,$tt);
		//	imagettftext($thumb, $fz, 0, $x+2, ($y+$a*$fh), $shadow2,$fontpos,$tt);
		//	imagettftext($thumb, $fz, 0, $x, ($y+$a*$fh)+2, $shadow2,$fontpos,$tt);
		//	imagettftext($thumb, $fz, 0, $x+1, ($y+$a*$fh), $shadow,$fontpos,$tt);
		//	imagettftext($thumb, $fz, 0, $x, ($y+$a*$fh)+1, $shadow,$fontpos,$tt);
		//	imagettftext($thumb, $fz, 0, $x-2, ($y+$a*$fh), $shadow,$fontpos,$tt);
		//	imagettftext($thumb, $fz, 0, $x, ($y+$a*$fh)-2, $shadow,$fontpos,$tt);
			imagettftext($thumb, $fz, 0, $x-1, ($y+$a*$fh), $white,$fontpos,$tt);
			imagettftext($thumb, $fz, 0, $x, ($y+$a*$fh)-1, $white,$fontpos,$tt);
			imagettftext($thumb, $fz, 0, $x, ($y+$a*$fh), $white,$fontpos,$tt);
			//圖檔,字大小,角度,x位置,y位置
			$a++;
		}
	}
?>
