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

class PVMathematics extends PVStaticObject {
	
	
	function PVMathematics(){
		
	}
	
	
	/*****Time Functions*****/
	
	public static function convertTimeIntoSeconds($days=0, $hours=0, $minutes=0, $seconds=0){
		  
		  $total_seconds=0;
		  
		  if(empty($days) || !PVValidator::isInteger($days)){
			  $days=0;
		  }
		  else{
			  $days=86400*$days;
			 
		  }
		  
		  if(empty($hours) || !PVValidator::isInteger($hours)){
			  $hours=0;
		  }
		  else{
			  $hours=3600*$hours;
			 
		  }
		  
		  if(empty($minutes) || !PVValidator::isInteger($minutes)){
			  $minutes=0;
		  }
		  else{
			  $minutes=60*$minutes;
			 
		  }
		  
		  
		  
		  if(empty($seconds) || !PVValidator::isInteger($seconds)){
			  $seconds=0;
		  }
		  
		  return $total_seconds=$days+$hours+$minutes+$seconds;
		  
	  }//end convertTimeIntoSeconds
	  
	public static function convertSecondsToHours($seconds){
		 
		return $seconds/3600;
	}
	  

	public static function convertSecondsToMinutes($seconds){
		 
		return $seconds/60;
	}
	
	public static function convertSecondsToDays($seconds){
		 
		return $seconds/60;
	}
	
	public static function convertSecondsIntoElapsedTime($seconds){
		
		$days = floor($seconds / 86400); 
		$hours = floor( ( $seconds % 86400) / 3600) ; 
		$minutes= floor ( ( $seconds % 3600) / 60) ; 
		$second=floor($seconds % 60) ;
		
		if(empty($days)){
			$days='00';	
		}
		else if(strlen($days)==1){
			$days='0'.$days;	
		}
		
		if(empty($hours)){
			$hours='00';	
		}
		else if(strlen($hours)==1){
			$hours='0'.$hours;	
		}
		
		
		if(empty($minutes)){
			$minutes='00';	
		}
		else if(strlen($minutes)==1){
			$minutes='0'.$minutes;	
		}
		
		if(empty($second)){
			$second='00';	
		}
		else if(strlen($second)==1){
			$second='0'.$second;	
		}
		
		return $days.':'.$hours.':'.$minutes.':'.$second;
		
	}//end convert
	
}//end class
