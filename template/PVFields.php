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

	public static function createField( $args = array() ) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$args += self::_getFieldDefaults();
		$args = self::_applyFilter( get_class(), __FUNCTION__ , $args , array('event'=>'args'));
		$args = PVDatabase::makeSafe($args);
		extract($args);
	
		$query="INSERT INTO ".pv_getFieldsTableName()."(field_name ,field_type,field_description,field_title,max_length, max_size, columns,rows,value,searchable,readonly,show_title, is_required , on_blur,id,on_change,on_click,on_doubelclick,on_focus,on_keydown,on_keyup,on_keypress,on_mousedown,on_mouseup,on_mousemove,on_mouseover,on_mouseout,instructions,show_instructions,checked,disabled,lang,align,accept,class,size, field_prefix, field_suffix, field_css,app_id, field_prefix, field_suffix, field_css, owner_id, field_unique_name) VALUES( '$field_name' ,'$field_type', '$field_description' , '$field_title' , '$max_length' , '$max_size' , '$columns' , '$rows' ,'$value' , '$searchable' , '$readonly' , '$show_title' , '$is_required' , '$on_blur' , '$id' , '$on_change' , '$on_click' , '$on_doubelclick' ,'$on_focus' , '$on_keydown' ,' $on_keyup' , '$on_keypress' , '$on_mousedown' , '$on_mouseup' , '$on_mousemove' , '$on_mouseover' , '$on_mouseout' , '$instructions' , '$show_instructions' , '$checked' , '$disabled' , '$lang' ,'$align' , '$accept' , '$field_class' , '$size' , '$field_prefix' , '$field_suffix' , '$field_css' , '$app_id' , '$field_prefix', '$field_suffix', '$field_css' , '$owner_id' , '$field_unique_name')";
		$field_id=PVDatabase::return_last_insert_query($query, "field_id", pv_getFieldsTableName() );
		
		self::_notify(get_class().'::'.__FUNCTION__, $field_id, $args);
		$field_id = self::_applyFilter( get_class(), __FUNCTION__ , $field_id , array('event'=>'return'));
		
		return $field_id;
	}//end createField

	public static function updateField($args = array()) {
			
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
			
		$args += self::_getFieldDefaults();
		$args = self::_applyFilter( get_class(), __FUNCTION__ , $args , array('event'=>'args'));
		$args=PVDatabase::makeSafe($args);
		extract($args);
			
		$query="UPDATE ".pv_getFieldsTableName()." SET field_name='$field_name' , field_type='$field_type',field_description='$field_description' , field_title='$field_title' , max_length='$max_length' , max_size='$max_size', field_columns='$field_columns',field_rows='$field_rows' , field_value='$field_value' , searchable='$searchable' , readonly='$readonly' , show_title='$show_title' , is_required='$is_required' , on_blur='$on_blur',id='$id', on_change='$on_change',on_click='$on_click' , on_doubelclick='$on_doubelclick' ,on_focus='$on_focus' ,on_keydown='$on_keydown' ,on_keyup='$on_keyup' , on_keypress='$on_keypress' , on_mousedown='$on_mousedown',on_mouseup='$on_mouseup', on_mousemove='$on_mousemove' ,on_mouseover='$on_mouseover', on_mouseout='$on_mouseout' , instructions='$instructions',show_instructions='$show_instructions', checked='$checked' ,disabled='$disabled' , lang='$lang' , align='$align' ,accept='$accept' ,field_class='$field_class' , field_size='$size' , field_prefix='$field_prefix' , field_suffix='$field_suffix' , field_css='$field_css' ,app_id='$app_id', field_prefix='$field_prefix', field_suffix='$field_suffix', field_css='$field_css', owner_id='$owner_id', field_unique_name='$field_unique_name', content_type='$content_type', field_order='$field_order' WHERE field_id='$field_id' ";
		PVDatabase::query($query);
		self::_notify(get_class().'::'.__FUNCTION__, $args);
	}//end updateField
	
	
	public static function getFieldsList($args = array()) {
			
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
			
		$args += self::_getFieldDefaults();
		$args += self::_getSqlSearchDefaults();
		$args = self::_applyFilter( get_class(), __FUNCTION__ , $args , array('event'=>'args'));
		$args = PVDatabase::makeSafe($args);
		extract($args);
		
		$content_array=array();
		$table_name=pv_getFieldsTableName();
		$db_type=PVDatabase::getDatabaseType();
		
		$WHERE_CLAUSE='';
		
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
		
		self::_notify(get_class().'::'.__FUNCTION__, $content_array , $args);
		$content_array = self::_applyFilter( get_class(), __FUNCTION__ , $content_array , array('event'=>'return'));
    	
		return $content_array;
	}//end getFieldTypes
	
	public static function getField($field_id) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $field_id);		
		
		$field_id = self::_applyFilter( get_class(), __FUNCTION__ , $field_id , array('event'=>'args'));
		$field_id = PVDatabase::makeSafe($field_id);
		
		$query="SELECT * FROM ".pv_getFieldsTableName()." WHERE field_id='$field_id' ";
		
		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		
		self::_notify(get_class().'::'.__FUNCTION__, $row, $field_id);
		$row = self::_applyFilter( get_class(), __FUNCTION__ , $row , array('event'=>'return'));
		
		return $row;
	}//end getField
	
	public static function deleteField($field_id, $DELETE_OPTIONS=true, $DELETE_VALUES=true) {
			
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $field_id);
		
		$field_id=PVDatabase::makeSafe($field_id);
		
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
			self::_notify(get_class().'::'.__FUNCTION__, $field_id, $DELETE_OPTIONS,$DELETE_VALUES);
		}
		
	}//end deteleField
	
	public static function createFieldOption($args = array()) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$args = self::_applyFilter( get_class(), __FUNCTION__ , $args , array('event'=>'args'));
		$args=PVDatabase::makeSafe($args);
		extract($args);
			
		$query="INSERT INTO ".pv_getFieldsOptionsTableName()."(field_id, option_name, option_value, option_label, option_selected, option_disabled, option_class, option_dir, option_lang, option_style, option_title, option_on_click, option_on_doubelclick, option_on_keydown, option_on_keyup, option_on_keypress, option_on_mousedown, option_on_mouseup, option_on_mousemove, option_on_mouseover, option_on_mouseout, option_order) VALUES('$field_id' , '$option_name', '$option_value' , '$option_label' , '$option_selected' , '$option_disabled' , '$option_class' , '$option_dir' , '$option_lang' , '$option_style' , '$option_title' , '$option_on_click' , '$option_on_doubelclick' , '$option_on_keydown' , '$option_on_keyup', '$option_on_keypress', '$option_on_mousedown', '$option_on_mouseup', '$option_on_mousemove', '$option_on_mouseover', '$option_on_mouseout', '$option_order') ";
			
		$option_id = PVDatabase::return_last_insert_query($query, "option_id", pv_getFieldsOptionsTableName());
		self::_notify(get_class().'::'.__FUNCTION__, $option_id , $args);
		$option_id = self::_applyFilter( get_class(), __FUNCTION__ , $option_id , array('event'=>'return'));
		
		return $option_id;
	}//end createFieldOption
	
	
	
	public static function getFieldOptionsList($args = array()) {
				
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$args = self::_applyFilter( get_class(), __FUNCTION__ , $args , array('event'=>'args'));
		$args = PVDatabase::makeSafe($args);	
		extract($args);
		
		$content_array=array();
		$table_name=pv_getFieldsOptionsTableName();
		$db_type=PVDatabase::getDatabaseType();
		
		$WHERE_CLAUSE='';
		
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
		self::_notify(get_class().'::'.__FUNCTION__, $content_array , $args);
		$content_array = self::_applyFilter( get_class(), __FUNCTION__ , $content_array, array('event'=>'return'));
    	
		return $content_array;
	}//end getFieldTypes
	
	public static function getFieldOption($field_id, $option_id){
		$field_id=PVDatabase::makeSafe($field_id);
		$option_id=PVDatabase::makeSafe($option_id);
		
		if(!empty($field_id) && !empty($option_id)){
			$query="SELECT * FROM ".pv_getFieldsOptionsTableName()." WHERE field='$field_id' AND option_id='$option_id' ";
			$result=PVDatabase::query($query);
			$row=PVDatabase::fetchArray($result);
			
			$row=PVDatabase::formatData($row);
			self::_notify(get_class().'::'.__FUNCTION__, $row, $field_id, $option_id);
			$row = self::_applyFilter( get_class(), __FUNCTION__ , $row, array('event'=>'return'));
			
			return $row;
		}
	}//end getFeildOption
	
	public static function updateFieldOption($args = array()) {
			
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$args = self::_applyFilter( get_class(), __FUNCTION__ , $args , array('event'=>'args'));
		
		if(is_array($args)){
			$args=PVDatabase::makeSafe($args);
			extract($args);	
		}
		
		if(!empty($field_id) && !empty($option_id)){
			$query="UPDATE ".pv_getFieldsOptionsTableName()." SET option_name='$option_name', option_value='$option_value', option_label='$option_label', option_selected='$option_selected', option_disabled='$option_disabled', option_class='$option_class', option_dir='$option_dir', option_lang='$option_lang', option_style='$option_style', option_title='$option_title', option_on_click='$option_on_click', option_on_doubelclick='$option_on_doubelclick', option_on_keydown='$option_on_keydown', option_on_keyup='$option_on_keyup', option_on_keypress='$option_on_keypress', option_on_mousedown='$option_on_mousedown', option_on_mouseup='$option_on_mouseup', option_on_mousemove='$option_on_mousemove', option_on_mouseover='$option_on_mouseover', option_on_mouseout='$option_on_mouseout', option_order='$option_order' WHERE field='$field_id' AND option_id='$option_id'";
			PVDatabase::query($query);
			self::_notify(get_class().'::'.__FUNCTION__, $args);
		}
	}//end updateFieldOption
	
	public static function deleteFieldOption($field_id, $option_id){
		$field_id=ceil($field_id);
		$option_id=ceil($option_id);
		
		if(!empty($field_id) && !empty($option_id)){
			
			$query="DELETE FROM ".pv_getFieldsOptionsTableName()." WHERE field='$field_id' AND option_id='$option_id'";
			PVDatabase::query($query);
			self::_notify(get_class().'::'.__FUNCTION__, $field_id, $option_id);
		}
		
	}//end deleteFieldOption
	
	
	public static function createFieldValue($args = array()){
			
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
			
		$args += self::_getFieldValueDefaults();
		$args = self::_applyFilter( get_class(), __FUNCTION__ , $args , array('event'=>'args'));
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		if(!empty($field_id)){
			$query="INSERT INTO ".pv_getFieldValuesTableName()."(field_id, owner_id, app_id, field_value, content_id, field_grouping) VALUES( '$field_id', '$owner_id', '$app_id', '$field_value', '$content_id', '$field_grouping' )";
			$field_value_id = PVDatabase::return_last_insert_query($query, "field_value_id", pv_getFieldValuesTableName());
			
			self::_notify(get_class().'::'.__FUNCTION__, $field_value_id , $args);
			$field_value_id = self::_applyFilter( get_class(), __FUNCTION__ , $field_value_id , array('event'=>'return'));
			
			return $field_value_id;
		}
		
	}//end
	
	public static function getFieldValueList($args = array()) {
	
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
			
		$args += self::_getFieldValueDefaults();
		$args += self::_getSqlSearchDefaults();
		$args = self::_applyFilter( get_class(), __FUNCTION__ , $args , array('event'=>'args'));
		$args = PVDatabase::makeSafe($args);
		extract($args);
		
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
		self::_notify(get_class().'::'.__FUNCTION__, $content_array , $args);
		$content_array = self::_applyFilter( get_class(), __FUNCTION__ , $content_array , array('event'=>'return'));
    	
		return $content_array;
	}//end getFieldTypes
	
	public static function getFieldValue($field_value_id) {
			
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$field_value_id = self::_applyFilter( get_class(), __FUNCTION__ , $field_value_id , array('event'=>'args'));
		$field_value_id=PVDatabase::makeSafe($field_value_id);
		
		$query="SELECT * FROM ".pv_getFieldValuesTableName()." WHERE field_value_id='$field_value_id' ";
		
		$result = PVDatabase::query($query);
		$row = PVDatabase::fetchArray($result);
		PVDatabase::formatData($row);
		
		self::_notify(get_class().'::'.__FUNCTION__, $row , $field_value_id);
		$row = self::_applyFilter( get_class(), __FUNCTION__ , $row , array('event'=>'return'));
		
		return $row;
	}//end getFieldValue
	
	
	public static function updateFieldValue($args = array()) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
		
		$args += self::_getFieldValueDefaults();
		$args = self::_applyFilter( get_class(), __FUNCTION__ , $args , array('event'=>'args'));
		$args=PVDatabase::makeSafe($args);
		extract($args);
		
		if(!empty($field_value_id)){
			
			$query="UPDATE ".pv_getFieldValuesTableName()." SET field_id='$field_id', owner_id='$owner_id', app_id='$app_id', field_value='$field_value', content_id='$content_id', field_grouping='$field_grouping' WHERE field_value_id='$field_value_id'";
			PVDatabase::query($query);
			self::_notify(get_class().'::'.__FUNCTION__, $args);
		}//end !empty($field_value_id)
		
	}//end updateFieldValue
	
	public static function deleteFieldValue($field_value_id) {
		
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $field_value_id);
		
		$field_value_id = self::_applyFilter( get_class(), __FUNCTION__ , $field_value_id , array('event'=>'args'));
		$field_value_id=PVDatabase::makeSafe($field_value_id);
		
		if(!empty($field_value_id)){
			$query="DELETE FROM ".pv_getFieldValuesTableName()." WHERE field_value_id='$field_value_id'";
			PVDatabase::query($query);
			self::_notify(get_class().'::'.__FUNCTION__, $field_value_id);
		}
	}//end deltteFieldValue

	
	function fieldInterpreter($args = array()) {
			
		if(self::_hasAdapter(get_class(), __FUNCTION__) )
			return self::_callAdapter(get_class(), __FUNCTION__, $args);
			
		$args = self::_applyFilter( get_class(), __FUNCTION__ , $args , array('event'=>'args'));
		
		$output = '';
		$field = array();
		foreach($args as $key=> $value){
			$newkey = str_replace('field_', '', $key);
			if(!empty($value))
				$field[$newkey] = $value;
		}
		
		$output .= PVForms::label($field['title']);
		
		if($field['type']=='text' || $field['type']=='textfield') {
			$output .= PVForms::text($field['name'], $field);
		} else if($field['type']=='textarea') {
			$output .= PVForms::textarea($field['name'], '', $field);
		}
		
		self::_notify(get_class().'::'.__FUNCTION__, $output, $args);
		$output = self::_applyFilter( get_class(), __FUNCTION__ , $output , array('event'=>'return'));
		
		return $output;
	}//end fieldInterpreter
	
	protected static function _getFieldDefaults() {
		$defaults = array(
			'field_id' => 0,
			'field_name' => '',
			'field_type' => '',
			'field_description' => '',
			'field_title' => '',
			'max_length' => '',
			'max_size' => '',
			'field_columns' => '',
			'field_rows' => '',
			'field_value' => '',
			'searchable' => '',
			'readonly' => '',
			'show_title' => '',
			'enabled' => false,
			'show_creation' => false,
			'is_required' => false,
			'on_blur' => '',
			'id' => '',
			'on_change' => '',
			'on_click' => '',
			'on_doubleclick' => '',
			'on_focus' => '',
			'on_keydown' => '',
			'on_keyup' => '',
			'on_keypress' => '',
			'on_mousedown' => '',
			'on_mouseup' => '',
			'on_mousemove' => '',
			'on_mouseover' => '',
			'on_mouseout' => '',
			'instruction' => '',
			'show_instructions' => false,
			'checked' => false,
			'disabled' => false,
			'lang' => '',
			'align' => '',
			'accept' => '',
			'field_class' => '',
			'field_size' => '',
			'admin_editable' => false,
			'display_name' => '',
			'app_id' => '',
			'field_css' => '',
			'owner_id' => '',
			'field_unique_name' => '',
			'content_type' => '',
			'field_order' => 0
		);
		
		return $defaults;
	}
	
	protected static function _getFieldValueDefaults() {
		$defaults = array(
			'field_value_id' => 0,
			'field_id' => 0,
			'owner_id' => 0,
			'app_id' => 0,
			'content_id' => 0,
			'field_value' => '',
			'field_grouping' => ''
		);
		
		return $defaults;
	}
	
	
	
}//end class
?>
