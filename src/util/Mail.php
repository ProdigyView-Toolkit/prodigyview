<?php
namespace prodigyview\util;

use prodigyview\design\StaticObject;

/**
* Mail is responsible for sending email from the application.
* 
* Mail is a basic class for sending emails. The class either uses the default mail() function on the 
* server, or can be configured to send emails via SMTP.
* 
* Example:
 * ```php
* //Initialize The Class
* Mail::init(array(
*             'smtp_host' => 'external.example.com',
*             'smtp_username' => 'MyLogin',
*             'smtp_password' => 'abc123',
*             'smtp_port' => 582,
*             'mailer' => 'smtp',
*             'default_sender' => 'mydomain@example.com'
*       ));
* 
* //Send An Email
* Mail::sendEmail(array(
* 	'receiver' => 'jane@example.com',
* 	'sender'=>'jon@example.com',
* 	'subject'=>'Hello World'
* 	'message'=>'Dropping a line, saying hello'
* ));
* ```
 * 
* @package util 
*/
class Mail {
	
	use StaticObject;
	
	/**
	 * The SMTP Host 
	 */
	protected static $_smtp_host;
	
	/**
	 * The login user for SMTP
	 */
	protected static $_smtp_username;
	
	/**
	 * The login password for SMTP
	 */
	protected static $_smtp_password;
	
	/**
	 * The port for SMTP. Normally is 587
	 */
	protected static $_smtp_port;
	
	/**
	 * Which mailer to use, default is php, other option is smtp
	 */
	protected static $_mailer = 'php';
	
	/**
	 * The default from email to when sending emails
	 */
	protected static $_default_sender = '';
	
	/**
	 * Protects the class from being initalized multiple times via init
	 */
	protected static $_initialized = false;
	
	/**
	 * Configure the mail class with default options to be used.
	 * 
	 * @param array $config The options that will configure the class
	 * 		-'smtp_host' _string_: The stmp host for sending email over smtp
	 * 		-'smtp_username' _string_: The username for sending email using smtp
	 * 		-'smtp_password' _string_: The password for the user sending email over smtp
	 * 		-'smtp_port' _int_: The port for sending email over smtp
	 * 		-'mailer' _string_: The email client used for sending email. Default is 'php' but if the email
	 * 		client is smtp, the value should be 'smtp'
	 * 		-'default_sender '_string_: The email address that will be used to send an email if none is defined.
	 * 
	 * @return void
	 * @access public
	 */
	public static function init($config = array()) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $config);
		
		if(!self::$_initialized) {
			$config = self::_applyFilter( get_class(), __FUNCTION__ , $config , array('event'=>'args'));
			
			$defaults = array(
				'smtp_host' => '',
				'smtp_username' => '',
				'smtp_password' => '',
				'smtp_port' => '',
				'mailer' => 'php',
				'default_sender' => ''
			);
			
			$config += $defaults;
			
			self::$_smtp_host= $config['smtp_host'];
			self::$_smtp_username= $config['smtp_username'];
			self::$_smtp_password= $config['smtp_password'];
			self::$_smtp_port= $config['smtp_port'];
			self::$_mailer = $config['mailer'];
			self::$_default_sender = $config['default_sender'];
			
			self::_notify(get_class().'::'.__FUNCTION__, $config);
			
			self::$_initialized = true;
		}
	}
	
	/**
	 * Sends an email to a location. The is function uses the optionsset in the configuration file for determing how the
	 * email will be sent.
	 * 
	 * @param array $args Arguements that define how the email be will be sent
	 * 		-'receiver' _string_: The email address of the user that will recieve the email
	 * 		-'sender' _string_: The email address of the user that sent the email
	 * 		-'subject' _string_: The subject of the email that is being sent
	 * 		-'message' _string_: The message in the email
	 * 		-'carboncopy' _string_: Email addresses to send a carbon copy too. Optional.
	 * 		-'blindcopy' _string_: Email address to send a blind copy too. Optional.
	 * 		-'attachment' _string_: The location of a file to be attached to the email. Optional.
	 * 		-'reply_to' _string_: The email to send a reply too. Optional
	 * 		-'message_id' _string_: The header for the message id. Optional.
	 * 		-'errors_to' _string_: The email addrss to send errors to. Optional.
	 * 		-'return_path': _string_: Set the return path. Optional.
	 * 
	 * @return void
	 * @access public		
	 */
	public static function sendEmail($args=array() ) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$args = self::_applyFilter( get_class(), __FUNCTION__ , $args , array('event'=>'args'));
		
		if(self::$_mailer == 'smtp'){
			self::sendEMailSMTP($args);
		}
		else{
			self::sendEmailPHP($args );
		}
		
		self::_notify(get_class().'::'.__FUNCTION__, $args);
	}
	
	
	/**
	 * Sends an email to a location. The is function uses the optionsset in the configuration file for determing how the
	 * email will be sent.
	 * 
	 * @param array $args Arguements that define how the email be will be sent
	 * 		-'receiver' _string_: The email address of the user that will recieve the email
	 * 		-'sender' _string_: The email address of the user that sent the email
	 * 		-'subject' _string_: The subject of the email that is being sent
	 * 		-'message' _string_: The message in the email
	 * 		-'carboncopy' _string_: Email addresses to send a carbon copy too. Optional.
	 * 		-'blindcopy' _string_: Email address to send a blind copy too. Optional.
	 * 		-'attachment' _string_: The location of a file to be attached to the email. Optional.
	 * 		-'reply_to' _string_: The email to send a reply too. Optional
	 * 		-'message_id' _string_: The header for the message id. Optional.
	 * 		-'errors_to' _string_: The email addrss to send errors to. Optional.
	 * 		-'return_path': _string_: Set the return path. Optional.
	 * 
	 * @return void
	 * @access public		
	 */
	public static function sendEmailPHP($args=array()) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$args += self::getEmailDefaults();
		$args = self::_applyFilter( get_class(), __FUNCTION__ , $args , array('event'=>'args'));
		
		$eol = $args['eol'];
		$to = $args['receiver'];
		$subject = $args['subject'];
		$sender = $args['sender'];
		$message = $args['message'];
		$carboncopy = $args['carboncopy'];
		$blindcopy = $args['blindcopy'];
		$attachment= $args['attachment'];
		$text_message = $args['text_message'];
		$html_message = $args['html_message'];
			
		$section_seperator = md5(date('r', time()));
		$headers = "From: $sender\r\n";
		
		if(!empty($reply_to)){
			$headers .= "Reply-To: $reply_to";
		} else {
			$headers .= "Reply-To: $sender";
		}
		
		if(!empty($carboncopy)){
			$headers .= "\r\nCc: $carboncopy" ;
		}
		
		if(!empty($blindcopy)){
			$headers .= "\r\nBcc: $blindcopy";
		}
		
		if(!empty($errors_to)){
			$headers .= "\r\nErrors-To: $errors_to";
		}
		
		if(!empty($return_path)){
			$headers .= "\r\nReturn-Path: $return_path";
		}
		
		if(!empty($message_id)){
			$headers .= "\r\nMessage-ID: $message_id";
		}
		
		$headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-".$section_seperator."\"";
		
		if(!empty($attachment)){
			$attach = chunk_split(base64_encode(file_get_contents($attachment)));
			$filename=basename($attachment);
		}
		
		ob_start(); 
	?>
--PHP-mixed-<?php echo $section_seperator; ?> 
Content-Type: multipart/alternative; boundary="PHP-alt-<?php echo $section_seperator; ?>"

--PHP-alt-<?php echo $section_seperator; ?> 
Content-Type: text/plain; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

<?php 

if(empty($text_message)){
	echo strip_tags($message);
} else {
	echo $text_message;
}
?>

--PHP-alt-<?php echo $section_seperator; ?> 
Content-Type: text/html; charset="iso-8859-1"
Content-Transfer-Encoding: 7bit

<?php
if(empty($html_message)){
	echo $message;
} else {
	echo $html_message;
}

?>

--PHP-alt-<?php echo $section_seperator; ?>--

<?php if(!empty($file)){ ?>
--PHP-mixed-<?php echo $section_seperator; ?> 
Content-Type: application/zip; name="<?php echo $filename; ?>" 
Content-Transfer-Encoding: base64 
Content-Disposition: attachment 

<?php echo $attachment; ?>
--PHP-mixed-<?php echo $section_seperator; ?>--

<?php }//end if not empty file ?>

<?php

		$message = ob_get_clean();
		mail( $to, $subject, $message, $headers );
		self::_notify(get_class().'::'.__FUNCTION__, $args);

	}//end mailSingleUser
	
	/**
	 * Sends an email to a location. The is function uses the optionsset in the configuration file for determing how the
	 * email will be sent.
	 * 
	 * @param array $args Arguements that define how the email be will be sent
	 * 		-'receiver' _string_: The email address of the user that will recieve the email
	 * 		-'sender' _string_: The email address of the user that sent the email
	 * 		-'subject' _string_: The subject of the email that is being sent
	 * 		-'message' _string_: The message in the email
	 * 		-'carboncopy' _string_: Email addresses to send a carbon copy too. Optional.
	 * 		-'blindcopy' _string_: Email address to send a blind copy too. Optional.
	 * 		-'attachment' _string_: The location of a file to be attached to the email. Optional.
	 * 		-'reply_to' _string_: The email to send a reply too. Optional
	 * 		-'message_id' _string_: The header for the message id. Optional.
	 * 		-'errors_to' _string_: The email addrss to send errors to. Optional.
	 * 		-'return_path': _string_: Set the return path. Optional.
	 * 		-'smtp_username' _string_: The user name for the host
	 * 		-'smtp_password' _string_: The password for the smtp user
	 * 		-'smtp_host' _string_: The hast the SMTP resides at
	 * 		-'smtp_port' _string_: The port used to access the SMTP.
	 * 
	 * @return void
	 * @access public
	 * @todo allow for multiple attachments		
	 */
	public static function sendEMailSMTP($args = array()) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
	 	
		$args += self::getEmailDefaults();
		$args = self::_applyFilter( get_class(), __FUNCTION__ , $args , array('event'=>'args'));
		
		if(is_array($args)){
			
			extract($args);
			
			if(empty($smtp_username)){
				$smtp_username=self::$_smtp_username;
			}
			
			if(empty($smtp_password)){
				$smtp_password=self::$_smtp_password;
			}
			
			if(empty($smtp_host)){
				$smtp_host=self::$_smtp_host;
			}
			
			if(empty($smtp_port)){
				$smtp_port=self::$_smtp_port;
			}
			
			require_once "Mail.php";
			require_once 'Mail/mime.php';
			
			$stmp_info= array (
				'host' => $smtp_host,
           		'port' => $smtp_port,
            	'auth' => true,
            	'username' => $smtp_username,
            	'password' => $smtp_password
			);
			
			$headers = array (
				'From' => $sender,
          		'To' => $receiver,
          		'Subject' => $subject
			);
			  
			
			if(!empty($args['carboncopy'])){
				$headers['Cc']=$args['carboncopy'];
				$receiver.=','.$args['carboncopy'];
			}
			
			if(!empty($args['blindcopy'])){
				$headers['Bcc']=$args['blindcopy'];
				$receiver.=','.$args['blindcopy'];
			}
			
			if(!empty($args['reply_to'])){
				$headers['Reply-To']=$args['reply_to'];
			}
			
			if(!empty($args['return_path'])){
				$headers['Return-Path']=$args['return_path'];
			}
			
			if(!empty($args['errors_to'])){
				$headers['Errors-To']=$args['errors_to'];
			}

			if(!empty($args['message_id'])){
				$headers['Message-ID']=$args['message_id'];
			}
		
			if(empty($text_message)){
				$text = strip_tags($message);
			} else {
				$text = $text_message;
			}
			
			if(empty($html_message)){
				$html = $message;
			} else {
				$html= $html_message;
			}
			
			$mime = new \Mail_mime("\n");
			$mime->setTXTBody($text);
			$mime->setHTMLBody($html);
			if(!empty($attachment)){
				if(is_array($attachment)) {
					foreach($attachment as $file) {
						if(file_exists($file))
							$mime->addAttachment($file , FileManager::getFileMimeType($file));
					}//end foreach
				} else {
					$mime->addAttachment($attachment, FileManager::getFileMimeType($attachment));
				}
			}
			
			$body = $mime->get();
			$headers = $mime->headers($headers);
			
			$smtp = \Mail::factory('smtp', $stmp_info);
			$mail = $smtp->send($receiver, $headers, $body);
			self::_notify(get_class().'::'.__FUNCTION__, $args);
			
			return $mail;
		}
	
	}//end sendEmailPHPSMTP
	
	/**
	 * Retrieves the default values that should go out with each email
	 * 
	 * @return array
	 */
	private static function getEmailDefaults() {
		$defaults=array(
			'receiver'=>'',
			'sender'=>self::$_default_sender,
			'carboncopy'=>'',
			'blindcopy'=>'',
			'reply_to'=>'',
			'attachment'=>'',
			'attachment_name'=>'',
			'message'=>'',
			'html_message'=>'',
			'text_message'=>'',
			'errors_to'=>'',
			'return_path'=>'',
			'message_id'=>'',
			'eol'=>"\r\n"
		);
		
		return $defaults;
	}
	
}//end class
?>
