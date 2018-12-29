<?php

namespace prodigyview\template;

use prodigyview\design\StaticObject;
use prodigyview\util\Validator;
use prodigyview\network\Router;

if (!defined('PV_IMAGE')) {
	define('PV_IMAGE', '');
}

if (!defined('PV_VIDEO')) {
	define('PV_VIDEO', '');
}

if (!defined('PV_AUDIO')) {
	define('PV_AUDIO', '');
}

/**
 * HTML is a class designed for generating HTML elements to display to the user.
 *
 * The class takes in basic HTML forms with options. The functionality can be used with dynamic form
 * generation tools.
 *
 * Example:
 * ```php
 * //Create array of links
 * $links = array('Google', 'http://www.google.com', 'Facebook', 'http://www.facebook.com');
 *
 * $html = '';
 * $li = '';
 *
 * foreach($links as $key => $value):
 * 	 $li .= Html::li(Html::ahref($key, $value));
 * endforeach;
 *
 * $html = Html::ul($li);
 *
 * $html = Html::div($html, array('class' => 'container'));
 * echo $html;
 *
 * //The following will be printed
 * <div class="container">
 * 	 <ul>
 * 		<li><a href="http://www.google.com">Google</a><li>
 * 		<li><a href="http://www.facebook.com">Facebook</a><li>
 * 	 </ul>
 * </div>
 * ```
 *
 * @package template
 * @todo Add more HTML5 form elements
 */
class Html {
	
	use StaticObject;

	/**
	 * Displays an image in the <img /> tags. By default the location can be either an image
	 * in an url or the image location referenced will be from the PV_IMAGE define set.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $location Either a url of the image or the path to the image in the PV_IMAGE define
	 * location
	 * @param array $options Attributes that can be added to the image. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 * 				-'image_width' _double_: The width of the image
	 * 				-'image_height' _double_: The height of the image
	 * 				-'width' _double_: The width of the image
	 * 				-'height' _double_: The height of the image
	 * 				-'alt' _string_: Value to go in the alt tag of the image
	 * 				-'longdesc' _string_: Value to go in the longdesc tag of the image
	 * 				-'usemap' _string_: Value to go in the usemap tag of an image
	 *
	 * @return string $image The image tag returned as a string
	 * @access public
	 */
	public static function image($location, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $location, $options);

		$defaults = array(
			'alt' => '',
			'append_location' => true
		);
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'location' => $location,
			'options' => $options
		), array('event' => 'args'));
		
		$location = $filtered['location'];
		$options = $filtered['options'];

		$image = '<img ';

		if (Validator::isValidUrl($location)) {
			$image .= 'src="' . $location . '" ';
		} else if ($options['append_location']) {
			$image .= 'src="' . Router::url(PV_IMAGE . $location) . '" ';
		} else {
			$image .= 'src="' . Router::url($location) . '" ';
		}

		if (!empty($options['image_width'])) {
			$image .= 'width="' . $options['image_width'] . '" ';
		}

		if (!empty($options['image_height'])) {
			$image .= 'width="' . $options['image_height'] . '" ';
		}

		if (!empty($options['width'])) {
			$image .= 'width="' . $options['width'] . '" ';
		}

		if (!empty($options['height'])) {
			$image .= 'width="' . $options['height'] . '" ';
		}

		if (isset($options['alt'])) {
			$image .= 'alt="' . $options['alt'] . '" ';
		}

		if (!empty($options['longdesc'])) {
			$image .= 'longdesc="' . $options['longdesc'] . '" ';
		}

		if (!empty($options['usemap'])) {
			$image .= 'usemap="' . $options['usemap'] . '" ';
		}

		$image .= self::getStandardAttributes($options);
		$image .= self::getEventAttributes($options);

		$image .= '/>';

		self::_notify(get_class() . '::' . __FUNCTION__, $image, $location, $options);
		$image = self::_applyFilter(get_class(), __FUNCTION__, $image, array('event' => 'return'));

		return $image;
	}//end getImageDisplay

	/**
	 * Display a time passed in the HTML5 time field.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $time A time value
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 * 				-'datetime' _string_: Tags to go in the datetime tags
	 * 				-'pubdate' _string_: Tags to go in the pubdate
	 *
	 * @return string $time The time taged returned a time
	 * @access public
	 */
	public static function time($time, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $time, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'time' => $time,
			'options' => $options
		), array('event' => 'args'));
		
		$time = $filtered['time'];
		$options = $filtered['options'];

		$tag = '<time ';

		if (!empty($options['datetime'])) {
			$tag .= 'datetime="' . $options['datetime'] . '" ';
		}

		if (!empty($options['pubdate'])) {
			$tag .= 'pubdate="' . $options['pubdate'] . '" ';
		}

		$tag .= self::getStandardAttributes($options);
		$tag .= self::getEventAttributes($options);

		$tag .= '>' . $time . '</time>';

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $time, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;

	}//end getImageDisplay

	/**
	 * Generate an html element for displaying an iframe.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $src The source of the iframe
	 * @param string $data The data to inside the iframe take
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 * 				-'frameborder' _string_: Tags to go in the frameborder
	 * 				-'marginheight' _string_: Tags to go in the marginheight
	 * 				-'marginwidth' _string_: Tags to go in the marginweidth
	 * 				-'scrolling' _string_: Tags to go in the scrolling
	 * 				-'longdesc' _string_: Tags to go in the longdesc
	 *
	 * @return string $iframe The iframe taged returned a time
	 * @access public
	 */
	public static function iframe($src, $data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $time, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'src' => $src,
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$src = $filtered['src'];
		$data = $filtered['data'];
		$options = $filtered['options'];

		$tag = '<iframe src="' . $src . '" ';

		if (isset($options['frameborder']) && !empty($options['frameborder'])) {
			$tag .= 'frameborder="' . $options['frameborder'] . '" ';
		}

		if (isset($options['marginheight']) && !empty($options['marginheight'])) {
			$tag .= 'marginheight="' . $options['marginheight'] . '" ';
		}

		if (isset($options['marginwidth']) && !empty($options['marginwidth'])) {
			$tag .= 'marginwidth="' . $options['marginwidth'] . '" ';
		}

		if (isset($options['scrolling']) && !empty($options['scrolling'])) {
			$tag .= 'scrolling="' . $options['scrolling'] . '" ';
		}

		if (isset($options['longdesc']) && !empty($options['longdesc'])) {
			$tag .= 'longdesc="' . $options['longdesc'] . '" ';
		}

		$tag .= self::getStandardAttributes($options);
		$tag .= self::getEventAttributes($options);

		$tag .= '>' . $data . '</iframe>';

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $src, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;

	}//end getImageDisplay

	/**
	 * Display an ahref links
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 * @see Router::url()
	 *
	 * @param string $title The title of link that the user will see
	 * @param mixed $url A url that the link will point too. If the url is an array or not a valid url,
	 * it will be passed to Router::url.
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 * 				-'charset' _string_: Value to go in the charset tag.
	 * 				-'hreflang' _string_: Value to go in the hreflang tag.
	 * 				-'name' _string_: Value tog go in the name attribute
	 * 				-'rel' _string_: Value to go in the rel attribute
	 * 				-'shape' _string_: Value to o in the shape attribute
	 * 				-'target' _string_: Value to in the target attribute
	 *
	 * @return string $link The link tag returned as a string
	 * @access public
	 */
	public static function alink($title, $url, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $title, $url, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'title' => $title,
			'url' => $url,
			'options' => $options
		), array('event' => 'args'));
		
		$title = $filtered['title'];
		$url = $filtered['url'];
		$options = $filtered['options'];

		$link = '<a ';

		if (is_array($url) || !Validator::isValidUrl($url))
			$link .= 'href="' . Router::url($url) . '" ';
		else
			$link .= 'href="' . $url . '" ';

		if (!empty($options['charset'])) {
			$link .= 'charset="' . $options['charset'] . '" ';
		}

		if (!empty($options['hreflang'])) {
			$link .= 'hreflang="' . $options['hreflang'] . '" ';
		}

		if (!empty($options['name'])) {
			$link .= 'name="' . $options['name'] . '" ';
		}

		if (!empty($options['rel'])) {
			$link .= 'rel="' . $options['rel'] . '" ';
		}

		if (!empty($options['rev'])) {
			$link .= 'rev="' . $options['rev'] . '" ';
		}

		if (!empty($options['shape'])) {
			$link .= 'shape="' . $options['shape'] . '" ';
		}

		if (!empty($options['target'])) {
			$link .= 'target="' . $options['target'] . '" ';
		}

		$link .= self::getStandardAttributes($options);
		$link .= self::getEventAttributes($options);

		$link .= '>' . $title . '</a>';

		self::_notify(get_class() . '::' . __FUNCTION__, $link, $title, $url, $options);
		$link = self::_applyFilter(get_class(), __FUNCTION__, $link, array('event' => 'return'));

		return $link;

	}//end getImageDisplay

	/**
	 * Display a link
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 * @see Router::url()
	 *
	 * @param mixed $url A url that the link will point too. If the url is an array or not a valid url,
	 * it will be passed to Router::url.
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 * 				-'hreflang' _string_: Value to go in the hreflang tag.
	 * 				-'name' _string_: Value tog go in the name attribute
	 * 				-'rel' _string_: Value to go in the rel attribute
	 * 				-'media' _string_: Value to go in the media attribute.
	 * 				-'sizes' _string_: Value to in the sizes attribute
	 *
	 * @return string $link The link tag returned as a string
	 * @access public
	 */
	public static function link($url, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $url, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'url' => $url,
			'options' => $options
		), array('event' => 'args'));
		$url = $filtered['url'];
		$options = $filtered['options'];

		$link = '<link ';

		if (is_array($url) || !Validator::isValidUrl($url))
			$link .= 'href="' . Router::url($url) . '" ';
		else
			$link .= 'href="' . $url . '" ';

		if (!empty($options['hreflang'])) {
			$link .= 'hreflang="' . $options['hreflang'] . '" ';
		}

		if (!empty($options['name'])) {
			$link .= 'name="' . $options['name'] . '" ';
		}

		if (!empty($options['media'])) {
			$link .= 'media="' . $options['media'] . '" ';
		}

		if (!empty($options['rel'])) {
			$link .= 'rel="' . $options['rel'] . '" ';
		}

		if (!empty($options['type'])) {
			$link .= 'type="' . $options['type'] . '" ';
		}

		if (!empty($options['sizes'])) {
			$link .= 'sizes="' . $options['sizes'] . '" ';
		}

		$link .= self::getStandardAttributes($options);

		$link .= '/>';

		self::_notify(get_class() . '::' . __FUNCTION__, $link, $url, $options);
		$link = self::_applyFilter(get_class(), __FUNCTION__, $link, array('event' => 'return'));

		return $link;

	}//end getImageDisplay

	/**
	 * Generate a meta tag.
	 *
	 * @see self::getStandardAttributes()
	 *
	 * @param string $name The name of the meta tag being generated
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 * 				-'charset' _string_: Value to go in the charset tag.
	 * 				-'content' _string_: Value to go in the content attribute.
	 * 				-'name' _string_: Value tog go in the name attribute
	 * 				-'http-equiv' _string_: Value to go in the http-equiv attribute
	 *
	 * @return string $meta The meta tag returned as a string
	 * @access public
	 */
	public static function meta($name = '', $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'name' => $name,
			'options' => $options
		), array('event' => 'args'));
		
		$name = $filtered['name'];
		$options = $filtered['options'];

		$link = '<meta ';

		if (!empty($options['charset'])) {
			$link .= 'charset="' . $options['charset'] . '" ';
		}

		if (!empty($options['content'])) {
			$link .= 'content="' . $options['content'] . '" ';
		}

		if (!empty($name)) {
			$link .= 'name="' . $name . '" ';
		}

		if (!empty($options['http-equiv'])) {
			$link .= 'http-equiv="' . $options['http-equiv'] . '" ';
		}

		$link .= self::getStandardAttributes($options);

		$link .= '/>';

		self::_notify(get_class() . '::' . __FUNCTION__, $link, $name, $options);
		$link = self::_applyFilter(get_class(), __FUNCTION__, $link, array('event' => 'return'));

		return $link;
	}//end meta

	/**
	 * Displays a video using the HTML5 video component. For best usage, pass through a mp4, ogv and webm
	 * file.
	 *
	 * @param string $src The location of the video file to be played. Will be rendered by
	 * self::videoContentURL() function
	 * @param array $options Options that can be used to define attributes in the elements tag
	 * 				-'height' _double_: The height of the video
	 * 				-'width' _width_: The width of the video
	 * 				-'controls' _string_: The controls attributes.
	 * 				-'audio' '_string_: THe audio attribute
	 * 				-'autoplay' _string_: Automatically play the video
	 * 				-'loop' _string_ : Loop to play automatically
	 * 				-'poster' _string_ : Poster attribute
	 * 				-'preload' _string_: Preload attribute
	 * 				-'mp4_file' _string_: Location of the mp4 file
	 * 				-'webm_file' _string_: The location of the webm file
	 * 				-'ogv_file' _string_: Location of the ogv file
	 *
	 * @return string $video Returns the video tag
	 * @access public
	 */
	public static function video($src = '', $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $src, $options);

		$defaults = array(
			'controls' => 'controls',
			'error' => 'Sorry but your browser cannot play this HTML5 Element',
			'append_location' => true
		);
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'src' => $src,
			'options' => $options
		), array('event' => 'args'));
		$src = $filtered['src'];
		$options = $filtered['options'];

		$video = '<video ';

		if (!empty($src)) {
			$video .= 'src="' . self::videoContentURL($src, $options['append_location']) . '" ';
		}

		if (!empty($options['height'])) {
			$video .= 'height="' . $options['height'] . '" ';
		}

		if (!empty($options['width'])) {
			$video .= 'width="' . $options['width'] . '" ';
		}

		if (!empty($options['controls'])) {
			$video .= 'controls="' . $options['controls'] . '" ';
		}

		if (!empty($options['audio'])) {
			$video .= 'audio="' . $options['audio'] . '" ';
		}

		if (!empty($options['autoplay'])) {
			$video .= 'autoplay="' . $options['autoplay'] . '" ';
		}

		if (!empty($options['loop'])) {
			$video .= 'loop="' . $options['loop'] . '" ';
		}

		if (!empty($options['poster'])) {
			$video .= 'poster="' . $options['poster'] . '" ';
		}

		if (!empty($options['preload'])) {
			$video .= 'preload="' . $options['preload'] . '" ';
		}

		$video .= self::getMediaEventAttributes($options);
		$video .= '>';

		if (!empty($options['mp4_file'])) {
			$video .= '<source src="' . self::videoContentURL($options['mp4_file']) . '" type="video/mp4" >';
		}

		if (!empty($options['ogv_file'])) {
			$video .= '<source src="' . self::videoContentURL($options['ogv_file']) . '" type="video/ogg" >';
		}

		if (!empty($options['webm_file'])) {
			$video .= '<source src="' . self::videoContentURL($options['webm_file']) . '" type="video/webm" >';
		}
		$video .= $options['error'];
		$video .= '</video>';

		self::_notify(get_class() . '::' . __FUNCTION__, $video, $src, $options);
		$video = self::_applyFilter(get_class(), __FUNCTION__, $video, array('event' => 'return'));

		return $video;
	}//end getVideoDisplay

	/**
	 * Displays an audio clip using the HTML5 audio component. For best usage, pass through a wav, mp3
	 * and oga file.
	 *
	 * @param string $src The location of the audio file to be played. Will be rendered by
	 * self::audioContentURL() function
	 * @param array $options Options that can be used to define attributes in the elements tag
	 * 				-'controls' _string_: The controls attributes.
	 * 				-'audio' '_string_: THe audio attribute
	 * 				-'autoplay' _string_: Automatically play the video
	 * 				-'loop' _string_ : Loop to play automatically
	 * 				-'poster' _string_ : Poster attribute
	 * 				-'preload' _string_: Preload attribute
	 * 				-'wav_file' _string_: Location of the wav file
	 * 				-'mp3_file' _string_: The location of the mp3 file
	 * 				-'oga_file' _string_: Location of the oga file
	 *
	 * @return string $audio Returns the audio tag
	 * @access public
	 */
	public static function audio($src = '', $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $src, $options);

		$defaults = array(
			'controls' => 'controls',
			'error' => 'Sorry but your browser cannot play this HTML5 Element',
			'append_location' => true
		);
		
		$options += $defaults;

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'src' => $src,
			'options' => $options
		), array('event' => 'args'));
		
		$src = $filtered['src'];
		$options = $filtered['options'];

		$audio = '<audio ';

		if (!empty($src)) {
			$audio .= 'src="' . self::audioContentURL($src, $options['append_location']) . '" ';
		}

		if (!empty($options['controls'])) {
			$audio .= 'controls="' . $options['controls'] . '" ';
		}

		if (!empty($options['audio'])) {
			$audio .= 'audio="' . $options['audio'] . '" ';
		}

		if (!empty($options['autoplay'])) {
			$audio .= 'autoplay="' . $options['autoplay'] . '" ';
		}

		if (!empty($options['loop'])) {
			$audio .= 'loop="' . $options['loop'] . '" ';
		}

		if (!empty($options['preload'])) {
			$audio .= 'preload="' . $options['preload'] . '" ';
		}

		$audio .= self::getMediaEventAttributes($options);
		$audio .= '>';

		if (!empty($options['wav_file'])) {
			$audio .= '<source src="' . self::audioContentURL($options['wav_file']) . '" type="audio/wav" >';
		}

		if (!empty($options['mp3_file'])) {
			$audio .= '<source src="' . self::audioContentURL($options['mp3_file']) . '" type="audio/mpeg" >';
		}

		if (!empty($options['oga_file'])) {
			$audio .= '<source src="' . self::audioContentURL($options['oga_file']) . '" type="audio/ogg" >';
		}
		$audio .= $options['error'];
		$audio .= '</audio>';

		self::_notify(get_class() . '::' . __FUNCTION__, $audio, $src, $options);
		$audio = self::_applyFilter(get_class(), __FUNCTION__, $audio, array('event' => 'return'));

		return $audio;
	}//end getVideoDisplay

	/**
	 * Creates a div to display.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $data The information thatwill be displayed inside the div
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 *
	 * @return string $div The div element that was generated
	 * @access public
	 */
	public static function div($data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$data = $filtered['data'];
		$options = $filtered['options'];

		$tag = self::generateHtmlTag('div', $data, $options);
		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	/**
	 * Creates a <h1></h1> to display.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $data The information that will be displayed inside the heaader tag
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 *
	 * @return string $header The header element that was generated
	 * @access public
	 */
	public static function h1($data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$data = $filtered['data'];
		$options = $filtered['options'];

		$tag = self::generateHtmlTag('h1', $data, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	/**
	 * Creates a <h2></h2> to display.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $data The information that will be displayed inside the heaader tag
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 *
	 * @return string $header The header element that was generated
	 * @access public
	 */
	public static function h2($data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$data = $filtered['data'];
		$options = $filtered['options'];

		$tag = self::generateHtmlTag('h2', $data, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	/**
	 * Creates a <h3></h3> to display.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $data The information that will be displayed inside the heaader tag
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 *
	 * @return string $header The header element that was generated
	 * @access public
	 */
	public static function h3($data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$data = $filtered['data'];
		$options = $filtered['options'];

		$tag = self::generateHtmlTag('h3', $data, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	/**
	 * Creates a <h4></h4> to display.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $data The information that will be displayed inside the heaader tag
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 *
	 * @return string $header The header element that was generated
	 * @access public
	 */
	public static function h4($data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$data = $filtered['data'];
		$options = $filtered['options'];

		$tag = self::generateHtmlTag('h4', $data, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	/**
	 * Creates a <h5></h5> to display.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $data The information that will be displayed inside the heaader tag
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 *
	 * @return string $header The header element that was generated
	 * @access public
	 */
	public static function h5($data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$data = $filtered['data'];
		$options = $filtered['options'];

		$tag = self::generateHtmlTag('h5', $data, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	/**
	 * Creates a <h6></h6> to display.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $data The information that will be displayed inside the heaader tag
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 *
	 * @return string $header The header element that was generated
	 * @access public
	 */
	public static function h6($data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$data = $filtered['data'];
		$options = $filtered['options'];

		$tag = self::generateHtmlTag('h6', $data, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	/**
	 * Creates a paragraph tag ,<p></p>, to display.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $data The information that will be displayed inside the paragraph tag
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 *
	 * @return string $header The paragraph element that was generated
	 * @access public
	 */
	public static function p($data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$data = $filtered['data'];
		$options = $filtered['options'];

		$tag = self::generateHtmlTag('p', $data, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	/**
	 * Creates a span tag ,<span></span>, to display.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $data The information that will be displayed inside the span tag
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 *
	 * @return string $header The span element that was generated
	 * @access public
	 */
	public static function span($data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$data = $filtered['data'];
		$options = $filtered['options'];

		$tag = self::generateHtmlTag('span', $data, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	/**
	 * Creates a article tag ,<article></article>, to display.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $data The information that will be displayed inside the article tag
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 *
	 * @return string $article The article element that was generated
	 * @access public
	 */
	public static function article($data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$data = $filtered['data'];
		$options = $filtered['options'];

		$tag = self::generateHtmlTag('article', $data, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	/**
	 * Creates a address tag ,<address></address>, to display.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $data The information that will be displayed inside the address tag
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 *
	 * @return string $address The address element that was generated
	 * @access public
	 */
	public static function address($data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$data = $filtered['data'];
		$options = $filtered['options'];

		$tag = self::generateHtmlTag('address', $data, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	/**
	 * Creates a strong tag ,<strong></strong>, to display.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $data The information that will be displayed inside the strong tag
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 *
	 * @return string $strong The strong element that was generated
	 * @access public
	 */
	public static function strong($data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$data = $filtered['data'];
		$options = $filtered['options'];

		$tag = self::generateHtmlTag('strong', $data, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	/**
	 * Creates a summary tag ,<summary></ssummary>, to display.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $data The information that will be displayed inside the strong tag
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 *
	 * @return string $summary The summary element that was generated
	 * @access public
	 */
	public static function summary($data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$data = $filtered['data'];
		$options = $filtered['options'];

		$tag = self::generateHtmlTag('summary', $data, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	/**
	 * Creates a details tag ,<details></details>, to display.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $data The information that will be displayed inside the details tag
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 *
	 * @return string $details The details element that was generated
	 * @access public
	 */
	public static function details($data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$data = $filtered['data'];
		$options = $filtered['options'];

		$tag = self::generateHtmlTag('details', $data, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	/**
	 * Creates a aside tag ,<aside></aside>, to display.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $data The information that will be displayed inside the aside tag
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 *
	 * @return string $aside The aside element that was generated
	 * @access public
	 */
	public static function aside($data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$data = $filtered['data'];
		$options = $filtered['options'];

		$tag = self::generateHtmlTag('aside', $data, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	/**
	 * Creates a canvas tag ,<canvas></canvas>, to display.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $data The information that will be displayed inside the canvas tag
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 *
	 * @return string $aside The canvas element that was generated
	 * @access public
	 */
	public static function canvas($data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$data = $filtered['data'];
		$options = $filtered['options'];

		$tag = self::generateHtmlTag('canvas', $data, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	/**
	 * Creates a strong li ,<li></li>, to display.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $data The information that will be displayed inside the li tag
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 *
	 * @return string $strong The strong element that was generated
	 * @access public
	 */
	public static function li($data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$data = $filtered['data'];
		$options = $filtered['options'];

		$tag = self::generateHtmlTag('li', $data, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	/**
	 * Creates a strong ul ,<ul></ul>, to display.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $data The information that will be displayed inside the ul tag
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 *
	 * @return string $strong The strong element that was generated
	 * @access public
	 */
	public static function ul($data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$data = $filtered['data'];
		$options = $filtered['options'];

		$tag = self::generateHtmlTag('ul', $data, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	/**
	 * Creates a strong ol ,<ol></ol>, to display.
	 *
	 * @see self::getEventAttributes()
	 * @see self::getStandardAttributes()
	 *
	 * @param string $data The information that will be displayed inside the ol tag
	 * @param array $options Attributes that can be added to the element. includes
	 * self::getStandardAttributes and self::getEventAttributes
	 *
	 * @return string $strong The strong element that was generated
	 * @access public
	 */
	public static function ol($data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$data = $filtered['data'];
		$options = $filtered['options'];

		$tag = self::generateHtmlTag('ol', $data, $options);

		self::_notify(get_class() . '::' . __FUNCTION__, $tag, $data, $options);
		$tag = self::_applyFilter(get_class(), __FUNCTION__, $tag, array('event' => 'return'));

		return $tag;
	}

	/**
	 * Creates a progress input element with options passed too it.
	 *
	 * @see HTML::getStandardAttributes()
	 * @see HTML::getEventAttributes()
	 * @see HTML::getStandardAttributes()
	 * @see self::getFormAttributes()
	 *
	 * @param string $value The current value for the progress bar
	 * @param string $max The max value for the progress bar
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through HTML::getStandardAttributes,
	 * HTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 *
	 * @return string $element The string that creates the element
	 * @access public
	 */
	public static function progress($value, $max, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $name, $options, $css_options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'value' => $value,
			'max' => $max,
			'options' => $options
		), array('event' => 'args'));
		
		$value = $filtered['value'];
		$max = $filtered['max'];
		$options = $filtered['options'];

		$label = '<progress ';
		$label .= 'value="' . $value . '" ';
		$label .= 'max="' . $max . '" ';

		$label .= Html::getStandardAttributes($options);
		$label .= Html::getEventAttributes($options);

		$label .= '></progress>';

		self::_notify(get_class() . '::' . __FUNCTION__, $label, $value, $max, $options);
		$label = self::_applyFilter(get_class(), __FUNCTION__, $label, array('event' => 'return'));

		return $label;
	}

	/**
	 * Standard attributes that are present in many html tags. This functionisused for assigning those
	 * attribute by passing
	 * them in as an array and returning them as a string. Contains both html and html5 elements
	 *
	 * @param array $attributes Attribues that will be assigned if they match
	 * 			-'class' _string_: The class attribute
	 * 			-'id' _string_: The class attribute
	 * 			-'dir' _string_: The class attribute
	 * 			-'lang' _string_: The class attribute
	 *  		-'style' _string_: The class attribute
	 *  		-'title' _string_: The class attribute
	 *  		-'title' _string_: The class attribute
	 *  		-'xml:lang' _string_: The class attribute
	 *  		-'accesskey' _string_: The class attribute
	 *  		-'contenteditable' _string_: The class attribute
	 *  		-'contextmenu' _string_: The class attribute
	 *  		-'draggable' _string_: The class attribute
	 *  		-'dropzone' _string_: The class attribute
	 *  		-'hidden' _string_: The class attribute
	 *  		-'spellcheck' _string_: The class attribute
	 * 			-'title' _string_: The class attribute
	 *
	 * @return string $attributes Returns the matched attributes as a string
	 * @access public
	 */
	public static function getStandardAttributes(array $attributes = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $attributes);

		$attributes = self::_applyFilter(get_class(), __FUNCTION__, $attributes, array('event' => 'args'));

		$return_attributes = '';
		
		$accepted_attributes = array(
			'class',
			'id',
			'dir',
			'lang',
			'style',
			'title',
			'xml:lang',
			'accesskey',
			'contenteditable',
			'contextmenu',
			'draggable',
			'dropzone',
			'hidden',
			'spellcheck',
			'title'
		);

		foreach ($attributes as $key => $attribute) {
			if (in_array($key, $accepted_attributes) && !Validator::isInteger($key)) {
				$return_attributes .= $key . '="' . $attribute . '" ';
			}
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $return_attributes, $attributes);
		$return_attributes = self::_applyFilter(get_class(), __FUNCTION__, $return_attributes, array('event' => 'return'));

		return $return_attributes;
	}

	/**
	 * Matches options pased with javascript event actions such as onabort, onclick, etc.
	 *
	 * @param array $attributes An array of attributes check if its an event
	 *
	 * @return string Html attributes if any matched
	 */
	public static function getEventAttributes(array $attributes = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $attributes);

		$attributes = self::_applyFilter(get_class(), __FUNCTION__, $attributes, array('event' => 'args'));

		$return_attributes = '';
		$accepted_attributes = array(
			'onabort',
			'onclick',
			'ondblclick',
			'onmousedown',
			'onmousemove',
			'onmouseout',
			'onmouseover',
			'onmouseup',
			'onkeydown',
			'onkeypress',
			'onkeyup',
			'onblur',
			'onfocus',
			'ondrag',
			'ondragend',
			'ondragenter',
			'ondragleave',
			'ondragover',
			'ondragstart',
			'ondrop',
			'onmousewheel',
			'onscroll'
		);

		foreach ($attributes as $key => $attribute) {
			if (in_array($key, $accepted_attributes) && !Validator::isInteger($key)) {
				$return_attributes .= $key . '="' . $attribute . '" ';
			}
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $return_attributes, $attributes);
		$return_attributes = self::_applyFilter(get_class(), __FUNCTION__, $return_attributes, array('event' => 'return'));

		return $return_attributes;
	}

	/**
	 * Searches for media attributes that go with media tags like video/audio.
	 *
	 * @param array $attributes An array of attributes to assign
	 *
	 * @return string Html attributes if any matched
	 */
	public static function getMediaEventAttributes($attributes = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $attributes);

		$attributes = self::_applyFilter(get_class(), __FUNCTION__, $attributes, array('event' => 'args'));

		$return_attributes = '';
		$accepted_attributes = array(
			'oncanplay',
			'oncanplaythrough',
			'ondurationchange',
			'onemptied',
			'onended',
			'onerror',
			'onloadeddata',
			'onloadedmetadata',
			'onloadstart',
			'onpause',
			'onplay',
			'onplaying',
			'onprogress',
			'onratechange',
			'onreadystatechange',
			'onseeked',
			'onseeking',
			'onstalled',
			'onsuspend',
			'ontimeupdate',
			'onvolumechange',
			'onwaiting'
		);

		foreach ($attributes as $key => $attribute) {
			if (in_array($key, $accepted_attributes) && !Validator::isInteger($key)) {
				$return_attributes .= $key . '="' . $attribute . '" ';
			}
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $return_attributes, $attributes);
		$return_attributes = self::_applyFilter(get_class(), __FUNCTION__, $return_attributes, array('event' => 'return'));

		return $return_attributes;
	}

	/**
	 * Searches for media attributes that go with window javascript events.
	 *
	 * @param array $attributes An array of attributes to assign
	 *
	 * @return string Html attributes if any matched
	 */
	public static function getWindowAttributes($attributes = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $attributes);

		$attributes = self::_applyFilter(get_class(), __FUNCTION__, $attributes, array('event' => 'args'));

		$accepted_attributes = array(
			'onafterprint',
			'onbeforeprint',
			'ondurationchange',
			'onbeforeonload',
			'onblur',
			'onerror',
			'onfocus',
			'onhaschange',
			'onload',
			'onmessage',
			'onoffline',
			'ononline',
			'onpageshow',
			'onpopstate',
			'onredo',
			'onresize',
			'onstorage',
			'onundo',
			'onunload'
		);

		foreach ($attributes as $key => $attribute) {
			if (in_array($key, $accepted_attributes) && !Validator::isInteger($key)) {
				$return_attributes .= $key . '="' . $attribute . '" ';
			}
		}

		self::_notify(get_class() . '::' . __FUNCTION__, $return_attributes, $attributes);
		$return_attributes = self::_applyFilter(get_class(), __FUNCTION__, $return_attributes, array('event' => 'return'));

		return $return_attributes;
	}

	/**
	 * Creates an html tag.
	 *
	 * @param string $tag The name of the tag, such as div, main, etc.
	 * @param string $data The content that will go instead the element
	 * @param array $options Options than can be used to further distinguish the element. The options are
	 * 				the same values that will be passed through HTML::getStandardAttributes,
	 * HTML::getEventAttributes
	 * 				and get the self::getFormAttributes funtions
	 */
	public static function generateHtmlTag($tag, $data, $options = array()) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $tag, $data, $options);

		$filtered = self::_applyFilter(get_class(), __FUNCTION__, array(
			'tag' => $tag,
			'data' => $data,
			'options' => $options
		), array('event' => 'args'));
		
		$tag = $filtered['tag'];
		$data = $filtered['data'];
		$options = $filtered['options'];

		$generated_tag = '<' . $tag . ' ';
		$generated_tag .= self::getStandardAttributes($options);
		$generated_tag .= self::getEventAttributes($options);
		$generated_tag .= '>' . $data . '</' . $tag . '>';

		self::_notify(get_class() . '::' . __FUNCTION__, $generated_tag, $tag, $data, $options);
		$generated_tag = self::_applyFilter(get_class(), __FUNCTION__, $generated_tag, array('event' => 'return'));

		return $generated_tag;
	}

	/**
	 * Not sure if the function is needed or still make sense
	 *
	 * @param string $url url of the file
	 * @param boolean $append_location Appends the PV_AUDIo tage
	 *
	 * @return string
	 * @todo check if function is still valid
	 */
	private static function audioContentURL($url, $append_location = false) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $url);

		$url = self::_applyFilter(get_class(), __FUNCTION__, $url, array('event' => 'args'));

		if (!Validator::isValidURL($url) && $append_location) {
			$url = PV_AUDIO . $url;
		}
		return Router::url($url);
	}

	/**
	 * Not sure if the function is needed or still make sense
	 *
	 * @param string $url url of the file
	 * @param boolean $append_location Appends the PV_AUDIo tage
	 *
	 * @return string
	 * @todo check if function is still valid
	 */
	private static function videoContentURL($url, $append_location = false) {

		if (self::_hasAdapter(get_class(), __FUNCTION__))
			return self::_callAdapter(get_class(), __FUNCTION__, $url);

		$url = self::_applyFilter(get_class(), __FUNCTION__, $url, array('event' => 'args'));

		if (!Validator::isValidURL($url) && $append_location) {
			$url = PV_VIDEO . $url;
		}
		return Router::url($url);
	}

}//end fields
