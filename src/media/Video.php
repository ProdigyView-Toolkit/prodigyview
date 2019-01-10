<?php
namespace prodigyview\media;

use prodigyview\design\StaticObject;

/**
 * Video is a class designed to manipulate video files and transcoding to various formats.
 *
 * Video works with all kinds of audio files: mp4, mov, ogg, etc. It utilizes command tools like
 * FFMPEG to do the transcoding and will return the results from the command line.
 *
 * Example:
 * 
 * ```php
 * //Set the file to be converted
 * $old_file = '/path/to/file/video.mov';
 *
 * //Set the path of the new file
 * $new_file =  '/path/to/file/video.mp4';
 *
 * //Options to pass to the FFmpeg or other conversion tools
 * //The following will place a -f infront of the input
 * $options = array('input_f' => '');
 *
 * //Run the conversion
 * Video::init();
 * Video::convertAudioFile($old_file, $new_file , $options );
 * ```
 *
 * @package media
 */
class Video  {
	
	use StaticObject;

	/**
	 * The type of convet to use, default is ffmpeg
	 */
	protected static $converter = 'ffmpeg';
	
	/**
	 * Protects the class from being initalized multiple times via init
	 */
	protected static $_initialized = false;

	/**
	 * Initialize the static class. Currently can be used for modifying the default converter
	 * tool and its location, which is simply ffmpeg.
	 *
	 * @param array $config An array of configurations
	 *			-'converter' _string_: The converter tool and its location. Default is ffmpeg
	 *
	 * @return void
	 * @access public
	 */
	public static function init($config = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $config);
			
		if(!self::$_initialized) {

			$defaults = array('converter' => 'ffmpeg');
	
			$config += $defaults;
			$config = self::_applyFilter(get_class(), __FUNCTION__, $config, array('event' => 'args'));
	
			self::$converter = $config['converter'];
			self::_notify(get_class() . '::' . __FUNCTION__, $config);
			
			self::$_initialized = true;
		}
	}

	/**
	 * Convert the video file using a converter to another format or a different settings of the same
	 * format.
	 *
	 * @param string $current_file_location The location of the file that is going to be converted
	 * @param string $new_file_location The location of the new file
	 * @param array $options Options that can control how the conversion takes place.
	 * 			'conveter' _string_: The convert to be used and the location. Default is ffmpeg. To further
	 * define
	 * 			either added the path to the converter + ffmpeg or path to another converter besides ffmpeg.
	 * 			'input_' array: Should be an array that of options for how to treat the input file. The options
	 * 			should be the same options passed through the setEncodingOptions except the prefix should have
	 * 'input_'.
	 * 			For example if the option is 'ar' as in setEncodingOptions, add 'input_ar' as the option key.
	 * 			'output_' array: Should be an array that of options for how to treat the output file. The
	 * options
	 * 			should be the same options passed through the setEncodingOptions except the prefix should have
	 * 'output_'.
	 * 			For example if the option is 'ar' as in setEncodingOptions, add 'input_ar' as the option key.
	 *
	 * @return void The output is not returned but a new file will be created if the conversion succeeded
	 * @access public
	 */
	public static function convertVideoFile($current_file_location, $new_file_location, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $current_file_location, $new_file_location, $options);

		if (!is_array($options)) {
			$options = array();
		}

		$defaults = array('converter' => self::$converter);
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'current_file_location' => $current_file_location,
			'new_file_location' => $new_file_location,
			'options' => $options
		), array('event' => 'args'));
		
		$current_file_location = $filtered['current_file_location'];
		$new_file_location = $filtered['new_file_location'];
		$options = $filtered['options'];

		$converter = $options['converter'];

		$input_options = self::setEncodingOptions($options, 'input_');
		$output_options = self::setEncodingOptions($options, 'output_');

		$input_options .= Audio::setEncodingOptions($options, 'input_');
		$output_options .= Audio::setEncodingOptions($options, 'output_');

		exec("$converter $input_options -i $current_file_location -y $output_options $new_file_location");
		self::_notify(get_class() . '::' . __FUNCTION__, $current_file_location, $new_file_location, $options, $input_options, $output_options);
	}//end convertVideoFile

	/**
	 * The encoding options on how to encode a file using FFMPPEG. The options should be run in a command
	 * line
	 * formated. The current formmating will only handle options passed through that deal with video
	 * manipulation
	 * @see http://www.ffmpeg.org/ffmpeg.html
	 * @see http://www.ffmpeg.org/ffmpeg.html#Video-Options
	 * @see http://www.ffmpeg.org/ffmpeg.html#Advanced-Video-Options
	 *
	 * @param array $options Defined options to be used in the conversion. Options relate to those passed
	 * in a normal
	 * 		  FFMPEG command line fashion.The key of the array corresponds the command and the value
	 * responds to the command
	 * 		  value.
	 * @param string $input_type If the options have a prefix in front of the key, the prefix should be
	 * defined either.
	 *
	 * @return string $options A string of options that should be used on the command line with ffmpeg
	 * @access public
	 *
	 * @todo find ffmpeg documentation and use isset to remove notices
	 */
	public static function setEncodingOptions($options = array(), $input_type = '') {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options, $input_type);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'input_type' => $input_type,
			'options' => $options
		), array('event' => 'args'));
		
		$input_type = $filtered['input_type'];
		$options = $filtered['options'];

		$input_options = '';

		if (isset($options[$input_type . 'vframes'])) {
			$input_options .= ' -vframes ' . $options[$input_type . 'vframes'];
		}

		if (isset($options[$input_type . 'r'])) {
			$input_options .= ' -r ' . $options[$input_type . 'r'];
		}

		if (isset($options[$input_type . 's'])) {
			$input_options .= ' -s ' . $options[$input_type . 's'];
		}

		if (isset($options[$input_type . 't'])) {
			$input_options .= ' -t ' . $options[$input_type . 't'];
		}

		if (isset($options[$input_type . 'c'])) {
			$input_options .= ' -c ' . $options[$input_type . 'c'];
		}

		if (isset($options[$input_type . 'aspect'])) {
			$input_options .= ' -aspect ' . $options[$input_type . 'aspect'];
		}

		if (isset($options[$input_type . 'croptop'])) {
			$input_options .= ' -croptop ' . $options[$input_type . 'croptop'];
		}

		if (isset($options[$input_type . 'cropbottom'])) {
			$input_options .= ' -cropbottom ' . $options[$input_type . 'cropbottom'];
		}

		if (isset($options[$input_type . 'cropleft'])) {
			$input_options .= ' -cropleft ' . $options[$input_type . 'cropleft'];
		}

		if (isset($options[$input_type . 'cropright'])) {
			$input_options .= ' -cropright ' . $options[$input_type . 'cropright'];
		}

		if (isset($options[$input_type . 'padtop'])) {
			$input_options .= ' -padtop ' . $options[$input_type . 'padtop'];
		}

		if (isset($options[$input_type . 'padbottom'])) {
			$input_options .= ' -padbottom ' . $options[$input_type . 'padbottom'];
		}

		if (isset($options[$input_type . 'padleft'])) {
			$input_options .= ' -padleft ' . $options[$input_type . 'padleft'];
		}

		if (isset($options[$input_type . 'padright'])) {
			$input_options .= ' -padright ' . $options[$input_type . 'padright'];
		}

		if (isset($options[$input_type . 'padcolor'])) {
			$input_options .= ' -padcolor ' . $options[$input_type . 'padcolor'];
		}

		if (isset($options[$input_type . 'bt'])) {
			$input_options .= ' -bt ' . $options[$input_type . 'bt'];
		}

		if (isset($options[$input_type . 'maxrate'])) {
			$input_options .= ' -maxrate ' . $options[$input_type . 'maxrate'];
		}

		if (isset($options[$input_type . 'minrate'])) {
			$input_options .= ' -minrate ' . $options[$input_type . 'minrate'];
		}

		if (isset($options[$input_type . 'bufsize'])) {
			$input_options .= ' -bufsize ' . $options[$input_type . 'bufsize'];
		}

		if (isset($options[$input_type . 'acodec'])) {
			$input_options .= ' -acodec ' . $options[$input_type . 'acodec'];
		}

		if (isset($options[$input_type . 'vcodec'])) {
			$input_options .= ' -vcodec ' . $options[$input_type . 'vcodec'];
		}

		if (isset($options[$input_type . 'f'])) {
			$input_options .= ' -f ' . $options[$input_type . 'f'];
		}

		if (isset($options[$input_type . 'codec'])) {
			$input_options .= ' -codec ' . $options[$input_type . 'codec'];
		}

		if (isset($options[$input_type . 'pass'])) {
			$input_options .= ' -pass ' . $options[$input_type . 'pass'];
		}

		if (isset($options[$input_type . 'same_quant'])) {
			$input_options .= ' -same_quant ';
		}

		if (isset($options[$input_type . 'intra'])) {
			$input_options .= ' -intra ';
		}

		if (isset($options[$input_type . 'passlogfile'])) {
			$input_options .= ' -passlogfile ' . $options[$input_type . 'passlogfile'];
		}

		if (isset($options[$input_type . 'atag'])) {
			$input_options .= ' -atag ' . $options[$input_type . 'atag'];
		}

		if (isset($options[$input_type . 'vlang'])) {
			$input_options .= ' -vlang ' . $options[$input_type . 'vlang'];
		}

		if (isset($options[$input_type . 'vf'])) {
			$input_options .= ' -vf ' . $options[$input_type . 'vf'];
		}

		if (isset($options[$input_type . 'pix_fmt'])) {
			$input_options .= ' -pix_fmt ' . $options[$input_type . 'pix_fmt'];
		}

		if (isset($options[$input_type . 'sws_flags'])) {
			$input_options .= ' -sws_flags ' . $options[$input_type . 'sws_flags'];
		}

		if (isset($options[$input_type . 'g'])) {
			$input_options .= ' -g ' . $options[$input_type . 'g'];
		}

		if (isset($options[$input_type . 'c:v'])) {
			$input_options .= ' -c:v ' . $options[$input_type . 'c:v'];
		}

		if (isset($options[$input_type . 'c:a'])) {
			$input_options .= ' -c:a ' . $options[$input_type . 'c:a'];
		}

		if (isset($options[$input_type . 'q:v'])) {
			$input_options .= ' -q:v ' . $options[$input_type . 'q:v'];
		}

		if (isset($options[$input_type . 'q:a'])) {
			$input_options .= ' -q:a ' . $options[$input_type . 'q:a'];
		}

		if (isset($options[$input_type . 'vdt'])) {
			$input_options .= ' -vdt ' . $options[$input_type . 'vdt'];
		}

		if (isset($options[$input_type . 'qmin'])) {
			$input_options .= ' -qmin ' . $options[$input_type . 'qmin'];
		}

		if (isset($options[$input_type . 'qmax'])) {
			$input_options .= ' -qmax ' . $options[$input_type . 'qmax'];
		}

		if (isset($options[$input_type . 'qdiff'])) {
			$input_options .= ' -qdiff ' . $options[$input_type . 'qdiff'];
		}

		if (isset($options[$input_type . 'qblur'])) {
			$input_options .= ' -qblur ' . $options[$input_type . 'qblur'];
		}

		if (isset($options[$input_type . 'qcomp'])) {
			$input_options .= ' -qcomp ' . $options[$input_type . 'qcomp'];
		}

		if (isset($options[$input_type . 'lmin'])) {
			$input_options .= ' -lmin ' . $options[$input_type . 'lmin'];
		}

		if (isset($options[$input_type . 'lmax'])) {
			$input_options .= ' -lmax ' . $options[$input_type . 'lmax'];
		}

		if (isset($options[$input_type . 'mblmin'])) {
			$input_options .= ' -mblmin ' . $options[$input_type . 'mblmin'];
		}

		if (isset($options[$input_type . 'mblmax'])) {
			$input_options .= ' -mblmax ' . $options[$input_type . 'mblmax'];
		}

		if (isset($options[$input_type . 'rc_init_cplx'])) {
			$input_options .= ' -rc_init_cplx ' . $options[$input_type . 'rc_init_cplx'];
		}

		if (isset($options[$input_type . 'b_qfactor'])) {
			$input_options .= ' -b_qfactor ' . $options[$input_type . 'b_qfactor'];
		}

		if (isset($options[$input_type . 'i_qfactor'])) {
			$input_options .= ' -i_qfactor ' . $options[$input_type . 'i_qfactor'];
		}

		if (isset($options[$input_type . 'b_qoffset'])) {
			$input_options .= ' -b_qoffset ' . $options[$input_type . 'b_qoffset'];
		}

		if (isset($options[$input_type . 'i_qoffset'])) {
			$input_options .= ' -i_qoffset ' . $options[$input_type . 'i_qoffset'];
		}

		if (isset($options[$input_type . 'rc_eq'])) {
			$input_options .= ' -rc_eq ' . $options[$input_type . 'rc_eq'];
		}

		if (isset($options[$input_type . 'rc_override'])) {
			$input_options .= ' -rc_override ' . $options[$input_type . 'rc_override'];
		}

		if (isset($options[$input_type . 'me_method'])) {
			$input_options .= ' -me_method ' . $options[$input_type . 'me_method'];
		}

		if (isset($options[$input_type . 'dct_algo'])) {
			$input_options .= ' -dct_algo' . $options[$input_type . 'dct_algo'];
		}

		if (isset($options[$input_type . 'idct_algo'])) {
			$input_options .= ' -idct_algo ' . $options[$input_type . 'idct_algo'];
		}

		if (isset($options[$input_type . 'er'])) {
			$input_options .= ' -er ' . $options[$input_type . 'er'];
		}

		if (isset($options[$input_type . 'ec'])) {
			$input_options .= ' -ec ' . $options[$input_type . 'ec'];
		}

		if (isset($options[$input_type . 'bf'])) {
			$input_options .= ' -bf ' . $options[$input_type . 'bf'];
		}

		if (isset($options[$input_type . 'mbd'])) {
			$input_options .= ' -mbd ' . $options[$input_type . 'mbd'];
		}

		if (isset($options[$input_type . '4mv'])) {
			$input_options .= ' -4mv ' . $options[$input_type . '4mv'];
		}

		if (isset($options[$input_type . 'part'])) {
			$input_options .= ' -part ';
		}

		if (isset($options[$input_type . 'bug'])) {
			$input_options .= ' -bug ' . $options[$input_type . 'bug'];
		}

		if (isset($options[$input_type . 'strict'])) {
			$input_options .= ' -strict ' . $options[$input_type . 'strict'];
		}

		if (isset($options[$input_type . 'aic'])) {
			$input_options .= ' -aic ' . $options[$input_type . 'aic'];
		}

		if (isset($options[$input_type . 'umv'])) {
			$input_options .= ' -umv';
		}

		if (isset($options[$input_type . 'deinterlace'])) {
			$input_options .= ' -deinterlace ';
		}

		if (isset($options[$input_type . 'ilme'])) {
			$input_options .= ' -ilme ';
		}

		if (isset($options[$input_type . 'psnr'])) {
			$input_options .= ' -psnr ';
		}

		if (isset($options[$input_type . 'vstats'])) {
			$input_options .= ' -vstats ';
		}

		if (isset($options[$input_type . 'vstats_file'])) {
			$input_options .= ' -vstats_file ' . $options[$input_type . 'vstats_file'];
		}

		if (isset($options[$input_type . 'top'])) {
			$input_options .= ' -top ' . $options[$input_type . 'top'];
		}

		if (isset($options[$input_type . 'dc'])) {
			$input_options .= ' -dc ' . $options[$input_type . 'dc'];
		}

		if (isset($options[$input_type . 'vtag'])) {
			$input_options .= ' -vtag ' . $options[$input_type . 'vtag'];
		}

		if (isset($options[$input_type . 'qphist'])) {
			$input_options .= ' -qphist ' . $options[$input_type . 'qphist'];
		}

		if (isset($options[$input_type . 'vbsf'])) {
			$input_options .= ' -vbsf ' . $options[$input_type . 'vbsf'];
		}

		if (isset($options[$input_type . 'force_key_frames'])) {
			$input_options .= ' -force_key_frames ' . $options[$input_type . 'force_key_frames'];
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $input_options, $options, $input_type);
		$input_options = self::_applyFilter(get_class(), __FUNCTION__, $input_options, array('event' => 'return'));

		return $input_options;
	}//end setEncodingOptions

}//end class
?>
