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
	 * 		-'carboncopy' _string_: Email addresses to send a carbon copy too
	 * 		-'blindcopy' _string_: Email address to send a blind copy too
	 * 		-'file' _string_: The location of a file to be attached to the email
	 * 
	 * @return void
	 * @access public		
	 */
	public static function sendEmail($args=array() ){
		
		$config=pv_getSiteEmailConfiguration();
		if($config['mailer']=='smtp'){
			self::sendEMailSMTP($args);
		}
		else{
			self::sendEmailPHP($args );
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
	 * 		-'carboncopy' _string_: Email addresses to send a carbon copy too
	 * 		-'blindcopy' _string_: Email address to send a blind copy too
	 * 		-'file' _string_: The location of a file to be attached to the email
	 * 
	 * @return void
	 * @access public		
	 */
	public static function sendEmailPHP($args=array()){
		
		$args += self::getEmailDefaults();
		
		$to = $args['receiver'];
		$subject = $args['subject'];
		$sender = $args['sender'];
		$message = $args['message'];
		$carboncopy = $args['carboncopy'];
		$blindcopy = $args['blindcopy'];
		$file = $args['file'];
		$text_message = $args['text_message'];
		$html_message = $args['html_message'];
			
		$section_seperator = md5(date('r', time()));
		$headers = "From: $sender\r\nReply-To: $sender";
		
		if(!empty($carboncopy)){
			$headers .= "\r\nCc: $carboncopy" ;
		}
		if(!empty($blindcopy)){
			$headers .= "\r\nBcc: $blindcopy";
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
	 * 		-'carboncopy' _string_: Email addresses to send a carbon copy too
	 * 		-'blindcopy' _string_: Email address to send a blind copy too
	 * 		-'attachment' _string_: The location of a file to be attached to the email
	 * 		-'smtp_username' _string_: The user name for the host
	 * 		-'smtp_password' _string_: The password for the smtp user
	 * 		-'smtp_host' _string_: The hast the SMTP resides at
	 * 		-'smtp_port' _string_: The port used to access the SMTP.
	 * 
	 * @return void
	 * @access public
	 * @todo allow for multiple attachments		
	 */
	public static function sendEMailSMTP($args = array()){
	 	
		$args += self::getEmailDefaults();
		
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
		
			if(empty($text_message)){
				$text = strip_tags($message);
			}
			else{
				$text = $text_message;
			}
			
			
			if(empty($html_message)){
				$html = $message;
			}
			else{
				$html= $html_message;
			}
			
			$mime = new Mail_mime("\n");
			$mime->setTXTBody($text);
			$mime->setHTMLBody($html);
			if(!empty($attachment)){
				$mime->addAttachment($attachment, PVFileManager::getFileMimeType($attachment));
			}
			
			$body = $mime->get();
			$headers = $mime->headers($headers);
			
			$smtp = Mail::factory('smtp', $stmp_info);
			$mail = $smtp->send($receiver, $headers, $body);
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
			'text_message'=>''
		);
		
		return $defaults;
	}
	
	
	
}//end class

//sendEmailPHP("devin.dixon22@gmail.com", "contact@prtrack.net", "Test Email", "Just testing", 'dsd7872@uncw.edu', 'devin.dixon@my-lan.us');
?>