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
class PVFields extends PVStaticObject{

	private $rawDataID=1;
	private $dlListID=2;
	
	
	private $textFieldID=1;
 	private $textAreaID=2;
 	private $urlFieldID=3;
 	private $imageFieldID=5;
 	private $passwordFieldID=6;
 	private $editorTextAreaID=7;
 	private $emailaddressID=8;
	private $comboBoxID=9;
	private $radioButtonID=10;
	var $version=.8;
	var $uniqueName="pv_field_output";
	
	private static $content_filters;
	private static $comment_filters;
	private static $point_filters;
	private static $rating_filters;
	
	//Regular Content
	private static $content_id; 
	private static $app_id;  
	private static $parent_id; 
	private static $owner_id;
	private static $content_title;
	private static $content_alias;  
	private static $content_description; 
	private static $content_meta_tags;
	private static $content_meta_description; 
	private static $content_thumbnail;
	private static $date_created;
	private static $date_modified;
	private static $date_active;
	private static $date_inactive;
	private static $is_searchable;
	private static $allow_comments;
	private static $allow_rating;
	private static $content_active;
	private static $content_promoted;
	private static $content_permissions;
	private static $content_type;
	private static $content_language;
	private static $translate_content;
	private static $content_approved;
	private static $content_category;
	private static $content_parameters;
	private static $content_order;
	private static $sym_link;
	private static $content_taxonomy;
	
	//Image Content
	private static $image_type;
	private static $image_size;
	private static $image_url;
	private static $thumb_url;
	private static $image_width;
	private static $image_height;
	private static $thumb_width;
	private static $thumb_height;
	private static $image_src;		
		
	//Text Content
	private static $text_content;
	private static $text_page_group;
	private static $text_page_number;
	private static $text_src;
	
	//Video Content
	private static $video_type;
	private static $video_length;
	private static $video_allow_embedding;
	private static $flv_file;
	private static $mp4_file;
	private static $wmv_file;
	private static $mpeg_file;
	private static $rm_file;
	private static $avi_file;
	private static $mov_file;
	private static $asf_file;
	private static $enable_hq;
	private static $auto_hq;
	private static $video_src;
	private static $video_embed;
	
	//Event Content
	private static $event_location;
	private static $event_start_date;
	private static $event_end_date;
	private static $event_country;
	private static $event_address;
	private static $event_city;
	private static $event_state;
	private static $event_zip;
	private static $event_map;
	private static $event_src;
	private static $undefined_endtime;
	
	//Audio Content
	private static $audio_length;
	private static $mid_file;
	private static $wav_file;
	private static $aif_file;
	private static $mp3_file;
	private static $ra_file;
	private static $sample_length;
	private static $audio_src;
	private static $audio_type;
	
	//File Content
	private static $file_type;
	private static $file_size;
	private static $file_location;
	private static $file_name;
	private static $file_src;
	private static $file_downloadable;
	private static $file_max_downloads;
	private static $file_license;
	private static $file_version;
	
	//Product Content
	private static $product_id;
	private static $product_sku;
	private static $product_idsku;
	private static $product_vendor_id;
	private static $product_quantity;
	private static $product_price;
	private static $product_discount_price;
	private static $product_size;
	private static $product_color;
	private static $product_weight; 
	private static $product_height;
	private static $product_length; 
	private static $product_currency; 
	private static $product_in_stoc; 
	private static $product_type; 
	private static $product_tax_id; 
	private static $product_attribute; 
	private static $product_version;
	private static $product_in_stock;
	
	//Comments
	private static $comment_owner_id;
	private static $comment_date;
	private static $comment_id;
	private static $comment_approved;
	private static $comment_title;
	private static $comment_text;
	private static $comment_parent;
	private static $comment_author;
	private static $comment_author_email;
	private static $comment_author_website;
	private static $comment_content_id;
	private static $comment_type;
	private static $comment_owner_ip;
	
	//Points
	private static $point_value;
	private static $point_content_id;
	private static $point_comment_id;
	private static $point_app_id;
	private static $point_type;
	private static $point_user_id;
	private static $point_id;
	private static $point_user_ip;
	private static $point_date;
	
	//Rating
	private static $rating_content_id;
	private static $rating_comment_id;
	private static $rating;
	private static $rating_user_id; 
	private static $rating_type;
	private static $rating_id;
	private static $date_rated;
	private static $date_rerated;
	private static $rating_user_ip;
	
	
	
	
	
	function PVFieldOutput(){
		self::$content_filters=array();
		self::$comment_filters=array();
		self::$point_filters=array();
		self::$rating_filters=array();
	}//end FieldOutput
	
	
	public static function createField( $app_id=0, $owner_id=0 ,  $field_name ,$field_type ,$field_description , $field_title , $max_length, $max_size, $columns,$rows, $value, $searchable , $readonly , $show_title , $is_required , $on_blur , $id , $on_change, $on_click , $on_doubelclick , $on_focus , $on_keydown , $on_keyup , $on_keypress , $on_mousedown , $on_mouseup , $on_mousemove , $on_mouseover , $on_mouseout , $instructions , $show_instructions , $checked, $disabled , $lang, $align, $accept , $field_class , $size , $field_prefix , $field_suffix , $field_css , $field_prefix , $field_suffix , $field_css, $field_unique_name ){
		
		
	
		$query="INSERT INTO ".pv_getFieldsTableName()."(field_name ,field_type,field_description,field_title,max_length, max_size, columns,rows,value,searchable,readonly,show_title, is_required , on_blur,id,on_change,on_click,on_doubelclick,on_focus,on_keydown,on_keyup,on_keypress,on_mousedown,on_mouseup,on_mousemove,on_mouseover,on_mouseout,instructions,show_instructions,checked,disabled,lang,align,accept,class,size, field_prefix, field_suffix, field_css,app_id, field_prefix, field_suffix, field_css, owner_id, field_unique_name) VALUES( '$field_name' ,'$field_type', '$field_description' , '$field_title' , '$max_length' , '$max_size' , '$columns' , '$rows' ,'$value' , '$searchable' , '$readonly' , '$show_title' , '$is_required' , '$on_blur' , '$id' , '$on_change' , '$on_click' , '$on_doubelclick' ,'$on_focus' , '$on_keydown' ,' $on_keyup' , '$on_keypress' , '$on_mousedown' , '$on_mouseup' , '$on_mousemove' , '$on_mouseover' , '$on_mouseout' , '$instructions' , '$show_instructions' , '$checked' , '$disabled' , '$lang' ,'$align' , '$accept' , '$field_class' , '$size' , ��'$field_prefix' , '$field_suffix' , '$field_css' , '$app_id' , '$field_prefix', '$field_suffix', '$field_css' , '$owner_id' , '$field_unique_name')";
	
		$field_id=PVDatabase::return_last_insert_query($query, "field_id", pv_getFieldsTableName() );
		
		return $field_id;
	}//end createField
	
	public static function createFieldFromArray($args){
		$args=PVDatabase::makeSafe($args);
		
		if(is_array($args)){
		 	extract($args);
		}
		
		 $field_order=ceil($field_order);
		 $max_length=ceil($max_length);
		 $max_size=ceil($max_size);
		 $field_columns=ceil($field_columns);
		 $field_rows=ceil($field_rows);
		 $searchable=ceil($searchable);
		 $readonly=ceil($readonly);
		 $show_title=ceil($show_title);
		 $enabled=ceil($enabled);
		 $show_creation=ceil($show_creation);
		 $is_required=ceil($is_required);
		 $show_instructions=ceil($show_instructions);
		 $checked=ceil($checked);
		 $disabled=ceil($disabled);
		 $admin_editable=ceil($admin_editable);
		 $previewable=ceil($previewable);
		 $is_deleted=ceil($is_deleted);
		 $app_id=ceil($app_id);
		 $owner_id=ceil($owner_id);
		 $field_order=ceil($field_order);
		 
		 $query="INSERT INTO ".pv_getFieldsTableName()."(field_name ,field_type,field_description,field_title,max_length, max_size, field_columns,field_rows, field_value,searchable,readonly,show_title, is_required , on_blur,id,on_change,on_click,on_doubelclick,on_focus,on_keydown,on_keyup,on_keypress,on_mousedown,on_mouseup,on_mousemove,on_mouseover,on_mouseout,instructions,show_instructions,checked,disabled,lang,align,accept, field_class, field_size, field_prefix, field_suffix, field_css,app_id, owner_id, field_unique_name, content_type, field_order) VALUES( '$field_name' ,'$field_type', '$field_description' , '$field_title' , '$max_length' , '$max_size' , '$field_columns' , '$field_rows' ,'$field_value' , '$searchable' , '$readonly' , '$show_title' , '$is_required' , '$on_blur' , '$id' , '$on_change' , '$on_click' , '$on_doubelclick' ,'$on_focus' , '$on_keydown' ,' $on_keyup' , '$on_keypress' , '$on_mousedown' , '$on_mouseup' , '$on_mousemove' , '$on_mouseover' , '$on_mouseout' , '$instructions' , '$show_instructions' , '$checked' , '$disabled' , '$lang' ,'$align' , '$accept' , '$field_class' , '$size' , '$field_prefix' , '$field_suffix' , '$field_css' , '$app_id' ,  '$owner_id', '$field_unique_name', '$content_type' , '$field_order' )";
		
		 $field_id=PVDatabase::return_last_insert_query($query, "field_id", pv_getFieldsTableName() );
		
		 return $field_id;
		
	
	}//end createFieldArrays
	
	
	
	public static function updateField($args){
		
		if(is_array($args) && !empty($args['field_id'])){
			$args=PVDatabase::makeSafe($args);
			extract($args);
			
			$field_order=ceil($field_order);
			$max_length=ceil($max_length);
			$max_size=ceil($max_size);
			$field_columns=ceil($field_columns);
			$field_rows=ceil($field_rows);
			$searchable=ceil($searchable);
			$readonly=ceil($readonly);
			$show_title=ceil($show_title);
			$enabled=ceil($enabled);
			$show_creation=ceil($show_creation);
			$is_required=ceil($is_required);
			$show_instructions=ceil($show_instructions);
			$checked=ceil($checked);
			$disabled=ceil($disabled);
			$admin_editable=ceil($admin_editable);
			$previewable=ceil($previewable);
			$is_deleted=ceil($is_deleted);
			$app_id=ceil($app_id);
			$owner_id=ceil($owner_id);
			$field_order=ceil($field_order);
			
			$query="UPDATE ".pv_getFieldsTableName()." SET field_name='$field_name' , field_type='$field_type',field_description='$field_description' , field_title='$field_title' , max_length='$max_length' , max_size='$max_size', field_columns='$field_columns',field_rows='$field_rows' , field_value='$field_value' , searchable='$searchable' , readonly='$readonly' , show_title='$show_title' , is_required='$is_required' , on_blur='$on_blur',id='$id', on_change='$on_change',on_click='$on_click' , on_doubelclick='$on_doubelclick' ,on_focus='$on_focus' ,on_keydown='$on_keydown' ,on_keyup='$on_keyup' , on_keypress='$on_keypress' , on_mousedown='$on_mousedown',on_mouseup='$on_mouseup', on_mousemove='$on_mousemove' ,on_mouseover='$on_mouseover', on_mouseout='$on_mouseout' , instructions='$instructions',show_instructions='$show_instructions', checked='$checked' ,disabled='$disabled' , lang='$lang' , align='$align' ,accept='$accept' ,field_class='$field_size' , field_size='$size' , field_prefix='$field_prefix' , field_suffix='$field_suffix' , field_css='$field_css' ,app_id='$app_id', field_prefix='$field_prefix', field_suffix='$field_suffix', field_css='$field_css', owner_id='$owner_id', field_unique_name='$field_unique_name', content_type='$content_type', field_order='$field_order' WHERE field_id='$field_id' ";
			
		
			PVDatabase::query($query);
		}
	
		
		
	}//end updateField
	
	
	
	public static function getFieldsList( $args ){
	
		if(is_array($args)){
			extract($args);
		}
		
		$content_array=array();
		$table_name=pv_getFieldsTableName();
		$db_type=PVDatabase::getDatabaseType();
		
		$WHERE_CLAUSE="";
		
		$first=1;
		
		if(!empty($field_id)){
					
				$field_id=trim($field_id);
				
				if($first==0 && ($field_id[0]!='+' && $field_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_id[0]=='+' || $field_id[0]==',') && $first==1 ){
					$field_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_id, 'field_id');
				
				$first=0;
		}//end not empty app_id
		
		
		
		if(!empty($field_name)){
					
				$field_name=trim($field_name);
				
				if($first==0 && ($field_name[0]!='+' && $field_name[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_name[0]=='+' || $field_name[0]==',') && $first==1 ){
					$field_name[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_name, 'field_name');
				
				$first=0;
		}//end not empty app_id
		
		
		if(!empty($field_type)){
					
				$field_type=trim($field_type);
				
				if($first==0 && ($field_type[0]!='+' && $field_type[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_type[0]=='+' || $field_type[0]==',') && $first==1 ){
					$field_type[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_type, 'field_type');
				
				$first=0;
		}//end not empty app_id
		
		
		if(!empty($field_description)){
					
				$field_description=trim($field_description);
				
				if($first==0 && ($field_description[0]!='+' && $field_description[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_description[0]=='+' || $field_description[0]==',') && $first==1 ){
					$field_description[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_description, 'field_description');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($field_title)){
					
				$field_title=trim($field_title);
				
				if($first==0 && ($field_title[0]!='+' && $field_title[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_title[0]=='+' || $field_title[0]==',') && $first==1 ){
					$field_title[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_title, 'field_title');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($max_length)){
					
				$max_length=trim($max_length);
				
				if($first==0 && ($max_length[0]!='+' && $max_length[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($max_length[0]=='+' || $max_length[0]==',') && $first==1 ){
					$max_length[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($max_length, 'max_length');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($max_size)){
					
				$max_size=trim($max_size);
				
				if($first==0 && ($max_size[0]!='+' && $max_size[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($max_size[0]=='+' || $max_size[0]==',') && $first==1 ){
					$max_size[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($max_size, 'max_size');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($field_columns)){
					
				$field_columns=trim($field_columns);
				
				if($first==0 && ($field_columns[0]!='+' && $field_columns[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_columns[0]=='+' || $field_columns[0]==',') && $first==1 ){
					$field_columns[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_columns, 'field_columns');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($field_rows)){
					
				$field_rows=trim($field_rows);
				
				if($first==0 && ($field_rows[0]!='+' && $field_rows[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_rows[0]=='+' || $field_rows[0]==',') && $first==1 ){
					$field_rows[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_rows, 'field_rows');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($field_value)){
					
				$field_value=trim($field_value);
				
				if($first==0 && ($field_value[0]!='+' && $field_value[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_value[0]=='+' || $field_value[0]==',') && $first==1 ){
					$field_value[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_value, 'field_value');
				
				$first=0;
		}//end not empty app_id
		
		
		if(!empty($searchable)){
					
				$searchable=trim($searchable);
				
				if($first==0 && ($searchable[0]!='+' && $searchable[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($searchable[0]=='+' || $searchable[0]==',') && $first==1 ){
					$searchable[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($searchable, 'searchable');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($readonly)){
					
				$readonly=trim($readonly);
				
				if($first==0 && ($readonly[0]!='+' && $readonly[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($readonly[0]=='+' || $readonly[0]==',') && $first==1 ){
					$readonly[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($readonly, 'readonly');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($enabled)){
					
				$enabled=trim($enabled);
				
				if($first==0 && ($enabled[0]!='+' && $enabled[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($enabled[0]=='+' || $enabled[0]==',') && $first==1 ){
					$enabled[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($enabled, 'enabled');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($show_creation)){
					
				$show_creation=trim($show_creation);
				
				if($first==0 && ($show_creation[0]!='+' && $show_creation[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($show_creation[0]=='+' || $show_creation[0]==',') && $first==1 ){
					$show_creation[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($show_creation, 'show_creation');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($is_required)){
					
				$is_required=trim($is_required);
				
				if($first==0 && ($is_required[0]!='+' && $is_required[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($is_required[0]=='+' || $is_required[0]==',') && $first==1 ){
					$is_required[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($is_required, 'is_required');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($on_blur)){
					
				$on_blur=trim($on_blur);
				
				if($first==0 && ($on_blur[0]!='+' && $on_blur[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($on_blur[0]=='+' || $on_blur[0]==',') && $first==1 ){
					$on_blur[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($on_blur, 'on_blur');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($id)){
					
				$id=trim($id);
				
				if($first==0 && ($id[0]!='+' && $id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($id[0]=='+' || $id[0]==',') && $first==1 ){
					$id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($id, 'id');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($on_change)){
					
				$on_change=trim($on_change);
				
				if($first==0 && ($on_change[0]!='+' && $on_change[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($on_change[0]=='+' || $on_change[0]==',') && $first==1 ){
					$on_change[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($on_change, 'on_change');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($on_click)){
					
				$on_click=trim($on_click);
				
				if($first==0 && ($on_click[0]!='+' && $on_click[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($on_click[0]=='+' || $on_click[0]==',') && $first==1 ){
					$on_click[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($on_click, 'on_click');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($on_doubelclick)){
					
				$on_doubelclick=trim($on_doubelclick);
				
				if($first==0 && ($on_doubelclick[0]!='+' && $on_doubelclick[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($on_doubelclick[0]=='+' || $on_doubelclick[0]==',') && $first==1 ){
					$on_doubelclick[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($on_doubelclick, 'on_doubelclick');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($on_focus)){
					
				$on_focus=trim($on_focus);
				
				if($first==0 && ($on_focus[0]!='+' && $on_focus[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($on_focus[0]=='+' || $on_focus[0]==',') && $first==1 ){
					$on_focus[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($on_focus, 'on_focus');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($on_keydown)){
					
				$on_keydown=trim($on_keydown);
				
				if($first==0 && ($on_keydown[0]!='+' && $on_keydown[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($on_keydown[0]=='+' || $on_keydown[0]==',') && $first==1 ){
					$on_keydown[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($on_keydown, 'on_keydown');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($on_keyup)){
					
				$on_keyup=trim($on_keyup);
				
				if($first==0 && ($on_keyup[0]!='+' && $on_keyup[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($on_keyup[0]=='+' || $on_keyup[0]==',') && $first==1 ){
					$on_keyup[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($on_keyup, 'on_keyup');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($on_keypress)){
					
				$on_keypress=trim($on_keypress);
				
				if($first==0 && ($on_keypress[0]!='+' && $on_keypress[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($on_keypress[0]=='+' || $on_keypress[0]==',') && $first==1 ){
					$on_keypress[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($on_keypress, 'on_keypress');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($on_mousedown)){
					
				$on_mousedown=trim($on_mousedown);
				
				if($first==0 && ($on_mousedown[0]!='+' && $on_mousedown[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($on_mousedown[0]=='+' || $on_mousedown[0]==',') && $first==1 ){
					$on_mousedown[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($on_mousedown, 'on_mousedown');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($on_mouseup)){
					
				$on_mouseup=trim($on_mouseup);
				
				if($first==0 && ($on_mouseup[0]!='+' && $on_mouseup[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($on_mouseup[0]=='+' || $on_mouseup[0]==',') && $first==1 ){
					$on_mouseup[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($on_mouseup, 'on_mouseup');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($on_mousemove)){
					
				$on_mousemove=trim($on_mousemove);
				
				if($first==0 && ($on_mousemove[0]!='+' && $on_mousemove[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($on_mousemove[0]=='+' || $on_mousemove[0]==',') && $first==1 ){
					$on_mousemove[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($on_mousemove, 'on_mousemove');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($on_mouseover)){
					
				$on_mouseover=trim($on_mouseover);
				
				if($first==0 && ($on_mouseover[0]!='+' && $on_mouseover[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($on_mouseover[0]=='+' || $on_mouseover[0]==',') && $first==1 ){
					$on_mouseover[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($on_mouseover, 'on_mouseover');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($on_mouseout)){
					
				$on_mouseout=trim($on_mouseout);
				
				if($first==0 && ($on_mouseout[0]!='+' && $on_mouseout[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($on_mouseout[0]=='+' || $on_mouseout[0]==',') && $first==1 ){
					$on_mouseout[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($on_mouseout, 'on_mouseout');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($instructions)){
					
				$instructions=trim($instructions);
				
				if($first==0 && ($instructions[0]!='+' && $instructions[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($instructions[0]=='+' || $instructions[0]==',') && $first==1 ){
					$instructions[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($instructions, 'instructions');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($show_instructions)){
					
				$show_instructions=trim($show_instructions);
				
				if($first==0 && ($show_instructions[0]!='+' && $show_instructions[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($show_instructions[0]=='+' || $show_instructions[0]==',') && $first==1 ){
					$show_instructions[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($show_instructions, 'show_instructions');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($checked)){
					
				$checked=trim($checked);
				
				if($first==0 && ($checked[0]!='+' && $checked[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($checked[0]=='+' || $checked[0]==',') && $first==1 ){
					$checked[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($checked, 'checked');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($disabled)){
					
				$disabled=trim($disabled);
				
				if($first==0 && ($disabled[0]!='+' && $disabled[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($disabled[0]=='+' || $disabled[0]==',') && $first==1 ){
					$disabled[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($disabled, 'disabled');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($lang)){
					
				$lang=trim($lang);
				
				if($first==0 && ($lang[0]!='+' && $lang[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($lang[0]=='+' || $lang[0]==',') && $first==1 ){
					$lang[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($lang, 'lang');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($align)){
					
				$align=trim($align);
				
				if($first==0 && ($align[0]!='+' && $align[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($align[0]=='+' || $align[0]==',') && $first==1 ){
					$align[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($align, 'align');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($accept)){
					
				$accept=trim($accept);
				
				if($first==0 && ($accept[0]!='+' && $accept[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($accept[0]=='+' || $accept[0]==',') && $first==1 ){
					$accept[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($accept, 'accept');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($field_class)){
					
				$field_class=trim($field_class);
				
				if($first==0 && ($field_class[0]!='+' && $field_class[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_class[0]=='+' || $field_class[0]==',') && $first==1 ){
					$field_class[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_class, 'field_class');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($field_size)){
					
				$field_size=trim($field_size);
				
				if($first==0 && ($field_size[0]!='+' && $field_size[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_size[0]=='+' || $field_size[0]==',') && $first==1 ){
					$field_size[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_size, 'field_size');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($admin_editable)){
					
				$admin_editable=trim($admin_editable);
				
				if($first==0 && ($admin_editable[0]!='+' && $admin_editable[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($admin_editable[0]=='+' || $admin_editable[0]==',') && $first==1 ){
					$admin_editable[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($admin_editable, 'admin_editable');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($previewable)){
					
				$previewabletrim($previewable);
				
				if($first==0 && ($previewable[0]!='+' && $previewable[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($previewable[0]=='+' || $previewable[0]==',') && $first==1 ){
					$previewable[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($previewable, 'previewable');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($is_deleted)){
					
				$is_deleted=trim($is_deleted);
				
				if($first==0 && ($is_deleted[0]!='+' && $is_deleted[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($is_deleted[0]=='+' || $is_deleted[0]==',') && $first==1 ){
					$is_deleted[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($is_deleted, 'is_deleted');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($display_name)){
					
				$display_name=trim($display_name);
				
				if($first==0 && ($display_name[0]!='+' && $display_name[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($display_name[0]=='+' || $display_name[0]==',') && $first==1 ){
					$display_name[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($display_name, 'display_name');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($app_id)){
					
				$app_id=trim($app_id);
				
				if($first==0 && ($app_id[0]!='+' && $app_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($app_id[0]=='+' || $app_id[0]==',') && $first==1 ){
					$app_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($app_id, 'app_id');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($field_prefix)){
					
				$field_prefix=trim($field_prefix);
				
				if($first==0 && ($field_prefix[0]!='+' && $field_prefix[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_prefix[0]=='+' || $field_prefix[0]==',') && $first==1 ){
					$field_prefix[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_prefix, 'field_prefix');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($field_suffix)){
					
				$field_suffix=trim($field_suffix);
				
				if($first==0 && ($field_suffix[0]!='+' && $field_suffix[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_suffix[0]=='+' || $field_suffix[0]==',') && $first==1 ){
					$field_suffix[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_suffix, 'field_suffix');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($field_css)){
					
				$field_css=trim($field_css);
				
				if($first==0 && ($field_css[0]!='+' && $field_css[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_css[0]=='+' || $field_css[0]==',') && $first==1 ){
					$field_css[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_css, 'field_css');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($owner_id)){
					
				$owner_id=trim($owner_id);
				
				if($first==0 && ($owner_id[0]!='+' && $owner_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($owner_id[0]=='+' || $owner_id[0]==',') && $first==1 ){
					$owner_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($owner_id, 'owner_id');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($field_unique_name)){
					
				$field_unique_name=trim($field_unique_name);
				
				if($first==0 && ($field_unique_name[0]!='+' && $field_unique_name[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_unique_name[0]=='+' || $field_unique_name[0]==',') && $first==1 ){
					$field_unique_name[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_unique_name, 'field_unique_name');
				
				$first=0;
		}//end not empty app_id
		
		
		if(!empty($content_type)){
					
				$content_type=trim($content_type);
				
				if($first==0 && ($content_type[0]!='+' && $content_type[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($content_type[0]=='+' || $content_type[0]==',') && $first==1 ){
					$content_type[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($content_type, 'content_type');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($field_order)){
					
				$field_order=trim($field_order);
				
				if($first==0 && ($field_order[0]!='+' && $field_order[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_order[0]=='+' || $field_order[0]==',') && $first==1 ){
					$field_order[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_order, 'field_order');
				
				$first=0;
		}//end not empty app_id
		
		
		
		if(!empty($custom_where)){
			
			if(empty($WHERE_CLAUSE)){
				$WHERE_CLAUSE.=' WHERE ';
			}
			
			$WHERE_CLAUSE.=" $custom_where";
		}
	
		
		if(!empty($category_id)){
			$CATEGORY_JOIN.=' JOIN '.pv_getContentCategoryRelationsTableName().' ON '.pv_getContentCategoryRelationsTableName().'.content_id='.pv_getContentTableName().'.content_id';
		}
		
		
		if(!empty($join_users)){
			$CATEGORY_JOIN.='JOIN '.pv_getLoginTableName().' ON '.pv_getContentTableName().'.owner_id='.pv_getLoginTableName().'.user_id ';
		}
		
		if(!empty($custom_join)){
			$CATEGORY_JOIN.=" $custom_join ";
		}
		
		if(!empty($distinct)){
			$PREFIX_ARGS.=" DISTINCT $distinct, ";
		}
		
		if(!empty($limit) && $db_type=='mssql' && !$paged){
			$PREFIX_ARGS.=" TOP $limit ";
		}
		
		if($paged){
			$page_results=PVDatabase::getPagininationOffset(pv_getContentTableName(), $CATEGORY_JOIN , $WHERE_CLAUSE, $current_page, $results_per_page, $order_by_clause);
			
			if($db_type=='mysql' || $db_type=='postgresql'){
				$limit=' '.$page_results['limit_offset'];
			}
			else if($db_type=='mssql'){
				$WHERE_CLAUSE.=' '.$page_results['limit_offset'];
				$table_name=$page_results['from_clause'];
			}
		}
		
		
		if(!empty($group_by)){
			$WHERE_CLAUSE.=" GROUP BY $group_by";
		}
		
		if(!empty($having)){
			$WHERE_CLAUSE.=" HAVING $having";
		}
		
		if(!empty($order_by_clause)){
			$WHERE_CLAUSE.=" ORDER BY $order_by_clause";
		}
		
		if(!empty($limit) && !$paged && ($db_type=='mysql' || $db_type=='postgresql') ){
			$WHERE_CLAUSE.=" LIMIT $limit";
		}
		
		if($paged){
			$WHERE_CLAUSE.=" $limit";
		}
		
		if(empty($custom_select)){
			$custom_select='*';
		}
		
    	$query="$PREQUERY SELECT $PREFIX_ARGS $custom_select FROM $table_name $CATEGORY_JOIN $WHERE_CLAUSE";
    	
		$result = PVDatabase::query($query);
    	
    	while ($row = PVDatabase::fetchArray($result)){
			if($paged){
				$row['current_page']=$page_results['current_page'];
				$row['last_page']=$page_results['last_page'];
				$row['total_pages']=$page_results['total_pages'];
			}
			
    		array_push($content_array, $row);
    	}//end while
    	
    	$content_array=PVDatabase::formatData($content_array);
    	
		return $content_array;
		
	}//end getFieldTypes
	
	public static function getField($field_id=0){		
		
		$WHERE_CLAUSE=" WHERE field_id='$field_id' ";
		
		if(!empty($owner_id)){
			$WHERE_CLAUSE.=" AND owner_id='$owner_id' ";
		}
		
		if(!empty($app_id)){
			$WHERE_CLAUSE.=" AND app_id='$app_id' ";
		}
		
		
		$query="SELECT * FROM ".pv_getFieldsTableName()." $WHERE_CLAUSE";
		
		$result = PVDatabase::query($query);
		
		return PVDatabase::fetchArray($result);
	}//end getField
	
	public static function deleteField($field_id, $DELETE_OPTIONS=true, $DELETE_VALUES=true){
		$field_id=ceil($field_id);
		
		if(!empty($field_id)){
			$query="DELETE FROM ".pv_getFieldsTableName()." WHERE field_id='$field_id' ";
			PVDatabase::query($query);
			
			if($DELETE_OPTIONS){
				$query="DELETE FROM ".pv_getFieldsOptionsTableName()." WHERE field_id='$field_id' ";
				PVDatabase::query($query);
			}
			
			if($DELETE_VALUES){
				$query="DELETE FROM ".getFieldValuesTableName()." WHERE field_id='$field_id' ";
				PVDatabase::query($query);
			}
		}
		
	}//end deteleField
	
	public static function createFieldOption($args){
	
		if(is_array($args)){
			$args=PVDatabase::makeSafe($args);
			extract($args);
		}
		
		if(!empty($field_id)){
			
			$query="INSERT INTO ".pv_getFieldsOptionsTableName()."(field_id, option_name, option_value, option_label, option_selected, option_disabled, option_class, option_dir, option_lang, option_style, option_title, option_on_click, option_on_doubelclick, option_on_keydown, option_on_keyup, option_on_keypress, option_on_mousedown, option_on_mouseup, option_on_mousemove, option_on_mouseover, option_on_mouseout, option_order) VALUES('$field_id' , '$option_name', '$option_value' , '$option_label' , '$option_selected' , '$option_disabled' , '$option_class' , '$option_dir' , '$option_lang' , '$option_style' , '$option_title' , '$option_on_click' , '$option_on_doubelclick' , '$option_on_keydown' , '$option_on_keyup', '$option_on_keypress', '$option_on_mousedown', '$option_on_mouseup', '$option_on_mousemove', '$option_on_mouseover', '$option_on_mouseout', '$option_order') ";
			
			return PVDatabase::return_last_insert_query($query, "option_id", pv_getFieldsOptionsTableName());
		}
	
	}//end createFieldOption
	
	
	
	public static function getFieldOptionsList( $args ){
	
		if(is_array($args)){
			extract($args);
		}
		
		$content_array=array();
		$table_name=pv_getFieldsOptionsTableName();
		$db_type=PVDatabase::getDatabaseType();
		
		$WHERE_CLAUSE="";
		
		$first=1;
		
		if(!empty($field_id)){
					
				$field_id=trim($field_id);
				
				if($first==0 && ($field_id[0]!='+' && $field_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_id[0]=='+' || $field_id[0]==',') && $first==1 ){
					$field_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_id, 'field_id');
				
				$first=0;
		}//end not empty app_id
		
		
		
		if(!empty($option_id)){
					
				$option_id=trim($option_id);
				
				if($first==0 && ($option_id[0]!='+' && $option_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_id[0]=='+' || $option_id[0]==',') && $first==1 ){
					$option_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_id, 'option_id');
				
				$first=0;
		}//end not empty app_id
		
		
		if(!empty($option_name)){
					
				$option_name=trim($option_name);
				
				if($first==0 && ($option_name[0]!='+' && $option_name[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_name[0]=='+' || $option_name[0]==',') && $first==1 ){
					$option_name[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_name, 'option_name');
				
				$first=0;
		}//end not empty app_id
		
		
		if(!empty($option_value)){
					
				$option_value=trim($option_value);
				
				if($first==0 && ($option_value[0]!='+' && $option_value[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_value[0]=='+' || $option_value[0]==',') && $first==1 ){
					$option_value[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_value, 'option_value');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($option_label)){
					
				$option_label=trim($option_label);
				
				if($first==0 && ($option_label[0]!='+' && $option_label[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_label[0]=='+' || $option_label[0]==',') && $first==1 ){
					$option_label[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_label, 'option_label');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($option_selected)){
					
				$option_selected=trim($option_selected);
				
				if($first==0 && ($option_selected[0]!='+' && $option_selected[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_selected[0]=='+' || $option_selected[0]==',') && $first==1 ){
					$option_selected[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_selected, 'option_selected');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($option_disabled)){
					
				$option_disabled=trim($option_disabled);
				
				if($first==0 && ($option_disabled[0]!='+' && $option_disabled[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_disabled[0]=='+' || $option_disabled[0]==',') && $first==1 ){
					$option_disabled[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_disabled, 'option_disabled');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($option_class)){
					
				$option_class=trim($option_class);
				
				if($first==0 && ($option_class[0]!='+' && $option_class[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_class[0]=='+' || $option_class[0]==',') && $first==1 ){
					$option_class[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_class, 'option_class');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($option_dir)){
					
				$option_dir=trim($option_dir);
				
				if($first==0 && ($option_dir[0]!='+' && $option_dir[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_dir[0]=='+' || $option_dir[0]==',') && $first==1 ){
					$option_dir[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_dir, 'option_dir');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($option_lang)){
					
				$option_lang=trim($option_lang);
				
				if($first==0 && ($option_lang[0]!='+' && $option_lang[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_lang[0]=='+' || $option_lang[0]==',') && $first==1 ){
					$option_lang[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_lang, 'option_lang');
				
				$first=0;
		}//end not empty app_id
		
		
		if(!empty($option_style)){
					
				$option_style=trim($option_style);
				
				if($first==0 && ($option_style[0]!='+' && $option_style[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_style[0]=='+' || $option_style[0]==',') && $first==1 ){
					$option_style[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_style, 'option_style');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($option_title)){
					
				$option_title=trim($option_title);
				
				if($first==0 && ($option_title[0]!='+' && $option_title[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_title[0]=='+' || $option_title[0]==',') && $first==1 ){
					$option_title[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_title, 'option_title');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($option_on_click)){
					
				$option_on_click=trim($option_on_click);
				
				if($first==0 && ($option_on_click[0]!='+' && $option_on_click[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_on_click[0]=='+' || $option_on_click[0]==',') && $first==1 ){
					$option_on_click[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_on_click, 'option_on_click');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($option_on_doubelclick)){
					
				$option_on_doubelclick=trim($option_on_doubelclick);
				
				if($first==0 && ($option_on_doubelclick[0]!='+' && $option_on_doubelclick[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_on_doubelclick[0]=='+' || $option_on_doubelclick[0]==',') && $first==1 ){
					$option_on_doubelclick[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_on_doubelclick, 'option_on_doubelclick');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($option_on_keydown)){
					
				$option_on_keydown=trim($option_on_keydown);
				
				if($first==0 && ($option_on_keydown[0]!='+' && $option_on_keydown[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_on_keydown[0]=='+' || $option_on_keydown[0]==',') && $first==1 ){
					$is_required[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_on_keydown, 'option_on_keydown');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($option_on_keyup)){
					
				$option_on_keyup=trim($option_on_keyup);
				
				if($first==0 && ($option_on_keyup[0]!='+' && $option_on_keyup[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_on_keyup[0]=='+' || $option_on_keyup[0]==',') && $first==1 ){
					$option_on_keyup[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_on_keyup, 'option_on_keyup');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($option_on_keypress)){
					
				$option_on_keypress=trim($option_on_keypress);
				
				if($first==0 && ($id[0]!='+' && $id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_on_keypress[0]=='+' || $option_on_keypress[0]==',') && $first==1 ){
					$option_on_keypress[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_on_keypress, 'option_on_keypress');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($option_on_mousedown)){
					
				$option_on_mousedown=trim($option_on_mousedown);
				
				if($first==0 && ($option_on_mousedown[0]!='+' && $option_on_mousedown[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_on_mousedown[0]=='+' || $option_on_mousedown[0]==',') && $first==1 ){
					$option_on_mousedown[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_on_mousedown, 'option_on_mousedown');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($option_on_mouseup)){
					
				$option_on_mouseup=trim($option_on_mouseup);
				
				if($first==0 && ($option_on_mouseup[0]!='+' && $option_on_mouseup[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_on_mouseup[0]=='+' || $option_on_mouseup[0]==',') && $first==1 ){
					$option_on_mouseup[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_on_mouseup, 'option_on_mouseup');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($option_on_mousemove)){
					
				$option_on_mousemove=trim($option_on_mousemove);
				
				if($first==0 && ($option_on_mousemove[0]!='+' && $option_on_mousemove[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_on_mousemove[0]=='+' || $option_on_mousemove[0]==',') && $first==1 ){
					$option_on_mousemove[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_on_mousemove, 'option_on_mousemove');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($option_on_mouseover)){
					
				$option_on_mouseover=trim($option_on_mouseover);
				
				if($first==0 && ($option_on_mouseover[0]!='+' && $option_on_mouseover[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_on_mouseover[0]=='+' || $option_on_mouseover[0]==',') && $first==1 ){
					$option_on_mouseover[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_on_mouseover, 'option_on_mouseover');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($option_on_mouseout)){
					
				$option_on_mouseout=trim($option_on_mouseout);
				
				if($first==0 && ($option_on_mouseout[0]!='+' && $option_on_mouseout[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_on_mouseout[0]=='+' || $option_on_mouseout[0]==',') && $first==1 ){
					$option_on_mouseout[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_on_mouseout, 'option_on_mouseout');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($option_order)){
					
				$option_order=trim($option_order);
				
				if($first==0 && ($option_orderp[0]!='+' && $option_order[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($option_order[0]=='+' || $option_order[0]==',') && $first==1 ){
					$option_order[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($option_order, 'option_order');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($WHERE_CLAUSE)){
				$WHERE_CLAUSE=' WHERE '.$WHERE_CLAUSE;
		}

		
		if(!empty($custom_where)){
			
			if(empty($WHERE_CLAUSE)){
				$WHERE_CLAUSE.=' WHERE ';
			}
			
			$WHERE_CLAUSE.=" $custom_where";
		}
	
		
	
		if(!empty($custom_join)){
			$CATEGORY_JOIN.=" $custom_join ";
		}
		
		if(!empty($distinct)){
			$PREFIX_ARGS.=" DISTINCT $distinct, ";
		}
		
		if(!empty($limit) && $db_type=='mssql' && !$paged){
			$PREFIX_ARGS.=" TOP $limit ";
		}
		
		if($paged){
			$page_results=PVDatabase::getPagininationOffset(pv_getContentTableName(), $CATEGORY_JOIN , $WHERE_CLAUSE, $current_page, $results_per_page, $order_by_clause);
			
			if($db_type=='mysql' || $db_type=='postgresql'){
				$limit=' '.$page_results['limit_offset'];
			}
			else if($db_type=='mssql'){
				$WHERE_CLAUSE.=' '.$page_results['limit_offset'];
				$table_name=$page_results['from_clause'];
			}
		}
		
		
		if(!empty($group_by)){
			$WHERE_CLAUSE.=" GROUP BY $group_by";
		}
		
		if(!empty($having)){
			$WHERE_CLAUSE.=" HAVING $having";
		}
		
		if(!empty($order_by)){
			$WHERE_CLAUSE.=" ORDER BY $order_by";
		}
		
		if(!empty($limit) && !$paged && ($db_type=='mysql' || $db_type=='postgresql') ){
			$WHERE_CLAUSE.=" LIMIT $limit";
		}
		
		if($paged){
			$WHERE_CLAUSE.=" $limit";
		}
		
		if(empty($custom_select)){
			$custom_select='*';
		}
		
    	$query="$PREQUERY SELECT $PREFIX_ARGS $custom_select FROM $table_name $CATEGORY_JOIN $WHERE_CLAUSE";
    	
		$result = PVDatabase::query($query);
    	
    	while ($row = PVDatabase::fetchArray($result)){
			if($paged){
				$row['current_page']=$page_results['current_page'];
				$row['last_page']=$page_results['last_page'];
				$row['total_pages']=$page_results['total_pages'];
			}
			
    		array_push($content_array, $row);
    	}//end while
    	
    	$content_array=PVDatabase::formatData($content_array);
    	
		return $content_array;
		
	}//end getFieldTypes
	
	public static function getFieldOption($field_id, $option_id){
		$field_id=ceil($field_id);
		$option_id=ceil($option_id);
		
		if(!empty($field_id) && !empty($option_id)){
			$query="SELECT * FROM ".pv_getFieldsOptionsTableName()." WHERE field='$field_id' AND option_id='$option_id' ";
			$result=PVDatabase::query($query);
			$row=PVDatabase::fetchArray($result);
			$row=PVDatabase::formatData($row);
			
			return $row;
		}
	}//end getFeildOption
	
	public static function updateFieldOption($args){
		if(is_array($args)){
			$args=PVDatabase::makeSafe($args);
			extract($args);	
		}
		
		if(!empty($field_id) && !empty($option_id)){
			$query="UPDATE ".pv_getFieldsOptionsTableName()." SET option_name='$option_name', option_value='$option_value', option_label='$option_label', option_selected='$option_selected', option_disabled='$option_disabled', option_class='$option_class', option_dir='$option_dir', option_lang='$option_lang', option_style='$option_style', option_title='$option_title', option_on_click='$option_on_click', option_on_doubelclick='$option_on_doubelclick', option_on_keydown='$option_on_keydown', option_on_keyup='$option_on_keyup', option_on_keypress='$option_on_keypress', option_on_mousedown='$option_on_mousedown', option_on_mouseup='$option_on_mouseup', option_on_mousemove='$option_on_mousemove', option_on_mouseover='$option_on_mouseover', option_on_mouseout='$option_on_mouseout', option_order='$option_order' WHERE field='$field_id' AND option_id='$option_id'";
			PVDatabase::query($query);
		}
		
	}//end updateFieldOption
	
	public static function deleteFieldOption($field_id, $option_id){
		$field_id=ceil($field_id);
		$option_id=ceil($option_id);
		
		if(!empty($field_id) && !empty($option_id)){
			
			$query="DELETE FROM ".pv_getFieldsOptionsTableName()." WHERE field='$field_id' AND option_id='$option_id'";
			PVDatabase::query($query);
		}
		
	}//end deleteFieldOption
	
	
	public static function createFieldValue($args){
		
		if(is_array($args)){
			$args=PVDatabase::makeSafe($args);
			extract($args);
		}
		
		$field_id=ceil($field_id);
		
		if(!empty($field_id)){
		
			$query="INSERT INTO ".pv_getFieldValuesTableName()."(field_id, owner_id, app_id, field_value, content_id, field_grouping) VALUES( '$field_id', '$owner_id', '$app_id', '$field_value', '$content_id', '$field_grouping' )";
			
			return PVDatabase::return_last_insert_query($query, "field_value_id", pv_getFieldValuesTableName());
		
		}
		
		
	}//end
	
	public static function getFieldValueList( $args ){
	
		if(is_array($args)){
			extract($args);
		}
		
		$content_array=array();
		$table_name=pv_getFieldValuesTableName();
		$db_type=PVDatabase::getDatabaseType();
		
		$WHERE_CLAUSE="";
		
		$first=1;
		
		if(!empty($field_id)){
					
				$field_id=trim($field_id);
				
				if($first==0 && ($field_id[0]!='+' && $field_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_id[0]=='+' || $field_id[0]==',') && $first==1 ){
					$field_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_id, 'field_id');
				
				$first=0;
		}//end not empty app_id
		
		
		
		if(!empty($field_value_id)){
					
				$field_value_id=trim($field_value_id);
				
				if($first==0 && ($field_value_id[0]!='+' && $field_value_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_value_id[0]=='+' || $field_value_id[0]==',') && $first==1 ){
					$field_value_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_value_id, 'field_value_id');
				
				$first=0;
		}//end not empty app_id
		
		
		if(!empty($owner_id)){
					
				$owner_id=trim($owner_id);
				
				if($first==0 && ($owner_id[0]!='+' && $owner_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($owner_id[0]=='+' || $owner_id[0]==',') && $first==1 ){
					$owner_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($owner_id, 'owner_id');
				
				$first=0;
		}//end not empty app_id
		
		
		if(!empty($app_id)){
					
				$app_id=trim($app_id);
				
				if($first==0 && ($app_id[0]!='+' && $app_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($app_id[0]=='+' || $app_id[0]==',') && $first==1 ){
					$option_value[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($app_id, 'app_id');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($field_value)){
					
				$field_value=trim($field_value);
				
				if($first==0 && ($field_value[0]!='+' && $field_value[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_value[0]=='+' || $field_value[0]==',') && $first==1 ){
					$field_value[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_value, 'field_value');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($content_id)){
					
				$content_id=trim($content_id);
				
				if($first==0 && ($content_id[0]!='+' && $content_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($content_id[0]=='+' || $content_id[0]==',') && $first==1 ){
					$content_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($content_id, 'content_id');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($field_grouping)){
					
				$field_grouping=trim($field_grouping);
				
				if($first==0 && ($field_grouping[0]!='+' && $field_grouping[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_grouping[0]=='+' || $field_grouping[0]==',') && $first==1 ){
					$field_grouping[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_grouping, 'field_grouping');
				
				$first=0;
		}//end not empty app_id
		
		
		if(!empty($WHERE_CLAUSE)){
				$WHERE_CLAUSE=' WHERE '.$WHERE_CLAUSE;
		}
		
		
		if(!empty($custom_where)){
			
			if(empty($WHERE_CLAUSE)){
				$WHERE_CLAUSE.=' WHERE ';
			}
			
			$WHERE_CLAUSE.=" $custom_where";
		}
	
		
	
		if(!empty($custom_join)){
			$CATEGORY_JOIN.=" $custom_join ";
		}
		
		
		if($join_fields){
			$CATEGORY_JOIN.='JOIN '.pv_getFieldsTableName().' ON '.pv_getFieldsTableName().'.field_id='.pv_getFieldValuesTableName().'.field_id ';	
		}
		
		if(!empty($distinct)){
			$PREFIX_ARGS.=" DISTINCT $distinct, ";
		}
		
		if(!empty($limit) && $db_type=='mssql' && !$paged){
			$PREFIX_ARGS.=" TOP $limit ";
		}
		
		if($paged){
			$page_results=PVDatabase::getPagininationOffset(pv_getContentTableName(), $CATEGORY_JOIN , $WHERE_CLAUSE, $current_page, $results_per_page, $order_by_clause);
			
			if($db_type=='mysql' || $db_type=='postgresql'){
				$limit=' '.$page_results['limit_offset'];
			}
			else if($db_type=='mssql'){
				$WHERE_CLAUSE.=' '.$page_results['limit_offset'];
				$table_name=$page_results['from_clause'];
			}
		}
		
		
		if(!empty($group_by)){
			$WHERE_CLAUSE.=" GROUP BY $group_by";
		}
		
		if(!empty($having)){
			$WHERE_CLAUSE.=" HAVING $having";
		}
		
		if(!empty($order_by)){
			$WHERE_CLAUSE.=" ORDER BY $order_by";
		}
		
		if(!empty($limit) && !$paged && ($db_type=='mysql' || $db_type=='postgresql') ){
			$WHERE_CLAUSE.=" LIMIT $limit";
		}
		
		if($paged){
			$WHERE_CLAUSE.=" $limit";
		}
		
		if(empty($custom_select)){
			$custom_select='*';
		}
		
    	$query="$PREQUERY SELECT $PREFIX_ARGS $custom_select FROM $table_name $CATEGORY_JOIN $WHERE_CLAUSE";
    	
		$result = PVDatabase::query($query);
    	
    	while ($row = PVDatabase::fetchArray($result)){
			if($paged){
				$row['current_page']=$page_results['current_page'];
				$row['last_page']=$page_results['last_page'];
				$row['total_pages']=$page_results['total_pages'];
			}
			
    		array_push($content_array, $row);
    	}//end while
    	
    	$content_array=PVDatabase::formatData($content_array);
    	
		return $content_array;
		
	}//end getFieldTypes
	
	public static function getFieldValue($field_value_id){
		$field_value_id=ceil($field_value_id);
		
		$query="SELECT * FROM ".pv_getFieldValuesTableName()." WHERE field_value_id='$field_value_id' ";
		
		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		PVDatabase::formatData($row);
		
		return $row;
	}//end getFieldValue
	
	
	public static function updateFieldValue($args){
		
		if(is_array($args)){
			$args=PVDatabase::makeSafe($args);
			extract($args);
		}
		
		if(!empty($field_value_id)){
			
			$query="UPDATE ".pv_getFieldValuesTableName()." SET field_id='$field_id', owner_id='$owner_id', app_id='$app_id', field_value='$field_value', content_id='$content_id', field_grouping='$field_grouping' WHERE field_value_id='$field_value_id'";
			
			PVDatabase::query($query);
		}//end !empty($field_value_id)
		
	}//end updateFieldValue
	
	public static function deleteFieldValue($field_value_id){
		$field_value_id=ceil($field_value_id);
		
		if(!empty($field_value_id)){
			$query="DELETE FROM ".pv_getFieldValuesTableName()." WHERE field_value_id='$field_value_id'";
			PVDatabase::query($query);
		}
	}//end deltteFieldValue
	
	
	public static function addContentFieldRelationship($content_id, $field_id){
		
		$content_id=ceil($content_id);
		$field_id=ceil($field_id);
		
		if(!empty($content_id) && !empty($field_id)){
			$list=pv_getContentFieldRelationshipList(array('content_id'=>$content_id, 'field_id'=>$field_id));	
			
			if(empty($list)){
				$query="INSERT INTO ".pv_getContentFieldRelationsTableName()."(content_id, field_id ) VALUES( '$content_id' , '$field_id' )";
				PVDatabase::query($query);
			}
		}
		
	}//end addContentFieldRelationship
	
	public static function getContentFieldRelationshipList($args){
		
		if(is_array($args)){
			extract($args);
		}
		
		$content_array=array();
		$table_name=pv_getContentFieldRelationsTableName();
		$db_type=PVDatabase::getDatabaseType();
		
		$WHERE_CLAUSE="";
		
		$first=1;
		
		if(!empty($content_id)){
					
				$content_id=trim($content_id);
				
				if($first==0 && ($content_id[0]!='+' && $content_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($content_id[0]=='+' || $content_id[0]==',') && $first==1 ){
					$content_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($content_id, 'content_id');
				
				$first=0;
		}//end not empty app_id
		
		
		
		if(!empty($field_id)){
					
				$field_id=trim($field_id);
				
				if($first==0 && ($field_id[0]!='+' && $field_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($field_id[0]=='+' || $field_id[0]==',') && $first==1 ){
					$field_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($field_id, 'field_id');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($WHERE_CLAUSE)){
				$WHERE_CLAUSE=' WHERE '.$WHERE_CLAUSE;
		}
		
		
		if(!empty($custom_where)){
			
			if(empty($WHERE_CLAUSE)){
				$WHERE_CLAUSE.=' WHERE ';
			}
			
			$WHERE_CLAUSE.=" $custom_where";
		}
	
		
	
		if(!empty($custom_join)){
			$CATEGORY_JOIN.=" $custom_join ";
		}
		
		if($join_fields){
			$CATEGORY_JOIN.='JOIN '.pv_getFieldsTableName().' ON '.pv_getFieldsTableName().'.field_id='.pv_getContentFieldRelationsTableName().'.field_id ';	
		}
		
		if($join_content){
			$CATEGORY_JOIN.='JOIN '.pv_getContentTableName.' ON '.pv_getContentTableName.'.content_id='.pv_getContentFieldRelationsTableName().'.content_id ';	
		}
		
		if(!empty($distinct)){
			$PREFIX_ARGS.=" DISTINCT $distinct, ";
		}
		
		if(!empty($limit) && $db_type=='mssql' && !$paged){
			$PREFIX_ARGS.=" TOP $limit ";
		}
		
		if($paged){
			$page_results=PVDatabase::getPagininationOffset(pv_getContentTableName(), $CATEGORY_JOIN , $WHERE_CLAUSE, $current_page, $results_per_page, $order_by_clause);
			
			if($db_type=='mysql' || $db_type=='postgresql'){
				$limit=' '.$page_results['limit_offset'];
			}
			else if($db_type=='mssql'){
				$WHERE_CLAUSE.=' '.$page_results['limit_offset'];
				$table_name=$page_results['from_clause'];
			}
		}
		
		
		if(!empty($group_by)){
			$WHERE_CLAUSE.=" GROUP BY $group_by";
		}
		
		if(!empty($having)){
			$WHERE_CLAUSE.=" HAVING $having";
		}
		
		if(!empty($order_by)){
			$WHERE_CLAUSE.=" ORDER BY $order_by";
		}
		
		if(!empty($limit) && !$paged && ($db_type=='mysql' || $db_type=='postgresql') ){
			$WHERE_CLAUSE.=" LIMIT $limit";
		}
		
		if($paged){
			$WHERE_CLAUSE.=" $limit";
		}
		
		if(empty($custom_select)){
			$custom_select='*';
		}
		
    	$query="$PREQUERY SELECT $PREFIX_ARGS $custom_select FROM $table_name $CATEGORY_JOIN $WHERE_CLAUSE";
    	
		$result = PVDatabase::query($query);
    	
    	while ($row = PVDatabase::fetchArray($result)){
			if($paged){
				$row['current_page']=$page_results['current_page'];
				$row['last_page']=$page_results['last_page'];
				$row['total_pages']=$page_results['total_pages'];
			}
			
    		array_push($content_array, $row);
    	}//end while
    	
    	$content_array=PVDatabase::formatData($content_array);
    	
		return $content_array;
		
	}//end getContentFieldRelationshipList
	
	public static function deleteContentFieldRelationship($content_id=0, $field_id=0){
		
	}//end deleteContentFieldRelationship
	
	
	
	
	/*filedInpterpreter
	Intreprets a single field and outputs the data depending on the type.
	
	*/
	
	public static function addContentFilter($content_type, $content_section, $command, $function){
		self::$content_filters[$content_type][$content_section][$command][$function]='';
		
	}//end addFilter
	
	public static function addCommentFilter($comment_type, $comment_section, $command, $function){
		self::$comment_filters[$comment_type][$comment_section][$command][$function]='';
		
	}//end addFilter
	
	public static function addPointFilter($point_type, $point_section, $command, $function){
		self::$point_filters[$point_type][$point_section][$command][$function]='';
		
	}//end addFilter
	
	public static function addRatingFilter($rating_type, $rating_section, $command, $function){
		self::$rating_filters[$rating_type][$rating_section][$command][$function]='';
		
	}//end addFilter
	
	public static function executeContentFilter($content_section, $content){
		
		
		
		if(is_array(self::$content_filters[self::$content_type][$content_section])){
			$command_key=array_keys(self::$content_filters[self::$content_type][$content_section]);
			$command=$command_key[0];
			
		}
		
		if(is_array(self::$content_filters[self::$content_type][$content_section][$command])){
			$function_key=array_keys(self::$content_filters[self::$content_type][$content_section][$command]);
			$function=$function_key[0];
		}
		
		
		
		if($command=='function' && function_exists ( $function )){
			
			return call_user_func($function, $content);
		}
		else if($command=='append_pre'){
			return $function.$content;
		}
		else if($command=='append_post'){
			return $content.$function;
		}
		else if($command=='replace'){
			return $function;
		}
		
		else{
			return $content;	
		}
		
	}//end
	
	public static function executeCommentFilter($content_section, $content){
		
		if(is_array(self::$comment_filters[self::$comment_type][$content_section])){
			$command_key=array_keys(self::$comment_filters[self::$comment_type][$content_section]);
			$command=$command_key[0];
		}
		
		if(is_array(self::$comment_filters[self::$comment_type][$content_section][$command])){
			$function_key=array_keys(self::$comment_filters[self::$comment_type][$content_section][$command]);
			$function=$function_key[0];
		}
		
		
		if($command=='function' && function_exists ( $function )){
			
			return call_user_func($function, $content);
		}
		else{
			return $content;	
		}
		
	}//end
	
	public static function executePointFilter($content_section, $content){
		
		if(is_array(self::$point_filters[self::$point_type][$content_section])){
			$command_key=array_keys(self::$point_filters[self::$point_type][$content_section]);
			$command=$command_key[0];
		}
		
		if(is_array(self::$point_filters[self::$point_type][$content_section][$command])){
			$function_key=array_keys(self::$point_filters[self::$point_type][$content_section][$command]);
			$function=$function_key[0];
		}
		
		
		if($command=='function' && function_exists ( $function )){
			
			return call_user_func($function, $content);
		}
		else{
			return $content;	
		}
		
	}//end
	
	
	public static function executeRatingFilter($content_section, $content){
		
		if(is_array(self::$rating_filters[self::$rating_type][$content_section])){
			$command_key=array_keys(self::$rating_filters[self::$rating_type][$content_section]);
			$command=$command_key[0];
		}
		
		if(is_array(self::$rating_filters[self::$rating_type][$content_section][$command])){
			$function_key=array_keys(self::$rating_filters[self::$rating_type][$content_section][$command]);
			$function=$function_key[0];
		}
		
		
		if($command=='function' && function_exists ( $function )){
			
			return call_user_func($function, $content);
		}
		else{
			return $content;	
		}
		
	}//end
	
	/**************************************************************************
	* Content ID
	**************************************************************************/
	
	public static function printContentID($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('content_id', self::$content_id);
		}
		else{
			echo self::$content_id;
		}
	}//end printContentTitle
	
	public static function getContentID($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('content_id', self::$content_id);
		}
		else{
			return self::$content_id;
		}
	}//end getContentTitle
	
	public static function printContentAppID($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('app_id', self::$app_id);
		}
		else{
			echo self::$app_id;
		}
	}//end printContentTitle
	
	public static function getContentAppID($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('app_id', self::$app_id);
		}
		else{
			return self::$app_id;
		}
	}//end getContentTitle
	
	
	public static function printContentTitle($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('content_title', self::$content_title);
		}
		else{
			echo self::$content_title;
		}
	}//end printContentTitle
	
	public static function getContentTitle($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('content_title', self::$content_title);
		}
		else{
			return self::$content_title;
		}
	}//end getContentTitle
	
	public static function printContentOwnerID($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('owner_id', self::$owner_id);
		}
		else{
			echo self::$owner_id;
		}
	}//end printContentTitle
	
	public static function getContentOwnerID($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('owner_id', self::$owner_id);
		}
		else{
			return self::$owner_id;
		}
	}//end getContentTitle
	
	
	public static function printContentParentID($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('parent_id', self::$parent_id);
		}
		else{
			echo self::$parent_id;
		}
	}//end printContentTitle
	
	public static function getContentParentID($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('parent_id', self::$parent_id);
		}
		else{
			return self::$parent_id;
		}
	}//end getContentTitle
	
	public static function printContentAlias($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('content_alias', self::$content_alias);
		}
		else{
			echo self::$content_alias;
		}
	}//end printContentTitle
	
	public static function getContentAlias($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('content_alias', self::$content_alias);
		}
		else{
			return self::$content_alias;
		}
	}//end getContentTitle
	
	public static function printContentDescription($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('content_description', self::$content_description);
		}
		else{
			echo self::$content_description;
		}
	}//end printContentTitle
	
	public static function getContentDesciption($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('content_description', self::$content_description);
		}
		else{
			return self::$content_description;
		}
	}//end getContentTitle
	
	public static function printContentMetaTags($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('content_meta_tags', self::$content_meta_tags);
		}
		else{
			echo self::$content_meta_tags;
		}
	}//end printContentTitle
	
	public static function getContentMetaTags($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('content_meta_tags', self::$content_meta_tags);
		}
		else{
			return self::$content_meta_tags;
		}
	}//end getContentTitle
	
	public static function printContentMetaDescription($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('content_meta_description', self::$content_meta_description);
		}
		else{
			echo self::$content_meta_description;
		}
	}//end printContentTitle
	
	public static function getContentMetaDescription($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('content_meta_description', self::$content_meta_description);
		}
		else{
			return self::$content_meta_description;
		}
	}//end getContentTitle
	
	public static function printContentThumbnailLocation($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('content_thumbnail_location', self::$content_thumbnail);
		}
		else{
			echo self::$content_thumbnail;
		}
	}//end printContentTitle
	
	public static function getContentThumbnailLocation($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('content_thumbnail_location', self::$content_thumbnail);
		}
		else{
			return self::$content_thumbnail;
		}
	}//end getContentTitle
	
	public static function printContentThumbnail($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('content_thumbnail_location', self::$thumb_url);
		}
		else{
			echo self::$content_thumbnail;
		}
	}//end printContentTitle
	
	public static function getContentThumbnail($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('content_thumbnail_location', self::$content_thumbnail);
		}
		else{
			return self::$content_thumbnail;
		}
	}//end getContentTitle
	
	
	public static function printContentDateCreated($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('date_created', self::$date_created);
		}
		else{
			echo self::$date_created;
		}
	}//end printContentTitle
	
	public static function getContentDateCreated($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('date_created', self::$date_created);
		}
		else{
			return self::$date_created;
		}
	}//end getContentTitle
	
	public static function printContentDateModified($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('date_modified', self::$date_modified);
		}
		else{
			echo self::$date_modified;
		}
	}//end printContentTitle
	
	public static function getContentDateModified($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('date_modified', self::$date_modified);
		}
		else{
			return self::$date_modified;
		}
	}//end getContentTitle
	
	public static function printContentDateActive($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('date_active', self::$date_active);
		}
		else{
			echo self::$date_active;
		}
	}//end printContentTitle
	
	public static function getContentDateActive($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('date_active', self::$date_active);
		}
		else{
			return self::$date_active;
		}
	}//end getContentTitle
	
	public static function printContentDateInactive($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('date_inactive', self::$date_inactive);
		}
		else{
			echo self::$date_inactive;
		}
	}//end printContentTitle
	
	public static function getContentDateInactive($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('date_inactive', self::$date_inactive);
		}
		else{
			return self::$date_inactive;
		}
	}//end getContentTitle
	
	public static function printContentIsSearchable($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('is_searchable', self::$is_searchable);
		}
		else{
			echo self::$is_searchable;
		}
	}//end printContentTitle
	
	public static function getContentIsSearchable($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('is_searchable', self::$is_searchable);
		}
		else{
			return self::$is_searchable;
		}
	}//end getContentTitle
	
	public static function printContentAllowComments($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('allow_comments', self::$allow_comments);
		}
		else{
			echo self::$allow_comments;
		}
	}//end printContentTitle
	
	public static function getContentAllowComments($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('allow_comments', self::$allow_comments);
		}
		else{
			return self::$allow_comments;
		}
	}//end getContentTitle
	
	
	public static function printContentAllowRating($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('allow_rating', self::$allow_rating);
		}
		else{
			echo self::$allow_rating;
		}
	}//end printContentTitle
	
	public static function getContentAllowRating($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('allow_rating', self::$allow_rating);
		}
		else{
			return self::$allow_rating;
		}
	}//end getContentTitle
	
	
	public static function printContentActive($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('content_active', self::$content_active);
		}
		else{
			echo self::$content_active;
		}
	}//end printContentTitle
	
	public static function getContentActive($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('content_active', self::$content_active);
		}
		else{
			return self::$content_active;
		}
	}//end getContentTitle
	
	
	public static function printContentPromoted($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('content_promoted', self::$content_promoted);
		}
		else{
			echo self::$content_promoted;
		}
	}//end printContentTitle
	
	public static function getContentPromoted($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('content_promoted', self::$content_promoted);
		}
		else{
			return self::$content_promoted;
		}
	}//end getContentTitle
	
	
	public static function printContentPermissions($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('content_permissions', self::$content_permissions);
		}
		else{
			echo self::$content_permissions;
		}
	}//end printContentTitle
	
	public static function getContentPermissions($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('content_permissions', self::$content_permissions);
		}
		else{
			return self::$content_permissions;
		}
	}//end getContentTitle
	
	
	public static function printContentType($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('content_type', self::$content_type);
		}
		else{
			echo self::$content_type;
		}
	}//end printContentTitle
	
	public static function getContentType($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('content_type', self::$content_type);
		}
		else{
			return self::$content_type;
		}
	}//end getContentTitle
	
	
	public static function printContentLanguage($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('content_language', self::$content_language);
		}
		else{
			echo self::$content_language;
		}
	}//end printContentTitle
	
	public static function getContentLanguage($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('content_language', self::$content_language);
		}
		else{
			return self::$content_languagee;
		}
	}//end getContentTitle
	
	public static function printContentTranslate($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('translate_content', self::$translate_content);
		}
		else{
			echo self::$translate_content;
		}
	}//end printContentTitle
	
	public static function getContentTranslate($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('translate_content', self::$translate_content);
		}
		else{
			return self::$translate_content;
		}
	}//end getContentTitle
	
	public static function printContentApproved($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('content_approved', self::$content_approved);
		}
		else{
			echo self::$content_approved;
		}
	}//end printContentTitle
	
	public static function getContentApproved($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('content_approved', self::$content_approved);
		}
		else{
			return self::$content_approved;
		}
	}//end getContentTitle
	
	
	public static function printContentCategory($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('content_category', self::$content_category);
		}
		else{
			echo self::$content_category;
		}
	}//end printContentTitle
	
	public static function getContentCategory($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('content_category', self::$content_category);
		}
		else{
			return self::$content_category;
		}
	}//end getContentTitle
	
	
	public static function printContentParameters($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('content_parameters', self::$content_parameters);
		}
		else{
			echo self::$content_parameters;
		}
	}//end printContentTitle
	
	public static function getContentParemeters($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('content_parameters', self::$content_parameters);
		}
		else{
			return self::$content_parameters;
		}
	}//end getContentTitle
	
	
	public static function printContentOrder($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('content_order', self::$content_order);
		}
		else{
			echo self::$content_order;
		}
	}//end printContentTitle
	
	public static function getContentOrder($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('content_order', self::$content_order);
		}
		else{
			return self::$content_order;
		}
	}//end getContentTitle
	
	
	public static function printContentSymLink($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('sym_link', self::$sym_link);
		}
		else{
			echo self::$sym_link;
		}
	}//end printContentTitle
	
	public static function getContentSymLink($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('sym_link', self::$sym_link);
		}
		else{
			return self::$sym_link;
		}
	}//end getContentTitle
	
	public static function printContentTaxonomy($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('content_taxonomy', self::$content_taxonomy);
		}
		else{
			echo self::$content_taxonomy;
		}
	}//end printContentTitle
	
	public static function getContentTaxonomy($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('content_taxonomy', self::$content_taxonomy);
		}
		else{
			return self::$content_taxonomy;
		}
	}//end getContentTitle
	
	
	
	
	
	
	
	
	/**************************************************************************
	*Print out image content
	**************************************************************************/
	
	public static function printContentImageType($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('image_type', self::$image_type);
		}
		else{
			echo self::$image_type;
		}
	}//end printContentTitle
	
	public static function getContentImageType($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('image_type', self::$image_type);
		}
		else{
			return self::$image_type;
		}
	}//end getContentTitle
	
	
	public static function printContentImageSize($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('image_size', self::$image_size);
		}
		else{
			echo self::$image_size;
		}
	}//end printContentTitle
	
	public static function getContentImageSize($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('image_size', self::$image_size);
		}
		else{
			return self::$image_size;
		}
	}//end getContentTitle
	
	
	public static function printContentImageUrl($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('image_url', self::$image_url);
		}
		else{
			echo self::$image_url;
		}
	}//end printContentTitle
	
	public static function getContentImageUrl($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('image_url', self::$image_url);
		}
		else{
			return self::$image_url;
		}
	}//end getContentTitle
	
	
	public static function printContentThumbUrl($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('thumb_url', self::$thumb_url);
		}
		else{
			echo self::$thumb_url;
		}
	}//end printContentTitle
	
	public static function getContentThumbUrl($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('thumb_url', self::$thumb_url);
		}
		else{
			return self::$thumb_url;
		}
	}//end getContentTitle
	
	
	public static function printContentImageHeight($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('image_height', self::$image_height);
		}
		else{
			echo self::$image_height;
		}
	}//end printContentTitle
	
	public static function getContentImageHeight($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('image_height', self::$image_height);
		}
		else{
			return self::$image_height;
		}
	}//end getContentTitle
	
	
	public static function printContentImageWidth($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('image_width', self::$image_width);
		}
		else{
			echo self::$image_width;
		}
	}//end printContentTitle
	
	public static function getContentImageWidth($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('image_width', self::$image_width);
		}
		else{
			return self::$image_width;
		}
	}//end getContentTitle
	
	
	public static function printContentImageThumbWidth($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('thumb_width', self::$thumb_width);
		}
		else{
			echo self::$thumb_width;
		}
	}//end printContentTitle
	
	public static function getContentImageThumbWidth($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('thumb_width', self::$thumb_width);
		}
		else{
			return self::$thumb_width;
		}
	}//end getContentTitle
	
	public static function printContentImageThumbHeight($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('thumb_height', self::$thumb_height);
		}
		else{
			echo self::$thumb_height;
		}
	}//end printContentTitle
	
	public static function getContentImageThumbHeight($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('thumb_height', self::$thumb_height);
		}
		else{
			return self::$thumb_height;
		}
	}//end getContentTitle
	
	public static function printContentImageSrc($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('image_src', self::$image_src);
		}
		else{
			echo self::$image_src;
		}
	}//end printContentTitle
	
	public static function getContentImageSrc($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('image_src', self::$image_src);
		}
		else{
			return self::$image_src;
		}
	}//end getContentTitle
	
	/*
	* Text Content
	*/

	public static function printContentText($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('text_content', self::$text_content);
		}
		else{
			echo self::$text_content;
		}
	}//end printContentTitle
	
	public static function getContentText($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('text_content', self::$text_content);
		}
		else{
			return self::$text_content;
		}
	}//end getContentTitle
	
	
	public static function printContentTextPageGroup($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('text_page_group', self::$text_page_group);
		}
		else{
			echo self::$text_page_group;
		}
	}//end printContentTitle
	
	public static function getContentTextPageGroup($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('text_page_group', self::$text_page_group);
		}
		else{
			return self::$text_page_group;
		}
	}//end getContentTitle
	
	
	public static function printContentTextPageNumber($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('text_page_number', self::$text_page_number);
		}
		else{
			echo self::$text_page_number;
		}
	}//end printContentTitle
	
	public static function getContentTextPageNumber($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('text_page_number', self::$text_page_number);
		}
		else{
			return self::$text_page_number;
		}
	}//end getContentTitle
	
	
	
	public static function printContentTextSrc($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('text_src', self::$text_src);
		}
		else{
			echo self::$text_src;
		}
	}//end printContentTitle
	
	public static function getContentTextSrc($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('text_src', self::$text_src);
		}
		else{
			return self::$text_src;
		}
	}//end getContentTitle
	
	/**************************************************************************
	* Video Content
	**************************************************************************/
	
	public static function printContentVideoType($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('video_type', self::$video_type);
		}
		else{
			echo self::$video_type;
		}
	}//end printContentTitle
	
	public static function getContentVideoType($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('video_type', self::$video_type);
		}
		else{
			return self::$video_type;
		}
	}//end getContentTitle
	
	public static function printContentVideoLength($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('video_length', self::$video_length);
		}
		else{
			echo self::$video_length;
		}
	}//end printContentTitle
	
	public static function getContentVideoLength($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('video_length', self::$video_length);
		}
		else{
			return self::$video_length;
		}
	}//end getContentTitle
	
	
	public static function printContentVideoAllowEmbedding($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('video_allow_embedding', self::$video_allow_embedding);
		}
		else{
			echo self::$video_allow_embedding;
		}
	}//end printContentTitle
	
	public static function getContentVideoAllowEmbedding($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('video_allow_embedding', self::$video_allow_embedding);
		}
		else{
			return self::$video_allow_embedding;
		}
	}//end getContentTitle
	
	
	public static function printContentVideoFlvFile($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('flv_file', self::$flv_file);
		}
		else{
			echo self::$flv_file;
		}
	}//end printContentTitle
	
	public static function getContentVideoFlvFile($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('flv_file', self::$flv_file);
		}
		else{
			return self::$flv_file;
		}
	}//end getContentTitle
	
	
	
	public static function printContentVideoMp4File($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('mp4_file', self::$mp4_file);
		}
		else{
			echo self::$mp4_file;
		}
	}//end printContentTitle
	
	public static function getContentVideoMp4File($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('mp4_file', self::$mp4_file);
		}
		else{
			return self::$mp4_file;
		}
	}//end getContentTitle
	
	
	
	public static function printContentVideoWmvFile($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('wmv_file', self::$wmv_file);
		}
		else{
			echo self::$wmv_file;
		}
	}//end printContentTitle
	
	public static function getContentVideoWmvFile($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('wmv_file', self::$wmv_file);
		}
		else{
			return self::$wmv_file;
		}
	}//end getContentTitle
	
	
	public static function printContentVideoMpegvFile($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('mpeg_file', self::$mpeg_file);
		}
		else{
			echo self::$mpeg_file;
		}
	}//end printContentTitle
	
	public static function getContentVideoMpegFile($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('mpeg_file', self::$mpeg_file);
		}
		else{
			return self::$mpeg_file;
		}
	}//end getContentTitle
	
	
	
	public static function printContentVideoRmFile($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('rm_file', self::$rm_file);
		}
		else{
			echo self::$rm_file;
		}
	}//end printContentTitle
	
	public static function getContentVideoRmFile($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('rm_file', self::$rm_file);
		}
		else{
			return self::$rm_file;
		}
	}//end getContentTitle
	
	
	public static function printContentVideoAviFile($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('avi_file', self::$avi_file);
		}
		else{
			echo self::$avi_file;
		}
	}//end printContentTitle
	
	public static function getContentVideoAviFile($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('avi_file', self::$avi_file);
		}
		else{
			return self::$avi_file;
		}
	}//end getContentTitle
	
	
	public static function printContentVideoMovFile($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('mov_file', self::$mov_file);
		}
		else{
			echo self::$mov_file;
		}
	}//end printContentTitle
	
	public static function getContentVideoMovFile($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('mov_file', self::$mov_file);
		}
		else{
			return self::$mov_file;
		}
	}//end getContentTitle
	
	
	public static function printContentVideoAsfFile($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('asf_file', self::$asf_file);
		}
		else{
			echo self::$asf_file;
		}
	}//end printContentTitle
	
	public static function getContentVideoAsfFile($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('asf_file', self::$asf_file);
		}
		else{
			return self::$asf_file;
		}
	}//end getContentTitle
	
	
	public static function printContentVideoEnableHQ($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('enable_hq', self::$enable_hq);
		}
		else{
			echo self::$enable_hq;
		}
	}//end printContentTitle
	
	public static function getContentVideoEnableHQ($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('enable_hq', self::$enable_hq);
		}
		else{
			return self::$enable_hq;
		}
	}//end getContentTitle
	

	
	
	public static function printContentVideoAutoHQ($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('auto_hq', self::$auto_hq);
		}
		else{
			echo self::$auto_hq;
		}
	}//end printContentTitle
	
	public static function getContentVideoAutoHQ($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('auto_hq', self::$auto_hq);
		}
		else{
			return self::$auto_hq;
		}
	}//end getContentTitle
	
	
	public static function printContentVideoSrc($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('video_src', self::$video_src);
		}
		else{
			echo self::$video_src;
		}
	}//end printContentTitle
	
	public static function getContentVideoSrc($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('video_src', self::$video_src);
		}
		else{
			return self::$video_src;
		}
	}//end getContentTitle
	
	
	
	public static function printContentVideoEmbed($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('video_embed', self::$video_embed);
		}
		else{
			echo self::$video_embed;
		}
	}//end printContentTitle
	
	public static function getContentVideoEmbed($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('video_embed', self::$video_embed);
		}
		else{
			return self::$video_embed;
		}
	}//end getContentTitle
	
	
	/**************************************************************************
	* Event Informartion
	**************************************************************************/
	
	public static function printContentEventLocation($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('event_location', self::$event_location);
		}
		else{
			echo self::$event_location;
		}
	}//end printContentTitle
	
	public static function getContentEventLocation($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('event_location', self::$event_location);
		}
		else{
			return self::$event_location;
		}
	}//end getContentTitle
	
	public static function printContentEventStartDate($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('event_start_date', self::$event_start_date);
		}
		else{
			echo self::$event_start_date;
		}
	}//end printContentTitle
	
	public static function getContentEventStartDate($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('event_start_date', self::$event_start_date);
		}
		else{
			return self::$event_start_date;
		}
	}//end getContentTitle
	
	
	public static function printContentEventEndDate($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('event_end_date', self::$event_end_date);
		}
		else{
			echo self::$event_end_date;
		}
	}//end printContentTitle
	
	public static function getContentEventEndDate($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('event_end_date', self::$event_end_date);
		}
		else{
			return self::$event_end_date;
		}
	}//end getContentTitle
	
	public static function printContentEventCountry($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('event_country', self::$event_country);
		}
		else{
			echo self::$event_country;
		}
	}//end printContentTitle
	
	public static function getContentEventCountry($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('event_country', self::$event_country);
		}
		else{
			return self::$event_country;
		}
	}//end getContentTitle
	
	
	public static function printContentEventAddress($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('event_address', self::$event_address);
		}
		else{
			echo self::$event_address;
		}
	}//end printContentTitle
	
	public static function getContentEventAddress($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('event_address', self::$event_address);
		}
		else{
			return self::$event_address;
		}
	}//end getContentTitle
	
	public static function printContentEventCity($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('event_city', self::$event_city);
		}
		else{
			echo self::$event_city;
		}
	}//end printContentTitle
	
	public static function getContentEventCity($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('event_city', self::$event_city);
		}
		else{
			return self::$event_city;
		}
	}//end getContentTitle
	
	
	public static function printContentEventState($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('event_state', self::$event_state);
		}
		else{
			echo self::$event_state;
		}
	}//end printContentTitle
	
	public static function getContentEventState($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('event_state', self::$event_state);
		}
		else{
			return self::$event_state;
		}
	}//end getContentTitle
	
	
	public static function printContentEventZip($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('event_zip', self::$event_zip);
		}
		else{
			echo self::$event_zip;
		}
	}//end printContentTitle
	
	public static function getContentEventZip($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('event_zip', self::$event_zip);
		}
		else{
			return self::$event_zip;
		}
	}//end getContentTitle
	
	public static function printContentEventMap($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('event_map', self::$event_map);
		}
		else{
			echo self::$event_map;
		}
	}//end printContentTitle
	
	public static function getContentEventMap($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('event_map', self::$event_map);
		}
		else{
			return self::$event_map;
		}
	}//end getContentTitle
	
	
	public static function printContentEventSrc($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('event_src', self::$event_src);
		}
		else{
			echo self::$event_src;
		}
	}//end printContentTitle
	
	public static function getContentEventSrc($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('event_src', self::$event_src);
		}
		else{
			return self::$event_src;
		}
	}//end getContentTitle
	
	
	public static function printContentEventUndefinedEndtime($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('undefined_endtime', self::$undefined_endtime);
		}
		else{
			echo self::$undefined_endtime;
		}
	}//end printContentTitle
	
	public static function getContentEventUndefinedEndtime($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('undefined_endtime', self::$undefined_endtime);
		}
		else{
			return self::$undefined_endtime;
		}
	}//end getContentTitle
	
	/**************************************************************************
	* Content Audio
	**************************************************************************/
	
	public static function printContentAudioLength($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('audio_length', self::$audio_length);
		}
		else{
			echo self::$audio_length;
		}
	}//end printContentTitle
	
	public static function getContentAudioLength($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('audio_length', self::$audio_length);
		}
		else{
			return self::$audio_length;
		}
	}//end getContentTitle
	
	public static function printContentAudioMidFile($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('mid_file', self::$mid_file);
		}
		else{
			echo self::$mid_file;
		}
	}//end printContentTitle
	
	public static function getContentAudioMidFile($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('mid_file', self::$mid_file);
		}
		else{
			return self::$mid_file;
		}
	}//end getContentTitle
	
	public static function printContentAudioWavFile($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('wav_file', self::$wav_file);
		}
		else{
			echo self::$wav_file;
		}
	}//end printContentTitle
	
	public static function getContentAudioWavFile($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('wav_file', self::$wav_file);
		}
		else{
			return self::$wav_file;
		}
	}//end getContentTitle
	
	
	public static function printContentAudioAifFile($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('aif_file', self::$aif_file);
		}
		else{
			echo self::$aif_file;
		}
	}//end printContentTitle
	
	public static function getContentAudioAifFile($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('aif_file', self::$aif_file);
		}
		else{
			return self::$aif_file;
		}
	}//end getContentTitle
	
	
	public static function printContentAudioMp3File($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('mp3_file', self::$mp3_file);
		}
		else{
			echo self::$mp3_file;
		}
	}//end printContentTitle
	
	public static function getContentAudioMp3File($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('mp3_file', self::$mp3_file);
		}
		else{
			return self::$mp3_file;
		}
	}//end getContentTitle
	
	
	public static function printContentAudioRaFile($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('ra_file', self::$ra_file);
		}
		else{
			echo self::$ra_file;
		}
	}//end printContentTitle
	
	public static function getContentEventAudioRaFile($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('ra_file', self::$ra_file);
		}
		else{
			return self::$ra_file;
		}
	}//end getContentTitle
	
	public static function printContentAudioSampleLength($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('sample_length', self::$sample_length);
		}
		else{
			echo self::$sample_length;
		}
	}//end printContentTitle
	
	public static function getContentAudioSampleLength($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('sample_length', self::$sample_length);
		}
		else{
			return self::$sample_length;
		}
	}//end getContentTitle
	
	
	public static function printContentAudioSrc($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('audio_src', self::$audio_src);
		}
		else{
			echo self::$audio_src;
		}
	}//end printContentTitle
	
	public static function getContentAudioSrc($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('audio_src', self::$audio_src);
		}
		else{
			return self::$audio_src;
		}
	}//end getContentTitle
	
	/**************************************************************************
	*File Conent
	**************************************************************************/
	
	public static function printContentFileType($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('file_type', self::$file_type);
		}
		else{
			echo self::$file_type;
		}
	}//end printContentTitle
	
	public static function getContentFileType($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('file_type', self::$file_type);
		}
		else{
			return self::$file_type;
		}
	}//end getContentTitle
	
	
	public static function printContentFileSize($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('file_size', self::$file_size);
		}
		else{
			echo self::$file_size;
		}
	}//end printContentTitle
	
	public static function getContentFileSize($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('file_size', self::$file_size);
		}
		else{
			return self::$file_size;
		}
	}//end getContentTitle
	
	
	public static function printContentFileLocation($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('file_location', self::$file_location);
		}
		else{
			echo self::$file_location;
		}
	}//end printContentTitle
	
	public static function getContentFileLocation($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('file_location', self::$file_location);
		}
		else{
			return self::$file_location;
		}
	}//end getContentTitle
	
	public static function printContentFileName($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('file_name', self::$file_name);
		}
		else{
			echo self::$file_name;
		}
	}//end printContentTitle
	
	public static function getContentFileName($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('file_name', self::$file_name);
		}
		else{
			return self::$file_name;
		}
	}//end getContentTitle
	
	public static function printContentFileSrc($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('file_src', self::$file_src);
		}
		else{
			echo self::$file_src;
		}
	}//end printContentTitle
	
	public static function getContentFileSrc($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('file_src', self::$file_src);
		}
		else{
			return self::$file_src;
		}
	}//end getContentTitle
	
	public static function printContentFileDownloadable($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('file_downloadable', self::$file_downloadable);
		}
		else{
			echo self::$file_downloadable;
		}
	}//end printContentTitle
	
	public static function getContentFileDownloadable($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('file_downloadable', self::$file_downloadable);
		}
		else{
			return self::$file_downloadable;
		}
	}//end getContentTitle
	
	public static function printContentFileMaxDownloads($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('file_max_download', self::$file_max_download);
		}
		else{
			echo self::$file_max_download;
		}
	}//end printContentTitle
	
	public static function getContentFileMaxDownloads($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('file_max_downloads', self::$file_max_downloads);
		}
		else{
			return self::$file_max_downloads;
		}
	}//end getContentTitle
	
	public static function printContentFileVersion($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('file_version', self::$file_version);
		}
		else{
			echo self::$file_version;
		}
	}//end printContentTitle
	
	public static function getContentFileVersion($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('file_version', self::$file_version);
		}
		else{
			return self::$file_version;
		}
	}//end getContentTitle
	
	public static function printContentFileLicense($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('file_license', self::$file_license);
		}
		else{
			echo self::$file_license;
		}
	}//end printContentTitle
	
	public static function getContentFileLicense($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('file_license', self::$file_license);
		}
		else{
			return self::$file_license;
		}
	}//end getContentTitle
	
	
	/*************************************************************************
	* Products
	*************************************************************************/
	
	public static function printContentProductID($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('product_id', self::$product_id);
		}
		else{
			echo self::$product_id;
		}
	}//end printContentTitle
	
	public static function getContentProductID($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('product_id', self::$product_id);
		}
		else{
			return self::$product_id;
		}
	}//end getContentTitle
	
	public static function printContentProductSKU($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('product_sku', self::$product_sku);
		}
		else{
			echo self::$product_sku;
		}
	}//end printContentTitle
	
	public static function getContentProductSKU($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('product_sku', self::$product_sku);
		}
		else{
			return self::$product_sku;
		}
	}//end getContentTitle
	
	
	public static function printContentProductIDSKU($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('product_idsku', self::$product_idsku);
		}
		else{
			echo self::$product_idsku;
		}
	}//end printContentTitle
	
	public static function getContentProductIDSKU($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('product_idsku', self::$product_idsku);
		}
		else{
			return self::$product_idsku;
		}
	}//end getContentTitle
	
	public static function printContentProductVendorID($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('product_vendor_id', self::$product_vendor_id);
		}
		else{
			echo self::$product_vendor_id;
		}
	}//end printContentTitle
	
	public static function getContentProductVendorID($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('product_vendor_id', self::$product_vendor_id);
		}
		else{
			return self::$product_vendor_id;
		}
	}//end getContentTitle
	
	public static function printContentProductQuantity($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('product_quantity', self::$product_quantity);
		}
		else{
			echo self::$product_quantity;
		}
	}//end printContentTitle
	
	public static function getContentProductQuantity($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('product_quantity', self::$product_quantity);
		}
		else{
			return self::$product_quantity;
		}
	}//end getContentTitle
	
	public static function printContentProductPrice($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('product_price', self::$product_price);
		}
		else{
			echo self::$product_price;
		}
	}//end printContentTitle
	
	public static function getContentProductPrice($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('product_price', self::$product_price);
		}
		else{
			return self::$product_price;
		}
	}//end getContentTitle
	
	
	public static function printContentProductDiscountPrice($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('product_discount_price', self::$product_discount_price);
		}
		else{
			echo self::$product_discount_price;
		}
	}//end printContentTitle
	
	public static function getContentProductDiscountPrice($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('product_discount_price', self::$product_discount_price);
		}
		else{
			return self::$product_discount_price;
		}
	}//end getContentTitle
	
	public static function printContentProductSize($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('product_size', self::$product_size);
		}
		else{
			echo self::$product_size;
		}
	}//end printContentTitle
	
	public static function getContentProductSize($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('product_size', self::$product_size);
		}
		else{
			return self::$product_size;
		}
	}//end getContentTitle
	
	public static function printContentProductColor($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('product_color', self::$product_color);
		}
		else{
			echo self::$product_color;
		}
	}//end printContentTitle
	
	public static function getContentProductColor($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('product_color', self::$product_color);
		}
		else{
			return self::$product_color;
		}
	}//end getContentTitle
	
	
	public static function printContentProductWeight($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('product_weight', self::$product_weight);
		}
		else{
			echo self::$product_weight;
		}
	}//end printContentTitle
	
	public static function getContentProductWeight($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('product_weight', self::$product_weight);
		}
		else{
			return self::$product_weight;
		}
	}//end getContentTitle
	
	public static function printContentProductHeight($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('product_height', self::$product_height);
		}
		else{
			echo self::$product_height;
		}
	}//end printContentTitle
	
	public static function getContentProductHeight($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('product_height', self::$product_height);
		}
		else{
			return self::$product_height;
		}
	}//end getContentTitle
	
	
	public static function printContentProductLength($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('product_length', self::$product_length);
		}
		else{
			echo self::$product_length;
		}
	}//end printContentTitle
	
	public static function getContentProductLenght($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('product_length', self::$product_length);
		}
		else{
			return self::$product_length;
		}
	}//end getContentTitle
	
	public static function printContentProductCurrency($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('product_currency', self::$product_currency);
		}
		else{
			echo self::$product_currency;
		}
	}//end printContentTitle
	
	public static function getContentProductCurrency($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('product_currency', self::$product_currency);
		}
		else{
			return self::$product_currency;
		}
	}//end getContentTitle
	
	public static function printContentProductInStock($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('product_in_stock', self::$product_in_stock);
		}
		else{
			echo self::$product_in_stock;
		}
	}//end printContentTitle
	
	public static function getContentProductInStock($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('product_in_stock', self::$product_in_stock);
		}
		else{
			return self::$product_in_stock;
		}
	}//end getContentTitle
	
	public static function printContentProductType($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('product_type', self::$product_type);
		}
		else{
			echo self::$product_type;
		}
	}//end printContentTitle
	
	public static function getContentProductType($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('product_type', self::$product_type);
		}
		else{
			return self::$product_type;
		}
	}//end getContentTitle
	
	public static function printContentProductTaxID($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('product_tax_id', self::$product_tax_id);
		}
		else{
			echo self::$product_tax_id;
		}
	}//end printContentTitle
	
	public static function getContentProductTaxID($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('product_tax_id', self::$product_tax_id);
		}
		else{
			return self::$product_tax_id;
		}
	}//end getContentTitle
	
	public static function printContentProductAttribute($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('product_attribute', self::$product_attribute);
		}
		else{
			echo self::$product_attribute;
		}
	}//end printContentTitle
	
	public static function getContentProductAttribute($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('product_attribute', self::$product_attribute);
		}
		else{
			return self::$product_attribute;
		}
	}//end getContentTitle
	
	
	public static function printContentProductVersion($apply_filter=true){
		if($apply_filter==true){
			echo self::executeContentFilter('product_version', self::$product_version);
		}
		else{
			echo self::$product_version;
		}
	}//end printContentTitle
	
	public static function getContentProductVersion($apply_filter=true){
		if($apply_filter==true){
			return self::executeContentFilter('product_version', self::$product_version);
		}
		else{
			return self::$product_version;
		}
	}//end getContentTitle
	
	
	/**************************************************************************
	*Comments
	**************************************************************************/
	
	public static function printCommentOwnerID($apply_filter=true){
		if($apply_filter==true){
			echo self::executeCommentFilter('owner_id', self::$comment_owner_id);
		}
		else{
			echo self::$comment_owner_id;
		}
	}//end printContentTitle
	
	public static function getCommentOwnerID($apply_filter=true){
		if($apply_filter==true){
			return self::executeCommentFilter('owner_id', self::$comment_owner_id);
		}
		else{
			return self::$comment_owner_id;
		}
	}//end getContentTitle
	
	public static function printCommentDate($apply_filter=true){
		if($apply_filter==true){
			echo self::executeCommentFilter('comment_date', self::$comment_date);
		}
		else{
			echo self::$comment_date;
		}
	}//end printContentTitle
	
	public static function getCommentDate($apply_filter=true){
		if($apply_filter==true){
			return self::executeCommentFilter('comment_date', self::$comment_date);
		}
		else{
			return self::$comment_date;
		}
	}//end getContentTitle
	
	public static function printCommentApproved($apply_filter=true){
		if($apply_filter==true){
			echo self::executeCommentFilter('comment_approved', self::$comment_approved);
		}
		else{
			echo self::$comment_approved;
		}
	}//end printContentTitle
	
	public static function getCommentApproved($apply_filter=true){
		if($apply_filter==true){
			return self::executeCommentFilter('comment_approved', self::$comment_approved);
		}
		else{
			return self::$comment_approved;
		}
	}//end getContentTitle
	
	public static function printCommentTitle($apply_filter=true){
		if($apply_filter==true){
			echo self::executeCommentFilter('comment_title', self::$comment_title);
		}
		else{
			echo self::$comment_title;
		}
	}//end printContentTitle
	
	public static function getCommentTitle($apply_filter=true){
		if($apply_filter==true){
			return self::executeCommentFilter('comment_title', self::$comment_title);
		}
		else{
			return self::$comment_title;
		}
	}//end getContentTitle
	
	public static function printCommentText($apply_filter=true){
		if($apply_filter==true){
			echo self::executeCommentFilter('comment_text', self::$comment_text);
		}
		else{
			echo self::$comment_text;
		}
	}//end printContentTitle
	
	public static function getCommentText($apply_filter=true){
		if($apply_filter==true){
			return self::executeCommentFilter('comment_text', self::$comment_text);
		}
		else{
			return self::$comment_text;
		}
	}//end getContentTitle
	
	
	public static function printCommentParent($apply_filter=true){
		if($apply_filter==true){
			echo self::executeCommentFilter('comment_parent', self::$comment_parent);
		}
		else{
			echo self::$comment_parent;
		}
	}//end printContentTitle
	
	public static function getCommentParent($apply_filter=true){
		if($apply_filter==true){
			return self::executeCommentFilter('comment_parent', self::$comment_parent);
		}
		else{
			return self::$comment_parent;
		}
	}//end getContentTitle
	
	
	public static function printCommentAuthor($apply_filter=true){
		if($apply_filter==true){
			echo self::executeCommentFilter('comment_author', self::$comment_author);
		}
		else{
			echo self::$comment_author;
		}
	}//end printContentTitle
	
	public static function getCommentAuthor($apply_filter=true){
		if($apply_filter==true){
			return self::executeCommentFilter('comment_author', self::$comment_author);
		}
		else{
			return self::$comment_author;
		}
	}//end getContentTitle
	
	public static function printCommentAuthorEmail($apply_filter=true){
		if($apply_filter==true){
			echo self::executeCommentFilter('comment_author_email', self::$comment_author_email);
		}
		else{
			echo self::$comment_author_email;
		}
	}//end printContentTitle
	
	public static function getCommentAuthorEmail($apply_filter=true){
		if($apply_filter==true){
			return self::executeCommentFilter('comment_author_email', self::$comment_author_email);
		}
		else{
			return self::$comment_author_email;
		}
	}//end getContentTitle
	
	public static function printCommentAuthorWebsite($apply_filter=true){
		if($apply_filter==true){
			echo self::executeCommentFilter('comment_author_website', self::$comment_author_website);
		}
		else{
			echo self::$comment_author_website;
		}
	}//end printContentTitle
	
	public static function getCommentAuthorWebsite($apply_filter=true){
		if($apply_filter==true){
			return self::executeCommentFilter('comment_author_website', self::$comment_author_website);
		}
		else{
			return self::$comment_author_website;
		}
	}//end getContentTitle
	
	public static function printCommentContentID($apply_filter=true){
		if($apply_filter==true){
			echo self::executeCommentFilter('comment_content_id', self::$comment_content_id);
		}
		else{
			echo self::$comment_content_id;
		}
	}//end printContentTitle
	
	public static function getCommentContentID($apply_filter=true){
		if($apply_filter==true){
			return self::executeCommentFilter('comment_content_id', self::$comment_content_id);
		}
		else{
			return self::$comment_content_id;
		}
	}//end getContentTitle
	
	public static function printCommentID($apply_filter=true){
		if($apply_filter==true){
			echo self::executeCommentFilter('comment_id', self::$comment_id);
		}
		else{
			echo self::$comment_id;
		}
	}//end printContentTitle
	
	public static function getCommentID($apply_filter=true){
		if($apply_filter==true){
			return self::executeCommentFilter('comment_id', self::$comment_id);
		}
		else{
			return self::$comment_id;
		}
	}//end getContentTitle
	
	
	public static function printCommentOwnerIP($apply_filter=true){
		if($apply_filter==true){
			echo self::executeCommentFilter('owner_ip', self::$comment_owner_ip);
		}
		else{
			echo self::$comment_owner_ip;
		}
	}//end printContentTitle
	
	public static function getCommentOwnerIP($apply_filter=true){
		if($apply_filter==true){
			return self::executeCommentFilter('owner_ip', self::$comment_owner_ip);
		}
		else{
			return self::$comment_owner_ip;
		}
	}//end getContentTitle
	
	/*****************************************
	Points
	*******************************************/
	
	public static function printPointValue($apply_filter=true){
		if($apply_filter==true){
			echo self::executePointFilter('point_value', self::$point_value);
		}
		else{
			echo self::$point_value;
		}
	}//end printContentTitle
	
	public static function getPointValue($apply_filter=true){
		if($apply_filter==true){
			return self::executePointFilter('point_value', self::$point_value);
		}
		else{
			return self::$point_value;
		}
	}//end getContentTitle
	
	public static function printPointContentID($apply_filter=true){
		if($apply_filter==true){
			echo self::executePointFilter('content_id', self::$point_content_id);
		}
		else{
			echo self::$point_content_id;
		}
	}//end printContentTitle
	
	public static function getPointContentID($apply_filter=true){
		if($apply_filter==true){
			return self::executePointFilter('content_id', self::$point_content_id);
		}
		else{
			return self::$point_content_id;
		}
	}//end getContentTitle
	
	public static function printPointCommentID($apply_filter=true){
		if($apply_filter==true){
			echo self::executePointFilter('comment_id', self::$point_comment_id);
		}
		else{
			echo self::$point_comment_id;
		}
	}//end printContentTitle
	
	public static function getPointCommentID($apply_filter=true){
		if($apply_filter==true){
			return self::executePointFilter('comment_id', self::$point_comment_id);
		}
		else{
			return self::$point_comment_id;
		}
	}//end getContentTitle
	
	
	public static function printPointAppID($apply_filter=true){
		if($apply_filter==true){
			echo self::executePointFilter('app_id', self::$point_app_id);
		}
		else{
			echo self::$point_app_id;
		}
	}//end printContentTitle
	
	public static function getPointAppID($apply_filter=true){
		if($apply_filter==true){
			return self::executePointFilter('app_id', self::$point_app_id);
		}
		else{
			return self::$point_app_id;
		}
	}//end getContentTitle
	
	
	public static function printPointType($apply_filter=true){
		if($apply_filter==true){
			echo self::executePointFilter('point_type', self::$point_type);
		}
		else{
			echo self::$point_type;
		}
	}//end printContentTitle
	
	public static function getPointType($apply_filter=true){
		if($apply_filter==true){
			return self::executePointFilter('point_type', self::$point_type);
		}
		else{
			return self::$point_type;
		}
	}//end getContentTitle
	
	
	public static function printPointUserID($apply_filter=true){
		if($apply_filter==true){
			echo self::executePointFilter('user_id', self::$point_user_id);
		}
		else{
			echo self::$point_user_id;
		}
	}//end printContentTitle
	
	public static function getPointUserID($apply_filter=true){
		if($apply_filter==true){
			return self::executePointFilter('user_id', self::$point_user_id);
		}
		else{
			return self::$point_user_id;
		}
	}//end getContentTitle
	
	public static function printPointID($apply_filter=true){
		if($apply_filter==true){
			echo self::executePointFilter('point_id', self::$point_id);
		}
		else{
			echo self::$point_id;
		}
	}//end printContentTitle
	
	public static function getPointID($apply_filter=true){
		if($apply_filter==true){
			return self::executePointFilter('point_id', self::$point_id);
		}
		else{
			return self::$point_id;
		}
	}//end getContentTitle
	
	public static function printPointUserIP($apply_filter=true){
		if($apply_filter==true){
			echo self::executePointFilter('user_ip', self::$point_user_ip);
		}
		else{
			echo self::$point_user_ip;
		}
	}//end printContentTitle
	
	public static function getPointUserIP($apply_filter=true){
		if($apply_filter==true){
			return self::executePointFilter('user_ip', self::$point_user_ip);
		}
		else{
			return self::$point_user_ip;
		}
	}//end getContentTitle
	
	public static function printPointDate($apply_filter=true){
		if($apply_filter==true){
			echo self::executePointFilter('point_date', self::$point_date);
		}
		else{
			echo self::$point_date;
		}
	}//end printContentTitle
	
	public static function getPointDate($apply_filter=true){
		if($apply_filter==true){
			return self::executePointFilter('point_date', self::$point_date);
		}
		else{
			return self::$point_date;
		}
	}//end getContentTitle
	
	/*************************************
	* Rating
	*************************************/
	
	public static function printRatingContentID($apply_filter=true){
		if($apply_filter==true){
			echo self::executeRatingFilter('content_id', self::$rating_content_id);
		}
		else{
			echo self::$rating_content_id;
		}
	}//end printContentTitle
	
	public static function getRatingContentID($apply_filter=true){
		if($apply_filter==true){
			return self::executeRatingFilter('content_id', self::$rating_content_id);
		}
		else{
			return self::$rating_content_id;
		}
	}//end getContentTitle
	
	public static function printRatingCommentID($apply_filter=true){
		if($apply_filter==true){
			echo self::executeRatingFilter('comment_id', self::$rating_comment_id);
		}
		else{
			echo self::$rating_comment_id;
		}
	}//end printContentTitle
	
	public static function getRatingCommentID($apply_filter=true){
		if($apply_filter==true){
			return self::executeRatingFilter('comment_id', self::$rating_comment_id);
		}
		else{
			return self::$rating_comment_id;
		}
	}//end getContentTitle
	
	public static function printRating($apply_filter=true){
		if($apply_filter==true){
			echo self::executeRatingFilter('rating', self::$rating);
		}
		else{
			echo self::$rating;
		}
	}//end printContentTitle
	
	public static function getRating($apply_filter=true){
		if($apply_filter==true){
			return self::executeRatingFilter('rating', self::$rating);
		}
		else{
			return self::$rating;
		}
	}//end getContentTitle
	
	public static function printRatingUserID($apply_filter=true){
		if($apply_filter==true){
			echo self::executeRatingFilter('user_id', self::$rating_user_id);
		}
		else{
			echo self::$rating_user_id;
		}
	}//end printContentTitle
	
	public static function getRatingUserID($apply_filter=true){
		if($apply_filter==true){
			return self::executeRatingFilter('user_id', self::$rating_user_id);
		}
		else{
			return self::$rating_user_id;
		}
	}//end getContentTitle
	
	public static function printRatingType($apply_filter=true){
		if($apply_filter==true){
			echo self::executeRatingFilter('rating_type', self::$rating_type);
		}
		else{
			echo self::$rating_type;
		}
	}//end printContentTitle
	
	public static function getRatingType($apply_filter=true){
		if($apply_filter==true){
			return self::executeRatingFilter('rating_type', self::$rating_type);
		}
		else{
			return self::$rating_type;
		}
	}//end getContentTitle
	
	public static function printRatingID($apply_filter=true){
		if($apply_filter==true){
			echo self::executeRatingFilter('rating_id', self::$rating_id);
		}
		else{
			echo self::$rating_id;
		}
	}//end printContentTitle
	
	public static function getRatingID($apply_filter=true){
		if($apply_filter==true){
			return self::executeRatingFilter('rating_id', self::$rating_id);
		}
		else{
			return self::$rating_id;
		}
	}//end getContentTitle
	
	public static function printRatingDate($apply_filter=true){
		if($apply_filter==true){
			echo self::executeRatingFilter('date_rated', self::$date_rated);
		}
		else{
			echo self::$date_rated;
		}
	}//end printContentTitle
	
	public static function getRatingDate($apply_filter=true){
		if($apply_filter==true){
			return self::executeRatingFilter('date_rated', self::$date_rated);
		}
		else{
			return self::$date_rated;
		}
	}//end getContentTitle
	
	public static function printRatingDateRerated($apply_filter=true){
		if($apply_filter==true){
			echo self::executeRatingFilter('date_rerated', self::$date_rerated);
		}
		else{
			echo self::$date_rerated;
		}
	}//end printContentTitle
	
	public static function getRatingDateRerated($apply_filter=true){
		if($apply_filter==true){
			return self::executeRatingFilter('date_rerated', self::$date_rerated);
		}
		else{
			return self::$date_rerated;
		}
	}//end getContentTitle
	
	public static function printRatingUserIP($apply_filter=true){
		if($apply_filter==true){
			echo self::executeRatingFilter('user_ip', self::$rating_user_ip);
		}
		else{
			echo self::$rating_user_ip;
		}
	}//end printContentTitle
	
	public static function getRatingUserIP($apply_filter=true){
		if($apply_filter==true){
			return self::executeRatingFilter('user_ip', self::$rating_user_ip);
		}
		else{
			return self::$rating_user_ip;
		}
	}//end getContentTitle
	
	
	
	
	public static function setContentVariables($args){
		
		//Regular Content Variables
		self::$content_id=$args['content_id']; 
		self::$app_id=$args['app_id'];  
		self::$parent_id=$args['parent_id']; 
		self::$owner_id=$args['owner_id'];
		self::$content_title=$args['content_title'];
		self::$content_alias=$args['content_alias'];  
		self::$content_description=$args['content_description']; 
		self::$content_meta_tags=$args['content_meta_tags'];
		self::$content_meta_description=$args['content_meta_description']; 
		self::$content_thumbnail=$args['content_thumbnai'];
		self::$date_created=$args['date_created'];
		self::$date_modified=$args['date_modified'];
		self::$date_active=$args['date_active'];
		self::$date_inactive=$args['date_inactive'];
		self::$is_searchable=$args['is_searchable'];
		self::$allow_comments=$args['allow_comments'];
		self::$allow_rating=$args['allow_rating'];
		self::$content_active=$args['content_active'];
		self::$content_promoted=$args['content_promoted'];
		self::$content_permissions=$args['content_permissions'];
		self::$content_type=$args['content_type'];
		self::$content_language=$args['content_language'];
		self::$translate_content=$args['translate_content'];
		self::$content_approved=$args['content_approved'];
		self::$content_category=$args['content_category'];
		self::$content_parameters=$args['content_parameters'];
		self::$content_order=$args['content_order'];
		self::$sym_link=$args['sym_link'];
		self::$content_taxonomy=$args['content_taxonomy'];
		
		
		//Image Content
		self::$image_type=$args['image_type'];
		self::$image_size=$args['image_size'];
		self::$image_url=$args['image_url'];
		self::$thumb_url=$args['thumb_url'];
		self::$content_type=$args['content_type'];
		self::$image_width=$args['image_width'];
		self::$image_height=$args['image_height'];
		self::$thumb_width=$args['thumb_width'];
		self::$thumb_height=$args['thumb_height'];
		self::$image_src=$args['image_src'];
	
		//Text Content
		self::$text_content=$args['text_content'];
		self::$text_page_group=$args['text_page_group'];
		self::$text_page_number=$args['text_page_number'];
		self::$text_src=$args['text_src'];
		
		//Video Content
		self::$video_type=$args['video_type'];
		self::$video_length=$args['video_length'];
		self::$video_allow_embedding=$args['video_allow_embedding'];
		self::$flv_file=$args['flv_file'];
		self::$mp4_file=$args['mp4_file'];
		self::$wmv_file=$args['wmv_file'];
		self::$mpeg_file=$args['mpeg_file'];
		self::$rm_file=$args['rm_file'];
		self::$avi_file=$args['avi_file'];
		self::$mov_file=$args['mov_file'];
		self::$asf_file=$args['asf_file'];
		self::$enable_hq=$args['enable_hq'];
		self::$auto_hq=$args['auto_hq'];
		self::$video_src=$args['video_src'];
		self::$video_embed=$args['video_embed'];
		
		//Event Content
		self::$event_location=$args['event_location'];
		self::$event_start_date=$args['event_start_date'];
		self::$event_end_date=$args['event_end_date'];
		self::$event_country=$args['event_country'];
		self::$event_address=$args['event_address'];
		self::$event_city=$args['event_city'];
		self::$event_state=$args['event_state'];
		self::$event_zip=$args['event_zip'];
		self::$event_map=$args['event_map'];
		self::$event_src=$args['event_src'];
		self::$undefined_endtime=$args['undefined_endtime'];
		
		//Audio Content
		self::$audio_length=$args['audio_length'];
		self::$mid_file=$args['mid_file'];
		self::$wav_file=$args['wav_file'];
		self::$aif_file=$args['aif_file'];
		self::$mp3_file=$args['mp3_file'];
		self::$ra_file=$args['ra_file'];
		self::$sample_length=$args['sample_length'];
		self::$audio_src=$args['audio_src'];
		self::$audio_type=$args['audio_type'];
		
		//File Content
		self::$file_type=$args['file_type'];
		self::$file_size=$args['file_size'];
		self::$file_location=$args['file_location'];
		self::$file_name=$args['file_name'];
		self::$file_src=$args['file_src'];
		self::$file_downloadable=$args['file_downloadable'];
		self::$file_max_downloads=$args['file_max_downloads'];
		self::$file_version=$args['file_version'];
		self::$file_license=$args['file_license'];
		
		//Product Content
		self::$product_id=$args['product_id'];
		self::$product_sku=$args['product_sku'];
		self::$product_idsku=$args['product_idsku'];
		self::$product_vendor_id=$args['product_vendor_id'];
		self::$product_quantity=$args['product_quantity'];
		self::$product_price=$args['product_price'];
		self::$product_discount_price=$args['product_discount_price'];
		self::$product_size=$args['product_size'];
		self::$product_color=$args['product_color'];
		self::$product_weight=$args['product_weight']; 
		self::$product_height=$args['product_height'];
		self::$product_length=$args['product_length']; 
		self::$product_currency=$args['product_currency']; 
		self::$product_in_stock=$args['product_in_stock']; 
		self::$product_type=$args['product_type']; 
		self::$product_tax_id=$args['product_tax_id']; 
		self::$product_attribute=$args['product_attribute']; 
		self::$product_version=$args['product_version'];
		
	}//end setVariables
	
	
	public static function setCommentVariables($args){
		self::$comment_owner_id=$args['owner_id'];
		self::$comment_date=$args['comment_date'];
		self::$comment_approved=$args['comment_approved'];
		self::$comment_title=$args['comment_title'];
		self::$comment_text=$args['comment_text'];
		self::$comment_parent=$args['comment_parent'];
		self::$comment_author=$args['comment_author'];
		self::$comment_author_email=$args['comment_author_email'];
		self::$comment_author_website=$args['comment_author_website'];
		self::$comment_content_id=$args['content_id'];
		self::$comment_id=$args['comment_id'];
		self::$comment_type=$args['comment_type'];
		self::$comment_owner_ip=$args['owner_ip'];
		
	}//end setCommentVariables
	
	public static function setPointVariables($args){
		
		self::$point_value=$args['point_value'];
		self::$point_content_id=$args['content_id'];
		self::$point_comment_id=$args['comment_id'];
		self::$point_app_id=$args['app_id'];
		self::$point_type=$args['point_type'];
		self::$point_user_id=$args['user_id'];
		self::$point_id=$args['point_id'];
		self::$point_user_ip=$args['user_ip'];
		self::$point_date=$args['point_date'];
		
	}//send setPointVariables
	
	public static function setRatingVariables($args){
		
		self::$rating_content_id=$args['content_id'];
		self::$rating_comment_id=$args['comment_id'];
		self::$rating=$args['rating'];
		self::$rating_user_id=$args['user_id']; 
		self::$rating_type=$args['rating_type'];
		self::$rating_id=$args['rating_id'];
		self::$date_rated=$args['date_rated'];
		self::$date_rerated=$args['date_rerated'];
		self::$rating_user_ip=$args['user_ip'];
		
	}//end setRatingVariables
	
	function fieldInterpreter($field_array){
		$output_type=$field_array['output_type'];
		
		if($output_type==$this->rawDataID){
			$this->printDataRaw($query, $field_array, $title_array,$type_array, $show_title_array);
			
		}
		else if($output_type==$this->dlListID){
			$this->printDLListprintDLList($field_array, $single_item);
		}
		
	}//end fieldInterpreter
	
	/*
	*/
	function fieldInterpreterArray($field_array, $output_type){
		
		foreach ($field_array as &$value) {
   			 echo "$output_type";
			
		
			if($output_type==$this->rawDataID){
				$this->printDataRaw($query, $field_array, $title_array,$type_array, $show_title_array);
				
			}
			else if($output_type==$this->dlListID){
				$this->printDLList($value, 0);
			}
		
		}
		
	}//end fieldInterpreter
	
	
	function printArrayContainer($value_array){
		
		//Define Fields
		$insert_fields="";
		$insert_values="";
		$from_statement=$value_array['database_from'];
		$where_statement=$value_array['database_where'];
		$title_array=array();
		$show_title_array=array();
		$field_array=array();
		$type_array=array();
		$output_type=$value_array['output_type'];
		
		//Retrieve elements from array that are numeric and forms a SELECT
		//SQL Statement
		//Values were most likeley where pushed on
		for($i=0; $i<sizeof($value_array); $i++){
			$temp=$value_array[$i];
			if(is_array($temp)){
				$field_name=$temp['field_name'];
				$field_title=$temp['field_title'];
				$field_type=$temp['field_type'];
				$show_title=$temp['show_title'];
				array_push($field_array, $field_name);
				$show_title_array[$field_name]=$show_title;
				$title_array[$field_name]=$field_title;	
				$type_array[$field_name]=$field_type;
				
				echo "<input type=\"hidden\" id=\"$field_name\" name=\"$field_name\" value=\"$field_name\" />";
			}
		}//end for
		
		
		//Create the Array that selects the fields in the profiler
		for($i=0; $i<sizeof($field_array); $i++){
			$temp=trim($field_array[$i]);
			if($i==0 && !empty($temp)){
				$insert_fields.=" $temp ";
			}
			else if( !empty($field_array[$i]) && $i==sizeof($field_array)-1){
				$insert_fields.=" , ".$field_array[$i]. " ";
			}
			else if (!empty($temp) && $temp!=NULL ){
			$insert_fields.=" , ".$temp;
					
		 	}
		}//end for
		
		//Create the Query
		$query="SELECT $insert_fields FROM $from_statement  $where_statement ";
		
		
		
		
		
		
		if($output_type==$this->rawDataID){
			$this->printDataRaw($query, $field_array, $title_array,$type_array, $show_title_array);
			
		}
		else if($output_type==$this->dlListID){
			$this->printDLList($query, $field_array, $title_array,$type_array, $show_title_array);
		}
	}//end fieldInterpreter
	
	function printDataRaw($query, $field_array, $title_array,$type_array, $show_title_array ){
		
		//Create a connector Object
		$connector = new DBConnector();
		
		//Make the query safe for the database
		$query=$connector->makeSafe($query);
	
		//Execute the query and return the result
		$result =$connector->query($query);
		
	
		//Matches the title with the field and prints
		//out a DT list using that
		if( $connector->resultRowCount($result) > 0) {
			
			
			
			while($row = $connector->fetchArray($result)){
				for($i=0; $i<sizeof($field_array); $i++){
					if( !empty($field_array[$i]) && $field_array[$i]!=NULL ){
						
						$field=$field_array[$i];
						$field_text=trim($row[$field]);
						$title=$title_array[$field];
						$show_title=$show_title_array[$field];
						$type=$type_array[$field];

						if(!empty($field_text) && $field_text!=NULL){
							if($type==$this->comboBoxID){
								$field_text=$this->convertComboBox($field, $field_text);
								echo"<dt>$title</dt><dd>$field_text</dd>";
							}
							else if($type==$this->imageFieldID){
								if($show_title==1){
									echo"$title";
								}
								$this->convertToSingleImage($field_text,$field);
							}
							else{
							echo"$title $field_text";
							}
						}
					}
					
				}//end while
			}//end for
			
			
			
		}//end if
		
		$connector->closeDB();
		
		
	}//
	
	/*printDLLIST
	Prints the specificed array into a generic DL list format. It is suggested that
	the DL be customized using CSS.
	*/
	function printDLList($field_array, $single_item){
		
		$title=$field_array['field_title'];
		
		if($single_item==1){
			echo "<dl>";
		}
	
						
		if(!empty($field_text) && $field_text!=NULL){
			if($type==$this->comboBoxID){
			   $field_text=$this->convertComboBox($field, $field_text);
			    echo"<dt>$title</dt><dd>$field_text</dd>";
								
			}
			else if($type==$this->imageFieldID){
				 	if($show_title==1){
						echo"<dt>$title</dt>";
					}
					echo "<dd>";
					$this->convertToSingleImage($field_text,$field);
					echo "</dd>";
			}
			else{
				echo"<dt>$title</dt><dd>$field_text</dd>";
			}
		}
			
		if($single_item==1){
			echo "</dl>";
		}
			
	
		
			
	}//end printDLList
	
	function convertToSingleImage($field_text,$field){
		$picture=new PictureGallery();
		$params=array();
		$params['picture_id']=$field_text;
		$params['css_class']=$field;
		$picture->commandInterpreter("showSinglePicture", $params);
		
	}//end conver to SingleImage
	
	
	function convertComboBox($field_name, $option_id){
	
		$option="";
		$query="SELECT option FROM prtrack.pv_group_fields_options JOIN prtrack.pv_group_fields ON pv_group_fields_options.field_id=pv_group_fields.field_id WHERE field_name='$field_name' AND option_id=$option_id ";
		
		$connector = new DBConnector();
		
		//Make the query safe for the database
		//$query=$connector->makeSafe($query);
	
		//Execute the query and return the result
		$result =$connector->query($query);
		
		
		if( $connector->resultRowCount($result) > 0) {
			
			while($row = $connector->fetchArray($result)){
				$option=$row['option'];
				
			}//end while
		
		}
	
		$connector->closeDB();
		
		return $option;
	}//end convertComboBox
	
	function getVersion(){
		return $this->version;
	}
	
	
	function getUniqueName(){
		return $this->uniqueName;
	}//end get
	
	
	
}//end class
?>
