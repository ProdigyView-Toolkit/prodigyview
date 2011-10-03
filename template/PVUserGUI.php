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

class PVUsersGUI{

	
	
	public static function printUserImage($user_id, $img_class='', $img_id=''){
		
		$user_id=PVDatabase::makeSafe($user_id);	
		$image_url='';
		$query="SELECT user_image FROM ".pv_getLoginTableName()." WHERE user_id='$user_id'";
		
		$result=PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		
		if(empty($row['user_image'])){
			echo '<img src="apps/UserManager/default_user.png" />';
		}
		else{
			echo '<img src="'.$row['user_image'].'" />';
		}
	
		return $image_url;
	
	}//end getUserImageUrl
	
	public static function printUserImageThumb($user_id, $img_class='', $img_id=''){
		
		$user_id=PVDatabase::makeSafe($user_id);	
		$image_url='';
		
		$query="SELECT user_image_thumb FROM ".pv_getLoginTableName()." WHERE user_id='$user_id'";
		
		$result=PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
	
		if(empty($row['user_image_thumb'])){
			echo '<img src="apps/UserManager/default_user.png" />';
		}
		else{
			echo '<img src="'.$row['user_image_thumb'].'" />';
		}
	}//getUserImageThumbUrl
	
}