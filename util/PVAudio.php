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

class PVAudio extends PVStaticObject {

	protected static $converter = 'ffmpeg';

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

		$defaults = array('converter' => 'ffmpeg');

		$config += $defaults;
		$config = self::_applyFilter(get_class(), __FUNCTION__, $config, array('event' => 'args'));

		self::$converter = $config['converter'];
		self::_notify(get_class() . '::' . __FUNCTION__, $config);
	}

	/**
	 * Upload and convert new audio files. This functionw will soon be deprecated
	 * and only and update will be allowed.
	 */
	public static function uploadAudioFileToContent($args = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $args);

		$defaults = array(
			'convert_to_midi' => false, 
			'convert_to_mp3' => false, 
			'convert_to_wav' => false, 
			'convert_to_aiff' => false, 
			'convert_to_realaudio' => false, 
			'convert_to_oga' => false
		);

		$args += $defaults;
		$args = self::_applyFilter(get_class(), __FUNCTION__, $args, array('event' => 'args'));
		$args = PVDatabase::makeSafe($args);
		extract($args);

		$audio_folder_location = PV_AUDIO;
		$save_name = "";

		$audio_types = array("audio/basic", "audio/midi", "audio/mpeg", "audio/x-aiff", "audio/x-mpegurl", "audio/x-pn-realaudio", "audio/x-realaudio", "audio/x-wav");

		if (in_array(strtolower($file_type), $audio_types)) {

			$image_exist = true;

			while ($image_exist) {
				$randomFileName = pv_generateRandomString(20, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890');

				if (!file_exists(PV_ROOT . "$audio_folder_location$randomFileName.mp3") || !file_exists(ROOT . "$audio_folder_location$randomFileName.mpeg") || !file_exists(ROOT . "$audio_folder_location$randomFileName.mid") || !file_exists(ROOT . "$audio_folder_location$randomFileName.kar") || !file_exists(ROOT . "$audio_folder_location$randomFileName.mp2") || !file_exists(ROOT . "$audio_folder_location$randomFileName.midi") || !file_exists(ROOT . "$audio_folder_location$randomFileName.aif") || !file_exists(ROOT . "$audio_folder_location$randomFileName.rm") || !file_exists(ROOT . "$audio_folder_location$randomFileName.oga")) {
					$image_exist = false;
				}
			}//end while

			if (PVValidator::isMidiFile($file_type)) {
				$save_name = "$audio_folder_location$randomFileName.mid";
				$mid_file = $randomFileName . '.mid';
			} else if (PVValidator::isMpegAudioFile($file_type)) {
				$save_name = "$audio_folder_location$randomFileName.mp3";
				$mp3_file = $randomFileName . '.mp3';
			} else if (PVValidator::isAiffFile($file_type)) {
				$save_name = "$audio_folder_location$randomFileName.aif";
				$aif_file = $randomFileName . '.aif';
			} else if (PVValidator::isRealAudioFile($file_type)) {
				$save_name = "$audio_folder_location$randomFileName.rm";
				$ra_file = $randomFileName . '.rm';
			} else if (PVValidator::isWavFile($file_type)) {
				$save_name = "$audio_folder_location$randomFileName.wav";
				$wav_file = $randomFileName . '.wav';
			} else if (PVValidator::isOGGAudioFile($file_type)) {
				$save_name = "$audio_folder_location$randomFileName.oga";
				$oga_file = $randomFileName . '.oga';
			}

			$file_name = PVDatabase::makeSafe($file_name);
			$file_type = PVDatabase::makeSafe($file_type);
			$file_size = PVDatabase::makeSafe($file_size);
			$save_name = PVDatabase::makeSafe($save_name);

			if (empty($app_id)) {
				$app_id = 0;
			}

			if (empty($file_size)) {
				$file_size = 0;
			}

			if (move_uploaded_file($tmp_name, PV_ROOT . $save_name) || PVFileManager::copyNewFile($tmp_name, PV_ROOT . $save_name)) {

				if ($args['convert_to_midi']) {
					self::convertAudioFile(PV_ROOT . $save_name, PV_ROOT . PV_AUDIO . $randomFileName . '.mid', @$args['convert_to_midi_options']);
					$mid_file = $randomFileName . '.mid';
				}

				if ($args['convert_to_mp3']) {
					self::convertAudioFile(PV_ROOT . $save_name, PV_ROOT . PV_AUDIO . $randomFileName . '.mp3', @$args['convert_to_mp3_options']);
					$mp3_file = $randomFileName . '.mp3';
				}

				if ($args['convert_to_wav']) {
					self::convertAudioFile(PV_ROOT . $save_name, PV_ROOT . PV_AUDIO . $randomFileName . '.wav', @$args['convert_to_wav_options']);
					$wav_file = $randomFileName . '.wav';
				}

				if ($args['convert_to_aiff']) {
					self::convertAudioFile(PV_ROOT . $save_name, PV_ROOT . PV_AUDIO . $randomFileName . '.aiff', @$args['convert_to_aiff_options']);
					$aif_file = $randomFileName . '.aif';
				}

				if ($args['convert_to_realaudio']) {
					self::convertAudioFile(PV_ROOT . $save_name, PV_ROOT . PV_AUDIO . $randomFileName . '.ra', @$args['convert_to_ra_options']);
					$ra_file = $randomFileName . '.rm';
				}

				if ($args['convert_to_oga']) {
					self::convertAudioFile(PV_ROOT . $save_name, PV_ROOT . PV_AUDIO . $randomFileName . '.oga', @$args['convert_to_oga_options']);
					$oga_file = $randomFileName . '.oga';
				}

				if (!empty($content_id)) {
					$query = "INSERT INTO " . pv_getAudioContentTableName() . "( audio_id,  audio_length, mid_file , wav_file , aif_file , mp3_file , ra_file , oga_file, sample_length , audio_src ,audio_type ) VALUES( '$content_id', '$audio_length' , '$mid_file' , '$wav_file' , '$aif_file' , '$mp3_file' , '$ra_file' , '$oga_file', '$sample_length' , '$audio_src', '$audio_type') ";
					PVDatabase::query($query);
				}

				return 1;
			} else {
				return self::$UPLOAD_FAILED;
			}

		} else {
			return self::$INVALID_TYPE;
		}

	}//end uploadAudioFile

	/*
	 * Update existing audio content and convert audio files as needed.
	 * Takes in an array of arguements that describe the file.
	 */
	public static function updateAudioContent($args) {
		$defaults = array('convert_to_midi' => false, 'convert_to_mp3' => false, 'convert_to_wav' => false, 'convert_to_aiff' => false, 'convert_to_realaudio' => false, 'convert_to_oga' => false);

		$args += $defaults;
		$args = PVDatabase::makeSafe($args);
		extract($args);

		$audio_folder_location = PV_AUDIO;
		$save_name = "";

		$query = "SELECT mid_file, wav_file , aif_file, mp3_file, ra_file, oga_file FROM " . pv_getAudioContentTableName() . " WHERE audio_id='$content_id'";
		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);

		$current_file_name = '';

		if (PVValidator::isMidiFile($file_type)) {
			$current_file_name = $row['mid_file'];
		}

		if (PVValidator::isWavFile($file_type)) {
			$current_file_name = $row['wav_file'];
		}

		if (PVValidator::isAiffFile($file_type)) {
			$current_file_name = $row['aif_file'];
		}

		if (PVValidator::isOGGAudioFile($file_type)) {
			$current_file_name = $row['oga_file'];
		}

		if (PVValidator::isMpegAudioFile($file_type)) {
			$current_file_name = $row['mp3_file'];
		}

		if (PVValidator::isRealAudioFile($file_type)) {
			$current_file_name = $row['ra_file'];
		}

		$basename = basename($current_file_name);
		$randomFileName = substr($basename, 0, strrpos($basename, '.'));

		$audio_types = array("audio/basic", "audio/midi", "audio/mpeg", "audio/x-aiff", "audio/x-mpegurl", "audio/x-pn-realaudio", "audio/x-realaudio", "audio/x-wav");

		if (PVValidator::isAudioFile($file_type)) {

			if (empty($randomFileName)) {

				if (in_array(strtolower($file_type), $image_types)) {
					$image_exist = true;

					while ($image_exist) {
						$randomFileName = pv_generateRandomString(20, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890');

						if (!file_exists(PV_ROOT . $audio_folder_location . $randomFileName . '.mp3') || !file_exists(PV_ROOT . $audio_folder_location . $randomFileName . '.mpeg') || !file_exists(PV_ROOT . $audio_folder_location . $randomFileName . '.mid') || !file_exists(PV_ROOT . $audio_folder_location . $randomFileName . '.wav') || !file_exists(PV_ROOT . $audio_folder_location . $randomFileName . '.aiff') || !file_exists(PV_ROOT . "$audio_folder_location$randomFileName.midi") || !file_exists(PV_ROOT . "$audio_folder_location$randomFileName.aif") || !file_exists(PV_ROOT . $audio_folder_location . $randomFileName . 'rm') || !file_exists(PV_ROOT . $audio_folder_location . $randomFileName . '.oga')) {
							$image_exist = false;
						}//end if
					}//end while
				}

			}//end end if empy

			if (PVValidator::isMidiFile($file_type)) {
				$save_name = $audio_folder_location . $randomFileName . '.mid';
				$mid_file = $randomFileName . '.mid';
			} else if (PVValidator::isMpegAudioFile($file_type)) {
				$save_name = $audio_folder_location . $randomFileName . '.mp3';
				$mp3_file = $randomFileName . '.mp3';
			} else if (PVValidator::isAiffFile($file_type)) {
				$save_name = $audio_folder_location . $randomFileName . '.aiff';
				$aif_file = $randomFileName . '.aiff';
			} else if (PVValidator::isRealAudioFile($file_type)) {
				$save_name = $audio_folder_location . $randomFileName . '.rm';
				$ra_file = $randomFileName . '.rm';
			} else if (PVValidator::isWavFile($file_type)) {
				$save_name = $audio_folder_location . $randomFileName . '.wav';
				$wav_file = $randomFileName . '.wav';
			} else if (PVValidator::isOGGAudioFile($file_type)) {
				$save_name = $audio_folder_location . $randomFileName . '.oga';
				$oga_file = $randomFileName . '.oga';
			}

			$file_name = PVDatabase::makeSafe($file_name);
			$file_type = PVDatabase::makeSafe($file_type);
			$file_size = PVDatabase::makeSafe($file_size);
			$save_name = PVDatabase::makeSafe($save_name);

			if (empty($app_id)) {
				$app_id = 0;
			}

			if (empty($file_size)) {
				$file_size = 0;
			}

			if (move_uploaded_file($tmp_name, PV_ROOT . $save_name) || PVFileManager::copyFile($tmp_name, PV_ROOT . $save_name)) {

				if ($args['convert_to_midi']) {
					self::convertAudioFile(PV_ROOT . $save_name, PV_ROOT . PV_AUDIO . $randomFileName . '.mid', $args['convert_to_midi_options']);
					$mid_file = $randomFileName . '.mid';
				}

				if ($args['convert_to_mp3']) {
					self::convertAudioFile(PV_ROOT . $save_name, PV_ROOT . PV_AUDIO . $randomFileName . '.mp3', $args['convert_to_mp3_options']);
					$mp3_file = $randomFileName . '.mp3';
				}

				if ($args['convert_to_wav']) {
					self::convertAudioFile(PV_ROOT . $save_name, PV_ROOT . PV_AUDIO . $randomFileName . '.wav', $args['convert_to_wav_options']);
					$wav_file = $randomFileName . '.wav';
				}

				if ($args['convert_to_aiff']) {
					self::convertAudioFile(PV_ROOT . $save_name, PV_ROOT . PV_AUDIO . $randomFileName . '.aiff', $args['convert_to_aiff_options']);
					$aif_file = $randomFileName . '.aiff';
				}

				if ($args['convert_to_realaudio']) {
					self::convertAudioFile(PV_ROOT . $save_name, PV_ROOT . PV_AUDIO . $randomFileName . '.ra', $args['convert_to_ra_options']);
					$ra_file = $randomFileName . '.ra';
				}

				if ($args['convert_to_oga']) {
					self::convertAudioFile(PV_ROOT . $save_name, PV_ROOT . PV_AUDIO . $randomFileName . '.oga', $args['convert_to_oga_options']);
					$oga_file = $randomFileName . '.oga';
				}

				$query = "UPDATE " . pv_getAudioContentTableName() . " SET audio_type='$file_type', audio_size='$file_size', mid_file='$mid_file', mp3_file='$mp3_file', aif_file='$aif_file', ra_file='$ra_file', wav_file='$wav_file', oga_file='$oga_file', sample_length='$sample_length', audio_src='$audio_src' WHERE  audio_id='$content_id' ";
				PVDatabase::query($query);

				return 1;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}// end upload Image

	/**
	 * Converts a sound file from one format to a different one or one with different attribute. The convert is
	 * executed on the command line and by default is set to use ffmpeg.
	 *
	 * @param string $current_file_location The location of the current file to be converted.
	 * @param string $new_file_location The location to output the new file once converted.
	 * @param array $options Options that can control how the conversion takes place.
	 * 			'conveter' _string_: The convert to be used and the location. Default is ffmpeg. To further define
	 * 			either added the path to the converter +ffmpeg or path to another converter besides ffmpeg.
	 * 			'input_' array: Should be an array that of options for how to treat the input file. The options
	 * 			should be the same options passed through the setEncodingOptions except the prefix should have 'input_'.
	 * 			For example if the option is 'ar' as in setEncodingOptions, add 'input_ar' as the option key.
	 * 			'output_' array: Should be an array that of options for how to treat the output file. The options
	 * 			should be the same options passed through the setEncodingOptions except the prefix should have 'output_'.
	 * 			For example if the option is 'ar' as in setEncodingOptions, add 'input_ar' as the option key.
	 *
	 * @return void The output is not returned but a new file will be created if the conversion succeeded
	 * @access public
	 */
	public static function convertAudioFile($current_file_location, $new_file_location, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $current_file_location, $new_file_location, $options);

		if (!is_array($options)) {
			$options = array();
		}

		$defaults = array('converter' => self::$converter);
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('current_file_location' => $current_file_location, 'new_file_location' => $new_file_location, 'options' => $options), array('event' => 'args'));
		$current_file_location = $filtered['current_file_location'];
		$new_file_location = $filtered['new_file_location'];
		$options = $filtered['options'];

		$converter = $options['converter'];

		$input_options = self::setEncodingOptions($options, 'input_');
		$output_options = self::setEncodingOptions($options, 'output_');

		exec("$converter -i $input_options $current_file_location $output_options $new_file_location ");
		self::_notify(get_class() . '::' . __FUNCTION__, $current_file_location, $new_file_location, $options, $input_options, $output_options);
	}//end convertAudioFile

	/**
	 * The encoding options on how to encode a file using FFMPPEG. The options should be run in a command line
	 * formated.
	 * @see http://www.ffmpeg.org/ffmpeg.html
	 * @see http://www.ffmpeg.org/ffmpeg.html#Audio-Options
	 * @see http://www.ffmpeg.org/ffmpeg.html#Advanced-Audio-options_003a
	 *
	 * @param array $options Defined options to be used in the conversion. Options relate to those passed in a normal
	 * 		  FFMPEG command line fashion.The key of the array corresponds the command and the value responds to the command
	 * 		  value.
	 * @param string $input_type If the options have a prefix in front of the key, the prefix should be defined either.
	 *
	 * @return string $options A string of options that should be used on the command line with ffmpeg
	 * @access public
	 *
	 * @todo find ffmpeg documentation and use isset to remove notices
	 */
	public static function setEncodingOptions($options = array(), $input_type = '') {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $options, $input_type);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array('input_type' => $input_type, 'options' => $options), array('event' => 'args'));
		$input_type = $filtered['input_type'];
		$options = $filtered['options'];

		$input_options = '';

		if (isset($options[$input_type . 'aframes'])) {
			$input_options .= ' -aframes ' . $options[$input_type . 'aframes'];
		}

		if (isset($options[$input_type . 'ar'])) {
			$input_options .= ' -ar ' . $options[$input_type . 'ar'];
		}

		if (isset($options[$input_type . 'ab'])) {
			$input_options .= ' -ab ' . $options[$input_type . 'ab'];
		}

		if (isset($options[$input_type . 'aq'])) {
			$input_options .= ' -aq ' . $options[$input_type . 'aq'];
		}

		if (isset($options[$input_type . 'ac'])) {
			$input_options .= ' -ac ' . $options[$input_type . 'ac'];
		}

		if (isset($options[$input_type . 'an'])) {
			$input_options .= ' -an ';
		}

		if (isset($options[$input_type . 'acodec'])) {
			$input_options .= ' -acodec ' . $options[$input_type . 'acodec'];
		}

		if (isset($options[$input_type . 'newaudio'])) {
			$input_options .= ' -newaudio ';
		}

		if (isset($options[$input_type . 'alang'])) {
			$input_options .= ' -alang ' . $options[$input_type . 'alang'];
		}

		if (isset($options[$input_type . 'atag'])) {
			$input_options .= ' -atag ' . $options[$input_type . 'atag'];
		}

		if (isset($options[$input_type . 'audio_service_type'])) {
			$input_options .= ' -audio_service_type ' . $options[$input_type . 'audio_service_type'];
		}

		if (isset($options[$input_type . 'absf'])) {
			$input_options .= ' -absf ' . $options[$input_type . 'absf'];
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $input_options, $options, $input_type);
		$input_options = self::_applyFilter(get_class(), __FUNCTION__, $input_options, array('event' => 'return'));

		return $input_options;
	}//end setEncodingOptions

	/**
	 * Get the duration of an audio file.
	 *
	 * @param string $file The location of the audio file to calculate the duration of
	 *
	 * @return string $duration The duration of the audio file
	 * @access public
	 * @todo Add in options of choosing the converter
	 */
	public static function getDuration($file) {

		ob_start();
		passthru("ffmpeg -i \"{$file}\" 2>&1");
		$duration = ob_get_contents();
		ob_end_clean();

		$search = '/Duration: (.*?),/';
		$duration = preg_match($search, $duration, $matches, PREG_OFFSET_CAPTURE, 3);

		return $matches[1][0];
	}

}//end class AudioRenderer
?>