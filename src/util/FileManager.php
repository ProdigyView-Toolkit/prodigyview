<?php
namespace prodigyview\util;

use prodigyview\design\StaticObject;

//Define the directory seperator
if (!defined('DS')) {
	define('DS', '/');
}

/**
 * FileManager allows easy manipulation of the file system such as making directories or getting
 * mime types.
 *
 * The class has various functions that make file manipulation reasonably easy.
 * ```php
 * Examples:
 * //Count the number of files in a directory
 * echo FileManager::getFilesInDirectory('/path/to/directry');
 *
 * //Get Mime Type
 * echo FileManager::getFileMimeType('image.jpg');
 *
 * //Write To File
 * FileManager::getFileMimeType('/path/to/file', 'Hello World!');
 * ```
 *
 * @package util
 * @todo Add capabilites for streaming large files
 */
class FileManager {
	
	use StaticObject;

	/**
	 * @param $array File information to upload
	 *
	 * @todo figure out a point for this function. So do not use in the mean time.
	 */
	public static function phpFileUpload(array $params) {

		$allow_upload = 1;

		$file_name = $params['file_name'];
		$file_size = $params['file_size'];
		$file_type = $params['file_type'];
		$file_location = $params['file_location'];

		$upload_destination = $params['upload_destination'];
		$max_size = $params['max_size'];

		$allowed_extensions = $params['allowed_extensions'];

		if (count($allowed_extensions) > 0) {
			if (!in_array(substr(strrchr($file_name, '.'), 1), $allowed_extensions)) {
				$allow_upload = 0;
				echo "Invalid File Type";
			}
		}

		//$upload_location = $upload_location.basename( $_FILES['uploaded']['name']) ;

		if (empty($upload_destination)) {
			$allow_upload = 0;
			echo "Upload destination required";
		}

		if ($allow_upload == 1) {

			if (move_uploaded_file($file_location, $upload_destination)) {
				return basename($file_name);
			} else {
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

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $directory);

		$directory = self::_applyFilter(get_class(), __FUNCTION__, $directory, array('event' => 'args'));

		if (is_array($directory)) {
			foreach ($directory as $value) {
				self::deleteDirectory($value);
			}
		}

		if (!file_exists($directory)) {
			return false;
		}

		if (!is_dir($directory) || is_link($directory)) {
			return unlink($directory);
		}

		foreach (scandir($directory) as $item) {
			if ($item === '.' || $item === '..')
				continue;
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
	public static function getFileSize_NTFS(string $file) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $file);

		$file = self::_applyFilter(get_class(), __FUNCTION__, $file, array('event' => 'args'));
		$size = exec("for %v in (\"" . $file . "\") do @echo %~zv");
		$size = self::_applyFilter(get_class(), __FUNCTION__, $size, array('event' => 'return'));

		return $size;
	}

	/**
	 * Returns the file size using perl
	 *
	 * @param string $file The location of the file to get the size of
	 *
	 * @return boolean $size Returns the size of the file
	 * @access public
	 */
	public static function getFileSize_PERL(string $file) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $file);

		$file = self::_applyFilter(get_class(), __FUNCTION__, $file, array('event' => 'args'));
		$size = exec(" perl -e 'printf \"%d\n\",(stat(shift))[7];' " . $file . "");
		$size = self::_applyFilter(get_class(), __FUNCTION__, $size, array('event' => 'return'));

		return $size;
	}

	/**
	 * Scans a directory and geths all the folders, files and subfolder and files in that
	 * directory. Has a verbose mode that can give detailed information about the directory.
	 *
	 * @param string $directory The directory to be scanned
	 * @param array $options Options that can alter how the directory is scanned
	 * 			-'verbose' _boolean_: Enabling this mode will return everything in array of arrays. The array
	 * 			will contain more detailed information such as mime_type, extension, base name, etc. Default is false.
	 * 			-'magic_file' _string_: If finfo is installed and verbose is set to true, use this option to specifiy the magic
	 * 			file to use when getting the mime type of the file. Default is null
	 *
	 * @return array $files An array of subdirectories and fules
	 * @access public
	 */
	public static function getFilesInDirectory(string $directory, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $directory, $options);

		$defaults = array('verbose' => false);
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'directory' => $directory,
			'options' => $options
		), array('event' => 'args'));
		
		$directory = $filtered['directory'];
		$options = $filtered['options'];

		if (!is_dir($directory)) {
			return NULL;
		}

		$file_array = array();
		$dir = opendir($directory);

		while (false != ($file = readdir($dir))) {
			if (($file != ".") and ($file != "..")) {

				if (is_dir($directory . $file . DS) && !$options['verbose']) {

					$file_array[$directory . $file . DS] = self::getFilesInDirectory($directory . $file . DS, $options);

				} else if (is_dir($directory . $file . DS) && $options['verbose']) {
					$file_array[$directory . $file . DS] = array(
						'type' => 'folder',
						'directory' => $directory . $file . DS,
						'files' => self::getFilesInDirectory($directory . $file . DS, $options)
					);
				} else if ($options['verbose']) {
					$info = array(
						'type' => 'file',
						'basename' => pathinfo($file, PATHINFO_BASENAME),
						'extension' => pathinfo($file, PATHINFO_EXTENSION),
						'mime_type' => self::getFileMimeType($directory . $file, $options)
					);

					$file_array[$directory . $file] = $info;
				} else {
					$file_array[$directory . $file] = $file;
				}
			}
		}//end while

		$file_array = self::_applyFilter(get_class(), __FUNCTION__, $file_array, array('event' => 'return'));

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
	public static function getFileMimeType(string $file, array $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $file, $options);

		$defaults = array('magic_file' => null);

		$options += $defaults;
		
		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'file' => $file,
			'options' => $options
		), array('event' => 'args'));
		
		$file = $filtered['file'];
		$options = $filtered['options'];

		$mime_type = 'application/unknown';

		if (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME_TYPE, $options['magic_file']);
			$mime_type = finfo_file($finfo, $file);
			finfo_close($finfo);
		} else if (function_exists('mime_content_type')) {
			$mime_type = mime_content_type($file);
		} else {
			ob_start();
			system("file -i -b {$file}");
			$buffer = ob_get_clean();
			$buffer = explode(';', $buffer);
			if (is_array($buffer))
				$mime_type = $buffer[0];
		}

		$mime_type = self::_applyFilter(get_class(), __FUNCTION__, $mime_type, array('event' => 'return'));

		return $mime_type;
	}//end getFileMimeType

	/**
	 * Read a file's contents on disk into a string.
	 *
	 * @param string $file The location of the file
	 * @param string $mode The mode to be used reading the file
	 * @param string $encoding The encoding to convert the file to. Optional.
	 * @param boolean $stream The option to stream a file byte by byte
	 *
	 * @return string $contents The contents read from the file
	 * @access public
	 */
	public static function readFile(string $file, string $mode = 'r', string $encoding = '', bool $stream = true) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $file, $mode, $encoding);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'file' => $file,
			'mode' => $mode,
			'encoding' => $encoding
		), array('event' => 'args'));
		
		$file = $filtered['file'];
		$mode = $filtered['mode'];
		$encoding = $filtered['encoding'];

		$returnData = '';

		if (!file_exists($file)) {
			return false;
		}

		if ($stream === false) {
			$returnData = file_get_contents($file);
		} else {

			$handler = fopen($file, $mode);
			
			if (!$handler) {
				return false;
			}

			$returnData = '';
			while (!feof($handler)) {
				$returnData .= fread($handler, filesize($file));
			}//end  while

			fclose($handler);
		}//end else

		if (!empty($encoding) && $current_encoding = mb_detect_encoding($returnData, 'auto', true) != $encoding) {
			$returnData = mb_convert_encoding($returnData, $encoding, $current_encoding);
		}

		return $returnData;
	}//end load file

	/**
	 * Write contents to a file on the server.
	 *
	 * @param string $file The path to the file that will be writteen out too
	 * @param string $content The content to be written to the file
	 * @param string $mode The mode to be used when writing the file. Default is 'w'.
	 * @param string $encoding An encoding to be used when writing the file. Optional.
	 *
	 * @return boolean $written Returns true if the file was written, otherwise false
	 * @access public
	 */
	public static function writeFile(string $file, $content, string $mode = 'w', string $encoding = '') {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $file, $content, $mode, $encoding);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'file' => $file,
			'mode' => $mode,
			'encoding' => $encoding,
			'content' => $content
		), array('event' => 'args'));
		
		$file = $filtered['file'];
		$mode = $filtered['mode'];
		$encoding = $filtered['encoding'];
		$content = $filtered['content'];

		if (!empty($encoding) && $current_encoding = mb_detect_encoding($content, 'auto', true) != $encoding) {
			$content = mb_convert_encoding($content, $encoding, $current_encoding);
		}

		if (!$handle = fopen($file, $mode)) {
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
	 * @param string $file The path to the file that will be writteen out too
	 * @param string $content The content to be written to the file
	 * @param string $mode The mode to be used when writing the file. Default is 'w'.
	 * @param string $encoding An encoding to be used when writing the file. Optional.
	 *
	 * @return boolean $written Returns true if the file was written, otherwise false
	 * @access public
	 * @todo Defaults for mode, content and encoding, Add a way for encoding file.
	 */
	public static function writeNewFile(string $file, $content, string $mode = 'w', string $encoding = '') {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $file, $content, $mode, $encoding);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'file' => $file,
			'mode' => $mode,
			'encoding' => $encoding,
			'content' => $content
		), array('event' => 'args'));
		
		$file = $filtered['file'];
		$mode = $filtered['mode'];
		$encoding = $filtered['encoding'];
		$content = $filtered['content'];

		if (file_exists($file))
			return false;

		return self::writeFile($file, $content, $mode, $encoding);
	}//end writeFile

	/**
	 * Write contents to a file on the server only if the file does exist
	 *
	 * @param string $file The path to the file that will be writteen out too
	 * @param string $content The content to be written to the file
	 * @param string $mode The mode to be used when writing the file. Default is 'w'.
	 * @param string $encoding An encoding to be used when writing the file. Optional.
	 *
	 * @return boolean $written Returns true if the file was written, otherwise false
	 * @access public
	 */
	public static function rewriteNewFile(string $file, $content, string $mode = 'w', string $encoding = '') {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $file, $content, $mode, $encoding);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'file' => $file,
			'mode' => $mode,
			'encoding' => $encoding,
			'content' => $content
		), array('event' => 'args'));
		
		$file = $filtered['file'];
		$mode = $filtered['mode'];
		$encoding = $filtered['encoding'];
		$content = $filtered['content'];

		if (!file_exists($file))
			return false;

		return self::writeFile($file, $content, $mode, $encoding);
	}//end writeFile

	/**
	 * Copy a file to another location
	 *
	 * @param string $currentFile The location of the current file to be copied
	 * @param string $newFile The location of the new file to be copied
	 *
	 * @return boolean $copied Returns true if the file was succesfully copied
	 * @access public
	 */
	public static function copyFile(string $currentFile, string $newFile) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $currentFile, $newFile);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'currentFile' => $currentFile,
			'newFile' => $newFile
		), array('event' => 'args'));
		
		$currentFile = $filtered['currentFile'];
		$newFile = $filtered['newFile'];

		if (is_dir($currentFile)) {
			return false;
		}

		if (!file_exists($currentFile)) {
			return false;
		}

		if (copy($currentFile, $newFile)) {
			return true;
		}

		return false;

	}//end copyFile

	/**
	 * Copy a file to another location only if the file DOES NOT exist
	 *
	 * @param string $currentFile The location of the current file to be copied
	 * @param string $newFile The location of the new file to be copied
	 *
	 * @return boolean $copied Returns true if the file was succesfully copied
	 * @access public
	 */
	public static function copyNewFile(string $currentFile, string $newFile) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $currentFile, $newFile);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'currentFile' => $currentFile,
			'newFile' => $newFile
		), array('event' => 'args'));
		
		$currentFile = $filtered['currentFile'];
		$newFile = $filtered['newFile'];

		if (file_exists($newFile))
			return false;

		return self::copyFile($currentFile, $newFile);
	}//end copyFile

	/**
	 * Copy an entire directory from one location to another location.
	 *
	 * @param string $oldDirectory The location of the old directory
	 * @param string $newDirectory The location of the new directory
	 *
	 * @return void
	 * @access public
	 */
	public static function copyDirectory(string $oldDirectory, string $newDirectory) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $oldDirectory, $newDirectory);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'oldDirectory' => $oldDirectory,
			'newDirectory' => $newDirectory
		), array('event' => 'args'));
		
		$oldDirectory = $filtered['oldDirectory'];
		$newDirectory = $filtered['newDirectory'];

		if (is_dir($oldDirectory)) {

			if (!file_exists($newDirectory)) {
				if (!mkdir($newDirectory)) {
					return FALSE;
				}//end if !mkdir
			}

			$directory = dir($oldDirectory);

			while (FALSE !== ($entry = $directory->read())) {

				if ($entry === '.' || $entry === '..') {
					continue;
				}//end if

				$subfolder = $oldDirectory . DS . $entry;

				if (is_dir($subfolder)) {
					self::copyDirectory($subfolder, $newDirectory . DS . $entry);
					continue;
				}//end if subfolder is dir

				copy($subfolder, $newDirectory . DS . $entry);

			}//end while

			$directory->close();

		} else {
			$target_dir = dirname($newDirectory);

			if (!file_exists($target_dir)) {
				mkdir($target_dir);
			}
			copy($oldDirectory, $newDirectory);
		}//end else
	}

	/**
	 * Copy an entire directory from one location to another location but only if the directory does not
	 * exist
	 *
	 * @param string $oldDirectory The location of the old directory
	 * @param string $newDirectory The location of the new directory
	 *
	 * @return void
	 * @access public
	 */
	public static function copyNewDirectory(string $oldDirectory, string $newDirectory) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $oldDirectory, $newDirectory);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'oldDirectory' => $oldDirectory,
			'newDirectory' => $newDirectory
		), array('event' => 'args'));
		
		$oldDirectory = $filtered['oldDirectory'];
		$newDirectory = $filtered['newDirectory'];

		if (!is_dir($oldDirectory) || file_exists($oldDirectory))
			return false;

		self::copyDirectory($oldDirectory, $newDirectory);
	}

	/**
	 * Copy a file from a url to a destination on the server.
	 *
	 * @param string $url The url in which the file to copy exist
	 * @param string $destination The location the server to copy the file to
	 * @param string $filename An optional name to assign the file
	 *
	 * @return boolean $success Returns true if the file was succesfully copied
	 */
	public static function copyFileFromUrl(string $url, string $destination, string $filename = '') {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $url, $destination, $filename);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'url' => $url,
			'destination' => $destination,
			'filename' => $filename
		), array('event' => 'args'));
		
		$url = $filtered['url'];
		$destination = $filtered['destination'];
		$filename = $filtered['filename'];

		@$file = fopen($url, 'rb');

		if (!$file || empty($destination)) {
			return false;
		} else {

			if (empty($filename)) {
				$filename = basename($url);
			}

			$fc = fopen($destination . "$filename", "wb");

			while (!feof($file)) {
				$line = fread($file, 1028);
				fwrite($fc, $line);
			}

			fclose($fc);

			return true;
		} //end else
	}//end copyfile From URL

	/**
	 * Returns the file that was last modified with a directory.
	 *
	 * @param string $directory The directory to search through when looking for the file
	 *
	 * @return string $file The file that was modified in that directory
	 * @access public
	 */
	public static function getLastestFileInDirectory(string $directory) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $directory);

		$lastMod = 0;
		$lastModFile = '';

		foreach (scandir($directory) as $entry) {
			if (is_file($directory . $entry) && filectime($directory . $entry) > $lastMod) {
				$lastMod = filectime($directory . $entry);
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
	public static function deleteFile(string $file) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $file);

		if (file_exists($file) && !is_dir($file)) {
			return unlink($file);
		}

		return false;
	}

}//end class
