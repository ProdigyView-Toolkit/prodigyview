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
class PVHtml extends PVStaticObject {
	
	
	public static function image($location, $options=array()){
		
		$defaults=array('alt'=>'');
		$options += $defaults;
		
		$image='<img ';
		
		if(PVValidator::isValidUrl($location)){
			$image.='src="'.$location.'" ';
		} else {
			$image.='src="'.PVRouter::url(PV_IMAGE.$location).'" ';
		}
		
		if(!empty($options['image_width'])){
			$image.='width="'.$options['image_width'].'" ';
		}
		
		if(!empty($options['image_height'])){
			$image.='width="'.$options['image_height'].'" ';
		}
		
		if(!empty($options['width'])){
			$image.='width="'.$options['width'].'" ';
		}
		
		if(!empty($options['height'])){
			$image.='width="'.$options['height'].'" ';
		}
		
		if(!empty($options['alt'])){
			$image.='alt="'.$options['alt'].'" ';
		}
		
		if(!empty($options['longdesc'])){
			$image.='longdesc="'.$options['longdesc'].'" ';
		}
		
		if(!empty($options['usemap'])){
			$image.='usemap="'.$options['usemap'].'" ';
		}
		
		$image.=self::getStandardAttributes($options);
		$image.=self::getEventAttributes($options);
		
		$image.='/>';
		
		return $image;
		
	}//end getImageDisplay
	
	
	public static function time($time, $options=array()){
		
		$tag='<time ';
		
		if(!empty($options['datetime'])){
			$tag.='datetime="'.$options['datetime'].'" ';
		}
		
		if(!empty($options['pubdate'])){
			$tag.='pubdate="'.$options['pubdate'].'" ';
		}
		
		$tag.=self::getStandardAttributes($options);
		$tag.=self::getEventAttributes($options);
		
		$tag.='>'.$time.'</time>';
		
		return $tag;
		
	}//end getImageDisplay
	
	
	public static function alink($title, $url, $options=array()){
		
		$link='<a ';
		
		if(is_array($url) || !PVValidator::isValidUrl($url))
			$link.='href="'.PVRouter::url($url).'" ';
		else
			$link.='href="'.$url.'" ';
		
		if(!empty($options['charset'])){
			$link.='charset="'.$options['charset'].'" ';
		}
		
		if(!empty($options['hreflang'])){
			$link.='hreflang="'.$options['hreflang'].'" ';
		}
		
		if(!empty($options['name'])){
			$link.='name="'.$options['name'].'" ';
		}
		
		if(!empty($options['rel'])){
			$link.='rel="'.$options['rel'].'" ';
		}
		
		if(!empty($options['rev'])){
			$link.='rev="'.$options['rev'].'" ';
		}
		
		if(!empty($options['shape'])){
			$link.='shape="'.$options['shape'].'" ';
		}
		
		if(!empty($options['target'])){
			$link.='target="'.$options['target'].'" ';
		}
		
		$link.=self::getStandardAttributes($options);
		$link.=self::getEventAttributes($options);
		
		$link.='>'.$title.'</a>';
		
		return $link;
		
	}//end getImageDisplay
	
	
	public static function link($url, $options=array()){
		
		$link='<link ';
		
		if(is_array($url) || !PVValidator::isValidUrl($url))
			$link.='href="'.PVRouter::url($url).'" ';
		else
			$link.='href="'.$url.'" ';
		
		if(!empty($options['hreflang'])){
			$link.='hreflang="'.$options['hreflang'].'" ';
		}
		
		if(!empty($options['name'])){
			$link.='name="'.$options['name'].'" ';
		}
		
		if(!empty($options['media'])){
			$link.='media="'.$options['media'].'" ';
		}

		if(!empty($options['rel'])){
			$link.='rel="'.$options['rel'].'" ';
		}
		
		if(!empty($options['sizes'])){
			$link.='sizes="'.$options['sizes'].'" ';
		}
		
		$link.=self::getStandardAttributes($options);
		
		$link.='/>';
		
		return $link;
		
	}//end getImageDisplay
	
	public static function meta($name='', $options=array()){
		
		$link='<meta ';
		
		if(!empty($options['charset'])){
			$link.='charset="'.$options['charset'].'" ';
		}
		
		if(!empty($options['content'])){
			$link.='content="'.$options['content'].'" ';
		}
		
		if(!empty($name)){
			$link.='name="'.$name.'" ';
		}
		
		if(!empty($options['http-equiv'])){
			$link.='http-equiv="'.$options['http-equiv'].'" ';
		}
		
		$link.=self::getStandardAttributes($options);
		
		$link.='/>';
		
		return $link;
	}//end meta
	
	public static function video($src='', $options=array()){
		$defaults=array('controls'=>'controls', 'error'=>'Sorry but your browser cannot play this HTML5 Element');
		$options += $defaults;
		
		$video='<video ';
		
		if(!empty($src)){
			$video.='src="'.self::videoContentURL($src).'" ';
		}
		
		if(!empty($options['width'])){
			$video.='width="'.$options['width'].'" ';
		}
		
		if(!empty($options['width'])){
			$video.='width="'.$options['width'].'" ';
		}
		
		if(!empty($options['controls'])){
			$video.='controls="'.$options['controls'].'" ';
		}
		
		if(!empty($options['audio'])){
			$video.='audio="'.$options['audio'].'" ';
		}
		
		if(!empty($options['autoplay'])){
			$video.='autoplay="'.$options['autoplay'].'" ';
		}
		
		if(!empty($options['loop'])){
			$video.='loop="'.$options['loop'].'" ';
		}
		
		if(!empty($options['poster'])){
			$video.='poster="'.$options['poster'].'" ';
		}

		if(!empty($options['preload'])){
			$video.='preload="'.$options['preload'].'" ';
		}
		
		$video.=self::getMediaEventAttributes($options);
		$video.='>';
		
		if(!empty($options['mp4_file'])){
			$video.='<source src="'.self::videoContentURL($options['mp4_file']).'" type="video/mp4" >';
		}
		
		if(!empty($options['ogv_file'])){
			$video.='<source src="'.self::videoContentURL($options['ogv_file']).'" type="video/ogg" >';
		}
		
		if(!empty($options['webm_file'])){
			$video.='<source src="'.self::videoContentURL($options['webm_file']).'" type="video/webm" >';
		}
		$video.=$options['error'];
		$video.='</video>';
		
		return $video;
	}//end getVideoDisplay
		
	public static function audio($src='', $options=array()){
		$defaults=array('controls'=>'controls', 'error'=>'Sorry but your browser cannot play this HTML5 Element');
		$options += $defaults;
		
		$audio='<audio ';
		
		if(!empty($src)){
			$audio.='src="'.self::audioContentURL($src).'" ';
		}
		
		if(!empty($options['controls'])){
			$audio.='controls="'.$options['controls'].'" ';
		}
		
		if(!empty($options['audio'])){
			$audio.='audio="'.$options['audio'].'" ';
		}
		
		if(!empty($options['autoplay'])){
			$audio.='autoplay="'.$options['autoplay'].'" ';
		}
		
		if(!empty($options['loop'])){
			$audio.='loop="'.$options['loop'].'" ';
		}

		if(!empty($options['preload'])){
			$audio.='preload="'.$options['preload'].'" ';
		}
		
		//$audio.=self::getMediaEventAttributes($options);
		$audio.='>';
		
		if(!empty($options['wav_file'])){
			$audio.='<source src="'.self::audioContentURL($options['wav_file']).'" type="audio/wav" >';
		}
		
		if(!empty($options['mp3_file'])){
			$audio.='<source src="'.self::audioContentURL($options['mp3_file']).'" type="audio/mpeg" >';
		}
		
		if(!empty($options['ogv_file'])){
			$audio.='<source src="'.self::audioContentURL($options['ogv_file']).'" type="audio/ogg" >';
		}
		$audio.=$options['error'];
		$audio.='</audio>';
		
		return $audio;
	}//end getVideoDisplay
	
	public static function div($data, $options=array()) {
		$tag='<div ';
		$tag.=self::getStandardAttributes($options);
		$tag.=self::getEventAttributes($options);
		$tag.='>'.$data.'</div>';
		
		return $tag;
	}
	
	public static function getStandardAttributes($attributes=array()){
		$return_attributes='';
		$accepted_attributes=array('class', 'id', 'dir', 'lang', 'style', 'title', 'xml:lang','accesskey', 'contenteditable', 'contextmenu', 'draggable', 'dropzone', 'hidden', 'spellcheck', 'title');
		
		foreach($attributes as $key => $attribute){
			if(in_array($key, $accepted_attributes) && !PVValidator::isInteger($key)){
				$return_attributes.=$key.'="'.$attribute.'" ';
			}
		}
		
		return $return_attributes;
	}
	
	public static function getEventAttributes($attributes=array()){
		$return_attributes='';
		$accepted_attributes=array('onabort', 'onclick', 'ondblclick', 'onmousedown', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onkeydown', 'onkeypress', 'onkeyup', 'onblur', 'onfocus', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onmousewheel', 'onscroll');
		
		foreach($attributes as $key => $attribute){
			if(in_array($key,$accepted_attributes) && !PVValidator::isInteger($key)){
				$return_attributes.=$key.'="'.$attribute.'" ';
			}
		}
		
		return $return_attributes;
	}

	public static function getMediaEventAttributes($attributes=array()){
		$return_attributes='';
		$accepted_attributes=array( 'oncanplay', 'oncanplaythrough', 'ondurationchange', 'onemptied', 'onended', 'onerror', 'onloadeddata', 'onloadedmetadata', 'onloadstart', 'onpause', 'onplay', 'onplaying', 'onprogress', 'onratechange', 'onreadystatechange', 'onseeked', 'onseeking', 'onstalled', 'onsuspend', 'ontimeupdate', 'onvolumechange', 'onwaiting');
		
		foreach($attributes as $key => $attribute){
			if(in_array($key,$accepted_attributes) && !PVValidator::isInteger($key)){
				$return_attributes.=$key.'="'.$attribute.'" ';
			}
		}
		
		return $return_attributes;
	}

	public static function getWindowAttributes($attibutes){
		$accepted_attributes=array( 'onafterprint', 'onbeforeprint', 'ondurationchange', 'onbeforeonload', 'onblur', 'onerror', 'onfocus', 'onhaschange', 'onload', 'onmessage', 'onoffline', 'ononline', 'onpageshow', 'onpopstate', 'onredo', 'onresize', 'onstorage', 'onundo', 'onunload');
		
		foreach($attributes as $key => $attribute){
			if(in_array($key,$accepted_attributes) && !PVValidator::isInteger($key)){
				$return_attributes.=$key.'="'.$attribute.'" ';
			}
		}
		
		return $return_attributes;
	}

	private static function audioContentURL($url){
		if(!PVValidator::isValidURL($url)){
			$url=PV_AUDIO.$url;
		}
		return PVRouter::url($url);
	}
	
	private static function videoContentURL($url){
		if(!PVValidator::isValidURL($url)){
			$url=PV_VIDEO.$url;
		}
		return PVRouter::url($url);
	}
}//end fields