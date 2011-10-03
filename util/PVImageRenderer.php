<?php
/*
*Copyright 2011 ProdigyView LLC. All rights reserved.
*
*Redistribution and use in source and binary forms, with or without modification, are
*permitted provided that the following conditions are met:
*
*   1. Redistributions of source code must retain the above copyright notice, this list of
*      conditions and the following disclaimer.
*
*   2. Redistributions in binary form must reproduce the above copyright notice, this list
*      of conditions and the following disclaimer in the documentation and/or other materials
*      provided with the distribution.
*
*THIS SOFTWARE IS PROVIDED BY ProdigyView LLC ``AS IS'' AND ANY EXPRESS OR IMPLIED
*WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
*FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL ProdigyView LLC OR
*CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
*CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
*SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
*ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
*NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
*ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*
*The views and conclusions contained in the software and documentation are those of the
*authors and should not be interpreted as representing official policies, either expressed
*or implied, of ProdigyView LLC.
*/
class PVImageRenderer extends PVStaticObject {

	static $INVALID_TYPE=-2;
	static $UPLOAD_FAILED=-3;
	var $version=.9;
	var $uniqueName="pv_image_renderer";

	function scaleImageByWidth($image_file, $new_width){
	
	
	}
	
	public static function uploadImageFromContent($content_id, $content_type, $file_name, $tmp_name, $file_size, $file_type, $image_width=300 , $image_height=300 , $thumbnailwidth=150, $thumbnailheight=150, $image_src=''){
		
		$image_folder_url=PV_IMAGE;
		$save_name="";

		
	
		$image_types = array ("image/bmp", "image/jpeg", "image/pjpeg", "image/gif", "image/x-png", "image/png", "image/pjpeg");
		
		if (pv_isImageFile($file_type)){
		 	
		 	$image_exist=true;
		 	
		 	while($image_exist){
		 		$randomFileName=pv_generateRandomString(20 , 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890');
		 		
		 		if(!file_exists(PV_ROOT."$image_folder_url$randomFileName.jpg") || !file_exists(PV_ROOT."$image_folder_url$randomFileName.png") || !file_exists(PV_ROOT."$image_folder_url$randomFileName.gif")){
		 			$image_exist=false;
		 		}
		 	}//end while
		 	
		 	if($file_type=='image/bmp'){
		 		$save_name="$image_folder_url$randomFileName.bmp";
				$save_location=$randomFileName.'.bmp';
		 	}
		 	else if(pv_isJpegFile($file_type)){
		 		$save_name="$image_folder_url$randomFileName.jpg";
				$save_location=$randomFileName.'.jpg';
		 	}
		 	else if($file_type=='image/gif'){
		 		$save_name="$image_folder_url$randomFileName.gif";
				$save_location=$randomFileName.'.gif';
			 }
			 else if(pv_isPngFile($file_type)){
			 	$save_name="$image_folder_url$randomFileName.png";
			 	$save_location=$randomFileName.'.png';
			 }			
		 	
			$file_name=PVDatabase::makeSafe($file_name); 
			$file_type=PVDatabase::makeSafe($file_type);
			$file_size=PVDatabase::makeSafe($file_size);
			$save_name=PVDatabase::makeSafe($save_name);
		
		
	
			if(empty($app_id)){
				$app_id=0;
			}
			
			 if(empty($file_size)){
				$file_size=0;
			}
			
			if(move_uploaded_file($tmp_name, PV_ROOT.$save_name) || PVFileManager::copyNewFile($tmp_name, PV_ROOT.$save_name)) {
				$thumb_url="";
				
				if($file_type=='image/bmp'){
					$thumb_url="$image_folder_url$randomFileName-tn.bmp";
					$thumb_name=$randomFileName.'-tn.bmp';
				}
				else if(pv_isJpegFile($file_type)){
					$thumb_url="$image_folder_url$randomFileName-tn.jpg";
					$thumb_name=$randomFileName.'-tn.jpg';
				}
				else if($file_type=='image/gif'){
					$thumb_url="$image_folder_url$randomFileName-tn.gif";
					$thumb_name=$randomFileName.'-tn.gif';
				}
				else if(pv_isPngFile($file_type)){
					$thumb_url="$image_folder_url$randomFileName-tn.png";
					$thumb_name=$randomFileName.'-tn.png';
				}			
					
				self::resizeImageGD(PV_ROOT.$save_name,PV_ROOT.DS.$thumb_url,$thumbnailwidth,$thumbnailheight);
					
				list($width, $height, $type, $attr)=getimagesize(PV_ROOT.$save_name); 
				
				if(empty($image_width)){
						$image_width=$width;
				}
				
				if(empty($image_height)){
						$image_height=$height;
				}
				
				if(empty($thumbnailwidth)){
						$thumbnailwidth=150;
				}
				
				if(empty($thumbnailheight)){
						$thumbnailheight=150;
				}
			
				$query="INSERT INTO ".pv_getImageContentTableName()."(image_id, image_type, image_size, image_url, thumb_url, image_width, image_height, thumb_width, thumb_height , image_src ) VALUES('$content_id', '$file_type', '$file_size', '$save_location', '$thumb_name', '$image_width', '$image_height', '$thumbnailwidth', '$thumbnailheight', '$image_src' )";
				PVDatabase::query($query);
				
				return 1;
			} else{
				return self::$UPLOAD_FAILED;
			}
		 	
		 
		 	
		 }
		 else{
		 	return self::$INVALID_TYPE;
		 }
	}// end upload Image
	
	public static function uploadImage($file_name, $tmp_name, $file_size, $file_type, $image_width=300 , $image_height=300 , $thumbnailwidth=150, $thumbnailheight=150){
		
		$image_folder_url=PV_IMAGE;
		$save_name="";

		
	
		$image_types = array ("image/bmp", "image/jpeg", "image/pjpeg", "image/gif", "image/x-png", "image/png", "image/pjpeg");
		
		if (pv_isImageFile($file_type)){
		 	
		 	$image_exist=true;
		 	
		 	while($image_exist){
		 		$randomFileName=pv_generateRandomString(20 , 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890');
		 		
		 		if(!file_exists(PV_ROOT."$image_folder_url$randomFileName.jpg") || !file_exists(PV_ROOT."$image_folder_url$randomFileName.png") || !file_exists(PV_ROOT."$image_folder_url$randomFileName.gif")){
		 			$image_exist=false;
		 		}
		 	}//end while
		 	
		 	if($file_type=='image/bmp'){
		 		$save_name="$image_folder_url$randomFileName.bmp";
		 	}
		 	else if(pv_isJpegFile($file_type)){
		 		$save_name="$image_folder_url$randomFileName.jpg";
		 	}
		 	else if($file_type=='image/gif'){
		 		$save_name="$image_folder_url$randomFileName.gif";
			 }
			 else if(pv_isPngFile($file_type)){
			 	$save_name="$image_folder_url$randomFileName.png";
			 }			
		 	
			$file_name=PVDatabase::makeSafe($file_name); 
			$file_type=PVDatabase::makeSafe($file_type);
			$file_size=PVDatabase::makeSafe($file_size);
			$save_name=PVDatabase::makeSafe($save_name);
		
		
	
			if(empty($app_id)){
				$app_id=0;
			}
			
			 if(empty($file_size)){
				$file_size=0;
			}
		
			if(move_uploaded_file($tmp_name, PV_ROOT.$save_name)) {
				
				$thumb_url="";
				
				if($file_type=='image/bmp'){
					$thumb_url="$image_folder_url$randomFileName-tn.bmp";
				}
				else if(pv_isJpegFile($file_type)){
					$thumb_url="$image_folder_url$randomFileName-tn.jpg";
				}
				else if($file_type=='image/gif'){
					$thumb_url="$image_folder_url$randomFileName-tn.gif";
				}
				else if(pv_isPngFile($file_type)){
					$thumb_url="$image_folder_url$randomFileName-tn.png";
				}			
					
				self::resizeImageGD(PV_ROOT.$save_name,PV_ROOT.DS.$thumb_url,$thumbnailwidth,$thumbnailheight);
					
				
			$images_array=array(
				'image_url'=> $save_name,
				'thumb_url' => $thumb_url
				);
				
				return $images_array;
			} else{
				return self::$UPLOAD_FAILED;
			}
		 	
		 
		 	
		 }
		 else{
		 	return self::$INVALID_TYPE;
		 }
	}// end upload Image
	
	public static function updateImageFromContent($content_id,  $content_type, $file_name, $tmp_name, $file_size, $file_type, $image_width=300 , $image_height=300 , $thumbnailwidth=150, $thumbnailheight=150, $image_src=''){
		
		$image_folder_url=PV_IMAGE;
		$save_name="";
		
		$query="SELECT image_url FROM ".pv_getImageContentTableName()." WHERE image_id='$content_id'";
		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		$current_file_name=$row['image_url'];
		
		
		$basename=basename($current_file_name);
		$randomFileName= substr($basename, 0, strrpos($basename, '.')); 
	
		$image_types = array ("image/bmp", "image/jpeg", "image/pjpeg", "image/gif", "image/x-png", "image/pjpeg");
		 
		if (pv_isImageFile($file_type)){
		
			if(empty($randomFileName)){
				
				 if (in_array (strtolower ($file_type), $image_types)){
				 	$randomFileName=pv_generateRandomString(20 , 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890');
				 	$image_exist=true;
				 	
				 	while($image_exist){
				 		if(!file_exists(PV_ROOT."$image_folder_url$randomFileName.jpg") || !file_exists(PV_ROOT."$image_folder_url$randomFileName.png") || !file_exists(PV_ROOT."$image_folder_url$randomFileName.gif")){
				 			$image_exist=false;
				 		}//end
				 	}//end while
				 }
			 	
			 }
			 
			 if($file_type=='image/bmp'){
			 	$save_name="$image_folder_url$randomFileName.bmp";
				$save_location=$randomFileName.'.bmp';
			 }
			 else if(pv_isJpegFile($file_type)){
			 	$save_name="$image_folder_url$randomFileName.jpg";
				$save_location=$randomFileName.'.jpg';
			 }
			 else if($file_type=='image/gif'){
			 	$save_name="$image_folder_url$randomFileName.gif";
			 	$save_location=$randomFileName.'.gif';
			 }
			 else if(pv_isPngFile($file_type)){
			 	$save_name="$image_folder_url$randomFileName.png";
				$save_location=$randomFileName.'.png';
			 }			
			 	
			$file_name=PVDatabase::makeSafe($file_name); 
			$file_type=PVDatabase::makeSafe($file_type);
			$file_size=PVDatabase::makeSafe($file_size);
			$save_name=PVDatabase::makeSafe($save_name);
			
			
	
			if(empty($app_id)){
				$app_id=0;
			}
			
			 if(empty($file_size)){
				$file_size=0;
			}
			
			
			if(move_uploaded_file($tmp_name, PV_ROOT.$save_name) || PVFileManager::copyFile($tmp_name, PV_ROOT.$save_name) ) {
				
				
				$thumb_url="";
				
				if($file_type=='image/bmp'){
					$thumb_url="$image_folder_url$randomFileName-tn.bmp";
					$thumb_name=$randomFileName.'-tn.bmp';
				}
				else if(pv_isJpegFile($file_type)){
					$thumb_url="$image_folder_url$randomFileName-tn.jpg";
					$thumb_name=$randomFileName.'-tn.jpg';
				}
				else if($file_type=='image/gif'){
					$thumb_url="$image_folder_url$randomFileName-tn.gif";
					$thumb_name=$randomFileName.'-tn.gif';
				}
				else if(pv_isPngFile($file_type)){
					$thumb_url="$image_folder_url$randomFileName-tn.png";
					$thumb_name=$randomFileName.'-tn.png';
				}						
					
				self::resizeImageGD(PV_ROOT.$save_name,PV_ROOT.DS.$thumb_url,$thumbnailwidth,$thumbnailheight);
					
				if(empty($image_width)){
						$image_width=300;
				}
				
				if(empty($image_height)){
						$image_height=300;
				}
				
				if(empty($thumbnailwidth)){
						$thumbnailwidth=150;
				}
				
				if(empty($thumbnailheight)){
						$thumbnailheight=150;
				}
			
				$query="UPDATE ".pv_getImageContentTableName()." SET image_type='$file_type', image_size='$file_size', image_url='$save_location', thumb_url='$thumb_name', image_width='$image_width', image_height='$image_height', thumb_width='$thumbnailwidth', thumb_height='$thumbnailheight', image_src='$image_src' WHERE  image_id='$content_id' ";
				PVDatabase::query($query);
				
				return 1;
			} else{
				echo "Not able to move file";
				return $this->UPLOAD_FAILED;
			}
			 	
			 
		 	
		 
		}else{
		 	return $this->INVALID_TYPE;
		 }
	}// end upload Image
	
	public static function updateImage($current_file_name, $file_name, $tmp_name, $file_size, $file_type, $image_width=300 , $image_height=300 , $thumbnailwidth=150, $thumbnailheight=150){
		
		$image_folder_url="/media/images/";
		$save_name="";
		
		
		$basename=basename($current_file_name);
		$randomFileName= substr($basename, 0, strrpos($basename, '.')); 
	
		$image_types = array ("image/bmp", "image/jpeg", "image/pjpeg", "image/gif", "image/x-png", "image/pjpeg");
		 
		if (pv_isImageFile($file_type)){
		
			if(empty($randomFileName)){
				
				 if (in_array (strtolower ($file_type), $image_types)){
				 	$randomFileName=pv_generateRandomString(20 , 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890');
				 	$image_exist=true;
				 	
				 	while($image_exist){
				 		if(!file_exists(PV_ROOT."$image_folder_url$randomFileName.jpg") || !file_exists(PV_ROOT."$image_folder_url$randomFileName.png") || !file_exists(PV_ROOT."$image_folder_url$randomFileName.gif")){
				 			$image_exist=false;
				 		}//end
				 	}//end while
				 }
			 	
			 }
			 
			 if($file_type=='image/bmp'){
			 	$save_name="$image_folder_url$randomFileName.bmp";
			 }
			 else if(pv_isJpegFile($file_type)){
			 	$save_name="$image_folder_url$randomFileName.jpg";
			 }
			 else if($file_type=='image/gif'){
			 	$save_name="$image_folder_url$randomFileName.gif";
			 }
			 else if(pv_isPngFile($file_type)){
			 	$save_name="$image_folder_url$randomFileName.png";
			 }			
			 	
			$file_name=PVDatabase::makeSafe($file_name); 
			$file_type=PVDatabase::makeSafe($file_type);
			$file_size=PVDatabase::makeSafe($file_size);
			$save_name=PVDatabase::makeSafe($save_name);
			
			
	
			if(empty($app_id)){
				$app_id=0;
			}
			
			 if(empty($file_size)){
				$file_size=0;
			}
			
			
			if(move_uploaded_file($tmp_name, PV_ROOT.$save_name)) {
				
				$thumb_url="";
				
				if($file_type=='image/bmp'){
					$thumb_url="$image_folder_url$randomFileName-tn.bmp";
					$thumb_name=$randomFileName.'-tn.bmp';
				}
				else if(pv_isJpegFile($file_type)){
					$thumb_url="$image_folder_url$randomFileName-tn.jpg";
					$thumb_name=$randomFileName.'-tn.jpg';
				}
				else if($file_type=='image/gif'){
					$thumb_url="$image_folder_url$randomFileName-tn.gif";
					$thumb_name=$randomFileName.'-tn.gif';
				}
				else if(pv_isPngFile($file_type)){
					$thumb_url="$image_folder_url$randomFileName-tn.png";
					$thumb_name=$randomFileName.'-tn.png';
				}			
					
				self::resizeImageGD(PV_ROOT.$save_name,PV_ROOT.DS.$thumb_url,$thumbnailwidth,$thumbnailheight);
					
				
				return 1;
			} else{
				echo "Not able to move file";
				return $this->UPLOAD_FAILED;
			}
			 	
			 
		 	
		 
		}else{
		 	return $this->INVALID_TYPE;
		 }
	}// end upload Image
	
	
	
	public static function resizeImageGD($name,$filename,$new_w=150,$new_h=150){
		
		if($new_w==0){
			$new_w=150;
		}
		if($new_h==0){
			$new_h=150;
		}		
	
		$system=explode('.',$name);
		
		if ( pv_checkFileMimeType($name , '/jpg|jpeg/', $search_method='PREG_MATCH') ){
			$src_img=imagecreatefromjpeg($name);
		}
		else if ( pv_checkFileMimeType($name , '/png/', $search_method='PREG_MATCH')  ){
			$src_img=imagecreatefrompng($name);
		}
		else{
			$src_img=imagecreatefromjpeg($name);
		}
		
		$old_x=imageSX($src_img);
		$old_y=imageSY($src_img);
		
		if ($old_x > $old_y) {
			$thumb_w=$new_w;
			$thumb_h=$old_y*($new_h/$old_x);
		}
		if ($old_x < $old_y) {
			$thumb_w=$old_x*($new_w/$old_y);
			$thumb_h=$new_h;
		}
		if ($old_x == $old_y) {
			$thumb_w=$new_w;
			$thumb_h=$new_h;
		}
		
		$dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
		imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
		
		if ( pv_checkFileMimeType($name , '/png/', $search_method='PREG_MATCH') ){
			imagepng($dst_img,$filename); 
		} else {
			imagejpeg($dst_img,$filename); 
		}
		
		imagedestroy($dst_img); 
		imagedestroy($src_img); 
	}

	public static function cropImage($src, $ouput, $width, $height) {
		
		$file_type=PVFileManager::getMimeType($src);
		
		if(PVValidator::isJpegFile($file_type)) {
			$original_image_gd = imagecreatefromjpeg($file_name);
		} else if (PVValidator::isGifFile($file_type)) {
			$original_image_gd = imagecreatefromgif($file_name);
		} else if(PVValidator::isPngFile($file_type)) {
			$original_image_gd = imagecreatefrompng($file_name);
		}
		
	}

	
	function getVersion(){
		return $this->version;
	}
	
	function getUniqueName(){
		return $this->uniqueName;
	}//end get
	
}//end class


?>