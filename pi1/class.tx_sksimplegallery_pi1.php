<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Steffen Kamper <steffen@dislabs.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Plugin 'Simple Gallery' for the 'sk_simplegallery' extension.
 *
 * @author	Steffen Kamper <steffen@dislabs.de>
 */


require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('sk_simplegallery').'res/PHP_JPEG_Metadata_Toolkit_1.11/EXIF.php');    

class tx_sksimplegallery_pi1 extends tslib_pibase {
	var $prefixId = 'tx_sksimplegallery_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_sksimplegallery_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'sk_simplegallery';	// The extension key.
	var $pi_checkCHash = TRUE;
	var $template;
	var $uploaddir;
	var $pidList;
	var $caption;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->uploaddir = 'uploads/tx_sksimplegallery/';
		
        
        if($this->conf['debug']) debug($this->piVars);
		// sys_language_mode defines what to do if the requested translation is not found
		$this->sys_language_mode = $this->conf['sys_language_mode']?$this->conf['sys_language_mode'] : $GLOBALS['TSFE']->sys_language_mode;

		// parse XML data into php array
		$this->pi_initPIflexForm(); 
		
		//Flexform Values
		//view
		$this->conf['view'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'view_input', 'sVIEW');
		
		#echo "VIEW: ".$this->conf['view'];
		$templateFile = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'template_file', 'sVIEW');
		if($templateFile=='')
			$this->conf['templateFile'] = $this->conf['templateFile']=='' ? 'typo3conf/ext/'.$this->extKey.'/pi1/template.html' : $this->conf['templateFile'];
		else
			$this->conf['templateFile'] ="uploads/tx_sksimplegallery/$templateFile";
		
        $tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'gal_effects', 'sVIEW');
		if($tmp!='') $this->conf['galEffects']=$tmp;
        
		//gallery view
		$singlePID = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'singlePID', 'sLIST');
		if($singlePID!='') $this->conf['singlePID']=$singlePID;
		
		$tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'list_maxW', 'sLIST');
		if($tmp!='') $this->conf['listView.']['file.']['maxW']=$tmp;
		$tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'list_maxH', 'sLIST');
		if($tmp!='') $this->conf['listView.']['file.']['maxH']=$tmp;
		$tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'list_W', 'sLIST');
		if($tmp!='') {
			$this->conf['listView.']['file.']['maxW']='';
			$this->conf['listView.']['file.']['width']=$tmp;
		}
		$tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'list_H', 'sLIST');
		if($tmp!='') {
			$this->conf['listView.']['file.']['maxH']='';
			$this->conf['listView.']['file.']['height']=$tmp;
		}
		
		//single view
		$tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'single_maxW', 'sSingle');
		if($tmp!='') $this->conf['thumbView.']['file.']['maxW']=$tmp;
		$tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'single_maxH', 'sSingle');
		if($tmp!='') $this->conf['thumbView.']['file.']['maxH']=$tmp;
		$tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'single_W', 'sSingle');
		if($tmp!='') {
			$this->conf['thumbView.']['file.']['maxW']='';
			$this->conf['thumbView.']['file.']['width']=$tmp;
		}
		$tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'single_H', 'sSingle');
		if($tmp!='') {
			$this->conf['thumbView.']['file.']['maxH']='';
			$this->conf['thumbView.']['file.']['height']=$tmp;
		}
		$tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'singleClickmode', 'sSingle');
		if($tmp==1) $this->conf['thumbMode']=$tmp;
		
		$tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'singlePic_maxW', 'sSingle');
		if($tmp!='') $this->conf['singleView.']['file.']['maxW']=$tmp;
		$tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'singlePic_maxH', 'sSingle');
		if($tmp!='') $this->conf['singleView.']['file.']['maxH']=$tmp;
		
        $tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'singleID', 'sSingle');
		if($tmp!='') $this->conf['singleView.']['singleID']=$tmp;
        $tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'singleLayout', 'sSingle');
		if($tmp!='') $this->conf['singleLayout']=$tmp;
        $tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'eCards', 'sSingle');
		if($tmp!='') $this->conf['activateEcards']=$tmp;
        
        //teaser view
		$tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'teaser_maxW', 'sTEASER');
		if($tmp!='') $this->conf['teaserView.']['file.']['maxW']=$tmp;
		$tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'teaser_maxH', 'sTEASER');
		if($tmp!='') $this->conf['teaserView.']['file.']['maxH']=$tmp;
		$tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'teaser_W', 'sTEASER');
		if($tmp!='') {
			$this->conf['teaserView.']['file.']['maxW']='';
			$this->conf['teaserView.']['file.']['width']=$tmp;
		}
		$tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'teaser_H', 'sTEASER');
		if($tmp!='') {
			$this->conf['teaserView.']['file.']['maxH']='';
			$this->conf['thumbView.']['file.']['height']=$tmp;
		}
		$tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'teaserCount', 'sTEASER');
		if($tmp!='') $this->conf['teaserViewCount']=$tmp;
		$tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'teaser_order', 'sTEASER');
		if($tmp!='') $this->conf['teaserViewSortBy']=$tmp;
		$tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'teaserTargetPID', 'sTEASER');
		if($tmp!='') $this->conf['teaserViewTarget']=$tmp;
		
        
        //which layout ?
        $this->conf['singleLayout']=intval($this->conf['singleLayout']);
        
        
        //pagebrowswer defaults
        if(intval($this->conf['listPageRecords'])==0) $this->conf['listPageRecords'] = 20;
        if(intval($this->conf['singlePageRecords'])==0) $this->conf['singlePageRecords'] = 20;
        
		//proceed now
		$this->template=$this->cObj->fileResource($this->conf['templateFile']);
		$this->pidList = $this->pi_getPidList($this->cObj->data['pages'],$this->cObj->data['recursive']);

		switch($this->conf['view']) {
			case 'LIST':
				$content=$this->Galleries();
				break;
			case 'SINGLE':
				$content=$this->SingleGallery();
				break;
            case 'TEASER':
				$content=$this->Teaser();
				break;
            case 'ECARD':
                $content=$this->eCards();
				break;
		}
	
		return $this->pi_wrapInBaseClass($content);
	}
	
	function Galleries() {
		$template['total'] = $this->cObj->getSubpart($this->template,'###LISTVIEW###');
		$template['item'] = $this->cObj->getSubpart($template['total'],'###LIST###');
		$innercontent='';
		
        //PB
        $PB='';
		if(intval($this->conf['listPageRecords'])>0 && $count>intval($this->conf['listPageRecords'])) {
			$maxpages=ceil($count/intval($this->conf['listPageRecords']));
			$limit=$page*$this->conf['pageRecords'].','.$this->conf['listPageRecords'];
			$PB.='<p class="pagebrowser">'.$this->pi_getLL('pi_list_browseresults_page','Page').' ';
			for($i=0;$i<$maxpages;$i++) {
				$PB.=($i==$page ? '<span class="active">'.($i+1) : '<span><a href="'.$this->pi_linkTP_keepPIvars_url(array('page'=>$i),1,0,$GLOBALS['TSFE']->id).'">'.($i+1).'</a>').'</span>';
			}
			$PB.='</p>';
		}
        
        //language patch from Marcus Krause
        $where = '';
		if ($this->sys_language_mode == 'strict' && $GLOBALS['TSFE']->sys_language_content) {
		    $tmpres = $this->cObj->exec_getQuery('tx_sksimplegallery_galleries', array('selectFields' => 'tx_sksimplegallery_galleries.l18n_parent', 'where' => 'tx_sksimplegallery_galleries.sys_language_uid = '.$GLOBALS['TSFE']->sys_language_content.$this->enableFields, 'pidInList' => $this->pidList));
			$strictUids = array();
		    while ($tmprow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($tmpres)) {
		        $strictUids[] = $tmprow['l18n_parent'];
		    }
		    $strStrictUids = implode(',', $strictUids);
		    $where .= '(tx_sksimplegallery_galleries.uid IN (' . ($strStrictUids?$strStrictUids:0) . ') OR tx_sksimplegallery_galleries.sys_language_uid=-1)';
		} else {
		    $where .= 'tx_sksimplegallery_galleries.sys_language_uid IN (0,-1)';
        }
		
		// Auswahl des Ordners
		if ($this->pidList) $where .= ' AND pid IN ('. $this->pidList .')';
		$where .= ' AND deleted = 0 AND hidden = 0';
        
        
		$query = $GLOBALS['TYPO3_DB']->SELECTquery(
                '*',         // SELECT ...
                'tx_sksimplegallery_galleries',     // FROM ...
                $where,    // WHERE...
                '',            // GROUP BY...
                'sorting',    // ORDER BY...
                $limit            // LIMIT ...
            );
	    
	    $res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
		while($temp = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			// get the translated record if the content language is not the default language
			if ($GLOBALS['TSFE']->sys_language_content) {
   				$OLmode = ($this->sys_language_mode == 'strict'?'hideNonTranslated':'');
   				$temp = $GLOBALS['TSFE']->sys_page->getRecordOverlay('tx_sksimplegallery_galleries', $temp, $GLOBALS['TSFE']->sys_language_content, $OLmode);
			}
            //prepare picture
			$this->conf['listView.']['file']=$temp['altgalpicture'] ? $this->uploaddir.$temp['altgalpicture'] : $this->uploaddir.$this->getGalpicture($temp['galpicture']); 
			$this->conf['listView.']['params'] = $this->pi_classParam('image');
			$this->conf['listView.']['altText'] = $temp['title'];
			$this->conf['listView.']['titleText'] = $temp['title'];
			$markerArray['###PICTURE###']=$this->ImageMarker($this->conf['listView.'],true);
			$imginfo=$GLOBALS['TSFE']->lastImageInfo;
			$markerArray['###WIDTH###']='style="width:'.$imginfo[0].'px;"';
			$markerArray['###TITLE###']=$temp['title'];
			$cache = 1;
    		$this->pi_USER_INT_obj = 0;
			if($this->conf['linkSingleDirect']==1) 
                $subpartArray['###LINK_ITEM###']= explode('|',$this->pi_linkTP('|',$urlParameters=array($this->prefixId.'[id]'=>$temp['uid'],$this->prefixId.'[single]'=>$this->getFirstPicture($temp['uid']),$this->prefixId.'[backpid]'=>$GLOBALS["TSFE"]->id),$cache,$altPageId=$this->conf['singlePID']));
            else
			    $subpartArray['###LINK_ITEM###']= explode('|',$this->pi_linkTP('|',$urlParameters=array($this->prefixId.'[id]'=>$temp['uid'],$this->prefixId.'[backpid]'=>$GLOBALS["TSFE"]->id),$cache,$altPageId=$this->conf['singlePID']));
			$innercontent.=$this->cObj->substituteMarkerArrayCached($template['item'], $markerArray,array(),$subpartArray);
		}
        $markerArray=array();
		$markerArray['###PAGEBROWSER###']=$PB; 
		$subpartArray['###LIST###']=$innercontent;
		return $this->cObj->substituteMarkerArrayCached($template['total'], $markerArray,$subpartArray,array());
	}
	
    function getFirstPicture($gal) {
        $res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_sksimplegallery_galleries','uid='.$gal);
        $row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
        $p=$row['pictures']!='' ? explode($row['pictures']) : array(0);
        return $p[0]; 
    }
    
    function getGalpicture($uid) {
       $where = '';
       if ($this->sys_language_mode == 'strict' && $GLOBALS['TSFE']->sys_language_content) {
       	   $tmpres = $this->cObj->exec_getQuery('tx_sksimplegallery_pictures', array('selectFields' => 'tx_sksimplegallery_pictures.l18n_parent', 'where' => 'tx_sksimplegallery_pictures.sys_language_uid = '.$GLOBALS['TSFE']->sys_language_content.$this->enableFields, 'pidInList' => $this->pidList));
		   $strictUids = array();
		   while ($tmprow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($tmpres)) {
		       $strictUids[] = $tmprow['l18n_parent'];
		   }
		   $strStrictUids = implode(',', $strictUids);
		   $where .= '(tx_sksimplegallery_pictures.uid IN (' . ($strStrictUids?$strStrictUids:0) . ') OR tx_sksimplegallery_pictures.sys_language_uid=-1)';
		} else {
		    $where .= 'tx_sksimplegallery_pictures.sys_language_uid IN (0,-1)';
        }
       $where .= ' AND uid = ' . intval($uid) . ' AND deleted = 0 AND hidden = 0';
       
       
       
       $query = $GLOBALS['TYPO3_DB']->SELECTquery(
                '*',         // SELECT ...
                'tx_sksimplegallery_pictures',     // FROM ...
                $where,    // WHERE...
                '',            // GROUP BY...
                '',    // ORDER BY...
                ''            // LIMIT ...
            );
       $res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query); 
       // already localized picture
	   if (!$GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
	       $query = $GLOBALS['TYPO3_DB']->SELECTquery(
		   			'picture',         // SELECT ...
					'tx_sksimplegallery_pictures',     // FROM ...
					'uid = ' . intval($uid) . ' AND deleted = 0 AND hidden = 0',
					'',            // GROUP BY...
					'',    // ORDER BY...
					''            // LIMIT ...
					);
	       $res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
		   $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		   return $row['picture'];
	   } else {
	       $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
	       // get the translated record if the content language is not the default language
	       if ($GLOBALS['TSFE']->sys_language_content) {
				$OLmode = ($this->sys_language_mode == 'strict'?'hideNonTranslated':'');
				$row = $GLOBALS['TSFE']->sys_page->getRecordOverlay('tx_sksimplegallery_pictures', $row, $GLOBALS['TSFE']->sys_language_content, $OLmode);
	       }
	       return $row['picture'];
	   }
    }
    
    function Teaser() {
        $template['total'] = $this->cObj->getSubpart($this->template,'###TEASERVIEW###');
		$template['item'] = $this->cObj->getSubpart($template['total'],'###THUMBLIST###');
        
        #get pictures from galleries
        $res=$GLOBALS['TYPO3_DB']->exec_SELECTquery(
            '*',
            'tx_sksimplegallery_galleries',
            'hidden=0 and deleted=0 and pid IN('.$this->pidList.')');
        while($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
            $pics.=$row['pictures']!=''?$row['pictures'].',':'';
        } 
        $pics=substr($pics,0,strlen($pics)-1);
        if ($this->sys_language_mode == 'strict' && $GLOBALS['TSFE']->sys_language_content) {
			$tmpres = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_sksimplegallery_pictures.l18n_parent', 'tx_sksimplegallery_pictures', 'tx_sksimplegallery_pictures.sys_language_uid = '.$GLOBALS['TSFE']->sys_language_content.$this->enableFields);
			$strictUids = array();
			while ($tmprow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($tmpres)) {
			    $strictUids[] = $tmprow['l18n_parent'];
			}
			$strStrictUids = implode(',', $strictUids);
			$where .= '(tx_sksimplegallery_pictures.uid IN (' . ($strStrictUids?$strStrictUids:0) . ') OR tx_sksimplegallery_pictures.sys_language_uid=-1)';
		} else
			$where .= 'tx_sksimplegallery_pictures.sys_language_uid IN (0,-1)';
            
        $res=$GLOBALS['TYPO3_DB']->exec_SELECTquery(
            '*',
            'tx_sksimplegallery_pictures',
            $where.' AND hidden=0 AND deleted=0 AND pid IN('.$this->pidList.') AND uid IN('.$pics.')',
            '',$orderBy=$this->conf['teaserViewSortBy'],
            $limit=$this->conf['teaserViewCount']); 
        
        while($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
            //find gallery with the pic
            $gres=$GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_sksimplegallery_galleries','FIND_IN_SET('.$row['uid'].',pictures)>0');
            $gal=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($gres);
            $thumbView=$this->conf['teaserView.'];  
            $thumbView['file']=$this->uploaddir.$row['picture'];
            
			$thumbView['params'] = $this->pi_classParam('image');
			$thumbView['altText'] = $row['title'];
			$thumbView['titleText'] = $row['title'];
			$thumbView['caption'] = $row['description'] ? strip_tags($row['description']) : $row[$this->config['popupAltDescriptionField']];
			$this->caption.=$row['title']."\n";
           #t3lib_div::debug($thumbView);     
            $markerArray['###THUMB###']=$this->ImageMarker($thumbView);
            $markerArray['###THUMBTITLE###']=$row['title'];
            $linkWrapArray['###LINK###']=explode('|',$this->pi_linkTP('|',array(
                $this->prefixId.'[id]'=>$gal['uid'],
                $this->prefixId.'[single]'=>$row['uid'],
            ),1,$this->conf['teaserViewTarget']));
            $innercontent.=$this->cObj->substituteMarkerArrayCached($template['item'], $markerArray,$subpartArray,$linkWrapArray);                 
        }
        $subpartArray['###THUMBLIST###']=$innercontent;
        
        return $this->cObj->substituteMarkerArrayCached($template['total'], $markerArray,$subpartArray,$linkWrapArray); ;
    }
    
	function SingleGallery() {
		$template['total'] = $this->cObj->getSubpart($this->template,$this->conf['singleLayout']==0?'###SINGLEVIEW###':'###SINGLEVIEW1###');
		$template['item'] = $this->cObj->getSubpart($template['total'],'###THUMBLIST###');
		$template['single'] = $this->cObj->getSubpart($template['total'],'###SINGLEONE###');
		$innercontent=$singlecontent=$cap='';
		$single=array();
		if(!isset($this->piVars['id'])) $this->piVars['id']=$this->conf['singleView.']['singleID']; 
		$page=intval($this->piVars['page']); 
        
        // get Data
		$where = '';
		if ($this->sys_language_mode == 'strict' && $GLOBALS['TSFE']->sys_language_content) {
			$tmpres = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_sksimplegallery_galleries.l18n_parent', 'tx_sksimplegallery_galleries', 'tx_sksimplegallery_galleries.sys_language_uid = '.$GLOBALS['TSFE']->sys_language_content.$this->enableFields);
			$strictUids = array();
		    while ($tmprow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($tmpres)) {
		        $strictUids[] = $tmprow['l18n_parent'];
		    }
		    $strStrictUids = implode(',', $strictUids);
		    $where .= '(tx_sksimplegallery_galleries.uid IN (' . ($strStrictUids?$strStrictUids:0) . ') OR tx_sksimplegallery_galleries.sys_language_uid=-1)';
		} else
		    $where .= 'tx_sksimplegallery_galleries.sys_language_uid IN (0,-1)';
		$where .= ' AND deleted = 0 AND hidden = 0';
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_sksimplegallery_galleries', $where.' AND uid='.intval($this->piVars['id']));
        if(!$GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
			return "no data for this view";
			break;
		}
        $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
        // get the translated record if the content language is not the default language
        if ($GLOBALS['TSFE']->sys_language_content) {
			$OLmode = ($this->sys_language_mode == 'strict'?'hideNonTranslated':'');
			$row = $GLOBALS['TSFE']->sys_page->getRecordOverlay('tx_sksimplegallery_galleries', $row, $GLOBALS['TSFE']->sys_language_content, $OLmode);
        }
        $thumb_ids=explode(',',$row['pictures']); 
        $count=count($thumb_ids);
        
        //PB
        $PB=''; 
        // initialize loop params
        $start=0;
        $end=$count;
        
        // calculate Pagebrowser  (only with $this->conf['singleLayout']==0)
        if($this->conf['singleLayout']==0 || ($this->conf['singleLayout']==1 && !$this->piVars['single']) ) {
		    if(intval($this->conf['singlePageRecords'])>0 && $count>intval($this->conf['singlePageRecords']) ) {
			    $maxpages=ceil($count/intval($this->conf['singlePageRecords']));
			    $start=$page*$this->conf['singlePageRecords'];
			    $end=$start+$this->conf['singlePageRecords']>$count?$count:$start+$this->conf['singlePageRecords'];
			    $PB.='<p class="pagebrowser">'.$this->pi_getLL('pi_list_browseresults_page','Page').' ';
			    for($i=0;$i<$maxpages;$i++) {
				    $PB.=($i==$page ? '<span class="active">'.($i+1) : '<span><a href="'.$this->pi_linkTP_keepPIvars_url(array('page'=>$i),1,0,$GLOBALS['TSFE']->id).'">'.($i+1).'</a>').'</span>';
			    }
			    $PB.='</p>';
		    }
        }

		$this->caption='';
		for($i=$start;$i<$end;$i++) {
			$where = '';
			if ($this->sys_language_mode == 'strict' && $GLOBALS['TSFE']->sys_language_content) {
			    $tmpres = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_sksimplegallery_pictures.l18n_parent', 'tx_sksimplegallery_pictures', 'tx_sksimplegallery_pictures.sys_language_uid = '.$GLOBALS['TSFE']->sys_language_content.$this->enableFields);
				$strictUids = array();
			    while ($tmprow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($tmpres)) {
			        $strictUids[] = $tmprow['l18n_parent'];
			    }
			    $strStrictUids = implode(',', $strictUids);
			    $where .= '(tx_sksimplegallery_pictures.uid IN (' . ($strStrictUids?$strStrictUids:0) . ') OR tx_sksimplegallery_pictures.sys_language_uid=-1)';
			} else
			    $where .= 'tx_sksimplegallery_pictures.sys_language_uid IN (0,-1)';
			$where .= ' AND uid='.intval($thumb_ids[$i]).' and hidden=0 and deleted=0';
			$query = $GLOBALS['TYPO3_DB']->SELECTquery(
                '*',         // SELECT ...
                'tx_sksimplegallery_pictures',     // FROM ...
                $where,    // WHERE...
                '',            // GROUP BY...
                '',    // ORDER BY...
                ''            // LIMIT ...
            );
			$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
			// already localized picture
			if (!$GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
				$query = $GLOBALS['TYPO3_DB']->SELECTquery(
			   			'*',	// SELECT ...
						'tx_sksimplegallery_pictures',     // FROM ...
						'uid = ' . intval($thumb_ids[$i]) . ' AND deleted = 0 AND hidden = 0',
						'',		// GROUP BY...
						'',		// ORDER BY...
						''		// LIMIT ...
						);
				$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
				$thumb = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			} else {
				$thumb = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				// get the translated record if the content language is not the default language
				if ($GLOBALS['TSFE']->sys_language_content) {
					$OLmode = ($this->sys_language_mode == 'strict'?'hideNonTranslated':'');
					$thumb = $GLOBALS['TSFE']->sys_page->getRecordOverlay('tx_sksimplegallery_pictures', $thumb, $GLOBALS['TSFE']->sys_language_content, $OLmode);
				}
			}	
            
			if($thumb) {
			    $this->conf['thumbView.']['file']=$this->uploaddir.$thumb['picture'];
			    $this->conf['thumbView.']['params'] = $this->pi_classParam('image');
			    $this->conf['thumbView.']['altText'] = $thumb['title'];
			    $this->conf['thumbView.']['titleText'] = $thumb['title'];
			    $this->conf['thumbView.']['caption'] = $thumb['description'] ? strip_tags($thumb['description']) : $thumb[$this->config['popupAltDescriptionField']];
			    $this->caption.=$thumb['title']."\n";
			    
                //retrieve EXIF Data
                $markerArray['###EXIF###']='';
                if(!$this->conf['donotreadEXIF']) {
                    $exif = $this->getExifData($this->uploaddir.$thumb['picture']);
                    $markerArray['###EXIF###']=is_array($exif) ? $this->cObj->stdWrap($this->infoExifDiv($exif),$this->conf['exifData.']) : ''; 
                }
			    $popup=$this->cObj->data['tx_kjimagelightbox2_imagelightbox2']==0?$this->conf['thumbMode']:0;
                if($this->conf['singleLayout']==1) $popup=0;
                $markerArray['###THUMB###']=$this->ImageMarker($this->conf['thumbView.'],$popup);
			    $imginfo=$GLOBALS['TSFE']->lastImageInfo;
			    $markerArray['###WIDTH###']='style="width:'.$imginfo[0].'px;"';
			    
                if($this->cObj->data['tx_kjimagelightbox2_imagelightbox2']==0 && $popup==0) {
				    $markerArray['###THUMB###']=$this->pi_linkTP_keepPIvars($markerArray['###THUMB###'],array('id'=>$this->piVars['id'],'backpid'=>$this->piVars['backpid'],'page'=>$this->piVars['page'],'single'=>$thumb['uid']),1,1,$GLOBALS["TSFE"]->id);
			    }
                
                $markerArray['###THUMBTITLE###']=$thumb['title'];
			    if($this->conf['activateEcards']==1) {  
                    $linkWrapArray['###ECARDLINK###']=explode('|',$this->pi_linkToPage('|',$this->conf['eCards.']['viewPID'],'',array($this->prefixId.'[newecard]'=>$thumb['uid'],$this->prefixId.'[backpid]'=>$GLOBALS["TSFE"]->id)));   
                } else {
                    $subpartArray['###ECARDLINK###']='';
                }
                // Adds hook for processing of extra item markers
		        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['sk_simplegallery']['extraSingleMarkerHook'])) {
			        foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['sk_simplegallery']['extraSingleMarkerHook'] as $_classRef) {
				        $_procObj = & t3lib_div::getUserObj($_classRef);
				        $markerArray = $_procObj->extraSingleMarkerProcessor($markerArray, $thumb, $this);
			        }
		        }
			    $innercontent.=$this->cObj->substituteMarkerArrayCached($template['item'], $markerArray,$subpartArray,$linkWrapArray);
                
			    if($thumb_ids[$i]==$this->piVars['single']) {
                    $single=$thumb;
                    
                    //change for Layout1, thx to Uwe Schmelzer
                    
                    //clear overview part of template
                    $subpartArray['###OVERVIEW###']='';
                    	// Store ID of prev/next image for later use
						// as prev/next Link in single view.
				      $single['linkToPreviousImage'] = '';
				      $single['linkToNextImage'    ] = '';

						if (($i-1) >= 0    ) {
							// set Link to previous image
							$link_param = array();
							$link_param['id'		] = $this->piVars['id'];
							$link_param['backpid'] = $this->piVars['backpid'];
							$link_param['page'	] = $this->piVars['page'];
							$link_param['single' ] = $thumb_ids[$i-1];
						    $link_text  = $this->cObj->stdWrap($this->pi_getLL('pi_list_browseresults_prev'),$this->conf['linkTextPrevious.']);
							
							// link marker content
					      $single['linkToPreviousPicture'] = $this->pi_linkTP_keepPIvars($link_text, $link_param, 1, 1, $GLOBALS["TSFE"]->id);

							// counter info marker content
					      $single['pictureXofY'          ] = '('. $link_param['single' ] .'/'. $end .')';

						} else	{
							// this is the first image
							$link_text  = $this->cObj->stdWrap($this->pi_getLL('pi_list_browseresults_first'),$this->conf['linkTextFirst.']);
							$single['linkToPreviousPicture'] = $link_text;
						}
						
			    		if (($i+1) < $end) {
							// set Link to next image
							$link_param = array();
							$link_param['id'		] = $this->piVars['id'];
							$link_param['backpid'] = $this->piVars['backpid'];
							$link_param['page'	] = $this->piVars['page'];
							$link_param['single' ] = $thumb_ids[$i+1];

							$link_text  = $this->cObj->stdWrap($this->pi_getLL('pi_list_browseresults_next'),$this->conf['linkTextNext.']); 
							$single['linkToNextPicture'] = $this->pi_linkTP_keepPIvars($link_text, $link_param, 1, 1, $GLOBALS["TSFE"]->id);

						} else	{
							// this is the last image
							$link_text  = $this->cObj->stdWrap($this->pi_getLL('pi_list_browseresults_last'),$this->conf['linkTextLast.']); 
					        $single['linkToNextPicture'] = $link_text;
						}


						// marker: pictureXofY
				      $single['NumberOfCurrentPicture'] = $i+1;
				      $single['TotalNumberOfPictures' ] = $end;
					  $single['Picture'] = $this->pi_getLL('pi_list_browseresults_picture');
					  $single['Of'] = $this->pi_getLL('pi_list_browseresults_of');
                      

						// marker: set Link to the thumbnails page
						$link_param = array();
						$link_param['id'		] = $this->piVars['id'];
						$link_param['backpid'] = $this->piVars['backpid'];
						$link_param['page'	] = $this->piVars['page'];
                        
                        $link_text  = $this->cObj->stdWrap($this->pi_getLL('pi_list_browseresults_up'),$this->conf['linkTextIndex.']);
						$single['linkToThumbnails'] = $this->pi_linkTP_keepPIvars($link_text, $link_param, 1, 1, $GLOBALS["TSFE"]->id);
                }
			}
		}
		
		
		$markerArray['###TITLE###']=$row['title'];
		$markerArray['###DESCRIPTION###']=$this->pi_RTEcssText($row['description']);
        if($this->conf['activateEcards']==1) {
            $linkWrapArray['###ECARDSINGLELINK###']=explode('|',$this->pi_linkToPage('|',$this->conf['eCards.']['viewPID'],'',array($this->prefixId.'[newecard]'=>$single['uid'],$this->prefixId.'[backpid]'=>$GLOBALS["TSFE"]->id,$this->prefixId.'[single]'=>$this->piVars['single'])));   
         
        } else {
            $subpartArray['###ECARDSINGLELINK###']='';
        }
        $markerArray['###BACKLINK###']=$this->piVars['backpid']>0 ? $this->pi_linkTP_keepPIvars($this->pi_getLL('back'),array(),1,1,$this->piVars['backpid']) : '';
		$markerArray['###PAGEBROWSER###']=$PB; 
		
		// Adds hook for processing of extra item markers
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['sk_simplegallery']['extraSingleMarkerHook'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['sk_simplegallery']['extraSingleMarkerHook'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$markerArray = $_procObj->extraSingleMarkerProcessor($markerArray, $thumb, $this);
			}
		}
        
		if(count($single)>0) {
			$this->conf['singleView.']['file']=$this->uploaddir.$single['picture'];
			$this->conf['singleView.']['params'] = $this->pi_classParam('image');
			$this->conf['singleView.']['altText'] = $single['title'];
			$this->conf['singleView.']['titleText'] = $single['title'];
			$this->conf['singleView.']['description'] = $this->pi_RTEcssText($single['description']);
            
            
            $markerArray['###SINGLEPICTURE###']=$this->ImageMarker($this->conf['singleView.'], $this->conf['thumbMode']);      
            
            
			$markerArray['###SINGLETITLE###']=$single['title'];
			$markerArray['###SINGLEDESCRIPTION###']=$this->pi_RTEcssText($single['description']);
			$singlecontent.=$this->cObj->substituteMarkerArrayCached($template['single'], $markerArray,array(),$subpartArray);
            
            $markerArray['###SINGLE_LINK_TO_PREVIOUS_PICTURE###'] = $single['linkToPreviousPicture' ];
			$markerArray['###SINGLE_LINK_TO_NEXT_PICTURE###'    ] = $single['linkToNextPicture'     ];
			$markerArray['###SINGLE_LINK_TO_THUMBNAILS###'      ] = $single['linkToThumbnails'      ];

			$markerArray['###SINGLE_NUMBER_OF_CURENT_PICTURE###'] = $single['NumberOfCurrentPicture'];
			$markerArray['###SINGLE_TOTAL_NUMBER_OF_PICTURES###'] = $single['TotalNumberOfPictures' ];
			$markerArray['###PICTURE###'                        ] = $single['Picture'];
			$markerArray['###OF###'                             ] = $single['Of'];
            
            
		} 
            
		
		$subpartArray['###THUMBLIST###']=$innercontent;
		$subpartArray['###SINGLEONE###']=$singlecontent;
        
        if($this->conf['singleLayout']==1 && !$this->piVars['single']) $subpartArray['###SINGLEPICTUREVIEW###']='';
        
		return $this->cObj->substituteMarkerArrayCached($template['total'], $markerArray,$subpartArray,$linkWrapArray);
		
	}
	
    function eCards() {
        #$content.='<pre>'.print_r($this->conf,true).'</pre>';
        #$content.='<pre>'.print_r($this->piVars,true).'</pre>';
        
        if($this->piVars['ecard']) 
            $content.=$this->showEcard();
        elseif ($this->piVars['newecard']) 
            $content.=$this->newEcard($this->piVars['newecard']);
        return $content;
    }
    
    function showEcard() {
        #$res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_sksimplegallery_ecards e,tx_sksimplegallery_pictures p', 'e.pic=p.uid and e.uid='.$this->piVars['ecard']);
        $res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_sksimplegallery_ecards', 'uid='.$this->piVars['ecard'].' and hidden=0 and deleted=0');
        
        $row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
        //t3lib_div::debug($row);
        
        $key=substr($row['picSrc'],0,strrpos($row['picSrc'],'.'));
        if($key!=$this->piVars['eckey']) return 'Wrong Ecard! '.$key.'|'.$this->piVars['eckey'];
        if(!is_file(PATH_site.'uploads/tx_sksimplegallery/ecards/'.$row['picSrc']))  {
            return 'Ecard doesn\'t exists';
        }
        
        $template['total'] = $this->cObj->getSubpart($this->template,'###ECARD###');
        
        $this->conf['singleView.']['file']=$this->uploaddir.$row['picture']; 
        $markerArray['###PICTURE###']='<img src="uploads/tx_sksimplegallery/ecards/'.$row['picSrc'].'" alt="eCard" title="eCard" />';    
        
        $markerArray['###DATE###']=date($this->conf['dateFormat'],$row['crdate']); #date($this->conf['dateFormat'],$row['crdate']);    
        $eConf['parameter']=$row['sendermail'];
        $markerArray['###FROM###']=$this->cObj->typoLink($row['sender'],$eConf); 
        $markerArray['###FROMEMAIL###']=$row['sendermail'];    
        $markerArray['###TO###']=$row['recipient'];    
        $markerArray['###TOEMAIL###']=$row['recipientmail'];    
        $markerArray['###TITLE###']=$row['subject'];    
        $markerArray['###MESSAGE###']=nl2br($row['message']);    
        
        
        return $this->cObj->substituteMarkerArrayCached($template['total'], $markerArray,$subpartArray,array());    
    }
    
    function newEcard($pic) {
        $sendMail=array();
        //check for lifetime of ecards
        $this->ecardLifetime();
        $res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_sksimplegallery_pictures','uid='.$pic);
        if($res) {
            $row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res); 
            $markerArray['###HIDDEN###']='
                <input type="hidden" name="'.$this->prefixId.'[backpid]" value="'.$this->piVars['backpid'].'" />
                <input type="hidden" name="'.$this->prefixId.'[single]" value="'.$this->piVars['single'].'" />
            ';
            $markerArray['###ERROR###']='';
            
            //Wurde gepostet ?
			if (isset($this->piVars['submit'])) {
                $err=array();
                
                #sender
                if(strlen($this->piVars['sender'])<$this->conf['eCards.']['senderMinChars']) {
					$err[]=sprintf($this->pi_getLL('ecard_sender_error'),intval($this->conf['eCards.']['senderMinChars']));
				}
                #sendermail
                if(!t3lib_div::validEmail($this->piVars['sendermail'])) {
                    $err[]=$this->pi_getLL('ecard_sendermail_error');
                }
                #recipient
                if(strlen($this->piVars['recipient'])<$this->conf['eCards.']['recipientMinChars']) {
					$err[]=sprintf($this->pi_getLL('ecard_recipient_error'),intval($this->conf['eCards.']['recipientMinChars']));
				}
                #recipientmail
                if(!t3lib_div::validEmail($this->piVars['recipientmail'])) {
                    $err[]=$this->pi_getLL('ecard_recipientmail_error');
                }
                #title
                if(strlen($this->piVars['title'])<$this->conf['eCards.']['titleMinChars']) {
					$err[]=sprintf($this->pi_getLL('ecard_title_error'),intval($this->conf['eCards.']['titleMinChars']));
				}
                #message
                if(strlen($this->piVars['message'])<$this->conf['eCards.']['messageMinChars']) {
					$err[]=sprintf($this->pi_getLL('ecard_message_error'),intval($this->conf['eCards.']['messageMinChars']));
				}
                
                #captcha response
                if (t3lib_extMgm::isLoaded('captcha') && $this->conf['useCaptcha'])	{
	                session_start();
	                if ($this->piVars['captchaResponse']!=$_SESSION['tx_captcha_string']) {
                       $err[]=$this->pi_getLL('captcha_error');    
                    }
	                $_SESSION['tx_captcha_string'] = '';
                }
                
                #freecap response
                if (t3lib_extMgm::isLoaded('sr_freecap') && !$this->conf['useCaptcha'] && $this->conf['useFreecap'] && is_object($this->freeCap) && !$this->freeCap->checkWord($this->piVars['captcha_response'])) {
                        $err[]=$this->pi_getLL('captcha_error');
                }
                
                if(count($err)>0) {
                    $markerArray['###ERROR###']=implode('<br />',$err);
                } else {
                    $markerArray['###ERROR###']='Prima!';
                    $doSendMail=true;
                }
            
            }
            $this->conf['singleView.']['file']=$this->uploaddir.$row['picture']; 
            $markerArray['###PICTURE###']=$this->ImageMarker($this->conf['singleView.']);    
            $pictureResource=$this->ImageMarker($this->conf['singleView.'],0,1);    
           
            $markerArray['###ECARDFORMLEGEND###']=$this->pi_getLL('ecard_new');
            $markerArray['###SENDER###']=$this->prefixId.'[sender]';
            $markerArray['###SENDERMAIL###']=$this->prefixId.'[sendermail]';
            $markerArray['###RECIPIENT###']=$this->prefixId.'[recipient]';
            $markerArray['###RECIPIENTMAIL###']=$this->prefixId.'[recipientmail]';
            $markerArray['###TITLE###']=$this->prefixId.'[title]';
            $markerArray['###MESSAGE###']=$this->prefixId.'[message]';
            $markerArray['###SUBMIT###']=$this->prefixId.'[submit]';
            
            $markerArray['###V_SENDER###']=$this->piVars['sender'];
            $markerArray['###V_SENDERMAIL###']=$this->piVars['sendermail'];
            $markerArray['###V_RECIPIENT###']=$this->piVars['recipient'];
            $markerArray['###V_RECIPIENTMAIL###']=$this->piVars['recipientmail'];
            $markerArray['###V_TITLE###']=$this->piVars['title'];
            $markerArray['###V_MESSAGE###']=$this->piVars['message'];
            $markerArray['###V_SUBMIT###']=$this->pi_getLL('ecard_submit');       
            
            $markerArray['###L_SENDER###']=$this->pi_getLL('ecard_sender');
            $markerArray['###L_SENDERMAIL###']=$this->pi_getLL('ecard_sendermail');
            $markerArray['###L_RECIPIENT###']=$this->pi_getLL('ecard_recipient');
            $markerArray['###L_RECIPIENTMAIL###']=$this->pi_getLL('ecard_recipientmail');
            
            $markerArray['###L_TITLE###']=$this->pi_getLL('ecard_title');
            $markerArray['###L_MESSAGE###']=$this->pi_getLL('ecard_message');
            $markerArray['###L_SENDER###']=$this->pi_getLL('ecard_sender');
            $markerArray['###L_CAPTCHA###']=$this->pi_getLL('captcha');
            
            
            #captcha
            if (t3lib_extMgm::isLoaded('captcha') && $this->conf['eCards.']['useCaptcha'])	{
	            $markerArray['###CAPTCHAINPUT###'] = '<input type="text" id="captcha" size=10 name="'.$this->prefixId.'[captchaResponse]" value="" />';
                $markerArray['###CAPTCHAPICTURE###'] = '<img src="'.t3lib_extMgm::siteRelPath('captcha').'captcha/captcha.php" alt="" />';
            } else {
	            $subpartArray['###CAPTCHA###'] = '';
            }
            
            #freecap
            if (t3lib_extMgm::isLoaded('sr_freecap') && !$this->conf['eCards.']['useCaptcha'] && $this->conf['eCards.']['useFreecap']) {
                $markerArray = array_merge($markerArray, $this->freeCap->makeCaptcha());
                $subpartArray['###CAPTCHA###'] = '';  
            } else {
                $subpartArray['###CAPTCHA_INSERT###'] = ''; 
            }
            
            //for the ecards
            $markerArray['###BACKLINK###']=$this->pi_linkTP($this->pi_getLL('back'),array($this->prefixId.'[single]'=>$this->piVars['single']),1,$this->piVars['backpid']);     
            
            if($doSendMail){
                $picNameString=md5($this->piVars['recipientmail']).'-'.$row['uid']; 
                $picName=$picNameString.substr($pictureResource,strrpos($pictureResource,'.'));
                $markerArray['###PICTURE_URL###']='<img src="'.t3lib_div::getIndpEnv('TYPO3_SITE_URL').'uploads/tx_sksimplegallery/ecards/'.$picName.'" title="ecard" />';            

                #insert in DB
                $insertArr = array(
                    'pid'=>intval($this->conf['eCards.']['storagePID']),
                    'tstamp'=>time(),
                    'crdate'=>time(),
                    'sender'=>$this->piVars['sender'],
                    'sendermail'=>$this->piVars['sendermail'],
                    'recipient'=>$this->piVars['recipient'],
                    'recipientmail'=>$this->piVars['recipientmail'],
                    'pic'=>$row['uid'],
                    'picSrc'=>$picName,
                    'subject'=>$this->piVars['title'],  
                    'message'=>$this->piVars['message'],  
                    
                );
                
                $res=$GLOBALS['TYPO3_DB']->exec_INSERTquery(
                  'tx_sksimplegallery_ecards',
                  $insertArr
                );
                
                $link=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_getPageLink($this->conf['eCards.']['viewPID'],'',array($this->prefixId.'[ecard]'=>mysql_insert_id(),$this->prefixId.'[eckey]'=>$picNameString));
                $markerArray['###LINK###']='<a href="'.$link.'">'.$link.'</a>';
                if(intval($this->conf['eCards.']['lifeTime'])>0) {  
                    $markerArray['###LIFETIME###']=sprintf($this->pi_getLL('ecard_linkWithLifetime'),$this->conf['eCards.']['lifeTime']);
                } else {
                    $markerArray['###LIFETIME###']=$this->pi_getLL('ecard_linkWithoutLifetime');
                }
                
                #copy picture
                copy(PATH_site.$pictureResource,PATH_site.'uploads/tx_sksimplegallery/ecards/'.$picName);
                #send the mail   
                $template['total'] = $this->cObj->getSubpart($this->template,'###ECARDMAIL###'); 
                $content=$this->cObj->substituteMarkerArrayCached($template['total'], $markerArray,$subpartArray,array());    
                
                require_once(PATH_t3lib.'class.t3lib_htmlmail.php'); 
                $this->htmlMail = t3lib_div::makeInstance('t3lib_htmlmail');
                $this->htmlMail->start();
                $this->htmlMail->recipient = $this->piVars['recipientmail'];
                $this->htmlMail->subject = $this->conf['eCards.']['subject'];
                $this->htmlMail->from_email = $this->piVars['sendermail'];
                $this->htmlMail->from_name = $this->piVars['sender'];
                $this->htmlMail->returnPath = $this->conf['eCards.']['returnEmail'];
                $this->htmlMail->addPlain($content);
                $this->htmlMail->setHTML($this->htmlMail->encodeMsg($content));
                $this->htmlMail->send($this->piVars['recipientmail']); 

                $markerArray['###ECARDSENDED###']=''; $content;
                #htmlspecialchars($pictureResource."|||".$picName.'|||'.$content);
                
                //show sucess
                $template['total'] = $this->cObj->getSubpart($this->template,'###ECARDSUCCESS###'); 
                return $content=$this->cObj->substituteMarkerArrayCached($template['total'], $markerArray,$subpartArray,array()); 
                
                
            } else {
                //show form
                $template['total'] = $this->cObj->getSubpart($this->template,'###ECARDFORM###'); 
                $content=$this->cObj->substituteMarkerArrayCached($template['total'], $markerArray,$subpartArray,array());    
                return $content;
            }
            
        } else {
            return 'picture doesn\'t exist.';
        }
    }
    
    function ecardLifetime() {
        if(intval($this->conf['eCards.']['lifeTime'])>0) {
            $res=$GLOBALS['TYPO3_DB']->exec_SELECTquery('*','tx_sksimplegallery_ecards','hidden=0 and deleted=0 and UNIX_TIMESTAMP(SUBDATE(NOW(),INTERVAL '.intval($this->conf['eCards.']['lifeTime']).' DAY))>crdate');
            if($res) {
                while($row=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
                    #delete picture
                    @unlink(PATH_site.$pictureResource,PATH_site.'uploads/tx_sksimplegallery/ecards/'.$row['picSrc']);
                     #delete record
                     $tmp=$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_sksimplegallery_ecards','uid='.$row['uid'],array('deleted'=>1));
                }
            }
        }    
    }
    
	function ImageMarker($lconf,$linkwrap=0,$resourceOnly=0) {
		#t3lib_div::debug($linkwrap,'linkwrap');
        if($linkwrap==1) {
			if($this->cObj->data['tx_kjimagelightbox2_imagelightbox2']==0) {
				if($this->conf['thumbMode']==1 || $lconf['linkMode']==1) {
					$lconf['imageLinkWrap'] = '1';
					$lconf['imageLinkWrap.'] = array(
						'JSwindow' => '1',
						'JSwindow.' => array('expand' => '0,'.($lconf['caption']!='' ? $this->conf['captionHeight']:'0'),),
						'enable' => '1',
						'title' => $lconf['titleText'],
						'bodyTag' => $this->conf['popupBodyTag'],
						'wrap' => '<a href="javascript:close();"> | </a>'.($lconf['caption']!='' ? $this->cObj->dataWrap($lconf['caption'],$this->conf['captionWrap']) : ''),

					);
				}
			}
		}
        if($this->conf['galEffects']!='') $lconf['file.']['params'].=' '.$this->conf['galEffects'];
        if($this->piVars['thumbeffect'])  $lconf['file.']['params']=$this->piVars['thumbeffect'];
		$this->cObj->data['imagecaption']=$this->caption;
		return $resourceOnly==0 ? $this->cObj->IMAGE($lconf) : $this->cObj->IMG_RESOURCE($lconf);
	}
    
    function getExifData($photo) {
		// http://nl.php.net/manual/en/function.exif-read-data.php
		// http://www.exif.org

		
		$data = get_EXIF_JPEG($photo);
		if ($data[0]) {
			if ($data[0][271]) {
				$exif['make'] = $data[0][271]['Text Value'];
				$exif['model'] = $data[0][272]['Text Value'];
				if ($data[0][34665]['Data'][0][33437]['Data'][0]['Denominator'])
					$exif['apertureF'] = 'f'.round($data[0][34665]['Data'][0][33437]['Data'][0]['Numerator']/$data[0][34665]['Data'][0][33437]['Data'][0]['Denominator'],1);
				$exif['digitalZoom'] = $data[0][34665]['Data'][0][41988]['Text Value'];
				$exif['exposureMode'] = $data[0][34665]['Data'][0][41986]['Text Value'];
				$exif['exposureTime'] = $data[0][34665]['Data'][0][33434]['Data'][0]['Numerator'].'/'.$data[0][34665]['Data'][0][33434]['Data'][0]['Denominator'].'s';
				$exif['flash'] = $data[0][34665]['Data'][0][37385]['Text Value'];
				$exif['flash'] = preg_replace('/,[^$]*/', '', $exif['flash']);
				if($data[0][34665]['Data'][0][37386]['Data'][0]['Denominator'])
					$exif['focalLength'] = round($data[0][34665]['Data'][0][37386]['Data'][0]['Numerator']/$data[0][34665]['Data'][0][37386]['Data'][0]['Denominator'],1).'mm';
				$exif['focalLength35mmFilm'] = $data[0][34665]['Data'][0][41989]['Text Value'];
				$exif['iso'] = $data[0][34665]['Data'][0][34855]['Data'][0].'iso';
				$exif['origX'] = $data[0][34665]['Data'][0][40962]['Data'][0];
				$exif['origY'] = $data[0][34665]['Data'][0][40963]['Data'][0];
				$exif['time'] = $data[0][34665]['Data'][0][36867]['Data'][0];
				$exif['whiteBalance'] = $data[0][34665]['Data'][0][41987]['Text Value'];
				return $exif;
			}
		}
	}
    
    function infoExifDiv($exif) {
        if($exif['make']) $d.=$this->cObj->stdWrap($this->pi_getLL('exif_make'),$this->conf['exifData.']['label.']).$this->cObj->stdWrap($exif['make'],$this->conf['exifData.']['data.']).'<br />';
        if($exif['model']) $d.=$this->cObj->stdWrap($this->pi_getLL('exif_model'),$this->conf['exifData.']['label.']).$this->cObj->stdWrap($exif['model'],$this->conf['exifData.']['data.']).'<br />';
        if($exif['digitalZoom']) $d.=$this->cObj->stdWrap($this->pi_getLL('exif_digitalZoom'),$this->conf['exifData.']['label.']).$this->cObj->stdWrap($exif['digitalZoom'],$this->conf['exifData.']['data.']).'<br />';
        if($exif['exposureMode']) $d.=$this->cObj->stdWrap($this->pi_getLL('exif_exposureMode'),$this->conf['exifData.']['label.']).$this->cObj->stdWrap($exif['exposureMode'],$this->conf['exifData.']['data.']).'<br />';
        if($exif['exposureTime']) $d.=$this->cObj->stdWrap($this->pi_getLL('exif_exposureTime'),$this->conf['exifData.']['label.']).$this->cObj->stdWrap($exif['exposureTime'],$this->conf['exifData.']['data.']).'<br />';
        if($exif['flash']) $d.=$this->cObj->stdWrap($this->pi_getLL('exif_flash'),$this->conf['exifData.']['label.']).$this->cObj->stdWrap($exif['flash'],$this->conf['exifData.']['data.']).'<br />';
        if($exif['focalLength35mmFilm']) $d.=$this->cObj->stdWrap($this->pi_getLL('exif_focalLength35mmFilm'),$this->conf['exifData.']['label.']).$this->cObj->stdWrap($exif['focalLength35mmFilm'],$this->conf['exifData.']['data.']).'<br />';
        if($exif['iso']) $d.=$this->cObj->stdWrap($this->pi_getLL('exif_iso'),$this->conf['exifData.']['label.']).$this->cObj->stdWrap($exif['iso'],$this->conf['exifData.']['data.']).'<br />';
        if($exif['origX']) $d.=$this->cObj->stdWrap($this->pi_getLL('exif_origX'),$this->conf['exifData.']['label.']).$this->cObj->stdWrap($exif['origX'],$this->conf['exifData.']['data.']).'<br />';
        if($exif['origY']) $d.=$this->cObj->stdWrap($this->pi_getLL('exif_origY'),$this->conf['exifData.']['label.']).$this->cObj->stdWrap($exif['origY'],$this->conf['exifData.']['data.']).'<br />';
        if($exif['time']) $d.=$this->cObj->stdWrap($this->pi_getLL('exif_time'),$this->conf['exifData.']['label.']).$this->cObj->stdWrap($exif['time'],$this->conf['exifData.']['data.']).'<br />';
        if($exif['whiteBalance']) $d.=$this->cObj->stdWrap($this->pi_getLL('exif_whiteBalance'),$this->conf['exifData.']['label.']).$this->cObj->stdWrap($exif['whiteBalance'],$this->conf['exifData.']['data.']).'<br />';
        if($exif['focalLength']) $d.=$this->cObj->stdWrap($this->pi_getLL('exif_focalLength'),$this->conf['exifData.']['label.']).$this->cObj->stdWrap($exif['focalLength'],$this->conf['exifData.']['data.']).'<br />';
        if($exif['apertureF']) $d.=$this->cObj->stdWrap($this->pi_getLL('exif_apertureF'),$this->conf['exifData.']['label.']).$this->cObj->stdWrap($exif['apertureF'],$this->conf['exifData.']['data.']).'<br />';
        
        return $d;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sk_simplegallery/pi1/class.tx_sksimplegallery_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sk_simplegallery/pi1/class.tx_sksimplegallery_pi1.php']);
}

?>
