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
class PVValidator extends PVStaticObject {
	
	private static $rules;
	
	function PVValidator(){
	
	}
	
	function init(){
		self::$rules=array();
		
		self::$rules['id']=array('type'=>'validator','function'=>'PVValidator::isID');
		self::$rules['integer']=array('type'=>'validator','function'=>'PVValidator::isInteger');
		self::$rules['double']=array('type'=>'validator','function'=>'PVValidator::isDouble');
		
		//Audio File Validation
		self::$rules['audio_file']=array('type'=>'validator','function'=>'PVValidator::isAudioFile');
		self::$rules['midi_file']=array('type'=>'validator','function'=>'PVValidator::isMidiFile');
		self::$rules['mp3_file']=array('type'=>'validator','function'=>'PVValidator::isMpegAudioFile');
		self::$rules['wav_file']=array('type'=>'validator','function'=>'PVValidator::isWavFile');
		self::$rules['aiff_file']=array('type'=>'validator','function'=>'PVValidator::isAiffFile');
		self::$rules['ra_file']=array('type'=>'validator','function'=>'PVValidator::isRealAudioFile');
		self::$rules['oga_file']=array('type'=>'validator','function'=>'PVValidator::isOGGAudioFile');
		
		//Image File Validation
		self::$rules['image_file']=array('type'=>'validator','function'=>'PVValidator::isImageFile');
		self::$rules['bmp_fie']=array('type'=>'validator','function'=>'PVValidator::isBmpFile');
		self::$rules['jpg_file']=array('type'=>'validator','function'=>'PVValidator::isJpegFile');
		self::$rules['png_file']=array('type'=>'validator','function'=>'PVValidator::isPngFile');
		self::$rules['gif_file']=array('type'=>'validator','function'=>'PVValidator::isGifFile');
		
		//Video File Validation
		self::$rules['video_file']=array('type'=>'validator','function'=>'PVValidator::isVideoFile');
		self::$rules['mpeg_file']=array('type'=>'validator','function'=>'PVValidator::isMpegVideoFile');
		self::$rules['quicktime_file']=array('type'=>'validator','function'=>'PVValidator::isQuickTimeFile');
		self::$rules['mov_file']=array('type'=>'validator','function'=>'PVValidator::isMovFile');
		self::$rules['avi_file']=array('type'=>'validator','function'=>'PVValidator::isAviFile');
		self::$rules['ogv_file']=array('type'=>'validator','function'=>'PVValidator::isOGGVideoFile');
		
		//Compressed File
		self::$rules['compressed_file']=array('type'=>'validator','function'=>'PVValidator::isCompressedFile');
		self::$rules['zip_file']=array('type'=>'validator','function'=>'PVValidator::isZipFile');
		self::$rules['tar_file']=array('type'=>'validator','function'=>'PVValidator::isTarFile');
		self::$rules['gtar_file']=array('type'=>'validator','function'=>'PVValidator::isGTarFile');
		
		//Other Validators
		self::$rules['url']=array('type'=>'validator','function'=>'PVValidator::isValidUrl');
		self::$rules['active_url']=array('type'=>'validator','function'=>'PVValidator::isActiveUrl');
		self::$rules['email']=array('type'=>'validator','function'=>'PVValidator::isValidEmail');
		
		//Other File Validiation
		self::$rules['css_file']=array('type'=>'validator','function'=>'PVValidator::isCssFile');
		self::$rules['html_file']=array('type'=>'validator','function'=>'PVValidator::isHtmlFile');
		self::$rules['htm_file']=array('type'=>'validator','function'=>'PVValidator::isHtmFile');
		self::$rules['asc_file']=array('type'=>'validator','function'=>'PVValidator::isAscFile');
		self::$rules['text_file']=array('type'=>'validator','function'=>'PVValidator::isTxtFile');
		self::$rules['rtext_file']=array('type'=>'validator','function'=>'PVValidator::isRtxFile');
		
		//Wor Files
		self::$rules['msdoc_file']=array('type'=>'validator','function'=>'PVValidator::isValidEmail');
		//self::$rules['email']=array('type'=>'validator','function'=>'PVValidator::isValidEmail');
		//self::$rules['email']=array('type'=>'validator','function'=>'PVValidator::isValidEmail');
		//self::$rules['email']=array('type'=>'validator','function'=>'PVValidator::isValidEmail');
		//self::$rules['email']=array('type'=>'validator','function'=>'PVValidator::isValidEmail');

		
		
		
		self::$rules['notempty']=array('type'=>'preg_match','rule'=>'/[^\s]+/m');
		
		/*self::$rules['integer'][]=function($value){
			return self::isInteger($value);
		};
		
		self::$rules['double']=function($value){
			return self::isDouble($value);
		};
		
		self::$rules['audio_file']=function($value){
			return self::isAudioFile($value);
		};*/

	}
	
	public static function addRule($rule, $option){
			self::$rules[$rule]=$option;	
	}
	
	public static function check($rule, $value){
		if(!isset(self::$rules[$rule])){
			return true;
		}	
		
		if(self::$rules[$rule]['type']=='validator'){
			return call_user_func(self::$rules[$rule]['function'], $value);
		} else if(self::$rules[$rule]['type']=='preg_match'){
			return preg_match(self::$rules[$rule]['rule'], $value);
		} else if(self::$rules[$rule]['type']=='function'){
			return self::$rules[$rule]['function'];
		}
		
	}//end check
	
	
	public static function isInteger($int){
	 
        if(is_numeric($int) === TRUE){
            if((int)$int == $int){
                return 1;
            }
        }
        return 0;
    }//end isInteger
    
    
    public static function isDouble($double){
	 
        if(is_numeric($double) === TRUE){
            if((double)$double == $double){
                return 1;
            }
        }
        return 0;
    }//end isInteger
    
    public static function isID($id){
    	if(self::isInteger($id) || preg_match ( '{[0-9a-f]{24}}' ,$id )){
    		return 1;
    	}
		
		return 0;
    }
    
    public static function isAudioFile($mimetype){
    	$audio_types = array ('audio/basic', 'audio/midi', 'audio/mpeg', 'audio/x-aiff', 'audio/x-mpegurl', 'audio/x-pn-realaudio', 'audio/x-realaudio', 'audio/x-wav');
		
		if (in_array (strtolower ($mimetype), $audio_types)){
			return 1;
		}
    	return 0;
    }//end isAudioFile
    
    public static function isMidiFile($mimetype){
    	if($mimetype=='audio/midi'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
    
    public static function isMpegAudioFile($mimetype){
    	if($mimetype=='audio/mpeg' || $mimetype=='audio/mp3'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
    
     public static function isAiffFile($mimetype){
    	if($mimetype=='audio/x-aiff'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
    
    public static function isWavFile($mimetype){
    	if($mimetype=='audio/x-wav'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
    
    public static function isRealAudioFile($mimetype){
    	if($mimetype=='audio/x-realaudio'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
    
    public static function isOGGAudioFile($mimetype){
    	if($mimetype=='audio/ogg' || $mimetype=='application/ogg' ){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
    
    
    /*Image Types Section
     * 
     * 
     */
    
    public static function isImageFile($mimetype){
		$mimetype=trim($mimetype);
		
    	$image_types = array ('image/bmp', 'image/gif', 'image/ief', 'image/jpeg', 'image/png', 'image/tiff', 'image/pjpeg', 'image/x-png');
		
		if (in_array (strtolower ($mimetype), $image_types)){
    		return 1;
    	}
		return 0;
    }//end isMidiFile
    
    public static function isBmpFile($mimetype){
    	if($mimetype=='image/bmp'){
    		return 1;
    	}
		return 0;
    }//end isMidiFile
    
     public static function isGifFile($mimetype){
    	if($mimetype=='image/gif'){
    		return 1;
    	}
		return 0;
    }//end isMidiFile
    
     public static function isIefFile($mimetype){
    	if($mimetype=='image/ief'){
    		return 1;
    	}
		return 0;
    }//end isMidiFile
    
    public static function isJpegFile($mimetype){
    	if($mimetype=='image/jpeg' || $mimetype=='image/pjpeg' ){
    		return 1;
    	}
		return 0;
    }//end isMidiFile
    
    public static function isPngFile($mimetype){
    	if($mimetype=='image/png' || $mimetype=='image/x-png' ){
    		return 1;
    	}
		return 0;
    }//end isMidiFile
    
    public static function isTiffFile($mimetype){
    	if($mimetype=='image/tiff'){
    		return 1;
    	}
		return 0;
    }//end isMidiFile
    
    /**
     * Video File Validation
     */
    
    public static function isVideoFile($mimetype){
    	$video_types = array ('video/mpeg', 'video/quicktime', 'video/vnd.mpegurl', 'video/x-msvideo', 'video/x-sgi-movie', 'video/mp4', 'video/ogg', 'video/webm', 'video/x-ms-wmv', 'application/x-troff-msvideo', 'video/avi', 'video/msvideo', 'video/mp4', 'application/mp4', 'application/vnd.rn-realmedia', 'video/x-ms-asf', 'video/ogg', 'application/ogg', 'video/webm', 'video/x-flv');
		
		if (in_array (strtolower ($mimetype), $video_types)){
    		return 1;
    	}
		return 0;
    }//end isMidiFile
    
     public static function isMpegVideoFile($mimetype){
    	if($mimetype=='video/mpeg'){
    		return 1;
    	}
		return 0;
    }//end isMidiFile
    
    public static function isWmvFile($mimetype){
    	if($mimetype=='video/x-ms-wmv'){
    		return 1;
    	}
		return 0;
    }//end isMidiFile
    
    public static function isMp4File($mimetype){
    	if($mimetype=='video/mp4' || $mimetype=='application/mp4'){
    		return 1;
    	}
		return 0;
    }//end isMidiFile
    
    public static function isFlvFile($mimetype){
    	if($mimetype=='video/x-flv'){
    		return 1;
    	}
		return 0;
    }//end isMidiFile
    
    public static function isQuickTimeFile($mimetype){
    	if($mimetype=='video/quicktime'){
    		return 1;
    	}
		return 0;
    }//end isMidiFile
    
    
    public static function isMovFile($mimetype){
    	if($mimetype=='video/quicktime'){
    		return 1;
    	}
		return 0;
    }//end isMidiFile
    
    public static function isMxuFile($mimetype){
    	if($mimetype=='video/vnd.mpegurl'){
    		return 1;
    	}
		return 0;
    }//end isMidiFile
    
    public static function isAviFile($mimetype){
    	if($mimetype=='video/x-msvideo' || $mimetype=='video/avi' || $mimetype=='video/msvideo' || $mimetype=='application/x-troff-msvideo'){
    		return 1;
    	}
		return 0;
    }//end isMidiFile
    
    public static function isOGGVideoFile($mimetype){
    	if($mimetype=='video/ogg' || $mimetype=='application/ogg' ){
    		return 1;
    	}
		return 0;
    }//end isMidiFile
    
    public static function isRealMediaFile($mimetype){
    	if($mimetype=='application/vnd.rn-realmedia'  ){
    		return 1;
    	}
		return 0;
    }//end isMidiFile
    
    public static function isAsfFile($mimetype){
    	if($mimetype=='video/x-ms-asf'){
    		return 1;
    	}
		return 0;
    }//end isMidiFile
    
     public static function isWebMFile($mimetype){
    	if($mimetype=='video/webm'){
    		return 1;
    	}
		return 0;
    }//end isMidiFile

    
    /**
     * Compressed Files
     */
    
    public static function isCompressedFile($mimetype){
    	$file_types = array ('application/zip', 'application/x-gtar', 'application/x-tar', 'application/x-zip');
		
		if (in_array (strtolower ($mimetype), $file_types)){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
    
     public static function isZipFile($mimetype){
    	if($mimetype=='application/zip'||  $mimetype=='application/x-zip'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
    
    public static function isGTarFile($mimetype){
    	if($mimetype=='application/x-gtar'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
    
    public static function isTarFile($mimetype){
    	if($mimetype=='application/x-tar'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
    
   /*******************************************************************
    *Text Files 
    ********************************************************************/
    public static function isCssFile($mimetype){
    	if($mimetype=='text/css'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
    
    public static function isHtmlFile($mimetype){
    	if($mimetype=='text/html'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
    
    public static function isHtmFile($mimetype){
    	if($mimetype=='text/html'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
    
    public static function isAscFile($mimetype){
    	if($mimetype=='text/plain'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
    
    public static function isTxtFile($mimetype){
    	if($mimetype=='text/plain'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
    
    public static function isRtxFile($mimetype){
    	if($mimetype=='text/richtext'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
	
	public static function isMicrosoftWordFile($mimetype){
		
		$file_types = array ('application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		
		if (in_array (strtolower ($mimetype), $file_types)){
    		return 1;
    	}
    	return 0;
	}//end isMicrosoftWordFile
	
	public static function isMicrosoftWordDocFile($mimetype){
    	if($mimetype=='application/msword'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
	
	public static function isMicrosoftWordDocxFile($mimetype){
    	if($mimetype=='application/vnd.openxmlformats-officedocument.wordprocessingml.document'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
	
	
	public static function isMicrosoftExcelFile($mimetype){
		
		$file_types = array ('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		
		if (in_array (strtolower ($mimetype), $file_types)){
    		return 1;
    	}
    	return 0;
	}//end isMicrosoftWordFile
	
	public static function isMicrosoftExcelXLSFile($mimetype){
    	if($mimetype=='application/vnd.ms-excel'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
	
	public static function isMicrosoftExcelXLSXFile($mimetype){
    	if($mimetype=='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
	
	public static function isMicrosoftPowerPointFile($mimetype){
		
		$file_types = array ('application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation');
		
		if (in_array (strtolower ($mimetype), $file_types)){
    		return 1;
    	}
    	return 0;
	}//end isMicrosoftWordFile
	
	public static function isMicrosoftPPTFile($mimetype){
    	if($mimetype=='application/vnd.ms-powerpoint'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
	
	public static function isMicrosoftPPTXFile($mimetype){
    	if($mimetype=='application/vnd.openxmlformats-officedocument.presentationml.presentation'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
    
    /*******************************************************************
    *Web Files 
    ********************************************************************/
    
    public static function isPdfFile($mimetype){
    	if($mimetype=='application/pdf'){
    		return 1;
    	}
    	return 0;
    }//end isMidiFile
	
	

	public static function isValidEmail($email){
		if(preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email)) {
		  	return 1;
		}
		
		return 0;
	}//end isValidEmail
	
	public static function isValidUrl($url) {
		return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
	}
	
	public static function isActiveUrl($url){
		
		$valid_url = @fsockopen($url, 80, $errno, $errstr, 30);
		 
		if($valid_url){
			return 1;  
		}
		return 0;
	}//end isActiveUrl
	
	public static function isApplicationInstalled($app_unique_id){
		
		if(!empty($app_unique_id)){
			$schema=pv_getSchema();
			$app_unique_id=PVDatabase::makeSafe($app_unique_id);
			$query="SELECT app_id FROM ".$schema."pv_app_manager WHERE app_unique_id='$app_unique_id' ";
			$result=PVDatabase::query($query);
			
			if(PVDatabase::resultRowCount($result)>0){
				return 1;	
			}
		}
		return 0;
	}//end isApplicationInstalled
	
	public static function isApplicationEnabled($app_unique_id){
		
		if(!empty($app_unique_id)){
			$schema=pv_getSchema();
			$app_unique_id=PVDatabase::makeSafe($app_unique_id);
			$query="SELECT app_id FROM ".$schema."pv_app_manager WHERE app_unique_id='$app_unique_id' AND enabled='1 '";
			$result=PVDatabase::query($query);
			
			if(PVDatabase::resultRowCount($result)>0){
				return 1;	
			}
		}
		return 0;
	}//end isApplicationInstalled
	
	public static function checkFileMimeType($file_location, $mime_text, $search_method='STRING_POSITION'){
		
		if(function_exists('finfo_open')){
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime_type = finfo_file($finfo, $file_location);	
		}
		else if(function_exists('mime_content_type')){
			$mime_type = mime_content_type( $file_location);	
		}
		else{
			$file_parts=explode('.',$file_location);
			$mime_type=$file_parts[count($file_parts)-1];
		}
		
		if($search_method=='STRING_POSITION'){
			$pos = strpos($mime_type, $mime_text);
			
			if ($pos === false) {
				return 0;
			} else {
				return 1;
			}
		}
		else if($search_method=='PREG_MATCH'){
			
			if (preg_match($mime_text ,$mime_type)  ){
				return 1;
			}
			else{
				return 0;	
			}	
		}
	}//end
    
}//end class


	/**
	*
	**/
	
	


?>