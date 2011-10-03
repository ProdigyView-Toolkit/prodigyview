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
class PVVideoRenderer extends PVStaticObject {

	function PVVideoRenderer(){
	
	
	}//end constructor
	
	public static function updateVideoContent($args=array()){
		$defaults=array(
			'convert_to_flv'=>false,
			'convert_to_mpeg'=>false,
			'convert_to_mp4'=>false,
			'convert_to_wmv'=>false,
			'convert_to_realmedia'=>false,
			'convert_to_avi'=>false,
			'convert_to_mov'=>false,
			'convert_to_asf'=>false,
			'convert_to_ogv'=>false,
			'convert_to_webm'=>false
		);
		
		$args+=$defaults;
		$args=PVDatabase::makeSafe*($args);
		extract($args);
		
		$video_folder_location=PV_VIDEO;
		$save_name="";
		
		$query="SELECT * FROM ".pv_getVideoContentTableName()." WHERE video_id='$content_id'";
		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		
		$current_file_name='';
		
		if(PVValidator::isFlvFile($file_type)){
			$current_file_name=$row['flv_file'];
		}
		
		if(PVValidator::isMp4File($file_type)){
			$current_file_name=$row['mp4_file'];
		}
		
		if(PVValidator::isWmvFile($file_type)){
			$current_file_name=$row['wmv_file'];
		}
		
		if(PVValidator::isAviFile($file_type)){
			$current_file_name=$row['avi_file'];
		}
		
		if(PVValidator::isMpegVideoFile($file_type)){
			$current_file_name=$row['mpeg_file'];
		}
		
		if(PVValidator::isRealMediaFile($file_type)){
			$current_file_name=$row['rm_file'];
		}
		
		if(PVValidator::isMovFile($file_type)){
			$current_file_name=$row['mov_file'];
		}
		
		if(PVValidator::isAsfFile($file_type)){
			$current_file_name=$row['asf_file'];
		}

		if(PVValidator::isOGGVideoFile($file_type)){
			$current_file_name=$row['ogv_file'];
		}
		
		if(PVValidator::isWebMFile($file_type)){
			$current_file_name=$row['webm_file'];
		}
		
		$basename=basename($current_file_name);
		$randomFileName= substr($basename, 0, strrpos($basename, '.')); 
		 
		if (PVValidator::isVideoFile($file_type)){
		
			if(empty($randomFileName)){
				
				 if (PVValidator::isVideoFile($file_type)){
				 	$image_exist=true;
				 	
				 	while($image_exist){
				 		$randomFileName=pv_generateRandomString(20 , 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890');
						
				 		if(!file_exists(PV_ROOT.$video_folder_location.$randomFileName.'.flv') || !file_exists(PV_ROOT.$video_folder_location.$randomFileName.'.mpeg') || !file_exists(PV_ROOT.$video_folder_location.$randomFileName.'.mp4') || !file_exists(PV_ROOT.$video_folder_location.$randomFileName.'.wmv') || !file_exists(PV_ROOT.$video_folder_location.$randomFileName.'.avi') || !file_exists(PV_ROOT."$video_folder_location$randomFileName.mpeg") || !file_exists(PV_ROOT."$video_folder_location$randomFileName.rm") || !file_exists(PV_ROOT.$video_folder_location.$randomFileName.'rm') || !file_exists(PV_ROOT.$video_folder_location.$randomFileName.'.ogv')  || !file_exists(PV_ROOT.$video_folder_location.$randomFileName.'.mov') || !file_exists(PV_ROOT.$video_folder_location.$randomFileName.'.asf') || !file_exists(PV_ROOT.$video_folder_location.$randomFileName.'.webm')){
				 			$image_exist=false;
				 		}//end if
				 	}//end while
				 }
			 	
			 }//end end if empy
			 
			if(PVValidator::isFlvFile($file_type)){
			 	$save_name=$video_folder_location.$randomFileName.'.flv';
				$flv_file=$randomFileName.'.flv';
			}
			else if(PVValidator::isMp4File($file_type)){
			 	$save_name=$video_folder_location.$randomFileName.'.mp4';
			 	$mp4_file=$randomFileName.'.mp4';
			}
			else if(PVValidator::isWmvFile($file_type)){
			 	$save_name=$video_folder_location.$randomFileName.'.wmv';
				$wmv_file=$randomFileName.'.wmv';
			}
			else if(PVValidator::isAviFile($file_type)){
				$save_name=$video_folder_location.$randomFileName.'.avi';
				$rm_file=$randomFileName.'.avi';
			}
			else if(PVValidator::isMpegVideoFile($file_type)){
				$save_name=$video_folder_location.$randomFileName.'.mpeg';
				$mpeg_file=$randomFileName.'.mpeg';
			} 
			else if(PVValidator::isRealMediaFile($file_type)){
				$save_name=$video_folder_location.$randomFileName.'.rm';
				$rm_file=$randomFileName.'.rm';
			}
			else if(PVValidator::isMovFile($file_type)){
				$save_name=$video_folder_location.$randomFileName.'.mov';
				$mov_file=$randomFileName.'.mov';
			}			
			else if(PVValidator::isAsfFile($file_type)){
				$save_name=$video_folder_location.$randomFileName.'.asf';
				$asf_file=$randomFileName.'.asf';
			}			
			else if(PVValidator::isOGGVideoFile($file_type)){
				$save_name=$video_folder_location.$randomFileName.'.ogv';
				$ogv_file=$randomFileName.'.ogv';
			}			
			else if(PVValidator::isWebMFile($file_type)){
				$save_name=$video_folder_location.$randomFileName.'.webm';
				$webm_file=$randomFileName.'.webm';
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
			
			if(move_uploaded_file($tmp_name, PV_ROOT.$save_name) || PVFileManager::copyFile($tmp_name, PV_ROOT.$save_name)) {
				
				if($args['convert_to_flv']){
					self::convertVideoFile(PV_ROOT.$save_name, PV_ROOT.PV_VIDEO.$randomFileName.'.flv', $args['convert_to_flv_options']);
					$flv_file=$randomFileName.'.flv';
				}
					
				if($args['convert_to_mpeg']){
					self::convertVideoFile(PV_ROOT.$save_name, PV_ROOT.PV_VIDEO.$randomFileName.'.mpeg', $args['convert_to_mpeg_options']);
					$mpeg_file=$randomFileName.'.mpeg';
				}
					
				if($args['convert_to_mp4']){
					self::convertVideoFile(PV_ROOT.$save_name, PV_ROOT.PV_VIDEO.$randomFileName.'.mp4', $args['convert_to_mp4_options']);
					$mp4_file=$randomFileName.'.mp4';
				}
					
				if($args['convert_to_wmv']){
					self::convertVideoFile(PV_ROOT.$save_name, PV_ROOT.PV_VIDEO.$randomFileName.'.wmv', $args['convert_to_wmv_options']);
					$wmv_file=$randomFileName.'.wmv';
				}
					
				if($args['convert_to_realmedia']){
					self::convertVideoFile(PV_ROOT.$save_name, PV_ROOT.PV_VIDEO.$randomFileName.'.rm', $args['convert_to_rm_options']);
					$rm_file=$randomFileName.'.rm';
				}
					
				if($args['convert_to_avi']){
					self::convertVideoFile(PV_ROOT.$save_name, PV_ROOT.PV_VIDEO.$randomFileName.'.avi', $args['convert_to_avi_options']);
					$avi_file=$randomFileName.'.avi';
				}
				
				if($args['convert_to_mov']){
					self::convertVideoFile(PV_ROOT.$save_name, PV_ROOT.PV_VIDEO.$randomFileName.'.mov', $args['convert_to_mov_options']);
					$mov_file=$randomFileName.'.mov';
				}
				
				if($args['convert_to_asf']){
					self::convertVideoFile(PV_ROOT.$save_name, PV_ROOT.PV_VIDEO.$randomFileName.'.asf', $args['convert_to_asf_options']);
					$asf_file=$randomFileName.'.asf';
				}
				
				if($args['convert_to_ogv']){
					self::convertVideoFile(PV_ROOT.$save_name, PV_ROOT.PV_VIDEO.$randomFileName.'.ogv', $args['convert_to_ogv_options']);
					$ogv_file=$randomFileName.'.ogv';
				}

				if($args['convert_to_webm']){
					self::convertVideoFile(PV_ROOT.$save_name, PV_ROOT.PV_VIDEO.$randomFileName.'.webm', $args['convert_to_webm_options']);
					$webm_file=$randomFileName.'.webm';
				}
					
				$query="UPDATE ".pv_getVideoContentTableName()." SET video_type='$file_type', video_length='$video_length', flv_file='$flv_file', mpeg_file='$mpeg_file', wmv_file='$wmv_file', rm_file='$rm_file', mp4_file='$mp4_file', avi_file='$avi_file', mov_file='$mov_file', asf_file='$asf_file', ogv_file='$ogv_file' , webm_file='$webm_file' WHERE  video_id='$content_id' ";
				PVDatabase::query($query);
				
				return 1;
			} else {
				return FALSE;
			}	 
		} else {
		 	return FALSE;
		 }
	}// end upload Image
	
	function convertVideoFile($current_file_location, $new_file_location, $options=array()){
		if(!is_array($options)){
			$options=array();
		}
		
		$defaults=array('converter'=>'ffmpeg');
		$options += $defaults;
		$converter=$options['converter'];
		
		$input_options=self::setEncodingOptions($options, 'input_');
		$output_options=self::setEncodingOptions($options, 'output_');
		
		exec( "$converter -i $current_file_location $input_options $new_file_location $output_options" );
		
		
	}//end convertVideoFile
	
	public static function setEncodingOptions($options=array(), $input_type=''){
		
		$input_options='';
		
		if($options[$input_type.'vframes'] ){
			$input_options.=' -vframes '.$options[$input_type.'vframes'];	
		}
		
		if($options[$input_type.'r']){
			$input_options.=' -r '.$options[$input_type.'r'];	
		}
		
		if($options[$input_type.'s']){
			$input_options.=' -s '.$options[$input_type.'s'];
		}	
		
		if($options[$input_type.'aspect']){
			$input_options.=' -aspect '.$options[$input_type.'aspect'];	
		}
		
		if($options[$input_type.'croptop']){
			$input_options.=' -croptop '.$options[$input_type.'croptop'];	
		}
		
		if($options[$input_type.'cropbottom']){
			$input_options.=' -cropbottom '.$options[$input_type.'cropbottom'];
		}
		
		if($options[$input_type.'cropleft']){
			$input_options.=' -cropleft '.$options[$input_type.'cropleft'];
		}
		
		if($options[$input_type.'cropright']){
			$input_options.=' -cropright '.$options[$input_type.'cropright'];
		}
		
		if($options[$input_type.'padtop']){
			$input_options.=' -padtop '.$options[$input_type.'padtop'];
		}
		
		if($options[$input_type.'padbottom']){
			$input_options.=' -padbottom '.$options[$input_type.'padbottom'];
		}
		
		if($options[$input_type.'padleft']){
			$input_options.=' -padleft '.$options[$input_type.'padleft'];
		}
		
		if($options[$input_type.'padright']){
			$input_options.=' -padright '.$options[$input_type.'padright'];
		}
		
		if($options[$input_type.'padcolor']){
			$input_options.=' -padcolor '.$options[$input_type.'padcolor'];
		}
		
		if($options[$input_type.'bt']){
			$input_options.=' -bt '.$options[$input_type.'bt'];
		}
		
		if($options[$input_type.'maxrate']){
			$input_options.=' -maxrate '.$options[$input_type.'maxrate'];
		}
		
		if($options[$input_type.'minrate']){
			$input_options.=' -minrate '.$options[$input_type.'minrate'];
		}
		
		if($options[$input_type.'bufsize']){
			$input_options.=' -bufsize '.$options[$input_type.'bufsize'];
		}
		
		if($options[$input_type.'vcodec']){
			$input_options.=' -vcodec '.$options[$input_type.'vcodec'];
		}
		
		if($options[$input_type.'pass']){
			$input_options.=' -pass '.$options[$input_type.'pass'];
		}

		if($options[$input_type.'same_quant']){
			$input_options.=' -same_quant ';	
		}
		
		if($options[$input_type.'intra']){
			$input_options.=' -intra ';
		}
		
		if($options[$input_type.'passlogfile']){
			$input_options.=' -passlogfile '.$options[$input_type.'passlogfile'];
		}
		
		if($options[$input_type.'atag']){
			$input_options.=' -atag '.$options[$input_type.'atag'];
		}
		
		if($options[$input_type.'vlang']){
			$input_options.=' -vlang '.$options[$input_type.'vlang'];
		}
		
		if($options[$input_type.'vf']){
			$input_options.=' -vf '.$options[$input_type.'vf'];
		}
		
		if($options[$input_type.'pix_fmt']){
			$input_options.=' -pix_fmt '.$options[$input_type.'pix_fmt'];
		}
		
		if($options[$input_type.'sws_flags']){
			$input_options.=' -sws_flags '.$options[$input_type.'sws_flags'];
		}

		if($options[$input_type.'g']){
			$input_options.=' -g '.$options[$input_type.'g'];
		}

		if($options[$input_type.'vdt']){
			$input_options.=' -vdt '.$options[$input_type.'vdt'];
		}

		if($options[$input_type.'qmin']){
			$input_options.=' -qmin '.$options[$input_type.'qmin'];
		}
		
		if($options[$input_type.'qmax']){
			$input_options.=' -qmax '.$options[$input_type.'qmax'];
		}
		
		if($options[$input_type.'qdiff']){
			$input_options.=' -qdiff '.$options[$input_type.'qdiff'];
		}
		
		if($options[$input_type.'qblur']){
			$input_options.=' -qblur '.$options[$input_type.'qblur'];
		}

		if($options[$input_type.'qcomp']){
			$input_options.=' -qcomp '.$options[$input_type.'qcomp'];
		}
		
		if($options[$input_type.'lmin']){
			$input_options.=' -lmin '.$options[$input_type.'lmin'];
		}
		
		if($options[$input_type.'lmax']){
			$input_options.=' -lmax '.$options[$input_type.'lmax'];
		}
		
		if($options[$input_type.'mblmin']){
			$input_options.=' -mblmin '.$options[$input_type.'mblmin'];
		}
		
		if($options[$input_type.'mblmax']){
			$input_options.=' -mblmax '.$options[$input_type.'mblmax'];
		}
		
		if($options[$input_type.'rc_init_cplx']){
			$input_options.=' -rc_init_cplx '.$options[$input_type.'rc_init_cplx'];
		}
		
		if($options[$input_type.'b_qfactor']){
			$input_options.=' -b_qfactor '.$options[$input_type.'b_qfactor'];
		}
		
		if($options[$input_type.'i_qfactor']){
			$input_options.=' -i_qfactor '.$options[$input_type.'i_qfactor'];
		}
		
		if($options[$input_type.'b_qoffset']){
			$input_options.=' -b_qoffset '.$options[$input_type.'b_qoffset'];
		}
		
		if($options[$input_type.'i_qoffset']){
			$input_options.=' -i_qoffset '.$options[$input_type.'i_qoffset'];
		}

		if($options[$input_type.'rc_eq']){
			$input_options.=' -rc_eq '.$options[$input_type.'rc_eq'];
		}
		
		if($options[$input_type.'rc_override']){
			$input_options.=' -rc_override '.$options[$input_type.'rc_override'];
		}
		
		if($options[$input_type.'me_method']){
			$input_options.=' -me_method '.$options[$input_type.'me_method'];
		}
		
		if($options[$input_type.'dct_algo']){
			$input_options.=' -dct_algo'.$options[$input_type.'dct_algo'];
		}

		if($options[$input_type.'idct_algo']){
			$input_options.=' -idct_algo '.$options[$input_type.'idct_algo'];
		}
		
		if($options[$input_type.'er']){
			$input_options.=' -er '.$options[$input_type.'er'];
		}
		
		if($options[$input_type.'ec']){
			$input_options.=' -ec '.$options[$input_type.'ec'];
		}
		
		if($options[$input_type.'bf']){
			$input_options.=' -bf '.$options[$input_type.'bf'];
		}
		
		if($options[$input_type.'mbd']){
			$input_options.=' -mbd '.$options[$input_type.'mbd'];
		}

		if($options[$input_type.'4mv']){
			$input_options.=' -4mv '.$options[$input_type.'4mv'];
		}
		
		if($options[$input_type.'part']){
			$input_options.=' -part ';
		}
		
		if($options[$input_type.'bug']){
			$input_options.=' -bug '.$options[$input_type.'bug'];
		}
		
		if($options[$input_type.'strict']){
			$input_options.=' -strict '.$options[$input_type.'strict'];
		}
		
		if($options[$input_type.'aic']){
			$input_options.=' -aic '.$options[$input_type.'aic'];
		}
		
		if($options[$input_type.'umv']){
			$input_options.=' -umv';
		}
		
		if($options[$input_type.'deinterlace']){
			$input_options.=' -deinterlace ';
		}

		if($options[$input_type.'ilme']){
			$input_options.=' -ilme ';
		}

		if($options[$input_type.'psnr']){
			$input_options.=' -psnr ';
		}
		
		if($options[$input_type.'vstats']){
			$input_options.=' -vstats ';
		}
		
		if($options[$input_type.'vstats_file']){
			$input_options.=' -vstats_file '.$options[$input_type.'vstats_file'];
		}
		
		if($options[$input_type.'top']){
			$input_options.=' -top '.$options[$input_type.'top'];
		}
		
		if($options[$input_type.'dc']){
			$input_options.=' -dc '.$options[$input_type.'dc'];
		}
		
		if($options[$input_type.'vtag']){
			$input_options.=' -vtag '.$options[$input_type.'vtag'];
		}
		
		if($options[$input_type.'qphist']){
			$input_options.=' -qphist '.$options[$input_type.'qphist'];
		}
		
		if($options[$input_type.'vbsf']){
			$input_options.=' -vbsf '.$options[$input_type.'vbsf'];
		}
		
		if($options[$input_type.'force_key_frames']){
			$input_options.=' -force_key_frames '.$options[$input_type.'force_key_frames'];
		}
		
		return $input_options;
	}//end setEncodingOptions

}//end class

?>