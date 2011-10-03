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
class PVContent extends PVStaticObject {
	
	/**
	 * Create basic content. All other contennt will extend this content. Please note that
	 * these fields are ONLY GUIDELINES. They can be used to serve your needs regardless 
	 * of their names.
	 * 
	 * @param array $fields The fields that will be created
	 * 		- 'app_id' _id_ : The id of the application associated with this content
	 * 		- 'parent_content' _id_ : The id of the parent associated with this content.
	 * 		- 'owner_id' _id_ : The user id of the owner of the content.
	 * 		- 'content_title _string_ : The title of the content
	 * 		- 'content_description' _string_ : The description of the content
	 * 		- 'content_meta_tags' _string_ : The meta tags that describe the content
	 * 		- 'content_meta_description' _string_: The meta description that describes this content
	 * 		_ 'content_thumbnail' _string_: The thumbnail forthis content
	 * 		- 'content_alias' _string_: The alias for this content. Should be unique.
	 * 		- 'date_created' _date/time: The date and time the content was created. General format is Y-m-d H:i:s.
	 * 		   Wlll default to the currrent date and time if empty.
	 * 		- 'date_modified' _date/time: The date and time the content was modified. General format is Y-m-d H:i:s.
	 * 		   Wlll default to the currrent date and time if empty.  
	 * 		- 'date_active' _date/time: The date and time the content will be active. General format is Y-m-d H:i:s.
	 * 		   Wlll default to the currrent date and time if empty.  
	 *  	- 'date_inactive' _date/time: The date and time the content will be inactive. General format is Y-m-d H:i:s.
	 * 		   Wlll default to the currrent date and time if empty.
	 * 		- 'is_searchable' _boolean_: Boolean flag if the content will be searchable.
	 * 		- 'allow_comments' _boolean_: Boolean flag that determines if content can be commented on
	 * 		- 'allow_rating' _boolean_ : Boolean flag that determines if the content can be rated
	 * 		- 'content_active' _boolean: Boolean flag that determines if the content is active
	 * 		- 'content_promoted' _boolean_: Boolean flag that determines if the is featured
	 * 		- 'content_permissions' _stinrg_  Allowed roles that can view this content
	 * 		- 'content_type' _string_ : The type of content this is. Should be indexed in the database
	 * 		   and user for distinguish different type of content.
	 * 		- 'content_language' _string_: The language the content is currently in
	 * 		- 'trnslate_content' _boolean_: Boolean flag that determines if the content is to be translated
	 * 		- 'content_approved'_ boolean_: Boolean flag that determines if the content is approved
	 * 		- 'content_category'_mixed_: Either the of a single category or an array of IDs that the the
	 * 		   content belongs too.
	 * 		- 'content_parameters' _string_ : Extra parameters that can be used to describe the content
	 * 		- 'sym_link' _string_: A symbolic link for the content.
	 * 		- 'content_order' _integer_: The order that the content is in.
	 * 		- 'content_access_level' _integer-: The level of access required to view the content
	 * 
	 * @return id $content_id The id of the content created
	 * @todo Find something to do with cieling values
	 */
	public static function createContent($args=array()){
		$args += self::getContentDefaults();
		
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		$content_taxonomy=$args['content_taxonomy'];
		$adjacent_table=$args['adjacent_table'];
		
		
		if(empty($content_category) && !is_array($content_category)){
			$content_category=0;
		}
		
		if(!empty($app_id) && !PVValidator::isID($app_id)){
			$app_id=PVApplication::getApplicationID($app_id);
		}
		
		if(empty($date_created)){
			$date_created=date("Y-m-d H:i:s", time()) ;
		}
		
		if(empty($date_modified)){
			$date_modified=date("Y-m-d H:i:s", time()) ;
		}
		
		if(empty($date_active)){
			$date_active=date("Y-m-d H:i:s", time()) ;
		}
		
		if(empty($date_inactive)){
			$date_inactive=date("Y-m-d H:i:s", time()) ;
		}
		
		$is_searchable=ceil($is_searchable);
		$allow_comments=ceil($allow_comments);
		$allow_rating=ceil($allow_rating);
		$content_active=ceil($content_active);
		$content_promoted=ceil($content_promoted);
		$translate_content=ceil($translate_content);
		$content_approved=ceil($content_approved);
		$content_order=ceil($content_order);
		$content_access_level=ceil($content_access_level);
		
		$query="INSERT INTO ".PVDatabase::getContentTableName()."(app_id , owner_id , parent_content, content_title , content_alias,  content_description , content_meta_tags , content_meta_description, content_thumbnail , date_created , date_modified , date_active , date_inactive, is_searchable, allow_comments , allow_rating , content_active , content_promoted , content_permissions , content_type , content_language, translate_content , content_approved ,  content_order, sym_link, content_parameters, content_access_level ) VALUES ( '$app_id' , '$owner_id' , '$parent_content' , '$content_title' , '$content_alias',  '$content_description' , '$content_meta_tags' , '$content_meta_description' , '$content_thumbnail' , '$date_created' , '$date_modified' , '$date_active' , '$date_inactive', '$is_searchable', '$allow_comments' , '$allow_rating' , '$content_active' , '$content_promoted' , '$content_permissions' , '$content_type' , '$content_language', '$translate_content' , '$content_approved' , '$content_order', '$sym_link', '$content_parameters', '$content_access_level' )";
		
		$content_id=PVDatabase::return_last_insert_query($query, "content_id", PVDatabase::getContentTableName() );
		
		if(is_array($content_category)){
			foreach($content_category as $category_value){
				$query="INSERT INTO ".PVDatabase::getContentCategoryRelationsTableName()."(category_id, content_id) VALUES('$category_value',' $content_id')";
				PVDatabase::query($query);
			}//end foreach	
		}
		else{
			$query="INSERT INTO ".PVDatabase::getContentCategoryRelationsTableName()."(category_id, content_id) VALUES('$content_category',' $content_id')";
			PVDatabase::query($query);
		}
		
		if(is_array($content_taxonomy)){
			foreach($content_taxonomy as $taxonomy_value){
				if(!empty($taxonomy_value)){
					$query="INSERT INTO ".PVDatabase::getContentTaxonomyTableName()."(content_id , taxonomy_term ) VALUES( '$content_id', '$taxonomy_value' )";
					PVDatabase::query($query);
				}
			
			}//end foreach	
		} else if(!empty($content_taxonomy)) {
			$query="INSERT INTO ".PVDatabase::getContentTaxonomyTableName()."(content_id , taxonomy_term ) VALUES( '$content_id', '$taxonomy_value' )";
			PVDatabase::query($query);
		}
		
		self::insertAdjacentTables($content_id, $adjacent_table);
		
		return $content_id;
	
	}//end createContent
	
	/**
	 * Creates content that is primarly geared toward text. 
	 */
	public static function createTextContent($args=array()){
		$defaults=self::getTextContentDefaults();
		$args += $defaults;
		
		$args['adjacent_table']='pv_content_text';
		$content_id=self::createContent($args);
		
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		$text_page_number=ceil($text_page_number);
		$text_page_group=ceil($text_page_group);

		if(!empty($content_id)){
			$query="INSERT INTO ".PVDatabase::getTextContentTableName()."(text_id, text_content, text_page_group , text_page_number, text_src ) VALUES( '$content_id', '$text_content', '$text_page_group' , '$text_page_number', '$text_src')";
			PVDatabase::query($query);
		}
		
		return $content_id;
	}//end createTextContent
	
	public static function createImageContent($args=array()){
		$defaults=self::getImageContentDefaults();
		$args += $defaults;
		
		$args['adjacent_table']='pv_content_images';
		$content_id=self::createContent($args);
		
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		if(!empty($content_id)){
			$query="INSERT INTO ".PVDatabase::getImageContentTableName()."( image_id , image_type, image_size , image_url, thumb_url, image_width , image_height , thumb_width, thumb_height, image_src) VALUES( '$content_id' , '$image_type', '$image_size' , '$image_url' , '$thumb_url' , '$image_width' , '$image_height' , '$thumb_width' , '$thumb_height', '$image_src')";
			PVDatabase::query($query);
		}
		
		return $content_id;
	}//end createImageContent
	
	
	
	public static function createImageContentWithFile($args=array()){
		$defaults=self::getImageContentDefaults();
		$args += $defaults;
		
		$args['adjacent_table']='pv_content_images';
		$content_id=self::createContent($args);
		
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		if(!empty($content_id)){
			$return=PVImageRenderer::uploadImageFromContent($content_id, $content_type, $file_name, $tmp_name, $file_size, $file_type, $image_width , $image_height , $thumbnailwidth, $thumbnailheight, $image_src );	
		}
		
		return $content_id;
	
	}//end createImageContentWithFile
	
	
	public static function createVideoContent($args=array()){
		$defaults=self::getVideoContentDefaults();
		$args += $defaults;
		
		$args['adjacent_table']='pv_content_video';
		$content_id=self::createContent($args);
		
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		if(!empty($content_id)){
			$query="INSERT INTO ".PVDatabase::getVideoContentTableName()."( video_id , video_type , video_length , video_allow_embedding, flv_file , mp4_file, wmv_file , mpeg_file, rm_file , avi_file, mov_file, asf_file, enable_hq, auto_hq, video_src, video_embed) VALUES( '$content_id', '$video_type' , '$video_length' , '$video_allow_embedding', '$flv_file' , '$mp4_file' , '$wmv_file' , '$mpeg_file', '$rm_file' , '$avi_file', '$mov_file', '$asf_file', '$enable_hq', '$auto_hq' , '$video_src', '$video_embed')";
			PVDatabase::query($query);
		}
	
		return $content_id;
	}//end createVideoContent
	
	public static function createVideoContentWithFile($args=array()){
		$defaults=self::getVideoContentDefaults();
		$args += $defaults;
		
		$content_id=self::createContent($args);
		
		if(!empty($content_id)){
			$args['content_id']=$content_id;
			PVVideoRenderer::updateVideoContent($args);
		}

		return $content_id;
	}//end createFileContent
	
	
	public static function createEventContent($args=array()){
		$defaults=self::getEventContentDefaults();
		$args += $defaults;
		
		$args['adjacent_table']='pv_content_events';
		$content_id=self::createContent($args);
		
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		if(empty($event_start_date)){
			$event_start_date=date("Y-m-d H:i:s", time()) ;
		}
		
		if(empty($event_end_date)){
			$event_end_date=date("Y-m-d H:i:s", time()) ;
		}
		
		if(empty($undefined_endtime)){
			$undefined_endtime=0;	
		}
		
		$undefined_endtime=ceil($undefined_endtime);
		
		if(!empty($content_id)){
			$query="INSERT INTO ".PVDatabase::getEventContentTableName()."( event_id, event_location , event_start_date , event_end_date , event_country , event_address , event_city , event_state , event_zip , event_map ,event_src, event_contact, undefined_endtime ) VALUES( '$content_id' ,'$event_location' , '$event_start_date' , '$event_end_date' , '$event_country' , '$event_address' , '$event_city' , '$event_state' , '$event_zip' , '$event_map' , '$event_src', '$event_contact', '$undefined_endtime') ";
			PVDatabase::query($query);
		}
		
		return $content_id;
	}//end createVideoContent
	
	
	
	public static function createAudioContent($args=array()){
		$defaults=self::getAudioContentDefaults();
		$args += $defaults;
		
		$args['adjacent_table']='pv_content_audio';
		$content_id=self::createContent($args);
		
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		if(!empty($content_id)){
			$query="INSERT INTO ".PVDatabase::getAudioContentTableName()."( audio_id,  audio_length, mid_file , wav_file , aif_file , mp3_file , ra_file , oga_file, sample_length , audio_src ,audio_type ) VALUES( '$content_id', '$audio_length' , '$mid_file' , '$wav_file' , '$aif_file' , '$mp3_file' , '$ra_file' , '$oga_file','$sample_length' , '$audio_src', '$audio_type') ";
			PVDatabase::query($query);
		}
		
		return $content_id;
	}//end createVideoContent
	
	public static function createAudioContentWithFile($args=array()){
		$defaults=self::getAudioContentDefaults();
		$args += $defaults;
		
		$args['adjacent_table']='pv_content_audio';
		$content_id=self::createContent($args);
		$args['content_id']=$content_id;
		
		PVAudioRenderer::uploadAudioFileToContent($args);
		
		return $content_id;
	}//end createVideoContent
	
	
	public static function createFileContent($args=array()){
		$defaults=self::getFileContentDefaults();
		$args += $defaults;
		
		$args['adjacent_table']='pv_content_files';
		$content_id=self::createContent($args);
		
		$args=PVDatabase::makeSafe($args);
		extract($args, EXTR_SKIP);
		
		$file_size=ceil($file_size);
		$file_downloadable=ceil($file_downloadable);
		$file_max_downloads=ceil($file_max_downloads);
		
		if(!PVValidator::isDouble($file_version) && !PVValidator::isInteger($file_version)){
			$file_version=0;
		}
		
		if(!empty($content_id)){
			$query="INSERT INTO ".PVDatabase::getFileContentTableName()."(file_id, file_type, file_size, file_location, file_src, file_name, file_downloadable, file_max_downloads, file_version, file_license ) VALUES( '$content_id' , '$file_type', '$file_size', '$file_location' , '$file_src' ,  '$file_name' , '$file_downloadable', '$file_max_downloads', '$file_version' , '$file_license' )";
			PVDatabase::query($query);
		}
		
		return $content_id;
	}//end createFileContent
	
	
	public static function createFileContentWithFile($args=array()){
		$defaults=self::getFileContentDefaults();
		$args += $defaults;
		
		$args['adjacent_table']='pv_content_files';
		$content_id=self::createContent($args);
		
		$args=PVDatabase::makeSafe($args);
		extract($args, EXTR_SKIP);
		
		$file_size=ceil($file_size);
		$file_downloadable=ceil($file_downloadable);
		$file_max_downloads=ceil($file_max_downloads);
		
		
		if(!PVValidator::isDouble($file_version) && !PVValidator::isInteger($file_version)){
			$file_version=0;
		}
		
		if(!empty($content_id)){
			$query="INSERT INTO ".PVDatabase::getFileContentTableName()."(file_id, file_type, file_size, file_location, file_src, file_name, file_downloadable, file_max_downloads, file_version, file_license ) VALUES( '$content_id' , '$file_type', '$file_size', '$file_location' , '$file_src' ,  '$file_name' , '$file_downloadable', '$file_max_downloads', '$file_version' , '$file_license' )";
			PVDatabase::query($query);
		}
		
		if(!empty($tmp_name)){
			PVFileManager::uploadFileFromContent($content_id,  $file_name, $tmp_name, $file_size, $file_type);
		}
		
		return $content_id;
	}//end createFileContent
	
	
	public static function createProductContent($args=array()){
		$defaults=self::getProductContentDefaults();
		$args += $defaults;
		
		$args['adjacent_table']='pv_content_product';
		$content_id=self::createContent($args);
		
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		if(!empty($content_id)){
			$query="INSERT INTO ".PVDatabase::getProductContentTableName()."(product_id ,product_sku ,product_idsku ,product_vendor_id ,product_quantity ,product_price ,product_discount_price ,product_size ,product_color ,product_weight , product_height , product_length , product_currency , product_in_stock , product_type , product_tax_id , product_attribute , product_version ) VALUES( '$content_id' ,'$product_sku' ,'$product_idsku' ,'$product_vendor_id' , '$product_quantity' , '$product_price'  , '$product_discount_price'  , '$product_size' , '$product_color' , '$product_weight' , '$product_height' , '$product_length' , '$product_currency' , '$product_in_stock' , '$product_type' , '$product_tax_id' , '$product_attribute' , '$product_version') ";
			PVDatabase::query($query);
		}
		
		return $content_id;
		
	}//end createProductFile
	
	
	public static function createCategory($args=array()){
		$defaults=self::getCategoryDefaults();
		$args += $defaults;
		
		$args=PVDatabase::makeSafe($args);
		extract($args, EXTR_SKIP);
		
		$query="INSERT INTO ".PVDatabase::getContentCategoriesTableName()."( category_name, category_unique_name, parent_category, app_id , category_order , category_description, category_alias, category_type, category_owner ) VALUES( '$category_name', '$category_unique_name', '$parent_category', '$app_id' , '$category_order' , '$category_description' , '$category_alias' ,  '$category_type' , '$category_owner' ) ";
		$category_id=PVDatabase::return_last_insert_query($query, "category_id", PVDatabase::getContentCategoriesTableName() );
	
		return $category_id;
	}

	private static function generateBasicWhereSqlClause($order_by_clause='', $limit='', $app_id='', $owner_id='', $content_type='',  $parent_id='', $category_id='', $content_approved='', $content_promoted='', $content_active='', $is_searchable='',$date_created='', $date_modified='', $date_active='', $date_inactive='', $custom_where=''){
		
		if(is_array($order_by_clause)){
    		//$order_by_clause=PVDatabase::makeSafe($order_by_clause['order_by_clause']);
    		$limit=PVDatabase::makeSafe($order_by_clause['limit']);
    		$app_id=PVDatabase::makeSafe($order_by_clause['app_id']);
    		$owner_id=PVDatabase::makeSafe($order_by_clause['owner_id']);
    		$content_type=PVDatabase::makeSafe($order_by_clause['content_type']);
    		$parent_id=PVDatabase::makeSafe($order_by_clause['parent_content']);
    		$category_id=$order_by_clause['category_id'];
    		$content_approved=PVDatabase::makeSafe($order_by_clause['content_approved']);
    		$content_promoted=PVDatabase::makeSafe($order_by_clause['content_promoted']);
    		$content_active=PVDatabase::makeSafe($order_by_clause['content_active']);
    		$content_taxonomy=PVDatabase::makeSafe($order_by_clause['content_taxonomy']);
    		$custom_where=$order_by_clause['custom_where'];
    		$content_alias=PVDatabase::makeSafe($order_by_clause['content_alias']);
			$content_id=$order_by_clause['content_id'];
    		
    		//Must Be Last
    		$order_by_clause=PVDatabase::makeSafe($order_by_clause['order_by']);
    		
    	}//end if is array
    	else{
    	
	    	$order_by_clause=PVDatabase::makeSafe($order_by_clause);
	    	$limit=PVDatabase::makeSafe($limit);
	    	$app_id=PVDatabase::makeSafe($app_id);
	    	$owner_id=PVDatabase::makeSafe($owner_id);
	    	$content_type=PVDatabase::makeSafe($content_type);
	    	$parent_id=PVDatabase::makeSafe($parent_id);
	    	$category_id=PVDatabase::makeSafe($category_id);
	    	$content_approved=PVDatabase::makeSafe($content_approved);
    		$content_promoted=PVDatabase::makeSafe($content_promoted);
    		$content_taxonomy=$content_taxonomy;
    		$content_active=PVDatabase::makeSafe($content_active);
    	}
    	
    	$WHERE_CLAUSE="";
    	
    	if(!empty($content_taxonomy)){
    		$WHERE_CLAUSE=" JOIN ".PVDatabase::getContentTaxonomyTableName()." ON ".PVDatabase::getContentTaxonomyTableName().".content_id=".PVDatabase::getContentTableName().".content_id ";
    	}
    	
		if(!empty($app_id) || !empty($owner_id) || !empty($content_type) || !empty($parent_id) || !empty($category_id) || !empty($content_approved) || !empty($content_promoted) || !empty($content_active) || !empty($event_start_date) || !empty($event_end_date) ){
			$first=1;
			
			
			
			if(!empty($app_id)){
				
				$app_id=trim($app_id);
				
				if( $app_id[0]=='+' || $app_id[0]==',' ){
					$app_id[0]='';
				}
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($app_id, 'app_id');
				$first=0;
			}
			
			if(!empty($owner_id)){
				$owner_id=trim($owner_id);
				
				if($first==0 && ($owner_id[0]!='+' && $owner_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				else if( $owner_id[0]=='+' || $owner_id[0]==',' ){
					$owner_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($owner_id, 'owner_id');
				$first=0;
				
			}
			
			if(!empty($content_type)){
				
				$content_type=trim($content_type);
				
				if($first==0 && ($content_type[0]!='+' && $content_type[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				else if( $content_type[0]=='+' || $content_type[0]==',' ){
					$content_type[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($content_type, PVDatabase::getContentTableName().'.content_type');
				$first=0;
			}
			
			if(!empty($content_alias)){
				$content_alias=trim($content_alias);
				
				if($first==0 && ($content_alias[0]!='+' && $content_alias[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				else if( $content_alias[0]=='+' || $content_alias[0]==',' ){
					$content_alias[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($content_alias, 'content_alias');
				$first=0;
			}
			
			if(!empty($parent_content)){
				$parent_content=trim($parent_content);
				
				if($first==0 && ($parent_content[0]!='+' && $parent_content[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				else if( $parent_content[0]=='+' || $parent_content[0]==',' ){
					$parent_content[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($parent_content, 'parent_content');
				$first=0;
			}
			
			if(!empty($content_id)){
				$content_id=trim($content_id);
				
				if($first==0 && ($content_id[0]!='+' && $content_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				else if( $content_id[0]=='+' || $content_id[0]==',' ){
					$content_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($content_id, 'content_id');
				$first=0;
			}
			
			if(!empty($category_id)){
				$category_id=trim($category_id);
				
				if($first==0 && ($category_id[0]!='+' && $category_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				else if( $category_id[0]=='+' || $category_id[0]==',' ){
					$category_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($category_id, PVDatabase::getContentCategoryRelationsTableName().'.category_id');
				$first=0;
			}
			
			if(!empty($content_approved)){
				$content_approved=trim($content_approved);
				
				if($first==0 && ($content_approved[0]!='+' && $content_approved[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				else if( $content_approved[0]=='+' || $content_approved[0]==',' ){
					$content_approved[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($content_approved, 'content_approved');
				$first=0;
			}
			
			if(!empty($content_promoted)){
				$content_promoted=trim($content_promoted);
				
				if($first==0 && ($content_promoted[0]!='+' && $content_promoted[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				else if( $content_promoted[0]=='+' || $content_promoted[0]==',' ){
					$content_promoted[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($content_promoted, 'content_promoted');
				$first=0;
			}
			
			if(!empty($content_active)){
				$content_active=trim($content_active);
				
				if($first==0 && ($content_active[0]!='+' && $content_active[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				else if( $content_active[0]=='+' || $content_active[0]==',' ){
					$content_active[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($content_active, 'content_active');
				$first=0;
			}
			
			if(!empty($custom_where)){
				if($first==0){
					$WHERE_CLAUSE.=" AND ";
				}
				$WHERE_CLAUSE.=" $custom_where ";
				$first=0;
			}
			
			if(!empty($content_taxonomy)){
				if($first==0){
					$WHERE_CLAUSE.=" AND ";
				}
				
				if(is_array($content_taxonomy)){
					$WHERE_CLAUSE.=self::parseSQLArrayOperators($content_taxonomy, 'taxonomy_term');
				}
				else{
					$WHERE_CLAUSE.=" taxonomy_term='$content_taxonomy' ";
				}
				$first=0;
			}
		}
		
		return $WHERE_CLAUSE;
	
	}//end generateBasicWhereSqlClaouse
	
	private static function generateAudioContentWhereSQL($WHERE_CLAUSE, $args){
		
		if(empty($WHERE_CLAUSE)){
			$first=1;	 
		}
		else{
			$first=0;	
		}
		
		if(is_array($args)){
			extract($args);
		}
		
		if(!empty($audio_length)){
				
			$audio_length=trim($audio_length);
			
			if($first==0 && ($audio_length[0]!='+' && $audio_length[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($audio_length[0]=='+' || $audio_length[0]==',') && $first==1 ){
				$audio_length[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($audio_length, 'audio_length');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($mid_file)){
				
			$mid_file=trim($mid_file);
			
			if($first==0 && ($mid_file[0]!='+' && $mid_file[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($mid_file[0]=='+' || $mid_file[0]==',') && $first==1 ){
				$mid_file[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($mid_file, 'mid_file');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($wav_file)){
				
			$wav_file=trim($wav_file);
			
			if($first==0 && ($wav_file[0]!='+' && $wav_file[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($wav_file[0]=='+' || $wav_file[0]==',') && $first==1 ){
				$wav_file[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($wav_file, 'wav_file');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($aif_file)){
				
			$aif_file=trim($aif_file);
			
			if($first==0 && ($aif_file[0]!='+' && $aif_file[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($aif_file[0]=='+' || $aif_file[0]==',') && $first==1 ){
				$aif_file[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($aif_file, 'aif_file');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($mp3_file)){
				
			$mp3_file=trim($mp3_file);
			
			if($first==0 && ($mp3_file[0]!='+' && $mp3_file[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($mp3_file[0]=='+' || $mp3_file[0]==',') && $first==1 ){
				$mp3_file[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($mp3_file, 'mp3_file');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($ra_file)){
				
			$ra_file=trim($ra_file);
			
			if($first==0 && ($ra_file[0]!='+' && $ra_file[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($ra_file[0]=='+' || $ra_file[0]==',') && $first==1 ){
				$ra_file[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($ra_file, 'ra_file');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($sample_length)){
				
			$sample_length=trim($sample_length);
			
			if($first==0 && ($sample_length[0]!='+' && $sample_length[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($sample_length[0]=='+' || $sample_length[0]==',') && $first==1 ){
				$sample_length[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($sample_length, 'sample_length');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($audio_src)){
				
			$audio_src=trim($audio_src);
			
			if($first==0 && ($audio_src[0]!='+' && $audio_src[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($audio_src[0]=='+' || $audio_src[0]==',') && $first==1 ){
				$audio_src[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($audio_src, 'audio_src');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($audio_type)){
				
			$audio_type=trim($audio_type);
			
			if($first==0 && ($audio_type[0]!='+' && $audio_type[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($audio_type[0]=='+' || $audio_type[0]==',') && $first==1 ){
				$audio_type[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($audio_type, 'audio_type');
			
			$first=0;
		}//end not empty app_id
		
		return $WHERE_CLAUSE;
		
	}//end generateAudioContentWhereSQL
	
	private static function generateVideoContentWhereSQL($WHERE_CLAUSE, $args){
		
		if(empty($WHERE_CLAUSE)){
			$first=1;	 
		}
		else{
			$first=0;	
		}
		
		if(is_array($args)){
			extract($args);
		}
		
		if(!empty($video_type)){
				
			$video_type=trim($video_type);
			
			if($first==0 && ($video_type[0]!='+' && $video_type[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($video_type[0]=='+' || $video_type[0]==',') && $first==1 ){
				$video_type[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($video_type, 'video_type');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($video_length)){
				
			$video_length=trim($video_length);
			
			if($first==0 && ($video_length[0]!='+' && $video_length[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($video_length[0]=='+' || $video_length[0]==',') && $first==1 ){
				$video_length[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($video_length, 'video_length');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($video_allow_embedding)){
				
			$video_allow_embedding=trim($video_allow_embedding);
			
			if($first==0 && ($video_allow_embedding[0]!='+' && $video_allow_embedding[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($video_allow_embedding[0]=='+' || $video_allow_embedding[0]==',') && $first==1 ){
				$video_allow_embedding[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($video_allow_embedding, 'video_allow_embedding');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($flv_file)){
				
			$flv_file=trim($flv_file);
			
			if($first==0 && ($flv_file[0]!='+' && $flv_file[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($flv_file[0]=='+' || $flv_file[0]==',') && $first==1 ){
				$flv_file[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($flv_file, 'flv_file');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($mp4_file)){
				
			$mp4_file=trim($mp4_file);
			
			if($first==0 && ($mp4_file[0]!='+' && $mp4_file[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($mp4_file[0]=='+' || $mp4_file[0]==',') && $first==1 ){
				$mp4_file[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($mp4_file, 'mp4_file');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($wmv_file)){
				
			$wmv_file=trim($wmv_file);
			
			if($first==0 && ($wmv_file[0]!='+' && $wmv_file[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($wmv_file[0]=='+' || $wmv_file[0]==',') && $first==1 ){
				$wmv_file[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($wmv_file, 'wmv_file');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($mpeg_file)){
				
			$mpeg_file=trim($mpeg_file);
			
			if($first==0 && ($mpeg_file[0]!='+' && $mpeg_file[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($mpeg_file[0]=='+' || $mpeg_file[0]==',') && $first==1 ){
				$mpeg_file[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($mpeg_file, 'mpeg_file');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($rm_file)){
				
			$rm_file=trim($rm_file);
			
			if($first==0 && ($rm_file[0]!='+' && $rm_file[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($rm_file[0]=='+' || $rm_file[0]==',') && $first==1 ){
				$rm_file[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($rm_file, 'rm_file');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($avi_file)){
				
			$avi_file=trim($avi_file);
			
			if($first==0 && ($avi_file[0]!='+' && $avi_file[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($avi_file[0]=='+' || $avi_file[0]==',') && $first==1 ){
				$avi_file[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($avi_file, 'avi_file');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($mov_file)){
				
			$mov_file=trim($mov_file);
			
			if($first==0 && ($mov_file[0]!='+' && $mov_file[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($mov_file[0]=='+' || $mov_file[0]==',') && $first==1 ){
				$mov_file[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($mov_file, 'mov_file');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($asf_file)){
				
			$asf_file=trim($asf_file);
			
			if($first==0 && ($asf_file[0]!='+' && $asf_file[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($asf_file[0]=='+' || $asf_file[0]==',') && $first==1 ){
				$asf_file[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($asf_file, 'asf_file');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($enable_hq)){
				
			$enable_hq=trim($enable_hq);
			
			if($first==0 && ($enable_hq[0]!='+' && $enable_hq[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($enable_hq[0]=='+' || $enable_hq[0]==',') && $first==1 ){
				$enable_hq[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($enable_hq, 'enable_hq');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($auto_hq)){
				
			$auto_hq=trim($auto_hq);
			
			if($first==0 && ($auto_hq[0]!='+' && $auto_hq[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($auto_hq[0]=='+' || $auto_hq[0]==',') && $first==1 ){
				$auto_hq[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($auto_hq, 'auto_hq');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($video_src)){
				
			$video_src=trim($video_src);
			
			if($first==0 && ($video_src[0]!='+' && $video_src[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($video_src[0]=='+' || $video_src[0]==',') && $first==1 ){
				$video_src[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($video_src, 'video_src');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($video_embed)){
				
			$video_embed=trim(video_embed);
			
			if($first==0 && ($video_embed[0]!='+' && $video_embedc[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($video_embed[0]=='+' || $video_embed[0]==',') && $first==1 ){
				$video_embed[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($video_embed, 'video_embed');
			
			$first=0;
		}//end not empty app_id
		
		
		return $WHERE_CLAUSE;
		
	}//end generateAudioContentWhereSQL
	
	private static function generateTextContentWhereSQL($WHERE_CLAUSE, $args){
		
		if(empty($WHERE_CLAUSE)){
			$first=1;	 
		}
		else{
			$first=0;	
		}
		
		if(is_array($args)){
			extract($args);
		}
		
		if(!empty($text_content)){
				
			$text_content=trim($text_content);
			
			if($first==0 && ($text_content[0]!='+' && $text_content[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($text_content[0]=='+' || $text_content[0]==',') && $first==1 ){
				$text_content[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($text_content, 'text_content');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($text_page_group)){
				
			$text_page_group=trim($text_page_group);
			
			if($first==0 && ($text_page_group[0]!='+' && $text_page_group[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($text_page_group[0]=='+' || $text_page_group[0]==',') && $first==1 ){
				$text_page_group[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($text_page_group, 'text_page_group');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($text_page_number)){
				
			$text_page_number=trim($text_page_number);
			
			if($first==0 && ($text_page_number[0]!='+' && $text_page_number[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($text_page_number[0]=='+' || $text_page_number[0]==',') && $first==1 ){
				$text_page_number[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($text_page_number, 'text_page_number');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($text_section)){
				
			$text_section=trim($text_section);
			
			if($first==0 && ($text_section[0]!='+' && $text_section[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($text_section[0]=='+' || $text_section[0]==',') && $first==1 ){
				$text_section[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($text_section, 'text_section');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($text_src)){
				
			$text_src=trim($text_src);
			
			if($first==0 && ($text_src[0]!='+' && $text_src[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($text_src[0]=='+' || $text_src[0]==',') && $first==1 ){
				$text_src[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($text_src, 'text_src');
			
			$first=0;
		}//end not empty app_id
		
		return $WHERE_CLAUSE;
		
	}//end generateAudioContentWhereSQL
	
	private static function generateFileContentWhereSQL($WHERE_CLAUSE, $args){
		
		if(empty($WHERE_CLAUSE)){
			$first=1;	 
		}
		else{
			$first=0;	
		}
		
		if(is_array($args)){
			extract($args);
		}
		
		if(!empty($file_license)){
				
			$file_license=trim($file_license);
			
			if($first==0 && ($file_license[0]!='+' && $file_license[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($file_license[0]=='+' || $file_license[0]==',') && $first==1 ){
				$file_license[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($file_license, 'file_license');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($file_version)){
				
			$file_version=trim($file_version);
			
			if($first==0 && ($file_version[0]!='+' && $file_version[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($file_version[0]=='+' || $file_version[0]==',') && $first==1 ){
				$file_version[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($file_version, 'file_version');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($file_type)){
				
			$file_type=trim($file_type);
			
			if($first==0 && ($file_type[0]!='+' && $file_type[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($file_type[0]=='+' || $file_type[0]==',') && $first==1 ){
				$file_type[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($file_type, 'file_type');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($file_size)){
				
			$file_size=trim($file_size);
			
			if($first==0 && ($file_size[0]!='+' && $file_size[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($file_size[0]=='+' || $file_size[0]==',') && $first==1 ){
				$file_size[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($file_size, 'file_size');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($file_location)){
				
			$file_location=trim($file_location);
			
			if($first==0 && ($file_location[0]!='+' && $file_location[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($file_location[0]=='+' || $file_location[0]==',') && $first==1 ){
				$file_location[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($file_location, 'file_location');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($file_name)){
				
			$file_name=trim($file_name);
			
			if($first==0 && ($file_name[0]!='+' && $file_name[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($file_name[0]=='+' || $file_name[0]==',') && $first==1 ){
				$file_name[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($file_name, 'file_name');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($file_src)){
				
			$file_src=trim($file_src);
			
			if($first==0 && ($file_src[0]!='+' && $file_src[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($file_src[0]=='+' || $file_src[0]==',') && $first==1 ){
				$file_src[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($file_src, 'file_src');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($file_downloadable)){
				
			$file_downloadable=trim($file_downloadable);
			
			if($first==0 && ($file_downloadable[0]!='+' && $file_downloadable[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($file_downloadable[0]=='+' || $file_downloadable[0]==',') && $first==1 ){
				$file_downloadable[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($file_downloadable, 'file_downloadable');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($file_max_downloads)){
				
			$file_max_downloads=trim($file_max_downloads);
			
			if($first==0 && ($file_max_downloads[0]!='+' && $file_max_downloads[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($file_max_downloads[0]=='+' || $file_max_downloads[0]==',') && $first==1 ){
				$file_max_downloads[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($file_max_downloads, 'file_max_downloads');
			
			$first=0;
		}//end not empty app_id
		
		return $WHERE_CLAUSE;
		
	}//end generateAudioContentWhereSQL
	
	private static function generateImageContentWhereSQL($WHERE_CLAUSE, $args){
		
		if(empty($WHERE_CLAUSE)){
			$first=1;	 
		}
		else{
			$first=0;	
		}
		
		if(is_array($args)){
			extract($args);
		}
		
		if(!empty($image_type)){
				
			$image_type=trim($image_type);
			
			if($first==0 && ($image_type[0]!='+' && $image_type[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($image_type[0]=='+' || $image_type[0]==',') && $first==1 ){
				$image_type[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($image_type, 'image_type');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($image_size)){
				
			$image_size=trim($image_size);
			
			if($first==0 && ($image_size[0]!='+' && $image_size[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($image_size[0]=='+' || $image_size[0]==',') && $first==1 ){
				$image_size[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($image_size, 'image_size');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($image_url)){
				
			$image_url=trim($image_url);
			
			if($first==0 && ($image_url[0]!='+' && $image_url[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($image_url[0]=='+' || $image_url[0]==',') && $first==1 ){
				$image_url[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($image_url, 'image_url');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($thumb_url)){
				
			$thumb_url=trim($thumb_url);
			
			if($first==0 && ($thumb_url[0]!='+' && $thumb_url[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($thumb_url[0]=='+' || $thumb_url[0]==',') && $first==1 ){
				$thumb_url[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($thumb_url, 'thumb_url');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($image_width)){
				
			$image_width=trim($image_width);
			
			if($first==0 && ($image_width[0]!='+' && $image_width[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($image_width[0]=='+' || $image_width[0]==',') && $first==1 ){
				$image_width[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($image_width, 'image_width');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($image_height)){
				
			$image_height=trim($image_height);
			
			if($first==0 && ($image_height[0]!='+' && $image_height[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($image_height[0]=='+' || $image_height[0]==',') && $first==1 ){
				$image_height[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($image_height, 'image_height');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($thumb_width)){
				
			$thumb_width=trim($thumb_width);
			
			if($first==0 && ($thumb_width[0]!='+' && $thumb_width[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($thumb_width[0]=='+' || $thumb_width[0]==',') && $first==1 ){
				$thumb_width[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($thumb_width, 'thumb_width');
			
			$first=0;
		}//end not empty app_id
		
		if(!empty($thumb_height)){
				
			$thumb_height=trim($thumb_height);
			
			if($first==0 && ($thumb_height[0]!='+' && $thumb_height[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($thumb_height[0]=='+' || $thumb_height[0]==',') && $first==1 ){
				$thumb_height[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($thumb_height, 'thumb_height');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($image_src)){
				
			$image_src=trim($image_src);
			
			if($first==0 && ($image_src[0]!='+' && $image_src[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($image_src[0]=='+' || $image_src[0]==',') && $first==1 ){
				$image_src[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($image_src, 'image_src');
			
			$first=0;
		}//end not empty app_id
		
		
		return $WHERE_CLAUSE;
		
	}//end generateAudioContentWhereSQL
	
	
	private static function generateProductContentWhereSQL($WHERE_CLAUSE, $args){
		
		if(empty($WHERE_CLAUSE)){
			$first=1;	 
		}
		else{
			$first=0;	
		}
		
		if(is_array($args)){
			extract($args);
		}
		
		if(!empty($product_sku)){
				
			$product_sku=trim($product_sku);
			
			if($first==0 && ($product_sku[0]!='+' && $product_sku[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($product_sku[0]=='+' || $product_sku[0]==',') && $first==1 ){
				$product_sku[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($product_sku, 'product_sku');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($product_idsku)){
				
			$product_idsku=trim($product_idsku);
			
			if($first==0 && ($product_idsku[0]!='+' && $product_idsku[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($product_idsku[0]=='+' || $product_idsku[0]==',') && $first==1 ){
				$product_idsku[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($product_idsku, 'product_idsku');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($product_vendor_id)){
				
			$product_vendor_id=trim($product_vendor_id);
			
			if($first==0 && ($product_vendor_id[0]!='+' && $product_vendor_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($product_vendor_id[0]=='+' || $product_vendor_id[0]==',') && $first==1 ){
				$product_vendor_id[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($product_vendor_id, 'product_vendor_id');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($product_quantity)){
				
			$product_quantity=trim($product_quantity);
			
			if($first==0 && ($product_quantity[0]!='+' && $product_quantity[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($product_quantity[0]=='+' || $product_quantity[0]==',') && $first==1 ){
				$product_quantity[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($product_quantity, 'product_quantity');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($product_price)){
				
			$product_price=trim($product_price);
			
			if($first==0 && ($product_price[0]!='+' && $product_price[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($product_price[0]=='+' || $product_price[0]==',') && $first==1 ){
				$image_type[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($product_price, 'product_price');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($product_discount_price)){
				
			$product_discount_price=trim($product_discount_price);
			
			if($first==0 && ($product_discount_price[0]!='+' && $product_discount_price[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($product_discount_price[0]=='+' || $product_discount_price[0]==',') && $first==1 ){
				$product_discount_price[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($product_discount_price, 'product_discount_price');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($product_size)){
				
			$product_size=trim($product_size);
			
			if($first==0 && ($product_size[0]!='+' && $product_size[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($product_size[0]=='+' || $product_size[0]==',') && $first==1 ){
				$product_size[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($product_size, 'product_size');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($product_color)){
				
			$product_color=trim($product_color);
			
			if($first==0 && ($product_color[0]!='+' && $product_color[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($product_color[0]=='+' || $product_color[0]==',') && $first==1 ){
				$product_color[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($product_color, 'product_color');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($product_weight)){
				
			$product_weight=trim($product_weight);
			
			if($first==0 && ($product_weight[0]!='+' && $product_weight[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($product_weight[0]=='+' || $product_weight[0]==',') && $first==1 ){
				$product_weight[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($product_weight, 'product_weight');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($product_height)){
				
			$product_height=trim($image_type);
			
			if($first==0 && ($product_height[0]!='+' && $product_height[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($product_height[0]=='+' || $product_height[0]==',') && $first==1 ){
				$product_height[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($product_height, 'product_height');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($product_length)){
				
			$product_length=trim($product_length);
			
			if($first==0 && ($product_length[0]!='+' && $product_length[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($product_length[0]=='+' || $product_length[0]==',') && $first==1 ){
				$product_length[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($product_length, 'product_length');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($product_currency)){
				
			$product_currency=trim($product_currency);
			
			if($first==0 && ($product_currency[0]!='+' && $product_currency[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($product_currency[0]=='+' || $product_currency[0]==',') && $first==1 ){
				$product_currency[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($product_currency, 'product_currency');
			
			$first=0;
		}//end not empty app_id
		
		

		if(!empty($product_in_stock)){
				
			$product_in_stock=trim($product_in_stock);
			
			if($first==0 && ($product_in_stock[0]!='+' && $product_in_stock[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($product_in_stock[0]=='+' || $product_in_stock[0]==',') && $first==1 ){
				$product_in_stock[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($product_in_stock, 'product_in_stock');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($product_type)){
				
			$product_type=trim($product_type);
			
			if($first==0 && ($product_type[0]!='+' && $product_type[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($product_type[0]=='+' || $product_type[0]==',') && $first==1 ){
				$product_type[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($product_type, 'product_type');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($product_tax_id)){
				
			$product_tax_id=trim($product_tax_id);
			
			if($first==0 && ($product_tax_id[0]!='+' && $product_tax_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($product_tax_id[0]=='+' || $product_tax_id[0]==',') && $first==1 ){
				$product_tax_id[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($product_tax_id, 'product_tax_id');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($product_attribute)){
				
			$product_attribute=trim($product_attribute);
			
			if($first==0 && ($product_attribute[0]!='+' && $product_attribute[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($product_attribute[0]=='+' || $product_attribute[0]==',') && $first==1 ){
				$product_attribute[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($product_attribute, 'product_attribute');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($product_version)){
				
			$product_version=trim($product_version);
			
			if($first==0 && ($product_version[0]!='+' && $product_version[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($product_version[0]=='+' || $product_version[0]==',') && $first==1 ){
				$product_version[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($product_version, 'product_version');
			
			$first=0;
		}//end not empty app_id
		
		return $WHERE_CLAUSE;
		
		
	}//end generateAudioContentWhereSQL
	
	
	private static function generateEventContentWhereSQL($WHERE_CLAUSE, $args){
		
		if(empty($WHERE_CLAUSE)){
			$first=1;	 
		}
		else{
			$first=0;	
		}
		
		if(is_array($args)){
			extract($args);
		}
		
		if(!empty($event_location)){
				
			$event_location=trim($event_location);
			
			if($first==0 && ($event_location[0]!='+' && $event_location[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($event_location[0]=='+' || $event_location[0]==',') && $first==1 ){
				$event_location[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($event_location, 'event_location');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($event_start_date)){
				
			$event_start_date=trim($event_start_date);
			
			if($first==0 && ($event_start_date[0]!='+' && $event_start_date[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($event_start_date[0]=='+' || $event_start_date[0]==',') && $first==1 ){
				$event_start_date[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($event_start_date, 'event_start_date');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($event_end_date)){
				
			$event_end_date=trim($event_end_date);
			
			if($first==0 && ($event_end_date[0]!='+' && $event_end_date[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($event_end_date[0]=='+' || $event_end_date[0]==',') && $first==1 ){
				$event_end_date[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($event_end_date, 'event_end_date');
			
			$first=0;
		}//end not empty app_id
		
		
		
		if(!empty($event_country)){
				
			$event_country=trim($event_country);
			
			if($first==0 && ($event_country[0]!='+' && $event_country[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($event_country[0]=='+' || $event_country[0]==',') && $first==1 ){
				$event_country[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($event_country, 'event_country');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($event_address)){
				
			$event_address=trim($event_address);
			
			if($first==0 && ($event_address[0]!='+' && $event_address[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($event_address[0]=='+' || $event_address[0]==',') && $first==1 ){
				$event_address[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($event_address, 'event_address');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($event_city)){
				
			$event_city=trim($event_city);
			
			if($first==0 && ($event_city[0]!='+' && $event_city[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($event_city[0]=='+' || $event_city[0]==',') && $first==1 ){
				$event_city[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($event_city, 'event_city');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($event_state)){
				
			$event_state=trim($event_state);
			
			if($first==0 && ($event_state[0]!='+' && $event_state[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($event_state[0]=='+' || $event_state[0]==',') && $first==1 ){
				$event_state[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($event_state, 'event_state');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($event_zip)){
				
			$event_zip=trim($event_zip);
			
			if($first==0 && ($event_zip[0]!='+' && $event_zip[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($event_zip[0]=='+' || $event_zip[0]==',') && $first==1 ){
				$event_zip[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($event_zip, 'event_zip');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($event_longitude)){
				
			$event_longitude=trim($event_longitude);
			
			if($first==0 && ($event_longitude[0]!='+' && $event_longitude[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($event_longitude[0]=='+' || $event_longitude[0]==',') && $first==1 ){
				$event_longitude[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($event_longitude, 'event_longitude');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($event_latitude)){
				
			$event_latitude=trim($event_latitude);
			
			if($first==0 && ($event_latitude[0]!='+' && $event_latitude[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($event_latitude[0]=='+' || $event_latitude[0]==',') && $first==1 ){
				$event_latitude[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($event_latitude, 'event_latitude');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($event_src)){
				
			$event_src=trim($event_src);
			
			if($first==0 && ($event_src[0]!='+' && $event_src[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($event_src[0]=='+' || $event_src[0]==',') && $first==1 ){
				$event_src[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($event_src, 'event_src');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($event_map)){
				
			$event_map=trim($event_map);
			
			if($first==0 && ($event_map[0]!='+' && $event_map[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($event_map[0]=='+' || $event_map[0]==',') && $first==1 ){
				$event_map[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($event_map, 'event_map');
			
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($undefined_endtime)){
				
			$undefined_endtime=trim($undefined_endtime);
			
			if($first==0 && ($undefined_endtime[0]!='+' && $undefined_endtime[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
				
			else if( ($undefined_endtime[0]=='+' || $undefined_endtime[0]==',') && $first==1 ){
				$undefined_endtime[0]='';
			}
			
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($undefined_endtime, 'undefined_endtime');
			
			$first=0;
		}//end not empty app_id
		
		
		return $WHERE_CLAUSE;
		
	}// generateEventContentWhereSQL
	
	
	
	
	
	public static function getContentList($order_by_clause=array()){
		$order_by_clause += self::getContentDefaults();
		
    	$content_array=array();
    	$db_type=PVDatabase::getDatabaseType();
		$table_name=PVDatabase::getContentTableName();
    	
    	$WHERE_CLAUSE=self::generateBasicWhereSqlClause($order_by_clause);
		
		if(!empty($WHERE_CLAUSE)){
			$WHERE_CLAUSE=' WHERE '.$WHERE_CLAUSE;
		}
    	
    	if(is_array($order_by_clause)){
			
			$custom_where=$order_by_clause['custom_where'];
			$custom_join=$order_by_clause['custom_join'];
			$custom_select=$order_by_clause['custom_select'];
			
			$order_by_clause=PVDatabase::makeSafe($order_by_clause);
    		
			$limit=$order_by_clause['limit'];
    		$category_id=$order_by_clause['category_id'];
    		$distinct=$order_by_clause['distinct'];
			$group_by=$order_by_clause['group_by'];
			$having=$order_by_clause['having'];
			$join_users=$order_by_clause['join_users'];
			$current_page=$order_by_clause['current_page'];
			$results_per_page=$order_by_clause['results_per_page'];
			$paged=$order_by_clause['paged'];
			$prequery=$order_by_clause['prequery'];
			$prefix_args=$order_by_clause['prefix_args'];
			
			$order_by_clause=$order_by_clause['order_by'];
    	}
    	
		$CATEGORY_JOIN='';
		
		if(!empty($category_id)){
			$CATEGORY_JOIN.=' JOIN '.PVDatabase::getContentCategoryRelationsTableName().' ON '.PVDatabase::getContentCategoryRelationsTableName().'.content_id='.PVDatabase::getContentTableName().'.content_id';
		}
		
		if(!empty($join_users)){
			$CATEGORY_JOIN.='JOIN '.PVDatabase::getLoginTableName().' ON '.PVDatabase::getContentTableName().'.owner_id='.PVDatabase::getLoginTableName().'.user_id ';
		}
		
		if(!empty($custom_join)){
			$CATEGORY_JOIN.=" $custom_join ";
		}
		
		if(!empty($distinct)){
			$prefix_args.=" DISTINCT $distinct, ";
		}
		
		if(!empty($limit) && $db_type=='mssql' && !$paged){
			$prefix_args.=" TOP $limit ";
		}
		
		if($paged){
			$page_results=PVDatabase::getPagininationOffset(PVDatabase::getContentTableName() , $CATEGORY_JOIN , $WHERE_CLAUSE, $current_page, $results_per_page, $order_by_clause);
			
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
		
    	$query="$prequery SELECT $prefix_args $custom_select FROM $table_name $CATEGORY_JOIN $WHERE_CLAUSE";
    	
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
	}//end getContentList
	
	public static function getImageContentList($order_by_clause=array()){
		$order_by_clause += self::getImageContentDefaults();
		$order_by_clause += self::getContentDefaults();
		
    	$content_array=array();
    	$db_type=PVDatabase::getDatabaseType();
		$table_name=PVDatabase::getContentTableName();
    	
    	$WHERE_CLAUSE=self::generateBasicWhereSqlClause($order_by_clause);
		
		$WHERE_CLAUSE=self::generateImageContentWhereSQL($WHERE_CLAUSE, $order_by_clause);
		
		if(!empty($WHERE_CLAUSE)){
			$WHERE_CLAUSE=' WHERE '.$WHERE_CLAUSE;
		}
    	
    	if(is_array($order_by_clause)){
			
			$custom_where=$order_by_clause['custom_where'];
			$custom_join=$order_by_clause['custom_join'];
			$custom_select=$order_by_clause['custom_select'];
			
			$order_by_clause=PVDatabase::makeSafe($order_by_clause);
			
    		$limit=$order_by_clause['limit'];
    		$category_id=$order_by_clause['category_id'];
			$distinct=$order_by_clause['distinct'];
			$group_by=$order_by_clause['group_by'];
			$having=$order_by_clause['having'];
			$join_users=$order_by_clause['join_users'];
			$current_page=$order_by_clause['current_page'];
			$results_per_page=$order_by_clause['results_per_page'];
			$paged=$order_by_clause['paged'];
			$prequery=$order_by_clause['prequery'];
			$prefix_args=$order_by_clause['prefix_args'];
			
    		$order_by_clause=$order_by_clause['order_by'];	
    	}
		
		$CATEGORY_JOIN=' JOIN '.PVDatabase::getImageContentTableName().' ON '.PVDatabase::getImageContentTableName().'.image_id='.PVDatabase::getContentTableName().'.content_id ';
		
		if(!empty($category_id)){
			$CATEGORY_JOIN.=' JOIN '.PVDatabase::getContentCategoryRelationsTableName().' ON '.PVDatabase::getContentCategoryRelationsTableName().'.content_id='.PVDatabase::getContentTableName().'.content_id';
		}
		
		
		if(!empty($join_users)){
			$CATEGORY_JOIN.='JOIN '.PVDatabase::getLoginTableName().' ON '.PVDatabase::getContentTableName().'.owner_id='.PVDatabase::getLoginTableName().'.user_id ';
		}
		
		if(!empty($custom_join)){
			$CATEGORY_JOIN.=" $custom_join ";
		}
		
		if(!empty($distinct)){
			$prefix_args.=" DISTINCT $distinct, ";
		}
		
		if(!empty($limit) && $db_type=='mssql' && !$paged){
			$prefix_args.=" TOP $limit ";
		}
		
		if($paged){
			$page_results=PVDatabase::getPagininationOffset(PVDatabase::getContentTableName(), $CATEGORY_JOIN , $WHERE_CLAUSE, $current_page, $results_per_page, $order_by_clause);
			
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
		
    	$query="$prequery SELECT $prefix_args $custom_select FROM $table_name $CATEGORY_JOIN $WHERE_CLAUSE";
    	
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
	}//end getContentList
	
	public static function getVideoContentList($order_by_clause=array()){
		$order_by_clause += self::getVideoContentDefaults();
		$order_by_clause += self::getContentDefaults();
		
    	$content_array=array();
    	$db_type=PVDatabase::getDatabaseType();
		$table_name=PVDatabase::getContentTableName();
    	
    	$WHERE_CLAUSE=self::generateBasicWhereSqlClause($order_by_clause);
		
		$WHERE_CLAUSE=self::generateVideoContentWhereSQL($WHERE_CLAUSE, $order_by_clause);
		
		if(!empty($WHERE_CLAUSE)){
			$WHERE_CLAUSE=' WHERE '.$WHERE_CLAUSE;
		}
    	
    	if(is_array($order_by_clause)){
			
			$custom_where=$order_by_clause['custom_where'];
			$custom_join=$order_by_clause['custom_join'];
			$custom_select=$order_by_clause['custom_select'];
    		
			$limit=$order_by_clause['limit'];
    		$category_id=$order_by_clause['category_id'];
			$distinct=$order_by_clause['distinct'];
			$group_by=$order_by_clause['group_by'];
			$having=$order_by_clause['having'];
			$join_users=$order_by_clause['join_users'];
			$current_page=$order_by_clause['current_page'];
			$results_per_page=$order_by_clause['results_per_page'];
			$paged=$order_by_clause['paged'];
			$prequery=$order_by_clause['prequery'];
			$prefix_args=$order_by_clause['prefix_args'];
			
    		$order_by_clause=$order_by_clause['order_by'];	
    	}
		
		$CATEGORY_JOIN=' JOIN '.PVDatabase::getVideoContentTableName().' ON '.PVDatabase::getVideoContentTableName().'.video_id='.PVDatabase::getContentTableName().'.content_id ';
		
		if(!empty($category_id)){
			$CATEGORY_JOIN.=' JOIN '.PVDatabase::getContentCategoryRelationsTableName().' ON '.PVDatabase::getContentCategoryRelationsTableName().'.content_id='.PVDatabase::getContentTableName().'.content_id';
		}
		
		if(!empty($join_users)){
			$CATEGORY_JOIN.='JOIN '.PVDatabase::getLoginTableName().' ON '.PVDatabase::getContentTableName().'.owner_id='.PVDatabase::getLoginTableName().'.user_id ';
		}
		
		if(!empty($custom_join)){
			$CATEGORY_JOIN.=" $custom_join ";
		}
		
		if(!empty($distinct)){
			$prefix_args.=" DISTINCT $distinct, ";
		}
		
		if(!empty($limit) && $db_type=='mssql' && !$paged){
			$prefix_args.=" TOP $limit ";
		}
		
		if($paged){
			$page_results=PVDatabase::getPagininationOffset( PVDatabase::getContentTableName(), $CATEGORY_JOIN , $WHERE_CLAUSE, $current_page, $results_per_page, $order_by_clause);
			
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
		
    	$query="$prequery SELECT $prefix_args $custom_select FROM $table_name $CATEGORY_JOIN $WHERE_CLAUSE";
    	
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
	}//end getContentList
	
	public static function getEventContentList($order_by_clause=array()){
		$order_by_clause += self::getEventContentDefaults();
		$order_by_clause += self::getContentDefaults();
		
    	$content_array=array();
    	$db_type=PVDatabase::getDatabaseType();
		$table_name=PVDatabase::getContentTableName();
    	
    	$WHERE_CLAUSE=self::generateBasicWhereSqlClause($order_by_clause);
		
		$WHERE_CLAUSE=self::generateEventContentWhereSQL($WHERE_CLAUSE, $order_by_clause);
		
		if(!empty($WHERE_CLAUSE)){
			$WHERE_CLAUSE=' WHERE '.$WHERE_CLAUSE;
		}
    	
    	if(is_array($order_by_clause)){
			
			$custom_where=$order_by_clause['custom_where'];
			$custom_join=$order_by_clause['custom_join'];
			$custom_select=$order_by_clause['custom_select'];
    		
    		$limit=$order_by_clause['limit'];
    		$category_id=$order_by_clause['category_id'];
			$distinct=$order_by_clause['distinct'];
			$group_by=$order_by_clause['group_by'];
			$having=$order_by_clause['having'];
			$join_users=$order_by_clause['join_users'];
			
    		$event_city=$order_by_clause['event_city'];
    		$event_state=$order_by_clause['event_state'];
    		$event_country=$order_by_clause['event_country'];
    		$event_address=$order_by_clause['event_address'];
    		$event_zip=$order_by_clause['event_zip'];
    		$event_location=$order_by_clause['event_location'];
			
			$current_page=$order_by_clause['current_page'];
			$results_per_page=$order_by_clause['results_per_page'];
			$paged=$order_by_clause['paged'];
			$prequery=$order_by_clause['prequery'];
			$prefix_args=$order_by_clause['prefix_args'];
    		
    		$order_by_clause=$order_by_clause['order_by'];
    	}

		$CATEGORY_JOIN=' JOIN '.PVDatabase::getEventContentTableName().' ON '.PVDatabase::getEventContentTableName().'.event_id='.PVDatabase::getContentTableName().'.content_id ';	
		
		if(!empty($category_id)){
			$CATEGORY_JOIN.=' JOIN '.PVDatabase::getContentCategoryRelationsTableName().' ON '.PVDatabase::getContentCategoryRelationsTableName().'.content_id='.PVDatabase::getContentTableName().'.content_id';
		}
		
		if(!empty($join_users)){
			$CATEGORY_JOIN.='JOIN '.PVDatabase::getLoginTableName().' ON '.PVDatabase::getContentTableName().'.owner_id='.PVDatabase::getLoginTableName().'.user_id ';
		}
		
		if(!empty($custom_join)){
			$CATEGORY_JOIN.=" $custom_join ";
		}
		
		if(!empty($distinct)){
			$prefix_args.=" DISTINCT $distinct, ";
		}
		
		if(!empty($limit) && $db_type=='mssql' && !$paged){
			$prefix_args.=" TOP $limit ";
		}
		
		if($paged){
			$page_results=PVDatabase::getPagininationOffset(PVDatabase::getContentTableName() , $CATEGORY_JOIN , $WHERE_CLAUSE, $current_page, $results_per_page, $order_by_clause);
			
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
		
    	$query="$prequery SELECT $prefix_args $custom_select FROM $table_name $CATEGORY_JOIN $WHERE_CLAUSE";
    	
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
	}//end getContentList
	
	public static function getFileContentList($order_by_clause=array()){
		$order_by_clause += self::getFileContentDefaults();
		$order_by_clause += self::getContentDefaults();
		
    	$content_array=array();
		$db_type=PVDatabase::getDatabaseType();
		$table_name=PVDatabase::getContentTableName();
    	
    	$WHERE_CLAUSE=self::generateBasicWhereSqlClause($order_by_clause);
		
		$WHERE_CLAUSE=self::generateFileContentWhereSQL($WHERE_CLAUSE, $order_by_clause);
		
		if(!empty($WHERE_CLAUSE)){
			$WHERE_CLAUSE=' WHERE '.$WHERE_CLAUSE;
		}
    	
    	if(is_array($order_by_clause)){
			
			$custom_where=$order_by_clause['custom_where'];
			$custom_join=$order_by_clause['custom_join'];
			$custom_select=$order_by_clause['custom_select'];
    		
    		$limit=$order_by_clause['limit'];
    		$category_id=$order_by_clause['category_id'];
			$distinct=$order_by_clause['distinct'];
			$group_by=$order_by_clause['group_by'];
			$having=$order_by_clause['having'];
			$join_users=$order_by_clause['join_users'];
			
    		$file_src=$order_by_clause['file_src'];
    		$file_type=$order_by_clause['file_type'];
    		$file_size=$order_by_clause['file_size'];
			
			$current_page=$order_by_clause['current_page'];
			$results_per_page=$order_by_clause['results_per_page'];
			$paged=$order_by_clause['paged'];
			$prefix_args=$order_by_clause['prefix_args'];
    		
    		$order_by_clause=$order_by_clause['order_by'];
    	}
	
		$CATEGORY_JOIN=' JOIN '.PVDatabase::getFileContentTableName().' ON '.PVDatabase::getFileContentTableName().'.file_id='.PVDatabase::getContentTableName().'.content_id  ';
			
		if(!empty($category_id)){
			$CATEGORY_JOIN.=' JOIN '.PVDatabase::getContentCategoryRelationsTableName().' ON '.PVDatabase::getContentCategoryRelationsTableName().'.content_id='.PVDatabase::getContentTableName().'.content_id';
		}
		
		if(!empty($join_users)){
			$CATEGORY_JOIN.='JOIN '.PVDatabase::getLoginTableName().' ON '.PVDatabase::getContentTableName().'.owner_id='.PVDatabase::getLoginTableName().'.user_id ';
		}
		
		if(!empty($custom_join)){
			$CATEGORY_JOIN.=" $custom_join ";
		}
		
		if(!empty($distinct)){
			$prefix_args.=" DISTINCT $distinct, ";
		}
		
		if(!empty($limit) && $db_type=='mssql' && !$paged){
			$prefix_args.=" TOP $limit ";
		}
		
		if($paged){
			$page_results=PVDatabase::getPagininationOffset( PVDatabase::getContentTableName(), $CATEGORY_JOIN , $WHERE_CLAUSE, $current_page, $results_per_page, $order_by_clause);
			
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
		
    	$query="$prequery SELECT $prefix_args $custom_select FROM $table_name $CATEGORY_JOIN $WHERE_CLAUSE";
    	
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
	}//end getContentList
	
	
	public static function getProductContentList($order_by_clause=array()){
		$order_by_clause += self::getProductContentDefaults();
		$order_by_clause += self::getContentDefaults();
		
    	$content_array=array();
		$db_type=PVDatabase::getDatabaseType();
		$table_name=PVDatabase::getContentTableName();
    	
    	$WHERE_CLAUSE=self::generateBasicWhereSqlClause($order_by_clause);
		
		$WHERE_CLAUSE=self::generateProductContentWhereSQL($WHERE_CLAUSE, $order_by_clause);
		
		if(!empty($WHERE_CLAUSE)){
			$WHERE_CLAUSE=' WHERE '.$WHERE_CLAUSE;
		}
    	
    	if(is_array($order_by_clause)){
    		
    		$limit=$order_by_clause['limit'];
    		$category_id=$order_by_clause['category_id'];
			
			$custom_where=$order_by_clause['custom_where'];
			$custom_join=$order_by_clause['custom_join'];
			$custom_select=$order_by_clause['custom_select'];
			
			$distinct=$order_by_clause['distinct'];
			$group_by=$order_by_clause['group_by'];
			$having=$order_by_clause['having'];
			$join_users=$order_by_clause['join_users'];
			
    		$file_src=$order_by_clause['file_src'];
    		$file_type=$order_by_clause['file_type'];
    		$file_size=$order_by_clause['file_size'];
			
			$current_page=$order_by_clause['current_page'];
			$results_per_page=$order_by_clause['results_per_page'];
			$paged=$order_by_clause['paged'];
			$prequery=$order_by_clause['prequery'];
			$prefix_args=$order_by_clause['prefix_args'];
    		
    		$order_by_clause=$order_by_clause['order_by'];
    	}
		
		$CATEGORY_JOIN=' JOIN '.PVDatabase::getProductContentTableName().' ON '.PVDatabase::getProductContentTableName().'.product_id='.PVDatabase::getContentTableName().'.content_id ';
		
		if(!empty($category_id)){
			$CATEGORY_JOIN.=' JOIN '.PVDatabase::getContentCategoryRelationsTableName().' ON '.PVDatabase::getContentCategoryRelationsTableName().'.content_id='.PVDatabase::getContentTableName().'.content_id';
		}
		
		if(!empty($join_users)){
			$CATEGORY_JOIN.='JOIN '.PVDatabase::getLoginTableName().' ON '.PVDatabase::getContentTableName().'.owner_id='.PVDatabase::getLoginTableName().'.user_id ';
		}
		
		if(!empty($custom_join)){
			$CATEGORY_JOIN.=" $custom_join ";
		}
		
		if(!empty($distinct)){
			$prefix_args.=" DISTINCT $distinct, ";
		}
		
		if(!empty($limit) && $db_type=='mssql' && !$paged){
			$prefix_args.=" TOP $limit ";
		}
		
		if($paged){
			$page_results=PVDatabase::getPagininationOffset( PVDatabase::getContentTableName(), $CATEGORY_JOIN , $WHERE_CLAUSE, $current_page, $results_per_page, $order_by_clause);
			
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
		
    	$query="$prequery SELECT $prefix_args $custom_select FROM $table_name $CATEGORY_JOIN $WHERE_CLAUSE";
    	
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
	}//end getContentList
	
	public static function getTextContentList($order_by_clause=array()){
		$order_by_clause += self::getTextContentDefaults();
		$order_by_clause += self::getContentDefaults();
		
    	$content_array=array();
    	$db_type=PVDatabase::getDatabaseType();
		$table_name=PVDatabase::getContentTableName();
    	
    	$WHERE_CLAUSE=self::generateBasicWhereSqlClause($order_by_clause);
		
		$WHERE_CLAUSE=self::generateTextContentWhereSQL($WHERE_CLAUSE, $order_by_clause);
		
		if(!empty($WHERE_CLAUSE)){
			$WHERE_CLAUSE=' WHERE '.$WHERE_CLAUSE;
		}
    	
    	if(is_array($order_by_clause)){
    		$limit=$order_by_clause['limit'];
    		$category_id=$order_by_clause['category_id'];
			
			$custom_where=$order_by_clause['custom_where'];
			$custom_join=$order_by_clause['custom_join'];
			$custom_select=$order_by_clause['custom_select'];
			
			$distinct=$order_by_clause['distinct'];
			$group_by=$order_by_clause['group_by'];
			$having=$order_by_clause['having'];
			$join_users=$order_by_clause['join_users'];
			
			$current_page=$order_by_clause['current_page'];
			$results_per_page=$order_by_clause['results_per_page'];
			$paged=$order_by_clause['paged'];
			$prequery=$order_by_clause['prequery'];
			$prefix_args=$order_by_clause['prefix_args'];
			
    		$order_by_clause=$order_by_clause['order_by'];
    	}
    	
		$CATEGORY_JOIN=' JOIN '.PVDatabase::getTextContentTableName().' ON '.PVDatabase::getTextContentTableName().'.text_id='.PVDatabase::getContentTableName().'.content_id ';
		
		if(!empty($category_id)){
			$CATEGORY_JOIN.=' JOIN '.PVDatabase::getContentCategoryRelationsTableName().' ON '.PVDatabase::getContentCategoryRelationsTableName().'.content_id='.PVDatabase::getContentTableName().'.content_id';
		}
		
		if(!empty($join_users)){
			$CATEGORY_JOIN.=' JOIN '.PVDatabase::getLoginTableName().' ON '.PVDatabase::getContentTableName().'.owner_id='.PVDatabase::getLoginTableName().'.user_id ';
		}
		
		if(!empty($custom_join)){
			$CATEGORY_JOIN.=" $custom_join ";
		}
		
		if(!empty($distinct)){
			$prefix_args.=" DISTINCT $distinct, ";
		}
		
		if(!empty($limit) && $db_type=='mssql' && !$paged){
			$prefix_args.=" TOP $limit ";
		}
		
		if($paged){
			$page_results=PVDatabase::getPagininationOffset(PVDatabase::getContentTableName(), $CATEGORY_JOIN , $WHERE_CLAUSE, $current_page, $results_per_page, $order_by_clause);
			
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
		
    	$query="$prequery SELECT $prefix_args $custom_select FROM $table_name $CATEGORY_JOIN $WHERE_CLAUSE";
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
	}//end getContentList
	
	public static function getAudioContentList($order_by_clause=array()){
		$order_by_clause += self::getAudioContentDefaults();
		$order_by_clause += self::getContentDefaults();
		
    	$content_array=array();
		$db_type=PVDatabase::getDatabaseType();
		$table_name=PVDatabase::getContentTableName();
    	
    	$WHERE_CLAUSE=self::generateBasicWhereSqlClause($order_by_clause);
		
		$WHERE_CLAUSE=self::generateAudioContentWhereSQL($WHERE_CLAUSE, $order_by_clause);
		
		if(!empty($WHERE_CLAUSE)){
			$WHERE_CLAUSE=' WHERE '.$WHERE_CLAUSE;
		}
    	
    	if(is_array($order_by_clause)){
    		$limit=$order_by_clause['limit'];
    		$category_id=$order_by_clause['category_id'];
			
			$custom_where=$order_by_clause['custom_where'];
			$custom_join=$order_by_clause['custom_join'];
			$custom_select=$order_by_clause['custom_select'];
			
			$distinct=$order_by_clause['distinct'];
			$group_by=$order_by_clause['group_by'];
			$having=$order_by_clause['having'];
			$join_users=$order_by_clause['join_users'];
			
			$current_page=$order_by_clause['current_page'];
			$results_per_page=$order_by_clause['results_per_page'];
			$paged=$order_by_clause['paged'];
			$prequery=$order_by_clause['prequery'];
			$prefix_args=$order_by_clause['prefix_args'];
			
    		$order_by_clause=$order_by_clause['order_by']; 		
    	}
   
		$CATEGORY_JOIN=' JOIN '.PVDatabase::getAudioContentTableName().' ON '.PVDatabase::getAudioContentTableName().'.audio_id='.PVDatabase::getContentTableName().'.content_id ';
		
		if(!empty($custom_where)){
			
			if(empty($WHERE_CLAUSE)){
				$WHERE_CLAUSE.=' WHERE ';
			}
			
			$WHERE_CLAUSE.=" $custom_where";
		}
	
		if(!empty($category_id)){
			$CATEGORY_JOIN.=' JOIN '.PVDatabase::getContentCategoryRelationsTableName().' ON '.PVDatabase::getContentCategoryRelationsTableName().'.content_id='.PVDatabase::getContentTableName().'.content_id';
		}
		
		if(!empty($join_users)){
			$CATEGORY_JOIN.='JOIN '.PVDatabase::getLoginTableName().' ON '.PVDatabase::getContentTableName().'.owner_id='.PVDatabase::getLoginTableName().'.user_id ';
		}
		
		if(!empty($custom_join)){
			$CATEGORY_JOIN.=" $custom_join ";
		}
		
		if(!empty($distinct)){
			$prefix_args.=" DISTINCT $distinct, ";
		}
		
		if(!empty($limit) && $db_type=='mssql' && !$paged){
			$prefix_args.=" TOP $limit ";
		}
		
		if($paged){
			$page_results=PVDatabase::getPagininationOffset(PVDatabase::getContentTableName(), $CATEGORY_JOIN , $WHERE_CLAUSE, $current_page, $results_per_page, $order_by_clause);
			
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
		
    	$query="$prequery SELECT $prefix_args $custom_select FROM $table_name $CATEGORY_JOIN $WHERE_CLAUSE";
    	
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
	}//end getContentList
	
	
	public static function getUniversalContentList($order_by_clause='', $limit='', $app_id='', $owner_id='', $content_type='', $parent_id='', $category_id=''){
			
    		$content_array=array();
    		$db_type=PVDatabase::getDatabaseType();
			$table_name=PVDatabase::getContentTableName();
    	
    		$WHERE_CLAUSE=self::generateBasicWhereSqlClause($order_by_clause);
			
			$WHERE_CLAUSE=self::generateEventContentWhereSQL($WHERE_CLAUSE, $order_by_clause);
			$WHERE_CLAUSE=self::generateTextContentWhereSQL($WHERE_CLAUSE, $order_by_clause);
			$WHERE_CLAUSE=self::generateProductContentWhereSQL($WHERE_CLAUSE, $order_by_clause);
			$WHERE_CLAUSE=self::generateImageContentWhereSQL($WHERE_CLAUSE, $order_by_clause);
			$WHERE_CLAUSE=self::generateVideoContentWhereSQL($WHERE_CLAUSE, $order_by_clause);
			$WHERE_CLAUSE=self::generateAudioContentWhereSQL($WHERE_CLAUSE, $order_by_clause);
			
			if(!empty($WHERE_CLAUSE)){
				$WHERE_CLAUSE=' WHERE '.$WHERE_CLAUSE;
			}
    	
			if(is_array($order_by_clause)){
				$limit=$order_by_clause['limit'];
				$category_id=$order_by_clause['category_id'];
				
				$custom_where=$order_by_clause['custom_where'];
				$custom_join=$order_by_clause['custom_join'];
				$custom_select=$order_by_clause['custom_select'];
				
				$distinct=$order_by_clause['distinct'];
				$group_by=$order_by_clause['group_by'];
				$having=$order_by_clause['having'];
				$join_users=$order_by_clause['join_users'];
				
				$current_page=$order_by_clause['current_page'];
				$results_per_page=$order_by_clause['results_per_page'];
				$paged=$order_by_clause['paged'];
				$prequery=$order_by_clause['prequery'];
			
				$order_by_clause=$order_by_clause['order_by'];
				
			}
    	
		
		
		$CATEGORY_JOIN=' JOIN '.PVDatabase::getAudioContentTableName().' ON '.PVDatabase::getAudioContentTableName().'.audio_id='.PVDatabase::getContentTableName().'.content_id 
		JOIN '.PVDatabase::getEventContentTableName().' ON '.PVDatabase::getEventContentTableName().'.event_id='.PVDatabase::getContentTableName().'.content_id 
		JOIN '.PVDatabase::getImageContentTableName().' ON '.PVDatabase::getImageContentTableName().'.image_id='.PVDatabase::getContentTableName().'.content_id 
		JOIN '.PVDatabase::getProductContentTableName().' ON '.PVDatabase::getProductContentTableName().'.product_id='.PVDatabase::getContentTableName().'.content_id
		JOIN '.PVDatabase::getFileContentTableName().' ON '.PVDatabase::getFileContentTableName().'.file_id='.PVDatabase::getContentTableName().'.content_id
		JOIN '.PVDatabase::getVideoContentTableName().' ON '.PVDatabase::getVideoContentTableName().'.video_id='.PVDatabase::getContentTableName().'.content_id
		JOIN '.PVDatabase::getTextContentTableName().' ON '.PVDatabase::getTextContentTableName().'.text_id='.PVDatabase::getContentTableName().'.content_id ';
		
		if(!empty($custom_where)){
			
			if(empty($WHERE_CLAUSE)){
				$WHERE_CLAUSE.=' WHERE ';
			}
			
			//$WHERE_CLAUSE.=" $custom_where";
		}
	
		if(!empty($category_id)){
			$CATEGORY_JOIN.=' JOIN '.PVDatabase::getContentCategoryRelationsTableName().' ON '.PVDatabase::getContentCategoryRelationsTableName().'.content_id='.PVDatabase::getContentTableName().'.content_id';
		}
		
		if(!empty($join_users)){
			$CATEGORY_JOIN.='JOIN '.PVDatabase::getLoginTableName().' ON '.PVDatabase::getContentTableName().'.owner_id='.PVDatabase::getLoginTableName().'.user_id ';
		}
		
		if(!empty($custom_join)){
			$CATEGORY_JOIN.=" $custom_join ";
		}
		
		if(!empty($distinct)){
			$prefix_args.=" DISTINCT $distinct, ";
		}
		
		if(!empty($limit) && $db_type=='mssql' && !$paged){
			$prefix_args.=" TOP $limit ";
		}
		
		if($paged){
			$page_results=PVDatabase::getPagininationOffset(PVDatabase::getContentTableName(), $CATEGORY_JOIN , $WHERE_CLAUSE, $current_page, $results_per_page, $order_by_clause);
			
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
		
    	$query="$prequery SELECT $prefix_args $custom_select FROM $table_name $CATEGORY_JOIN $WHERE_CLAUSE";
		
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
	}//end getContentList
	
	
	function getCategoryList($args=array()){
		$args += self::getCategoryDefaults();
		$args += self::_getSqlSearchDefaults();
		$custom_where=$args['custom_where'];
		$custom_join=$args['custom_join'];
		$custom_select=$args['custom_select'];
		$args= PVDatabase::makeSafe($args);
		extract($args, EXTR_SKIP);
		
			$first=1;
			
			$content_array=array();
			$table_name=PVDatabase::getContentCategoriesTableName();
			$db_type=PVDatabase::getDatabaseType();
				
			$WHERE_CLAUSE='';
			$CATEGORY_JOIN='';
			
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
		
		if(!empty($category_id)){
					
				$category_id=trim($category_id);
				
				if($first==0 && ($category_id[0]!='+' && $category_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($category_id[0]=='+' || $category_id[0]==',') && $first==1 ){
					$category_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($category_id, 'category_id');
				
				$first=0;
		}//end not empty app_id
		
		
		
		if(!empty($category_name)){
					
				$category_name=trim($category_name);
				
				if($first==0 && ($category_name[0]!='+' && $category_name[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($category_name[0]=='+' || $category_name[0]==',') && $first==1 ){
					$category_name[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($category_name, 'category_name');
				
				$first=0;
		}//end not empty app_id
		
		
		if(!empty($category_unique_name)){
					
				$category_unique_name=trim($category_unique_name);
				
				if($first==0 && ($category_unique_name[0]!='+' && $category_unique_name[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($category_unique_name[0]=='+' || $category_unique_name[0]==',') && $first==1 ){
					$category_unique_name[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($category_unique_name, 'category_unique_name');
				
				$first=0;
		}//end not empty app_id
		
		
		if(!empty($parent_category)){
					
				$parent_category=trim($parent_category);
				
				if($first==0 && ($parent_category[0]!='+' && $parent_category[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($parent_category[0]=='+' || $parent_category[0]==',') && $first==1 ){
					$parent_category[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($parent_category, 'parent_category');
				
				$first=0;
		}//end not empty app_id
		
		
		if(!empty($category_alias)){
					
				$category_alias=trim($category_alias);
				
				if($first==0 && ($category_alias[0]!='+' && $category_alias[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($category_alias[0]=='+' || $category_alias[0]==',') && $first==1 ){
					$category_alias[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($category_alias, 'category_alias');
				
				$first=0;
		}//end not empty app_id
		
		
		if(!empty($category_order)){
					
				$category_order=trim($category_order);
				
				if($first==0 && ($category_order[0]!='+' && $category_order[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($category_order[0]=='+' || $category_order[0]==',') && $first==1 ){
					$category_order[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($category_order, 'category_order');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($category_description)){
					
				$category_description=trim($category_description);
				
				if($first==0 && ($category_description[0]!='+' && $category_description[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($category_description[0]=='+' || $category_description[0]==',') && $first==1 ){
					$category_description[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($category_description, 'category_description');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($category_owner)){
					
				$category_owner=trim($category_owner);
				
				if($first==0 && ($category_owner[0]!='+' && $category_owner[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($category_owner[0]=='+' || $category_owner[0]==',') && $first==1 ){
					$category_owner[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($category_owner, 'category_owner');
				
				$first=0;
		}//end not empty app_id
		
		if(!empty($category_type)){
					
				$category_type=trim($category_type);
				
				if($first==0 && ($category_type[0]!='+' && $category_type[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($category_type[0]=='+' || $category_type[0]==',') && $first==1 ){
					$category_type[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($category_type, 'category_type');
				
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
		
		
		if(!empty($join_users)){
			$CATEGORY_JOIN.=' JOIN '.PVDatabase::getLoginTableName().' ON $table_name.category_owner='.PVDatabase::getLoginTableName().'.user_id ';
		}
		
		if($join_content){
			$CATEGORY_JOIN.=' JOIN '.PVDatabase::getContentCategoryRelationsTableName().' ON '.PVDatabase::getContentCategoryRelationsTableName().'.category_id=$table_name.category_id 
			JOIN '.PVDatabase::getContentTableName().' ON '.PVDatabase::getContentCategoryRelationsTableName().'.content_id='.PVDatabase::getContentTableName().'.content_id ';
		}
		
		if(!empty($custom_join)){
			$CATEGORY_JOIN.=" $custom_join ";
		}
		
		if(!empty($distinct)){
			$prefix_args.=" DISTINCT $distinct, ";
		}
		
		if(!empty($limit) && $db_type=='mssql' && !$paged){
			$prefix_args.=" TOP $limit ";
		}
		
		if($paged){
			$page_results=PVDatabase::getPagininationOffset($table_name, $CATEGORY_JOIN , $WHERE_CLAUSE, $current_page, $results_per_page, $order_by_clause);
			
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
		
    	$query="$prequery SELECT $prefix_args $custom_select FROM $table_name $CATEGORY_JOIN $WHERE_CLAUSE";
    	
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
	
	}//end getCategoryList
	
	public static function getContent($content_id){
	
		if(!empty($content_id)){
			
			$content_id=PVDatabase::makeSafe($content_id);
	    	$query="SELECT * FROM ".PVDatabase::getContentTableName()." WHERE content_id='$content_id' ";
	    	$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			$row=PVDatabase::formatData($row);
			
			return $row;
		}//end if content not empty
		
	}//end getContent
	
	public static function getTextContent($content_id){
		
		if(!empty($content_id)){
	    	
	    	$content_id=PVDatabase::makeSafe($content_id);
	    	$query="SELECT * FROM ".PVDatabase::getContentTableName()." JOIN ".PVDatabase::getTextContentTableName()." ON ".PVDatabase::getContentTableName().".content_id=".PVDatabase::getTextContentTableName().".text_id WHERE content_id='$content_id' ";
	    	
			$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			$row=PVDatabase::formatData($row);
			
			return $row;
		}//end if content not empty
	}//end getContent
	
	public static function getImageContent($content_id){
		
		if(!empty($content_id)){
	    	
			$content_id=PVDatabase::makeSafe($content_id);
	    	$query="SELECT * FROM ".PVDatabase::getContentTableName()." JOIN ".PVDatabase::getImageContentTableName()." ON ".PVDatabase::getContentTableName().".content_id=".PVDatabase::getImageContentTableName().".image_id WHERE content_id='$content_id' ";
	    	$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			$row=PVDatabase::formatData($row);
			
			return $row;
		}//end if content not empty	
	}//end getContent
	
	
	
	public static function getVideoContent($content_id){
		
		if(!empty($content_id)){
	    	
	    	$content_id=PVDatabase::makeSafe($content_id);
	    	$query="SELECT * FROM ".PVDatabase::getContentTableName()." JOIN ".PVDatabase::getVideoContentTableName()." ON ".PVDatabase::getContentTableName().".content_id=".PVDatabase::getVideoContentTableName().".video_id WHERE content_id='$content_id' ";
	    	$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			$row=PVDatabase::formatData($row);
			
			return $row;
		}//end if content not empty	
	}//end getContent
	
	public static function getEventContent($content_id){
	
		if(!empty($content_id)){
		
	    	$content_id=PVDatabase::makeSafe($content_id);
	    	$query="SELECT * FROM ".PVDatabase::getContentTableName()." JOIN ".PVDatabase::getEventContentTableName()." ON ".PVDatabase::getContentTableName().".content_id=".PVDatabase::getEventContentTableName().".event_id WHERE content_id='$content_id' ";
	    	$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			$row=PVDatabase::formatData($row);
			
			return $row;
		}
	}//end getContent
	
	public static function getAudioContent($content_id){
		
		if(!empty($content_id)){
	    	
	    	$content_id=PVDatabase::makeSafe($content_id);
	    	$query="SELECT * FROM ".PVDatabase::getContentTableName()." JOIN ".PVDatabase::getAudioContentTableName()." ON ".PVDatabase::getContentTableName().".content_id=".PVDatabase::getAudioContentTableName().".audio_id WHERE content_id='$content_id' ";
	    	$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			$row=PVDatabase::formatData($row);
			
			return $row;
		}//end if content not empty	
	}//end getContent
	
	public static function getFileContent($content_id){

		if(!empty($content_id)){
	    	
	    	$content_id=PVDatabase::makeSafe($content_id);
	    	$query="SELECT *  FROM ".PVDatabase::getContentTableName()." JOIN ".PVDatabase::getFileContentTableName()." ON ".PVDatabase::getContentTableName().".content_id=".PVDatabase::getFileContentTableName().".file_id WHERE content_id='$content_id' ";
	    	$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			$row=PVDatabase::formatData($row);
			
			return $row;
		}//end if content not empty	
	}//end getContent
	
	public static function getUniversalContent($content_id){

		if(!empty($content_id)){
	    	
	    	$content_id=PVDatabase::makeSafe($content_id);
	    	
	    	$query='SELECT *  FROM '.PVDatabase::getContentTableName().' JOIN '.PVDatabase::getAudioContentTableName().' ON '.PVDatabase::getAudioContentTableName().'.audio_id='.PVDatabase::getContentTableName().'.content_id 
		JOIN '.PVDatabase::getEventContentTableName().' ON '.PVDatabase::getEventContentTableName().'.event_id='.PVDatabase::getContentTableName().'.content_id 
		JOIN '.PVDatabase::getImageContentTableName().' ON '.PVDatabase::getImageContentTableName().'.image_id='.PVDatabase::getContentTableName().'.content_id 
		JOIN '.PVDatabase::getProductContentTableName().' ON '.PVDatabase::getProductContentTableName().'.product_id='.PVDatabase::getContentTableName().'.content_id
		JOIN '.PVDatabase::getFileContentTableName().' ON '.PVDatabase::getFileContentTableName().'.file_id='.PVDatabase::getContentTableName().'.content_id
		JOIN '.PVDatabase::getVideoContentTableName().' ON '.PVDatabase::getVideoContentTableName().'.video_id='.PVDatabase::getContentTableName().'.content_id
		JOIN '.PVDatabase::getTextContentTableName().' ON '.PVDatabase::getTextContentTableName().'.text_id='.PVDatabase::getContentTableName().'.content_id 
		WHERE content_id=\''.$content_id.'\' ';
	    	
	    	$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			$row=PVDatabase::formatData($row);
			
			return $row;
		}//end if content not empty	
	}//end getContent
	
	
	public static function getProductContent($content_id){

		if(!empty($content_id)){
	    	
	    	$content_id=PVDatabase::makeSafe($content_id);
	    	$query="SELECT *  FROM ".PVDatabase::getContentTableName()." JOIN ".PVDatabase::getProductContentTableName()." ON ".PVDatabase::getContentTableName().".content_id=".PVDatabase::getProductContentTableName().".product_id WHERE content_id='$content_id' ";
	    	$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			$row=PVDatabase::formatData($row);
			
			return $row;
		}//end if content not empty	
	}//end getContent
	
	
	public static function getCategory($category_id){
	
		if(!empty($category_id)){
		
			$category_id=PVDatabase::makeSafe($category_id);
			$query="SELECT * FROM ".PVDatabase::getContentCategoriesTableName()." WHERE category_id='$category_id' ";
			$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			
			return $row;
		}
	
	}//end getCategory
	
	public static function getContentCategories($content_id){
		
		if(!empty($content_id)){
			
			$content_id=PVDatabase::makeSafe($content_id);
			$content_array=array();
			
			$query="SELECT * FROM ".PVDatabase::getContentCategoryRelationsTableName()." JOIN ".PVDatabase::getContentCategoriesTableName()." ON  ".PVDatabase::getContentCategoryRelationsTableName().".category_id=".PVDatabase::getContentCategoriesTableName().".category_id WHERE content_id='$content_id' ";
			
			$result = PVDatabase::query($query);
    	
	    	while ($row = PVDatabase::fetchArray($result)){
	    		array_push($content_array,$row );
	    	}//end while
	    	
	    	return $content_array;
	
		}
		
	}//end getContentCategory


	
	public static function updateContent($args=array()) {
		$defaults=self::getContentDefaults();
		$args += $defaults;
		
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		if(!empty($content_id) ){
			if(empty($content_category) && !is_array($content_category)){
				$content_category=0;
			}
		}
		
		if(!empty($app_id) && !PVValidator::isID($app_id)){
			$app_id=PVApplication::getApplicationID($app_id);
		}
		
		$is_searchable=ceil($is_searchable);
		$allow_comments=ceil($allow_comments);
		$allow_rating=ceil($allow_rating);
		$content_active=ceil($content_active);
		$content_promoted=ceil($content_promoted);
		$translate_content=ceil($translate_content);
		$content_approved=ceil($content_approved);
		$content_order=ceil($content_order);
		
		if(!empty($content_id) ){
	    	$query="UPDATE ".PVDatabase::getContentTableName()." SET  app_id='$app_id', parent_content='$parent_content', owner_id='$owner_id', content_title='$content_title' , content_alias='$content_alias',  content_description='$content_description' , content_meta_tags='$content_meta_tags' , content_meta_description='$content_meta_description', content_thumbnail='$content_thumbnail' , date_created='$date_created' , date_modified='$date_modified' , date_active='$date_active' , date_inactive='$date_inactive', is_searchable='$is_searchable', allow_comments='$allow_comments' , allow_rating='$allow_rating' , content_active='$content_active' , content_promoted='$content_promoted' , content_permissions='$content_permissions' , content_type='$content_type' , content_language='$content_language', translate_content='$translate_content' , content_approved='$content_approved' , content_parameters='$content_parameters', content_order='$content_order', sym_link='$sym_link' WHERE content_id='$content_id' ";
	    	PVDatabase::query($query);
	    	
	    	$query="DELETE FROM ".PVDatabase::getContentCategoryRelationsTableName()." WHERE content_id=$content_id";
	    	PVDatabase::query($query);
	    	
			if(!empty($content_taxonomy)){
				self::updateContentTaxonomy($content_id, $content_taxonomy);
			}
			if(is_array($content_category)){
				foreach($content_category as $category_value){
				
					$query="INSERT INTO ".PVDatabase::getContentCategoryRelationsTableName()."(category_id, content_id) VALUES('$category_value',' $content_id')";
					PVDatabase::query($query);
				
				}//end foreach	
			}
			else{
			
				$query="INSERT INTO ".PVDatabase::getContentCategoryRelationsTableName()."(category_id, content_id) VALUES('$content_category',' $content_id')";
				PVDatabase::query($query);
			}
	    	
	    	$uid= PVUsers::getUserID();
	    	$ip = $_SERVER['REMOTE_ADDR'];
	    	$query="INSERT INTO ".PVDatabase::getContentModifiersTableName()."(content_id, user_id, user_ip) VALUES('$content_id', '$uid', '$ip' )";
	    	PVDatabase::query($query);
		}
	}//end updateContent
	
	public static function updateTextContent($args=array()){
		self::updateContent($args);
		
		$defaults=self::getTextContentDefaults();
		$args += $defaults;
		
		$args=PVDatabase::makeSafe($args);
		extract($args);
    	
    	if(!empty($content_id)){
    		$query="UPDATE ".PVDatabase::getTextContentTableName()." SET text_content='$text_content', text_page_group='$text_page_group', text_page_number='$text_page_number', text_src='$text_src' WHERE text_id='$content_id' ";
    		PVDatabase::query($query);
    	}
    	
	}//end
	
	
	public static function updateImageContent($args=array()){
    	self::updateContent($args);
    	
    	$defaults=self::getImageContentDefaults();
		$args += $defaults;
		
		$args=PVDatabase::makeSafe($args);
		extract($args); 	
    	
    	$sql=" image_url='$image_url '";
    	
    	if(!empty($thumb_url)){
    		$sql.=" , thumb_url='$thumb_url'";
    	}
    	if(!empty($image_type)){
    		$sql.=" , image_type='$image_type'";
    	}
    	
    	if($image_size!=0){
    		$sql.=" , image_size='$image_size'";
    	}
    	
		if($image_width!=0){
    		$sql.=" , image_width='$image_width'";
    	}
    	
		if($image_height!=0){
    		$sql.=" , image_height='$image_height'";
    	}
    	
		if($thumb_width!=0){
    		$sql.=" , thumb_width='$thumb_width'";
    	}
    	
		if($thumb_height!=0){
    		$sql.=" , thumb_height='$thumb_height'";
    	}
		
		$sql.=" , image_src='$image_src'";
    	
    	$query="UPDATE ".PVDatabase::getImageContentTableName()." SET $sql WHERE image_id='$content_id'";
		
    	PVDatabase::query($query);
	
	}//end updateImageContent
	
	
	public static function updateImageContentWithFile($args=array()){
    	self::updateContent($args);
		
		$defaults=self::getImageContentDefaults();
		$args += $defaults;
		
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		if(!empty($content_id)){
			$image=new PVImageRenderer();
			$return=$image->updateImageFromContent($content_id, $content_type, $file_name, $tmp_name, $file_size, $file_type, $image_width , $image_height , $thumbnailwidth, $thumbnailheight, $image_src );
		}
		
		return $content_id;
	}//end createTextField
	
	
	
	public static function updateEventContent($args=array()){
		self::updateContent($args);
		
		$defaults=self::getEventContentDefaults();
		$args += $defaults;
		
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
    	if(!empty($content_id)){
    		$query="UPDATE ".PVDatabase::getEventContentTableName()." SET event_location='$event_location', event_start_date='$event_start_date', event_end_date='$event_end_date', event_country='$event_country', event_address='$event_address', event_city='$event_city', event_state='$event_state', event_zip='$event_zip', event_map='$event_map', event_src='$event_src', event_contact='$event_contact', undefined_endtime='$undefined_endtime' WHERE event_id='$content_id' ";
    		PVDatabase::query($query);
    	}
    	
	}//end
	
	
	public static function updateVideoContent($args=array()){
		self::updateContent($args);
		
		$defaults=self::getVideoContentDefaults();
		$args += $defaults;
		
		$args=PVDatabase::makeSafe($args);
		extract($args);
    	
    	if(!empty($content_id)){
    		$query="UPDATE ".PVDatabase::getVideoContentTableName()." SET video_type='$video_type', video_length='$video_length', video_allow_embedding='$video_allow_embedding', flv_file='$flv_file', mp4_file='$mp4_file', wmv_file='$wmv_file', mpeg_file='$mpeg_file', rm_file='$rm_file' , avi_file='$avi_file', mov_file='$mov_file', asf_file='$asf_file', enable_hq='$enable_hq', auto_hq='$auto_hq', video_src='$video_src', video_embed='$video_embed'  WHERE video_id='$content_id' ";
    		PVDatabase::query($query);
    	}
	}//end
	
	
	
	public static function updateAudioContent($args=array()){
		self::updateContent($args);
		
		$defaults=self::getAudioContentDefaults();
		$args += $defaults;
		
		$args=PVDatabase::makeSafe($args);
		extract($args);

    	if(!empty($content_id)){
    		$query="UPDATE ".PVDatabase::getAudioContentTableName()." SET audio_length='$audio_length', mid_file='$mid_file', aif_file='$aif_file', mp3_file='$mp3_file', ra_file='$ra_file', oga_file='$oga_file', sample_length='$sample_length', audio_type='$audio_type', audio_src='$audio_src'   WHERE audio_id='$content_id' ";
			PVDatabase::query($query);
    	}
    	
    	return $content_id;
	}//end
	
	public static function updateFileContent($args=array()){
		self::updateContent($args);
		
		$defaults=self::getFileContentDefaults();
		$args += $defaults;
		
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		$file_size=ceil($file_size);
		$file_downloadable=ceil($file_downloadable);
		$file_max_downloads=ceil($file_max_downloads);
		
    	if(!empty($content_id)){
    		$query="UPDATE ".PVDatabase::getFileContentTableName()." SET file_type='$file_type', file_size='$file_size', file_location='$file_location', file_name='$file_name', file_src='$file_src', file_downloadable='$file_downloadable', file_max_downloads='$file_max_downloads' , file_version='$file_version', file_license='$file_license'  WHERE file_id='$content_id' ";
    		PVDatabase::query($query);
    	}
    	
    	return $content_id;
	}//end
	
	
	public static function updateFileContentWithFile($args=array()){
		self::updateContent($args);
		
		$defaults=self::getFileContentDefaults();
		$args += $defaults;
		
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		$file_size=ceil($file_size);
		$file_downloadable=ceil($file_downloadable);
		$file_max_downloads=ceil($file_max_downloads);
    	
    	if(!empty($content_id)){
    		$query="UPDATE ".PVDatabase::getFileContentTableName()." SET file_type='$file_type', file_size='$file_size', file_location='$file_location', file_name='$file_name', file_src='$file_src', file_downloadable='$file_downloadable', file_max_downloads='$file_max_downloads' , file_version='$file_version', file_license='$file_license'  WHERE file_id='$content_id' ";
    		PVDatabase::query($query);
    	}
		
		if(!empty($tmp_name)){
			PVFileManager::uploadFileFromContent($content_id,  $file_name, $tmp_name, $file_size, $file_type);
		}
    	
    	return $content_id;
	}//end
	
	
	public static function updateProductContent($args=array()){
		self::updateContent($args);
		
		$defaults=self::getProductContentDefaults();
		$args += $defaults;
		
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		if(!empty($content_id)){
    		$query="UPDATE ".PVDatabase::getProductContentTableName()." SET product_sku='$product_sku' ,product_idsku='$product_idsku' , product_vendor_id='$product_vendor_id' ,product_quantity='$product_quantity' , product_price='$product_price' ,product_discount_price='$product_discount_price' ,product_size='$product_size' ,product_color='$product_color' ,product_weight='$product_weight' , product_height='$product_height' , product_length='$product_length' , product_currency='$product_currency' , product_in_stock='$product_in_stock' , product_type='$product_type' , product_tax_id='$product_tax_id' , product_attribute='$product_attribute' , product_version='$product_version'  WHERE product_id='$content_id' ";
    		PVDatabase::query($query);
    	}
    	
    	return $content_id;
	}//end
	
	public static function updateCategory($args=array() ){
		$defaults=self::getCategoryDefaults();
		$args += $defaults;
		
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		$query="UPDATE ".PVDatabase::getContentCategoriesTableName()." SET category_name='$category_name' , category_unique_name='$category_unique_name' , parent_category='$parent_category' ,  app_id='$app_id' , category_order='$category_order' , category_description='$category_description', category_alias='$category_alias', category_type='$category_type', category_owner='$category_owner'  WHERE category_id='$category_id' ";
		
		return PVDatabase::query($query);
	}//end updateCategoryArry
	
	
	
	/**
	 * Delete Content Section
	 */
	
	public static function deleteContent($content_id, $recursive=FALSE, $adjacent_content=''){
		if(!empty($content_id)){
			$content_id=ceil($content_id);
			self::deleteAdjacentContent($content_id, $adjacent_content);
	    	
	    	$query="DELETE FROM ".PVDatabase::getContentFieldRelationsTableName()." WHERE content_id='$content_id' ";
	    	PVDatabase::query($query);
	    	
	    	$query="DELETE FROM ".PVDatabase::getContentTaxonomyTableName()." WHERE content_id='$content_id' ";
	    	PVDatabase::query($query);
	    	
	    	$query="DELETE FROM ".PVDatabase::getContentTableName()." WHERE content_id='$content_id' ";
	    	PVDatabase::query($query);
	    	
	    	$query="DELETE FROM ".PVDatabase::getContentCategoryRelationsTableName()." WHERE content_id='$content_id'";
	    	PVDatabase::query($query);
	    	
	    	$query="DELETE FROM ".PVDatabase::getContentCommentsTableName()." WHERE content_id='$content_id'";
	    	PVDatabase::query($query);
			
			$query="DELETE FROM ".PVDatabase::getContentRelationsTableName()." WHERE content_id='$content_id'";
	    	PVDatabase::query($query);
			
			$query="DELETE FROM ".PVDatabase::getContentMultiAuthorTableName()." WHERE content_id='$content_id'";
	    	PVDatabase::query($query);
			
			self::deleteTextContent($content_id, $recursive);
			self::deleteImageContent($content_id, $recursive);
			self::deleteVideoContent($content_id, $recursive);
			self::deleteEventContent($content_id, $recursive);
			self::deleteAudioContent($content_id, $recursive);
			self::deleteFileContent($content_id, $recursive);
			self::deleteProductContent($content_id, $recursive);
			
			if($recursive==TRUE){
				$subcontentlist=self::getContentList('', '', '', '', '',  $content_id, '');
				
				foreach($subcontentlist as $value){
					self::deleteContent($value['content_id'], $recursive);
				}//end foreach
			}//end if recursive
		
		}
		
	}//end getContent
	
	public static function deleteTextContent($content_id, $recursive=FALSE){
		
		if(!empty($content_id)){
	    	
	    	$query="DELETE FROM ".PVDatabase::getTextContentTableName()." WHERE text_id='$content_id' ";
	    	PVDatabase::query($query);
			
		}
		
	}//end getContent
	
	public static function deleteImageContent($content_id, $recursive=FALSE){
		
		if(!empty($content_id)){
	    	
	    	$query="SELECT image_url, thumb_url FROM ".PVDatabase::getImageContentTableName()." WHERE image_id='$content_id' ";
	    	$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			
			if(!empty($row['image_url']) && file_exists ( PV_ROOT.PV_IMAGE.$row['image_url'] )){
				unlink(PV_ROOT.PV_IMAGE.$row['image_url']);
			}
			
			
			if(!empty($row['thumb_url']) && file_exists ( PV_ROOT.PV_IMAGE.$row['thumb_url'] )){
				unlink(PV_ROOT.PV_IMAGE.$row['thumb_url']);
			}
	    	
	    	$query="DELETE FROM ".PVDatabase::getImageContentTableName()." WHERE image_id='$content_id' ";
	    	PVDatabase::query($query);
	    	
		}
		
	}//end getContent
	
	
	
	public static function deleteVideoContent($content_id, $recursive=FALSE){
		
		if(!empty($content_id)){
			
			$query="SELECT * FROM ".PVDatabase::getVideoContentTableName()." WHERE video_id='$content_id'";
			
			$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			
			if(!empty($row['flv_file']) && file_exists ( ROOT.PV_VIDEO.$row['flv_file'] )){
				unlink(ROOT.PV_VIDEO.$row['flv_file']);
			}
			
			if(!empty($row['mp4_file']) && file_exists ( ROOT.PV_VIDEO.$row['mp4_file'] )){
				unlink(ROOT.PV_VIDEO.$row['mp4_file']);
			}
			
			if(!empty($row['wmv_file']) && file_exists ( ROOT.PV_VIDEO.$row['wmv_file'] )){
				unlink(ROOT.PV_VIDEO.$row['wmv_file']);
			}
			
			if(!empty($row['mpeg_file']) && file_exists ( ROOT.PV_VIDEO.$row['mpeg_file'] )){
				unlink(ROOT.PV_VIDEO.$row['mpeg_file']);
			}
			
			if(!empty($row['rm_file']) && file_exists ( ROOT.PV_VIDEO.$row['rm_file'] )){
				unlink(ROOT.PV_VIDEO.$row['rm_file']);
			}
			
			if(!empty($row['avi_file']) && file_exists ( ROOT.PV_VIDEO.$row['avi_file'] )){
				unlink(ROOT.PV_VIDEO.$row['avi_file']);
			}
			
			if(!empty($row['mov_file']) && file_exists ( ROOT.PV_VIDEO.$row['mov_file'] )){
				unlink(ROOT.PV_VIDEO.$row['mov_file']);
			}
			
			if(!empty($row['asf_file']) && file_exists ( ROOT.PV_VIDEO.$row['asf_file'] )){
				unlink(ROOT.PV_VIDEO.$row['asf_file']);
			}
			
			if(!empty($row['ogv_file']) && file_exists ( PV_ROOT.PV_AUDIO.$row['ogv_file'] )){
				unlink(PV_ROOT.PV_AUDIO.$row['ogv_file']);
			}
			
			if(!empty($row['webm_file']) && file_exists ( PV_ROOT.PV_AUDIO.$row['webm_file'] )){
				unlink(PV_ROOT.PV_AUDIO.$row['webm_file']);
			}
			
	    	
	    	$query="DELETE FROM ".PVDatabase::getVideoContentTableName()." WHERE video_id='$content_id' ";
			
			PVDatabase::query($query);
	    	
			//self::deleteContent($content_id, FALSE, 'video_content');
		}
	}//end getContent
	
	public static function deleteEventContent($content_id, $recursive=FALSE){
		
		if(!empty($content_id)){
	    	
	    	$query="DELETE FROM ".PVDatabase::getEventContentTableName()." WHERE event_id='$content_id' ";
	    	PVDatabase::query($query);
			
		}
	}//end getContent
	
	public static function deleteAudioContent($content_id, $recursive=FALSE){
		if(!empty($content_id)){
	    	
			$query="SELECT * FROM ".PVDatabase::getAudioContentTableName()." WHERE audio_id='$content_id'";
			$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
	    	
			if(!empty($row['mid_file']) && file_exists ( PV_ROOT.PV_AUDIO.$row['mid_file'] )){
				unlink(PV_ROOT.PV_AUDIO.$row['mid_file']);
			}
			
			if(!empty($row['wav_file']) && file_exists ( PV_ROOT.PV_AUDIO.$row['wav_file'] )){
				unlink(PV_ROOT.PV_AUDIO.$row['wav_file']);
			}
			
			if(!empty($row['aif_file']) && file_exists ( PV_ROOT.PV_AUDIO.$row['aif_file'] )){
				unlink(PV_ROOT.PV_AUDIO.$row['aif_file']);
			}
			
			if(!empty($row['mp3_file']) && file_exists ( PV_ROOT.PV_AUDIO.$row['mp3_file'] )){
				unlink(PV_ROOT.PV_AUDIO.$row['mp3_file']);
			}
			
			if(!empty($row['ra_file']) && file_exists ( PV_ROOT.PV_AUDIO.$row['ra_file'] )){
				unlink(PV_ROOT.PV_AUDIO.$row['ra_file']);
			}
			
	    	$query="DELETE FROM ".PVDatabase::getAudioContentTableName()." WHERE audio_id='$content_id' ";
	    	PVDatabase::query($query);
			
		}
		
	}//end getContent
	
	public static function deleteFileContent($content_id, $recursive=FALSE){
		if(!empty($content_id)){
	    	
	    	$query="SELECT file_location, file_src FROM ".PVDatabase::getFileContentTableName()." WHERE file_id='$content_id'";
	    	$result = PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
	    	
			if(!empty($row['file_location']) && file_exists ( PV_ROOT.PV_FILE.$row['file_location'] )){
				unlink(PV_ROOT.PV_FILE.$row['file_location']);
			}
			
			if(!empty($row['file_src']) && file_exists ( PV_ROOT.PV_FILE.$row['file_src'] )){
				unlink(PV_ROOT.PV_FILE.$row['file_src']);
			}
			
	    	$query="DELETE FROM ".PVDatabase::getFileContentTableName()." WHERE file_id='$content_id' ";
	    	PVDatabase::query($query);
			//self::deleteContent($content_id, FALSE, 'file_content');
		}
	}//end getContent
	
	public static function deleteProductContent($content_id, $recursive=FALSE){
		if(!empty($content_id)){
			
	    	$query="DELETE FROM ".PVDatabase::getProductContentTableName()." WHERE product_id='$content_id' ";
	    	PVDatabase::query($query);
			//self::deleteContent($content_id, FALSE, 'file_content');
		}
	}//end getContent
	
	public static function deleteCategory($category_id, $recursive=FALSE){
		if(!empty($category_id)){
			
			$query="DELETE FROM ".PVDatabase::getContentCategoriesTableName()." WHERE category_id='$category_id' ";
			PVDatabase::query($query);
			
			if($recursive==TRUE){
				$args=array('parent_category'=>$category_id);
				$subcontentlist=self::getCategoryList($args);
				
				foreach($subcontentlist as $value){
					self::deleteCategory($value['category_id'], $recursive);
				}//end foreach
			}//end if recursive
		}
	}//end deleteCategory
	
	private static function deleteAdjacentContent($content_id, $content_type=''){
		
		if(!empty($content_id)){
			
			
		}//end if!empty
		
	}//end deleteAdjacent
	
	
	public static function addContentTaxonomy($content_id, $taxonomy_term, $taxonomy_term_parent=''){
		$content_id=ceil($content_id);
		
		if(!empty($content_id) || !empty($taxonomy_term)){
			if(is_array($taxonomy_term)){
				foreach($taxonomy_term as $term_value){
					if(!empty($term_value)){
						$content_id=PVDatabase::makeSafe($content_id);
						$term_value=PVDatabase::makeSafe($term_value);
						$taxonomy_term_parent=PVDatabase::makeSafe($taxonomy_term_parent);
						
						$query="INSERT INTO ".PVDatabase::getContentTaxonomyTableName()."(content_id, taxonomy_term, taxonomy_term_parent) VALUES( '$content_id', '$term_value' , '$taxonomy_term_parent' )";
						PVDatabase::query($query);
					}
				}//end
			
			}//end if is array
			else{
				
				$query="INSERT INTO ".PVDatabase::getContentTaxonomyTableName()."(content_id, taxonomy_term, taxonomy_term_parent) VALUES( '$content_id', '$taxonomy_term' , '$taxonomy_term_parent' )";
				PVDatabase::query($query);
			
			}
		
		}
	}//end addTaxonomyToContent
	
	public static function updateContentTaxonomy($content_id, $taxonomy_term, $taxonomy_term_parent='', $trim_values=true){
		$content_id=ceil($content_id);
		
		if(!empty($content_id) || !empty($taxonomy_term)){
			
			$query="DELETE FROM ".PVDatabase::getContentTaxonomyTableName()." WHERE content_id='$content_id'";
			
			if(!empty($taxonomy_term_parent)){
				$query.=" AND taxonomy_term_parent='$taxonomy_term_parent'";
			}
			
			PVDatabase::query($query);
			
			if(is_array($taxonomy_term)){
				foreach($taxonomy_term as $term_value){
					if(!empty($term_value)){
						$content_id=PVDatabase::makeSafe($content_id);
						$term_value=PVDatabase::makeSafe($term_value);
						$taxonomy_term_parent=PVDatabase::makeSafe($taxonomy_term_parent);
						
						if($trim_values){
							$term_value=trim($term_value);
						}
						
						$query="INSERT INTO ".PVDatabase::getContentTaxonomyTableName()."(content_id, taxonomy_term, taxonomy_term_parent) VALUES( '$content_id', '$term_value' , '$taxonomy_term_parent' )";
						PVDatabase::query($query);
					}
				}//end
			
			}//end if is array
			else{
				$taxonomy_term=PVDatabase::makeSafe($taxonomy_term);
				$taxonomy_term_parent=PVDatabase::makeSafe($taxonomy_term_parent);
				
				$query="INSERT INTO ".PVDatabase::getContentTaxonomyTableName()."(content_id, taxonomy_term, taxonomy_term_parent) VALUES( '$content_id', '$taxonomy_term' , '$taxonomy_term_parent' )";
				PVDatabase::query($query);
			}
		
		}
	}//end addTaxonomyToContent
	
	function getContentTaxonomy($content_id,$taxonomy_term_parent='' ){
		$content_id=ceil($content_id);
		
		if(!empty($content_id)){
			
			$content_array=array();
			$query="SELECT taxonomy_term FROM ".PVDatabase::getContentTaxonomyTableName()." WHERE content_id='$content_id'";
			
			if(!empty($taxonomy_term_parent)){
				$query.=" AND taxonomy_term_parent='$taxonomy_term_parent'";
			}
			
			
			$result=PVDatabase::query($query);
			
			if(PVDatabase::resultRowCount($result) > 0){
				while ($row = PVDatabase::fetchArray($result)){
		    		$content_array[$row['taxonomy_term']]=$row['taxonomy_term'];
		    	}//end while
			}
	    	
	    
	    	return $content_array;
		}
	}//end getContentTaxonomy
	
	
	public static function clearContentTaxonomy($content_id,  $taxonomy_term_parent=''){
		$content_id=ceil($content_id);
		
		if(!empty($content_id)){
			
			$query="DELETE FROM ".PVDatabase::getContentTaxonomyTableName()." WHERE content_id='$content_id'";
			
			if(!empty($taxonomy_term_parent)){
				$query.=" AND taxonomy_term_parent='$taxonomy_term_parent'";
			}
			
			PVDatabase::query($query);
		}
		
	}//end addTaxonomyToContent
	
	public static function getContentIDByAlias($content_alias, $app_id='', $owner_id='', $content_type=''){
	
		if(!empty($content_alias)){
			
			
		$WHERE_CLAUSE="";
    	
		if(!empty($app_id) || !empty($owner_id) || !empty($content_type) || !empty($content_alias) ){
			$first=1;
			
			$WHERE_CLAUSE.=" WHERE ";
			
			if(!empty($app_id)){
				$app_id=PVDatabase::makeSafe($app_id);
				$WHERE_CLAUSE.=" app_id='$app_id' ";
				$first=0;
			}
			
			if(!empty($owner_id)){
				$owner_id=PVDatabase::makeSafe($owner_id);
				if($first==0){
					$WHERE_CLAUSE.=" AND ";
				}
				$WHERE_CLAUSE.=" owner_id='$owner_id' ";
				$first=0;
			}
			
			if(!empty($content_type)){
				$content_type=PVDatabase::makeSafe($content_type);
				if($first==0){
					$WHERE_CLAUSE.=" AND ";
				}
				$WHERE_CLAUSE.=" content_type='$content_type' ";
				$first=0;
			}
			
			if(!empty($content_alias)){
				$content_alias=PVDatabase::makeSafe($content_alias);
				if($first==0){
					$WHERE_CLAUSE.=" AND ";
				}
				$WHERE_CLAUSE.=" content_alias='$content_alias' ";
				$first=0;
			}
			
			
		}
			
			$query="SELECT content_id FROM ".PVDatabase::getContentTableName()." $WHERE_CLAUSE";
			$result=PVDatabase::query($query);
			
			$row = PVDatabase::fetchArray($result);
			
			return $row['content_id'];
			
		}//end if not empty
	
	}//end 
	
	public static function getCategoryIDByAlias($category_alias, $app_id='', $category_unique_name='', $parent_category=''){
		if(PVValidator::isInteger($category_alias)){
			return $category_alias;
		}
		if(!empty($category_alias)){
			
			
			$WHERE_CLAUSE="";
	    	
			if(!empty($app_id) || !empty($category_unique_name) || !empty($parent_category) || !empty($category_alias)){
				$first=1;
				
				$WHERE_CLAUSE.=" WHERE ";
				
				if(!empty($app_id)){
					$app_id=PVDatabase::makeSafe($app_id);

					$WHERE_CLAUSE.=" app_id='$app_id' ";
					$first=0;
				}
				
				if(!empty($category_unique_name)){
					$category_unique_name=PVDatabase::makeSafe($category_unique_name);
					if($first==0){
						$WHERE_CLAUSE.=" AND ";
					}
					$WHERE_CLAUSE.=" category_unique_name='$category_unique_name' ";
					$first=0;
				}
				
				if(!empty($parent_category)){
					$parent_category=PVDatabase::makeSafe($parent_category);
					if($first==0){
						$WHERE_CLAUSE.=" AND ";
					}
					$WHERE_CLAUSE.=" parent_category='$parent_category' ";
					$first=0;
				}
				
				if(!empty($category_alias)){
					$category_alias=PVDatabase::makeSafe($category_alias);
					if($first==0){
						$WHERE_CLAUSE.=" AND ";
					}
					$WHERE_CLAUSE.=" category_alias='$category_alias' ";
					$first=0;
				}
				
				
			}
			
			$query="SELECT category_id FROM ".PVDatabase::getContentCategoriesTableName()." $WHERE_CLAUSE";
			$result=PVDatabase::query($query);
			$row = PVDatabase::fetchArray($result);
			
			return $row['category_id'];
			
		
		}//end empty category_alias
	
	}//end getCategoryIDByAslias
	
	public static function createUniqueContentAlias($content_alias, $content_id='', $count=''){
		
		if(!empty($content_alias)){
			$content_alias=trim($content_alias);
			$illegal_characters=array('/', '\\','+', '=', '?', ':', '$', '#', '%', '*', '{', '}', '<', '>','\'', '&', '@', ',', '.');
			$content_alias=strtolower($content_alias);
			$content_alias=str_replace( ' ', '-', $content_alias);
			$content_alias=str_replace( $illegal_characters , '', $content_alias);
			$tmp_content_alias=$content_alias;
			
			if(!empty($count)){
				$tmp_content_alias.='-'.$count;
			}
			$query="SELECT content_alias FROM ".PVDatabase::getContentTableName()." WHERE content_alias='$tmp_content_alias' ";
			
			if(!empty($content_id) ){
				$query.="AND content_id!='$content_id' ";
			}
			
			$result=PVDatabase::query($query);
			
			if(PVDatabase::resultRowCount($result)<=0){
				return $tmp_content_alias;
			}
			else{
				if(empty($count)){
					$count=1;
				}
				else{
					$count++;
				}
				
				
				
				return self::createUniqueContentAlias($content_alias, $content_id, $count);
			}
			
		}
	}//end createUniqueContentAlias
	
	public static function createUniqueCategoryAlias($category_alias, $category_id='', $count=''){
		
		if(!empty($category_alias)){
			$category_aliass=trim($category_alias);
			$illegal_characters=array('/', '\\','+', '=', '?', ':', '$', '#', '%', '*', '{', '}', '<', '>','\'', '&', '@');
			$category_alias=strtolower($category_alias);
			$category_alias=str_replace( ' ', '-', $category_alias);
			$category_alias=str_replace( $illegal_characters , '', $category_alias);
			$tmp_content_alias=$category_alias;
			
			if(!empty($count)){
				$tmp_content_alias.='-'.$count;
			}
			$query="SELECT category_alias FROM ".PVDatabase::getContentCategoriesTableName()." WHERE category_alias='$tmp_content_alias' ";
			
			if(!empty($category_id) ){
				$query.="AND category_id!='$category_id' ";
			}
			
			$result=PVDatabase::query($query);
			
			if(PVDatabase::resultRowCount($result)<=0){
				return $tmp_content_alias;
			}
			else{
				if(empty($count)){
					$count=1;
				}
				else{
					$count++;
				}
				
				
				
				return self::createUniqueCategoryAlias($category_alias, $category_id, $count);
			}
			
		}
	}//end createUniqueContentAlias
	
	function getContentIDByContentAlias($content_alias, $app_id='', $content_type=''){
		
		if(!empty($content_alias)){
		
			$content_alias=PVDatabase::makeSafe($content_alias);
			
			$query="SELECT content_id FROM ".PVDatabase::getContentTableName()." WHERE content_alias='$content_alias' ";
			
			if(!empty($app_id)){
				$app_id=PVDatabase::makeSafe($app_id);
				$query.=" AND app_id='$app_id' ";
			}
			
			if(!empty($content_type)){
				$content_type=PVDatabase::makeSafe($content_type);
				$query.=" AND content_type='$content_type' ";
			}
			
			$result = PVDatabase::query($query);
    	
    		$row = PVDatabase::fetchArray($result);
    		
			return $row['content_id'];
		}//end if!empty
	}//getContentIDByContentAlias
	
	function getCategoryIDByContentAlias($category_alias, $app_id='', $category_type=''){
		
		if(!empty($content_alias)){
		
			$content_alias=PVDatabase::makeSafe($content_alias);
			
			$query="SELECT category_id FROM ".PVDatabase::getContentCategoriesTableName()." WHERE category_alias='$category_alias' ";
			
			if(!empty($app_id)){
				$app_id=PVDatabase::makeSafe($app_id);
				$query.=" AND app_id='$app_id' ";
			}
			
			if(!empty($content_type)){
				$content_type=PVDatabase::makeSafe($content_type);
				$query.=" AND category_type='$category_type' ";
			}
			
			$result = PVDatabase::query($query);
    	
    		$row = PVDatabase::fetchArray($result);
    		
			return $row['category_id'];
		}//end if!empty
	}//getContentIDByContentAlias
	
	private static function insertAdjacentTables($content_id, $table_name){
		if(!empty($content_id)){
			
			if($table_name!='pv_content_text'){
				$query="INSERT INTO ".PVDatabase::getTextContentTableName()."(text_id) VALUES('$content_id')";
				PVDatabase::query($query);
			}
			
			if($table_name!='pv_content_events'){
				$query="INSERT INTO ".PVDatabase::getEventContentTableName()."(event_id) VALUES('$content_id')";
				PVDatabase::query($query);
			}
			
			if($table_name!='pv_content_images'){
				$query="INSERT INTO ".PVDatabase::getImageContentTableName()."(image_id) VALUES('$content_id')";
				PVDatabase::query($query);
			}
			
			if($table_name!='pv_content_video'){
				$query="INSERT INTO ".PVDatabase::getVideoContentTableName()."(video_id) VALUES('$content_id')";
				PVDatabase::query($query);
			}
			
			if($table_name!='pv_content_files'){
				$query="INSERT INTO ".PVDatabase::getFileContentTableName()."(file_id) VALUES('$content_id')";
				PVDatabase::query($query);
			}
			
			if($table_name!='pv_content_audio'){

				$query="INSERT INTO ".PVDatabase::getAudioContentTableName()."(audio_id) VALUES('$content_id')";
				PVDatabase::query($query);
			}
			
			if($table_name!='pv_content_product'){
				$query="INSERT INTO ".PVDatabase::getProductContentTableName()."(product_id) VALUES('$content_id')";
				PVDatabase::query($query);
			}
		
		}
		
	}//end insert adjust table
	
	public static function addContentView($content_id, $user_id=''){
		
		if(!empty($content_id)){
			
			if(empty($user_id)){
				$user_id=PVUsers::getUserID();
			}
			
			$ip = $_SERVER['REMOTE_ADDR'];
			
			$query="INSERT INTO ".PVDatabase::getContentViewsTableName()."(content_id, user_id, user_ip) VALUES( '$content_id' , '$user_id', '$ip' )";
			$view_id=PVDatabase::return_last_insert_query($query, 'view_id', PVDatabase::getContentViewsTableName() );
			return $view_id;
		}
		
	}//end addContentView
	
	public static function addContentViewUnique($content_id, $user_id=''){
		
		if(!empty($content_id)){
			
			if(empty($user_id)){
				$user_id=PVUsers::getUserID();
			}
			
			$ip = $_SERVER['REMOTE_ADDR'];
			
			
			
			if($user_id!=0){
				$WHERE_CLAUSE.="user_id='$user_id'";	
			}
			else{
				$WHERE_CLAUSE=" user_ip='$ip' AND user_id='0'";
			}
			
			$query="SELECT content_id FROM ".PVDatabase::getContentViewsTableName()."  WHERE content_id='$content_id' AND $WHERE_CLAUSE";
			$result = PVDatabase::query($query);
			
			if(PVDatabase::resultRowCount($result) <= 0){
				$query="INSERT INTO ".PVDatabase::getContentViewsTableName()."(content_id, user_id, user_ip) VALUES( '$content_id' , '$user_id', '$ip' )";
				$view_id=PVDatabase::return_last_insert_query($query, 'view_id', PVDatabase::getContentViewsTableName());
			return $view_id;
			}
		}//end if not empty
		
	}//end addContentView
	
	public static function getContentViews($content_id){
		
		if(!empty($content_id)){
			
			
			$query="SELECT content_id FROM ".PVDatabase::getContentViewsTableName()." WHERE content_id='$content_id' ";
			$result = PVDatabase::query($query);
			
			return PVDatabase::resultRowCount($result);
			
			
		}
		
	}//get content views
	
	public static function getContentViewsList($args=''){
		
		$content_array=array();
		$table_name=PVDatabase::getContentViewsTableName();
		$db_type=PVDatabase::getDatabaseType();
		
		if(is_array($args)){
			$args=PVDatabase::makeSafe($args);
			extract($args, EXTR_SKIP);
		}
		
		
			$first=1;
				
			$WHERE_CLAUSE.='';
			
			if(!empty($view_id)){
					
				$view_id=trim($view_id);
				
				if($first==0 && ($view_id[0]!='+' && $view_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($view_id[0]=='+' || $view_id[0]==',') && $first==1 ){
					$view_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($view_id, PVDatabase::getContentViewsTableName().'.view_id');
				
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
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($content_id, PVDatabase::getContentViewsTableName().'.content_id');
				
				$first=0;
			}//end not empty app_id
			
			
			
			if(!empty($user_ip)){
					
				$user_ip=trim($user_ip);
				
				if($first==0 && ($user_ip[0]!='+' && $user_ip[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($user_ip[0]=='+' || $user_ip[0]==',') && $first==1 ){
					$user_ip[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($user_ip, PVDatabase::getContentViewsTableName().'.user_ip');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($user_id)){
					
				$user_id=trim($user_id);
				
				if($first==0 && ($user_id[0]!='+' && $user_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($user_id[0]=='+' || $user_id[0]==',') && $first==1 ){
					$user_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($user_id, PVDatabase::getContentViewsTableName().'.user_id');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($viewed_date)){
					
				$viewed_date=trim($viewed_date);
				
				if($first==0 && ($viewed_date[0]!='+' && $viewed_date[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($viewed_date[0]=='+' || $viewed_date[0]==',') && $first==1 ){
					$viewed_date[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($viewed_date, PVDatabase::getContentViewsTableName().'.viewed_date');
				
				$first=0;
			}//end not empty app_id
			
		
		$JOINS='';
		
		if(!empty($custom_join)){
			
			$JOINS.=" $custom_join ";
			
		}
		
		if($join_users == true){
			$JOINS.=" JOIN ".PVDatabase::getLoginTableName()." ON ".PVDatabase::getLoginTableName().".user_id=".PVDatabase::getContentViewsTableName().".user_id ";	
			$SELECTS.=' , pv_login.* ';
		}
		
		
		if($join_content == true){
			$JOINS.=" JOIN ".PVDatabase::getContentTableName()." ON ".PVDatabase::getContentTableName().".content_id=".PVDatabase::getContentViewsTableName().".content_id ";
			$SELECTS.=' , pv_content.* ';
		}
		
		if(!empty($custom_where)){
			$WHERE_CLAUSE.=" $custom_where ";
		}
		
		
		if(!empty($WHERE_CLAUSE)){
			$WHERE_CLAUSE=' WHERE '.$WHERE_CLAUSE;
		}
		
		if(!empty($distinct)){
			$prefix_args.=" DISTINCT $distinct, ";
		}
		
		if(!empty($limit) && $db_type=='mssql' && !$paged){
			$prefix_args.=" TOP $limit ";
		}
		
		if($paged){
			$page_results=PVDatabase::getPagininationOffset($table_name, $JOINS , $WHERE_CLAUSE, $current_page, $results_per_page, $order_by);
			
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
		
    	$query="$prequery SELECT $prefix_args $custom_select FROM $table_name $JOINS $WHERE_CLAUSE";
    	
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
		
	}//get content views
	
	public static function getContentViewsByUserID($content_id, $user_id=''){
		
		if(!empty($content_id)){
			
			if(empty($user_id)){
				$user_id=PVUsers::getUserID();
			}
			
			
			$query="SELECT content_id FROM ".PVDatabase::getContentViewsTableName()." WHERE content_id='$content_id' AND user_id='$user_id' ";
			$result = PVDatabase::query($query);
			
			return PVDatabase::resultRowCount($result);	
			
		}
		
	}//get content views
	
	public static function getContentViewsByIP($content_id, $ip){
		
		if(!empty($content_id)){
			
			$query="SELECT content_id FROM ".PVDatabase::getContentViewsTableName()." WHERE content_id='$content_id' AND user_ip='$ip' ";
			$result = PVDatabase::query($query);
			
			return PVDatabase::resultRowCount($result);	
			
		}
		
	}//get content views
	
	public static function addContentRating($content_id, $rating=0, $user_id=0, $rating_type=''){
		
		if(!empty($content_id)){
			
			if(empty($user_id)){
				$user_id=PVUsers::getUserID();
			}
			
			if(empty($rating)){
				$rating=0;
			}
			
			$user_ip=$_SERVER['REMOTE_ADDR'];
			
			
			$query="INSERT INTO ".PVDatabase::getContentRatingTableName()."(content_id , user_id, rating, rating_type, user_ip) VALUES('$content_id' , '$user_id', '$rating', '$rating_type', '$user_ip')";
			
			$rating_id=PVDatabase::return_last_insert_query($query, 'rating_id' , PVDatabase::getContentRatingTableName() );
		
			return $rating_id;	
			
		}//end !empty
		
	}//end addContentRating
	
	
	public static function addUniqueContentRating($content_id, $rating=0, $user_id=0, $rating_type=''){
		
		if(!empty($content_id)){
			
			if(empty($user_id)){
				$user_id=PVUsers::getUserID();
			}
			
			if(empty($rating)){
				$rating=0;
			}
			
			$user_ip=$_SERVER['REMOTE_ADDR'];
			
			if(!empty($rating_type)){
				$RATING_SQL=" AND rating_type='$rating_type' ";	
			}
			
			if(empty($user_id)){
				$query="SELECT rating_id FROM ".PVDatabase::getContentRatingTableName()." WHERE content_id='$content_id' AND user_ip='$user_ip' $RATING_SQL";	
			}
			else{
				$query="SELECT rating_id FROM ".PVDatabase::getContentRatingTableName()." WHERE content_id='$content_id' AND user_id='$user_id' $RATING_SQL";
			}
			
			$result=PVDatabase::query($query);
			
			if(resultRowCount($result)<=0 ){
			
			
				$query="INSERT INTO ".PVDatabase::getContentRatingTableName()."(content_id , user_id, rating, rating_type, user_ip) VALUES('$content_id' , '$user_id', '$rating', '$rating_type', '$user_ip')";
				
				$rating_id=PVDatabase::return_last_insert_query($query, 'rating_id' , PVDatabase::getContentRatingTableName());
			
			}
		
			return $rating_id;	
			
		}//end !empty
		
	}//end addContentRating
	
	public static function getContentRatingList($content_id=0, $user_id=0, $rating=0, $rating_type=''){
		
			
			if(is_array($content_id)){
				$contents=$content_id;
				$content_id='';
				
				extract($contents);
			}
		
		
			$first=1;

			$content_array=array();
			$table_name=PVDatabase::getContentRatingTableName();
			$db_type=PVDatabase::getDatabaseType();
				
			$WHERE_CLAUSE.='';
				
				
				
			if(!empty($rating_id)){
					
				$rating_id=trim($rating_id);
				
				if($first==0 && ($rating_id[0]!='+' && $rating_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($rating_id[0]=='+' || $rating_id[0]==',') && $first==1 ){
					$rating_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($rating_id, 'rating_id');
				
				$first=0;
			}//end not empty app_id
			
			
			if(!empty($content_id)){
					
				$rating_id=trim($rating_id);
				
				if($first==0 && ($content_id[0]!='+' && $content_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($content_id[0]=='+' || $content_id[0]==',') && $first==1 ){
					$content_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($content_id, 'content_id');
				
				$first=0;
			}//end not empty content_id
			
			
			
			if(!empty($user_id)){
					
				$user_id=trim($user_id);
				
				if($first==0 && ($user_id[0]!='+' && $user_id[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($user_id[0]=='+' || $user_id[0]==',') && $first==1 ){
					$user_id[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($user_id, 'user_id');
				
				$first=0;
			}//end not empty content_id
			
			
			
			if(!empty($rating_type)){
					
				$user_id=trim($user_id);
				
				if($first==0 && ($rating_type[0]!='+' && $rating_type[0]!=',' ) ){
						$WHERE_CLAUSE.=" AND ";
					}
					
				else if( ($rating_type[0]=='+' || $rating_type[0]==',') && $first==1 ){
					$rating_type[0]='';
				}
				
				$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($rating_type, 'rating_type');
				
				$first=0;
			}//end not empty content_id
			
		$JOINS='';
			
		if(!empty($custom_where)){
			$WHERE_CLAUSE.=" $custom_where ";
		}
		
		if(!empty($custom_join)){
			
			$JOINS.=" $custom_join ";
			
		}
			
			
		if(!empty($WHERE_CLAUSE)){
			$WHERE_CLAUSE=' WHERE '.$WHERE_CLAUSE;
		}
			
		if(!empty($distinct)){
			$prefix_args.=" DISTINCT $distinct, ";
		}
		
		if(!empty($limit) && $db_type=='mssql' && !$paged){
			$prefix_args.=" TOP $limit ";
		}
		
		if($paged){
			$page_results=PVDatabase::getPagininationOffset($table_name, $JOINS , $WHERE_CLAUSE, $current_page, $results_per_page, $order_by);
			
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
		
    	$query="$prequery SELECT $prefix_args $custom_select FROM $table_name $JOINS $WHERE_CLAUSE";
    	
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
			
		
		
	}//end getContentRatings
	
	public static function getContentRating($rating_id){
		
		if(!empty($rating_id)){
			
			$rating_id=ceil($rating_id);
			
			$query="SELECT * FROM ".PVDatabase::getContentRatingTableName()." WHERE rating_id='$rating_id'  ";
			
			$result=PVDatabase::query($query);
			
			$row = PVDatabase::fetchArray($result);
			
			$row=PVDatabase::formatData($row);
			
			return $row;
			
			
		}//end while
		
		
	}//end getContentRating
	
	
	public static function getContentRatingCount($content_id, $rating_type=''){
		
		if(!empty($content_id)){
			$content_array=array();
			
			$content_id=ceil($content_id);
			$rating_type=PVDatabase::makeSafe($rating_type);
			
			$query="SELECT rating_id, user_id, date_rated, date_rerated, rating, rating_type user_ip FROM ".PVDatabase::getContentRatingTableName()." WHERE content_id='$content_id'  ";
			
			if(!empty($rating_type)){
				$query.=" AND rating_type='$rating_type'";
			}
			
			$result=PVDatabase::query($query);
			
			return PVDatabase::resultRowCount($result);
			
			
		}//end getContentRatings
		
	}//end getContentRatingCount
	
	public static function getContentRatingAverage($content_id, $rating_type=''){
		
		
		if(!empty($content_id)){
			$content_array=array();
			
			$content_id=ceil($content_id);
			$rating_type=PVDatabase::makeSafe($rating_type);
			
			$query="SELECT ".PVDatabase::dbAverageFunction('rating')." AS rating_average FROM ".PVDatabase::getContentRatingTableName()." WHERE content_id='$content_id'  ";
			
			if(!empty($rating_type)){
				$query.=" AND rating_type='$rating_type'";
			}
			
			$result=PVDatabase::query($query);
			
			$row=PVDatabase::fetchArray($result);
			
			return $row['rating_average'];
			
			
		}//end getContentRatings
		
	}//end getContentRatingAverage
	
	public static function deleteContentRating($rating_id){
		
		if(!empty($rating_id)){
			
			$rating_id=ceil($rating_id);
			
			$query="DELETE FROM ".PVDatabase::getContentRatingTableName()." WHERE rating_id='$rating_id'";
			
			PVDatabase::query($query);
			
		}//end not empty rating_id
		
	}//end removeContentRating
	
	public static function deleteContentViews($content_id, $user_id=''){
		
		if(!empty($content_id)){
			
				
		}//end if not empty
		
	}//end deleteContentViews
	
	
	public static function addContentMultiAuthor($user_id, $content_id, $owner_status=''){
		
		$user_id=PVDatabase::makeSafe($user_id);
		$content_id=PVDatabase::makeSafe($content_id);
		
		if(!empty($user_id) && !empty($content_id) ){
			
			$owner_status=PVDatabase::makeSafe($owner_status);
			
			$query="SELECT * FROM ".PVDatabase::getContentMultiAuthorTableName()." WHERE author_id='$user_id' AND content_id='$content_id' AND author_status='$owner_status' ";
			
			$result=PVDatabase::query($query);
			
			if(PVDatabase::resultRowCount($result) <=0 ){
				$query="INSERT INTO ".PVDatabase::getContentMultiAuthorTableName()."(author_id, content_id, author_status) VALUES( '$user_id', '$content_id', '$owner_status' )";
				PVDatabase::query($query);
			}
			
		}
		
	}//end addContentMultiAuthor
	
	public static function isContentMultiAuthor($user_id, $content_id, $owner_status=''){
		
		if(!empty($user_id) && !empty($content_id) ){
			
			$user_id=PVDatabase::makeSafe($user_id);
			$content_id=PVDatabase::makeSafe($content_id);
			$owner_status=PVDatabase::makeSafe($owner_status);
				
			$query="SELECT * FROM ".PVDatabase::getContentMultiAuthorTableName()." WHERE author_id='$user_id' AND content_id='$content_id'";
			
			if(!empty($owner_status)){
				$query.=" AND author_status='$owner_status'";
			}
			$result=PVDatabase::query($query);
		
			if(PVDatabase::resultRowCount($result) > 0){
				return 1;	
			}
			else{
				return 0;	
			}
				
		}
		else{
			return 0;		
		}
		
	}//end
	
	
	public static function getContentMutliAuthorList($args=array()){
		
		if(is_array($args)){
			extract($args);
		}
		
		$first=1;
		
		$content_array=array();
		$table_name=PVDatabase::getContentMultiAuthorTableName();
		$db_type=PVDatabase::getDatabaseType();
				
		$WHERE_CLAUSE.='';
				
				
				
		if(!empty($author_id)){
					
			$author_id=trim($author_id);
				
			if($first==0 && ($author_id[0]!='+' && $author_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($author_id[0]=='+' || $author_id[0]==',') && $first==1 ){
				$author_id[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($author_id, $table_name.'.author_id');
				
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
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($content_id, $table_name.'.content_id');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($author_status)){
					
			$author_status=trim($author_status);
				
			if($first==0 && ($author_status[0]!='+' && $author_status[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($author_status[0]=='+' || $author_status[0]==',') && $first==1 ){
				$author_status[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($author_status, $table_name.'.author_status');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($owner_added_date)){
					
			$owner_added_date=trim($owner_added_date);
				
			if($first==0 && ($owner_added_date[0]!='+' && $owner_added_date[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($owner_added_date[0]=='+' || $owner_added_date[0]==',') && $first==1 ){
				$owner_added_date[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($owner_added_date, 'owner_added_date');
				
			$first=0;
		}//end not empty app_id
		
		
		$JOINS='';
			
		if(!empty($custom_where)){
			$WHERE_CLAUSE.=" $custom_where ";
		}
		
		if($join_content){
			$JOINS.=" JOIN ".PVDatabase::getContentTableName()." ON ".PVDatabase::getContentTableName().".content_id=".PVDatabase::getContentMultiAuthorTableName().".content_id ";
		}
		
		if($join_users){
			$JOINS.=" JOIN ".PVDatabase::getLoginTableName()." ON ".PVDatabase::getLoginTableName().".user_id=".PVDatabase::getContentMultiAuthorTableName().".author_id ";
		}
		
		if(!empty($custom_join)){
			
			$JOINS.=" $custom_join ";
			
		}
			
		if(!empty($WHERE_CLAUSE)){
			$WHERE_CLAUSE=' WHERE '.$WHERE_CLAUSE;
		}
			
		if(!empty($distinct)){
			$prefix_args.=" DISTINCT $distinct, ";
		}
		
		if(!empty($limit) && $db_type=='mssql' && !$paged){
			$prefix_args.=" TOP $limit ";
		}
		
		if($paged){
			$page_results=PVDatabase::getPagininationOffset($table_name, $JOINS , $WHERE_CLAUSE, $current_page, $results_per_page, $order_by);
			
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
    	$query="$prequery SELECT $prefix_args $custom_select FROM $table_name $JOINS $WHERE_CLAUSE";
		
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
		
	}//end
	
	
	public static function removeContentMultiAuthor($user_id, $content_id, $owner_status=''){
		
		$user_id=PVDatabase::makeSafe($user_id);
		$content_id=PVDatabase::makeSafe($content_id);
		$owner_status=PVDatabase::makeSafe($owner_status);
		
		if(!empty($user_id) && !empty($content_id) ){
			$query="DELETE FROM ".PVDatabase::getContentMultiAuthorTableName()." WHERE author_id='$user_id' AND content_id='$content_id' ";
			
			if(!empty($owner_status)){
				$query.="AND author_status='$owner_status' ";
			}
			
			PVDatabase::query($query);
		}
		
		
	}//end removeContentMultiAuthor
	
	
	public static function addContentRelationship($content_id, $related_content_id, $content_relationship_type=''){
		$content_id=ceil($content_id);
		$related_content_id=ceil($related_content_id);
		
		if(!empty($content_id) && !empty($related_content_id)){
			
			if(!self::checkContentRelationship($content_id, $related_content_id, $content_relationship_type)){
				
				$content_relationship_type=PVDatabase::makeSafe($content_relationship_type);
				
				$query="INSERT INTO ".PVDatabase::getContentRelationsTableName()."(content_id, related_content_id, content_relationship_type ) VALUES('$content_id', '$related_content_id', '$content_relationship_type')";
				PVDatabase::query($query);
				
			}
		}
	}//end addContentRelationship
	
	
	public static function getContentRelationshipList($args){
		
		if(is_array($args)){
			extract($args);
		}
		
		$first=1;
		
		$content_array=array();
		$table_name=PVDatabase::getContentRelationsTableName();
		$db_type=PVDatabase::getDatabaseType();
				
		$WHERE_CLAUSE.='';
				
				
				
		if(!empty($related_content_id)){
					
			$related_content_id=trim($related_content_id);
				
			if($first==0 && ($related_content_id[0]!='+' && $related_content_id[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($related_content_id[0]=='+' || $related_content_id[0]==',') && $first==1 ){
				$related_content_id[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($related_content_id, $table_name.'.related_content_id');
				
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
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($content_id, $table_name.'.content_id');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($content_relationship_type)){
					
			$content_relationship_type=trim($content_relationship_type);
				
			if($first==0 && ($content_relationship_type[0]!='+' && $content_relationship_type[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($content_relationship_type[0]=='+' || $content_relationship_type[0]==',') && $first==1 ){
				$content_relationship_type[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($content_relationship_type, $table_name.'.content_relationship_type');
				
			$first=0;
		}//end not empty app_id
		
		
		if(!empty($content_relationship_date)){
					
			$content_relationship_date=trim($content_relationship_date);
				
			if($first==0 && ($content_relationship_date[0]!='+' && $content_relationship_date[0]!=',' ) ){
					$WHERE_CLAUSE.=" AND ";
				}
					
			else if( ($content_relationship_date[0]=='+' || $content_relationship_date[0]==',') && $first==1 ){
				$content_relationship_date[0]='';
			}
				
			$WHERE_CLAUSE.=' '.PVTools::parseSQLOperators($content_relationship_date, 'content_relationship_date');
				
			$first=0;
		}//end not empty app_id
		
		
		$JOINS='';
			
		if(!empty($custom_where)){
			$WHERE_CLAUSE.=" $custom_where ";
		}
		
		if($join_content){
			$JOINS.=" JOIN ".PVDatabase::getContentTableName()." ON ".PVDatabase::getContentTableName().".content_id=".PVDatabase::getContentRelationsTableName().".content_id ";
		}
		
		if($join_related_content){
			$JOINS.=" JOIN ".PVDatabase::getContentTableName()." ON ".PVDatabase::getContentTableName().".content_id=".PVDatabase::getContentRelationsTableName().".related_content_id ";
		}
		
		if(!empty($custom_join)){
			
			$JOINS.=" $custom_join ";
			
		}
			
		if(!empty($WHERE_CLAUSE)){
			$WHERE_CLAUSE=' WHERE '.$WHERE_CLAUSE;
		}
			
		if(!empty($distinct)){
			$prefix_args.=" DISTINCT $distinct, ";
		}
		
		if(!empty($limit) && $db_type=='mssql' && !$paged){
			$prefix_args.=" TOP $limit ";
		}
		
		if($paged){
			$page_results=PVDatabase::getPagininationOffset($table_name, $JOINS , $WHERE_CLAUSE, $current_page, $results_per_page, $order_by);
			
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
    	$query="$prequery SELECT $prefix_args $custom_select FROM $table_name $JOINS $WHERE_CLAUSE";
		
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
		
	}//end
	
	public static function checkContentRelationship($content_id, $related_content_id, $content_relationship_type=''){
		$content_id=PVDatabase::makeSafe($content_id);
		$related_content_id=PVDatabase::makeSafe($related_content_id);
		$content_relationship_type=PVDatabase::makeSafe($content_relationship_type);
		
		$query="SELECT * FROM ".PVDatabase::getContentRelationsTableName()." WHERE content_id='$content_id' AND related_content_id='$related_content_id' AND content_relationship_type='$content_relationship_type' ";
		$result=PVDatabase::query($query);
		$row=PVDatabase::fetchArray($result);
		
		if(empty($row)){
			return 0;
		}
		else{
			return 1;	
		}
	}//end checkContentRelationship
	
	
	public static function removeContentRelationship($content_id, $related_content_id=0, $content_relationship_type=''){
		$content_id=PVDatabase::makeSafe($content_id);
		$related_content_id=PVDatabase::makeSafe($related_content_id);
		$content_relationship_type=PVDatabase::makeSafe($content_relationship_type);
		
		if(!empty($content_id)){
			
			$query="DELETE FROM ".PVDatabase::getContentRelationsTableName()." WHERE content_id='$content_id' ";
			
			if(!empty($related_content_id)){
				$query.=" AND related_content_id='$related_content_id' ";
			}
			
			if(!empty($content_relationship_type)){
				$query.=" AND content_relationship_type='$content_relationship_type' ";
			}
			
			PVDatabase::query($query);
		}
		
	}//end removeContentRelationship
	
	private static function getCategoryDefaults() {
		$defaults=array(
			'category_id'=>'',
			'parent_category'=>0,
			'category_name'=>'',
			'app_id'=>0,
			'category_unique_name'=>'',
			'category_alias'=>'',
			'category_order'=>'',
			'category_description'=>'',
			'category_type'=>'',
			'category_owner'=>0
		);
		return $defaults;
	}//end getCategoryDefaults
	
	private static function getContentDefaults() {
		$defaults=array(
			'content_id'=>0,
			'parent_content'=>0,
			'app_id'=>0,
			'owner_id'=>0,
			'content_title'=>'',
			'content_description'=>'',
			'content_meta_tags'=>'',
			'content_meta_description'=>'',
			'content_thumbnail'=>'',
			'content_alias'=>'',
			'date_created'=>'',
			'date_modified'=>'',
			'date_active'=>'',
			'date_inactive'=>'',
			'is_searchable'=>0,
			'allow_comments'=>0,
			'allow_rating'=>0,
			'content_active'=>0,
			'content_promoted'=>0,
			'content_permissions'=>'',
			'content_type'=>'',
			'content_language'=>'',
			'translate_content'=>0,
			'content_approved'=>0,
			'content_parameters'=>'',
			'sym_link'=>'',
			'content_order'=>'',
			'content_access_level'=>0,
			'content_taxonomy'=>'',
			'adjacent_table'=>'',
			'content_category'=>'',
			'category_id'=>''
		);
		return $defaults += self::_getSqlSearchDefaults();
	}//end getContentDefaults
	
	private static function getAudioContentDefaults() {
		$defaults=array(
			'audio_id'=>0,
			'audio_length'=>'',
			'mid_file'=>'',
			'wav_file'=>'',
			'aif_file'=>'',
			'mp3_file'=>'',
			'ra_file'=>'',
			'sample_length'=>'',
			'audio_src'=>'',
			'audio_type'=>''
		);
		return $defaults += self::_getSqlSearchDefaults();
	}

	private static function getEventContentDefaults() {
		$defaults=array(
			'event_id'=>0,
			'event_location'=>'',
			'event_start_date'=>'',
			'event_end_date'=>'',
			'event_country'=>'',
			'event_address'=>'',
			'event_city'=>'',
			'event_state'=>'',
			'event_zip'=>'',
			'event_longitude'=>'',
			'event_latitude'=>'',
			'event_src'=>'',
			'event_contact'=>'',
			'event_map'=>'',
			'undefined_endtime'=>'',
		);
		return $defaults += self::_getSqlSearchDefaults();
	}

	private static function getTextContentDefaults() {
		$defaults=array(
			'text_id'=>0,
			'text_content'=>'',
			'text_page_group'=>'',
			'text_page_number'=>'',
			'text_section'=>'',
			'text_src'=>''
		);
		return $defaults += self::_getSqlSearchDefaults();
	}
	
	private static function getFileContentDefaults() {
		$defaults=array(
			'file_id'=>0,
			'file_type'=>'',
			'file_size'=>0,
			'file_location'=>'',
			'file_name'=>'',
			'file_src'=>'',
			'file_downloadable'=>0,
			'file_max_downloads'=>0,
			'file_version'=>0,
			'file_license'=>''
		);
		return $defaults += self::_getSqlSearchDefaults();
	}
	
	private static function getImageContentDefaults() {
		$defaults=array(
			'image_id'=>0,
			'image_type'=>'',
			'image_size'=>0,
			'image_url'=>'',
			'thumb_url'=>'',
			'image_width'=>0,
			'image_height'=>0,
			'thumb_width'=>0,
			'thumb_height'=>0,
			'image_src'=>''
		);
		return $defaults += self::_getSqlSearchDefaults();
	}
	
	private static function getProductContentDefaults() {
		$defaults=array(
			'product_id'=>0,
			'product_sku'=>'',
			'product_idsku'=>'',
			'product_vendor_id'=>'',
			'product_quantity'=>0,
			'product_price'=>0,
			'product_discount_price'=>0,
			'product_size'=>'',
			'product_color'=>'',
			'product_weight'=>0,
			'product_height'=>0,
			'product_length'=>0,
			'product_currency'=>'',
			'product_in_stock'=>0,
			'product_type'=>'',
			'product_tax_id'=>0,
			'product_attribute'=>'',
			'product_version'=>0
		);
		return $defaults += self::_getSqlSearchDefaults();
	}

	private static function getVideoContentDefaults() {
		$defaults=array(
			'video_id'=>0,
			'video_type'=>'',
			'video_length'=>'',
			'video_allow_embedding'=>0,
			'flv_file'=>'',
			'mp4_file'=>'',
			'wmv_file'=>'',
			'mpeg_file'=>'',
			'rm_file'=>'',
			'avi_file'=>'',
			'mov_file'=>'',
			'asf_file'=>'',
			'ogv_file'=>'',
			'webm_file'=>'',
			'enable_hq'=>0,
			'auto_hq'=>0,
			'video_src'=>'',
			'video_embed'=>''
		);
		return $defaults += self::_getSqlSearchDefaults();
	}
	
}//end class