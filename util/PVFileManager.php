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

class PVFileManager extends PVStaticObject {

	static $version=0.9;
	static $uniqueName="pv_file_manager";
	
	//ERROR STATUS
	
	static $FILE_DOES_NOT_EXIST=-2;
	static $FILE_EXIST=-3;
	static $DIRECTORY_DOES_NOT_EXIST=-4;
	static $DIRECTORY_EXIST=-5;
	static $IS_DIRECTORY=-6;
	static $IS__NOT_DIRECTORY=-7;
	static $IS_FILE=-8;
	static $IS_NOT_FILE=-9;
	static $MKDIR_DENIED=-10;
	
	
	function PVFileManager(){
		
	}//end class
	
	function commandInterpreter($command, $params){
		
		if($command="phpUploadFile"){
			return $this->phpFileUpload($params);
		}
		else if($command="deleteDirectory"){
			return $this->deleteDirectory($params);
		}
	
	
	}//end commandInterpreter
	
	
	public static function phpFileUpload($params){

		$allow_upload=1;
		
		$file_name=$params['file_name'];
		$file_size=$params['file_size'];
		$file_type=$params['file_type'];
		$file_location=$params['file_location'];
	 
		$upload_destination=$params['upload_destination'];
		$max_size=$params['max_size'];
		
		$allowed_extensions=$params['allowed_extensions'];
	 
		if(count($allowed_extensions)>0){
			  if (!in_array(substr(strrchr($file_name,'.'),1),  $allowed_extensions)) { 
			  	$allow_upload=0;
			  	echo "Invalid File Type";
			  }
		 }
	 
	 //$upload_location = $upload_location.basename( $_FILES['uploaded']['name']) ; 
	 
		 if(empty($upload_destination)){
		 	$allow_upload=0;
			echo "Upload destination required";
		 }
	 
	 
		 if($allow_upload==1){
			 
		 	if(move_uploaded_file($file_location, $upload_destination)) {
			 	return basename($file_name);
			 } 
			 else {
			 	return NULL;
			 }
		 }
	 
	 	return NULL;
	}//end upload file
	
	public static function deleteDirectory($directory) {
		
		if(is_array($directory)){
			foreach ($directory as &$value) {
	    		self::deleteDirectory($value);
			}
		}
		 
	    if (!file_exists($directory)){ 
	    	return true; 
	    }
    	
   		 if (!is_dir($directory) || is_link($directory)) {
   		 	return unlink($directory); 
   		 }
   		 
	        foreach (scandir($directory) as $item) { 
	            if ($item == '.' || $item == '..') continue; 
		            if (!self::deleteDirectory($directory . "/" . $item)) { 
		                chmod($directory . "/" . $item, 0777); 
		                if (!self::deleteDirectory($directory . "/" . $item)) {
		                	return false; 
		                }
	            };//end for 
        	}//end 
        return rmdir($directory); 
    }//end deleteDirectory 
    
    
	public static function getFileSize_NTFS($file_name) {
	    return exec("for %v in (\"".$file_name."\") do @echo %~zv");
	}
	
	
	public static function getFileSize_PERL($filename) {
    	return exec(" perl -e 'printf \"%d\n\",(stat(shift))[7];' ".$filename."");
	}
	
	
	public static function getFilesInDirectory($directory){
		
		if(!is_dir($directory)){
			return NULL;
		}
		
		$file_array=array();
		$dir = opendir($directory);
		
			
		while(false != ($file = readdir($dir))){
			if(($file != ".") and ($file != "..")){
				$file_array["$file"]=$file;
			}
		}//end while
		
		return $file_array;
	}//end getFilesInDirectory
	
	public static function loadFile($filePath, $charSet, $mode){
	    
		$returnData= "";
	
		if (floatval(phpversion()) >= 4.3) {
	        $returnData= file_get_contents($filePath);
	    } else {
	        if (!file_exists($filePath)){ 
	        	return -3;
	        }
	        
	        $handler = fopen($filePath, $mode);
	        if (!$handler){ 
	        	return -2;
	        }
	
	        $returnData= "";
	        while(!feof($handler)){
	            $returnData.= fread($handler, filesize($filePath));
	        }//end  while
	        
	        fclose($handler);
	    }//end else
	    
	   // if ($sEncoding = mb_detect_encoding($sData, 'auto', true) != $sCharset){
	       // $returnData= mb_convert_encoding($sData, $sCharset, $sEncoding);
	    //}
	    
	        return $returnData;
	}//end load file
	
	public static function writeFile($filePath, $mode, $content, $encoding){
	
			 if (!$handle = fopen($filePath, $mode)) {
		         return -2;
		    }
		
		    // Write $somecontent to our opened file.
		    if (fwrite($handle, $content) === FALSE) {
		      	return -3;
		    }
		
		    fclose($handle);
		    
		    return 1;
		
		
	
	
	}//end writeFile
	
	
	public static function writeNewFile($filePath, $mode, $content, $encoding){
	
		if(!file_exists($filePath)) {
		
		    if (!$handle = fopen($filePath, $mode)) {
		         return -2;
		    }
		
		    // Write $somecontent to our opened file.
		    if (fwrite($handle, $content) === FALSE) {
		      	return -3;
		    }
		
		    fclose($handle);
		    
		    return 1;
		
		} else {
		    return -4;
		}
	
	
	}//end writeFile
	
	public static function rewriteNewFile($filePath, $mode, $content, $encoding){
	
		if(file_exists($filePath)) {
		
		    if (!$handle = fopen($filePath, $mode)) {
		         return -2;
		    }
		
		    // Write $somecontent to our opened file.
		    if (fwrite($handle, $content) === FALSE) {
		      	return -3;
		    }
		
		    fclose($handle);
		    
		    return 1;
		
		} else {
		    return -4;
		}
	
	
	}//end writeFile
	
	public static function copyFile($currentFile, $newFile){
		if(is_dir($currentFile)){
			return false;
		}
		
		if(!file_exists($currentFile)){
			return false;
		}
		
		if( copy ( $currentFile  , $newFile )){
			return true;
		}
		else{
			return false;
		}
	}//end copyFile
	
	public static function copyNewFile($currentFile, $newFile){
		if(is_dir($currentFile)){
			return false;
		}
		
		if(!file_exists($currentFile)){
			return false;
		}
		
		if(file_exists($newFile)){
			return false;
		}
		
		if( copy ( $currentFile  , $newFile )){
			return true;
		}
		else{
			return false;
		}
	}//end copyFile
	
	public static function copyDirectory($currentDirectory, $newDirectory){
		copy ( $currentDirectory  , $newDirectory  );
	}
	
	public static function copyNewDirectory($currentDirectory, $newDirectory){
		copy ( $currentDirectory  , $newDirectory  );
	}
	
	public static function copyEntity($source, $target, $chmod=0777, $recursive=false){
		
		if ( is_dir( $source ) ) {
			
			if(!file_exists($target)){
				if(!mkdir( $target, $chmod, $recursive )){
					return self::MKDIR_DENIED;
				}//end if !mkdir
			}
			
			$directory = dir( $source );
			
			while ( FALSE !== ( $entry = $directory->read() ) ) {
				
				if ( $entry == '.' || $entry == '..' ) {
					continue;
				}//end if ..
				
				$subfolder = $source.DS.$entry; 
				
				if ( is_dir( $subfolder ) ) {
					self::copyEntity( $subfolder, $target.DS.$entry, $chmod,$recursive  );
					continue;
				}//end if subfolder is dir
				
				copy( $subfolder, $target .DS. $entry );
			
			}//end while
	 
			$directory->close();
			
		}else {
			$target_dir=dirname($target);
			
			if(!file_exists($target_dir)){
				mkdir($target_dir);
			}
			copy( $source, $target );
		}//end else
	}//end copyEnity
	
	public static function copyFileFromUrl($url, $destination, $filename=''){
		@$file = fopen ($url, "rb");
		
		if (!$file || empty($destination)) {
			return 0;
		}
		else {
			
			if(empty($filename)){
				$filename = basename($url);
			}//end if not empty
			
			$fc = fopen($destination."$filename", "wb");
			
			while (!feof ($file)) {
			   $line=fread ($file, 1028);
			   fwrite($fc,$line);
			}//end while
			
			fclose($fc);
			
			return 1;
		} //end else		
	}//end copyfile From URL
	
	public static function uploadFileFromContent($content_id,  $file_name, $tmp_name, $file_size, $file_type){
		
		$file_folder_url=PV_FILE;
		$save_name="";
		
		$query="SELECT * FROM ".pv_getFileContentTableName()." WHERE file_id='$content_id'";
		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		$current_file_name=$row['file_location'];
		
		$file_extension= pathinfo($file_name, PATHINFO_EXTENSION);

		$file_exist=true;
		 	
		while($file_exist){
		 	$randomFileName=pv_generateRandomString(20 , 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890').'.'.$file_extension;
		 		
		 	if(!file_exists(PV_ROOT.$file_folder_url.$randomFileName) ){
		 		$file_exist=false;
		 	}
		 }//end while
		 
		$file_name=PVDatabase::makeSafe($file_name); 
		$file_type=PVDatabase::makeSafe($file_type);
		$file_size=PVDatabase::makeSafe($file_size);
		$save_name=PVDatabase::makeSafe($file_folder_url.$randomFileName);
		
		if(empty($app_id)){
			$app_id=0;
		}
			
		if(empty($file_size)){
			$file_size=0;
		}
		
		if(move_uploaded_file($tmp_name, PV_ROOT.$save_name) || self::copyFile($tmp_name, PV_ROOT.$save_name)) {
			
			if(file_exists(PV_ROOT.$current_file_name) && !empty($current_file_name)){
				self::deleteFile(PV_ROOT.$file_folder_url.$current_file_name);
			}

			$query="UPDATE ".pv_getFileContentTableName()." SET file_type='$file_type', file_size='$file_size', file_name='$file_name', file_location='$randomFileName' WHERE file_id='$content_id' ";
			PVDatabase::query($query);
				
			return 1;
			
		} else{
			return FALSE;
		}
	
	}// end upload Image
	
	public static function getLastestFileInDirectory($dir){
		
		$lastMod = 0;
		$lastModFile = '';
		
		foreach (scandir($dir) as $entry) {
			if (is_file($dir.$entry) && filectime($dir.$entry) > $lastmod) {
				$lastMod = filectime($dir.$entry);
				$lastModFile = $entry;
				
			}
		}
		
		return $lastModFile;
		
	}//end get_lastest_file_in_directory
	
	public static function deleteFile($file){
		if(file_exists($file) && !is_dir($file)){
			return unlink($file);
		}
		
		return false;
	}
	
	public static function copyNewEntity($source, $target, $chmod=0777, $recursive=false){
		copy ( $currentDirectory  , $newDirectory  );
	}
	
	public static function setDirectorySeperator($path){
		$path=str_replace("/", DS, $path);

		return $path;
	}
	
	public static function getVersion(){
		return self::$version;
	}
	
	public static function getUniqueName(){
		return self::$uniqueName;
	}//end get
	

}//end class
?>