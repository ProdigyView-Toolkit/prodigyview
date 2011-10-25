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
	
	/**
	 * @todo figure out a point for this function. So do not use inthe eantime.
	 */
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
	
	/**
	 * Deletes an enitre directory on the server.
	 * 
	 * @param mixed $directory Can either be an array of directories or a single directory.
	 * 
	 * @return boolean $deleted
	 * @access public
	 */
	public static function deleteDirectory($directory) {
		
		if(is_array($directory)){
			foreach ($directory as $value) {
	    		self::deleteDirectory($value);
			}
		}
		 
	    if (!file_exists($directory)){ 
	    	return false; 
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
    
    /**
	 * Returns the file size based on an NFTS file system.
	 * 
	 * @param string $file The location of the file to get the size of
	 * 
	 * @return boolean $size Returns the size of the file
	 * @access public
	 */
	public static function getFileSize_NTFS($file) {
	    return exec("for %v in (\"".$file."\") do @echo %~zv");
	}
	
	/**
	 * Returns the file size using perl
	 * 
	 * @param string $file The location of the file to get the size of
	 * 
	 * @return boolean $size Returns the size of the file
	 * @access public
	 */
	public static function getFileSize_PERL($filename) {
    	return exec(" perl -e 'printf \"%d\n\",(stat(shift))[7];' ".$filename."");
	}
	
	/**
	 * Scans a directory and geths all the folders, files and subfolder and files in that
	 * directory. Has a verbose mode that can give detailed information about the directory.
	 * 
	 * @param string $directory The directory to be scanned
	 * @param array $options Options that can alter how the directory is scanned
	 * 			-'verbose' _boolean_: Enabling this mode will return everything in array of arrays. The array will contain
	 * 			more detailed information such as mime_type, extension, base name, etc. Default is false.
	 * 			-'magic_file' _string_: If finfo is installed and verbose is set to true, use this option to specifiy the magic
	 * 			file to use when getting the mime type of the file. Default is null
	 * 
	 * @return array $files An array of subdirectories and fules
	 * @access public
	 */
	public static function getFilesInDirectory($directory, $options=array()){
		$defaults=array('verbose'=>false);
		$options += $defaults;
		
		if(!is_dir($directory)){
			return NULL;
		}
		
		$file_array=array();
		$dir = opendir($directory);
		
		while(false != ($file = readdir($dir))){
			if(($file != ".") and ($file != "..")){
				
				if(is_dir($directory.$file.DS) && !$options['verbose'] ) {
					
					$file_array[$directory.$file.DS]=self::getFilesInDirectory($directory.$file.DS, $options);
					
				} else if(is_dir($directory.$file.DS) && $options['verbose']) {
					$file_array[$directory.$file.DS]=array('type'=>'folder', 'files'=>self::getFilesInDirectory($directory.$file.DS, $options)); 
				} else if($options['verbose']) {
					$info=array(
						'type'=>'file',
						'basename'=>pathinfo($file, PATHINFO_BASENAME),
						'extension'=>pathinfo($file, PATHINFO_EXTENSION),
						'mime_type'=>self::getFileMimeType($directory.$file, $options)
					);
					
					$file_array[$directory.$file]=$info;
				} else {
					$file_array[$directory.$file]=$file;
				}
			}
		}//end while
		
		return $file_array;
	}//end getFilesInDirectory
	
	/**
	 * Get the mime type of a file. Function is designed to degrade to other options if finfo_open or
	 * mime_content_type functions are not available.
	 * 
	 * @param string $file The name and location of the file
	 * @param array $options Options that can alter how the mime type is found
	 * 			-'magic_file' _string_: If finfo_open is installed, the magic file can be set for
	 * 			retreiving the mime type. Default is null
	 * 
	 * @return string $mime_type The mime type of the file.
	 * @access public
	 */
	public static function getFileMimeType($file, $options=array()){
		$defaults = array('magic_file'=>null);
		
		$options += $defaults;
		$mime_type='application/unknown';
		
		if(function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME, $options['magic_file']);
			$mime_type = finfo_file($finfo, $file);
			finfo_close($finfo);
		}else if(function_exists('mime_content_type')) {
			$mime_type = mime_content_type ( $file);
		} else {
			ob_start();
    		system("file -i -b {$file}");
    		$buffer = ob_get_clean();
    		$buffer = explode(';',$buffer);
    		if ( is_array($buffer) ) 
        		$mime_type= $buffer[0];
		}
		
		return $mime_type;
	}//end getFileMimeType
	
	/**
	 * Loads a file's contents on disk into memory for reading.
	 * 
	 * @param string $file_path The location of the file
	 * @param string $mode The mode to be used reading the file
	 * @param string $encoding The encoding to convert the file to. Optional.
	 * 
	 * @return string $contents The contents read from the file
	 * @access public
	 */
	public static function loadFile($filePath, $mode='r', $encoding=''){
	    
		$returnData= '';
	
		if (floatval(phpversion()) >= 4.3) {
	        $returnData= file_get_contents($filePath);
	    } else {
	        if (!file_exists($filePath)){ 
	        	return false;
	        }
	        
	        $handler = fopen($filePath, $mode);
	        if (!$handler){ 
	        	return false;
	        }
	
	        $returnData= '';
	        while(!feof($handler)){
	            $returnData.= fread($handler, filesize($filePath));
	        }//end  while
	        
	        fclose($handler);
	    }//end else
	    
	    if (!empty($encoding) && $current_encoding = mb_detect_encoding($returnData, 'auto', true) != $encoding){
	       $returnData= mb_convert_encoding($returnData, $encoding, $current_encoding);
	    }
	    
	    return $returnData;
	}//end load file
	
	/**
	 * Write contents to a file on the server.
	 * 
	 * @param string $file_path The path to the file that will be writteen out too
	 * @param string $content The content to be written to the file
	 * @param string $mode The mode to be used when writing the file. Default is 'w'.
	 * @param string $encoding An encoding to be used when writing the file. Optional.
	 * 
	 * @return boolean $written Returns true if the file was written, otherwise false
	 * @access public
	 */
	public static function writeFile($filePath, $content, $mode='w', $encoding=''){
		
		if (!empty($encoding) && $current_encoding = mb_detect_encoding($content, 'auto', true) != $encoding){
			$content= mb_convert_encoding($content, $encoding, $current_encoding);
	    }
	
		if (!$handle = fopen($filePath, $mode)) {
			return FALSE;
		}
	
		if (fwrite($handle, $content) === FALSE) {
			return FALSE;
		}
		
		fclose($handle);
		return TRUE;
	}//end writeFile
	
	
	/**
	 * Write contents to a file on the server only if the file does NOT already exist
	 * 
	 * @param string $file_path The path to the file that will be writteen out too
	 * @param string $content The content to be written to the file
	 * @param string $mode The mode to be used when writing the file. Default is 'w'.
	 * @param string $encoding An encoding to be used when writing the file. Optional.
	 * 
	 * @return boolean $written Returns true if the file was written, otherwise false
	 * @access public
	 * @todo Defaults for mode, content and encoding, Add a way for encoding file.
	 */
	public static function writeNewFile($filePath, $content, $mode='w', $encoding=''){
	
		if(file_exists($filePath))
			return false;
		
		return self::writeFile($filePath, $mode, $content, $encoding);
	}//end writeFile
	
	/**
	 * Write contents to a file on the server only if the file does exist
	 * 
	 * @param string $file_path The path to the file that will be writteen out too
	 * @param string $content The content to be written to the file
	 * @param string $mode The mode to be used when writing the file. Default is 'w'.
	 * @param string $encoding An encoding to be used when writing the file. Optional.
	 * 
	 * @return boolean $written Returns true if the file was written, otherwise false
	 * @access public
	 * @todo Defaults for mode, content and encoding, Add a way for encoding file.
	 */
	public static function rewriteNewFile($filePath, $content, $mode='w', $encoding=''){
	
		if(!file_exists($filePath))
			return false;
		
		return self::writeFile($filePath, $mode, $content, $encoding); 
	}//end writeFile
	
	/**
	 * Copy a file to another location
	 * 
	 * @param string $current_file The location of the current file to be copied
	 * @param string $new_file The location of the new file to be copied
	 * 
	 * @return boolean $copied Returns true if the file was succesfully copied
	 * @access public
	 */
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
		
		return false;
		
	}//end copyFile
	
	/**
	 * Copy a file to another location only if the file DOES NOT exist
	 * 
	 * @param string $current_file The location of the current file to be copied
	 * @param string $new_file The location of the new file to be copied
	 * 
	 * @return boolean $copied Returns true if the file was succesfully copied
	 * @access public
	 */
	public static function copyNewFile($currentFile, $newFile){
		
		if(file_exists($currentFile))
			return false;
		
		return self::copyFile($currentFile, $newFile);
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
	
	/**
	 * Copies a file from a url.
	 */
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
	
	/**
	 * Returns the file that was last modified with a directory.
	 * 
	 * @param string $directory The directory to search through when looking for the file
	 * 
	 * @return string $file The file that was modified in that directory
	 * @access public
	 */
	public static function getLastestFileInDirectory($dir){
		
		$lastMod = 0;
		$lastModFile = '';
		
		foreach (scandir($dir) as $entry) {
			if (is_file($dir.$entry) && filectime($dir.$entry) > $lastmod) {
				$lastMod = filectime($dir.$entry);
				$lastModFile = $entry;
			}
		}//end foreach
		
		return $lastModFile;
	}//end get_lastest_file_in_directory
	
	/**
	 * Delete's a file if the file exist.
	 * 
	 * @param string $file The location of the file to be deleted
	 * 
	 * @return boolean $deleted Returns true if the file was successfully deleted. Otherwise false.
	 * @access public
	 */
	public static function deleteFile($file){
		if(file_exists($file) && !is_dir($file)){
			return unlink($file);
		}
		
		return false;
	}
	
	/**
	 * 
	 */
	public static function copyNewEntity($source, $target, $chmod=0777, $recursive=false){
		copy ( $currentDirectory  , $newDirectory  );
	}
	
	public static function setDirectorySeperator($path){
		$path=str_replace("/", DS, $path);

		return $path;
	}
}//end class
?>