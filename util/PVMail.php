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
class PVMail extends PVStaticObject {
	
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
		
		$config=pv_getSiteEmailConfiguration();
		if($config['mailer']=='smtp'){
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
			
			$config=pv_getSiteEmailConfiguration();
			
			if(empty($smtp_username)){
				$smtp_username=$config['smtp_username'];
			}
			
			if(empty($smtp_password)){
				$smtp_password=$config['smtp_password'];
			}
			
			if(empty($smtp_host)){
				$smtp_host=$config['smtp_host'];
			}
			
			if(empty($smtp_port)){
				$smtp_port=$config['smtp_port'];
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
			
			$mime = new Mail_mime("\n");
			$mime->setTXTBody($text);
			$mime->setHTMLBody($html);
			if(!empty($attachment)){
				if(is_array($attachment)) {
					foreach($attachment as $file) {
						if(file_exists($file))
							$mime->addAttachment($file , PVFileManager::getFileMimeType($file));
					}//end foreach
				} else {
					$mime->addAttachment($attachment, PVFileManager::getFileMimeType($attachment));
				}
			}
			
			$body = $mime->get();
			$headers = $mime->headers($headers);
			
			$smtp = Mail::factory('smtp', $stmp_info);
			$mail = $smtp->send($receiver, $headers, $body);
			self::_notify(get_class().'::'.__FUNCTION__, $args);
		}
	
	}//end sendEmailPHPSMTP
	
	private static function getEmailDefaults() {
		$defaults=array(
			'receiver'=>'',
			'sender'=>'',
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