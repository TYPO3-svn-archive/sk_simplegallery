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
       
       
		// parse XML data into php array
		$this->pi_initPIflexForm(); 
		
		//Flexform Values
		//view
		$this->conf['view'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'view_input', 'sVIEW');
		#echo "VIEW: ".$this->conf['view'];
		$templateFile = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'template_file', 'sVIEW');
		if($$templateFile=='')
			$this->conf['templateFile'] = $this->conf['templateFile']=='' ? 'typo3conf/ext/'.$this->extKey.'/pi1/template.html' : $this->conf['templateFile'];
		else
			$this->conf['templateFile'] =$templateFile;
		
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
		if($tmp!='') $this->conf['thumbMode']=$tmp;
		
		$tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'singlePic_maxW', 'sSingle');
		if($tmp!='') $this->conf['singleView.']['file.']['maxW']=$tmp;
		$tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'singlePic_maxH', 'sSingle');
		if($tmp!='') $this->conf['singleView.']['file.']['maxH']=$tmp;
		
        $tmp=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'singleID', 'sSingle');
		if($tmp!='') $this->conf['singleView.']['singleID']=$tmp;
        
        
        
        
        
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
        
        
		$query = $GLOBALS['TYPO3_DB']->SELECTquery(
                '*',         // SELECT ...
                'tx_sksimplegallery_galleries',     // FROM ...
                'pid in ('.$this->pidList.') and deleted=0 and hidden=0',    // WHERE...
                '',            // GROUP BY...
                'title',    // ORDER BY...
                $limit            // LIMIT ...
            );
		$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
		while($temp = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			//prepare picture
			$this->conf['listView.']['file']=$temp['altgalpicture'] ? $this->uploaddir.$temp['altgalpicture'] : $this->uploaddir.$this->getGalpicture($temp['galpicture']); 
			$this->conf['listView.']['params'] = $this->pi_classParam('image');
			$this->conf['listView.']['altText'] = $temp['title'];
			$this->conf['listView.']['titleText'] = $temp['title'];
			$markerArray['###PICTURE###']=$this->ImageMarker($this->conf['listView.'],true);
			$imginfo=$GLOBALS['TSFE']->lastImageInfo;
			$markerArray['###WIDTH###']='style="width:'.$imginfo[0].'px;"';
			$markerArray['###TITLE###']=$temp['title'];
			$subpartArray['###LINK_ITEM###']= explode('|',$this->pi_linkToPage('|',$this->conf['singlePID'],'',array($this->prefixId.'[id]'=>$temp['uid'],$this->prefixId.'[backpid]'=>$GLOBALS["TSFE"]->id)));
			$innercontent.=$this->cObj->substituteMarkerArrayCached($template['item'], $markerArray,array(),$subpartArray);
		}
        $markerArray=array();
		$markerArray['###PAGEBROWSER###']=$PB; 
		$subpartArray['###LIST###']=$innercontent;
		return $this->cObj->substituteMarkerArrayCached($template['total'], $markerArray,$subpartArray,array());
	}
	
    function getGalpicture($uid) {
       $query = $GLOBALS['TYPO3_DB']->SELECTquery(
                'picture',         // SELECT ...
                'tx_sksimplegallery_pictures',     // FROM ...
                'uid='.$uid.' and hidden=0 and deleted=0',    // WHERE...
                '',            // GROUP BY...
                '',    // ORDER BY...
                ''            // LIMIT ...
            );
       $res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query); 
       $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
       return $row['picture'];
    }
    
	function SingleGallery() {
		$template['total'] = $this->cObj->getSubpart($this->template,'###SINGLEVIEW###');
		$template['item'] = $this->cObj->getSubpart($template['total'],'###THUMBLIST###');
		$template['single'] = $this->cObj->getSubpart($template['total'],'###SINGLEONE###');
		$innercontent=$singlecontent=$cap='';
		$single=array();
		if(!isset($this->piVars['id'])) $this->piVars['id']=$this->conf['singleView.']['singleID']; 
		$page=intval($this->piVars['page']); 
        
        // get Data
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_sksimplegallery_galleries', 'uid='.$this->piVars['id']); 
        if(!$res) {
			return "no data for this view";
			break;
		}
        $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res); 
        $thumb_ids=explode(',',$row['pictures']); 
        $count=count($thumb_ids);
        
        //PB
        $PB=''; 
        // initialize loop params
        $start=0;
        $end=$count;
        // calculate Pagebrowser
		if(intval($this->conf['singlePageRecords'])>0 && $count>intval($this->conf['singlePageRecords'])) {
			$maxpages=ceil($count/intval($this->conf['singlePageRecords']));
			$start=$page*$this->conf['singlePageRecords'];
			$end=$start+$this->conf['singlePageRecords']>$count?$count:$start+$this->conf['singlePageRecords'];
			$PB.='<p class="pagebrowser">'.$this->pi_getLL('pi_list_browseresults_page','Page').' ';
			for($i=0;$i<$maxpages;$i++) {
				$PB.=($i==$page ? '<span class="active">'.($i+1) : '<span><a href="'.$this->pi_linkTP_keepPIvars_url(array('page'=>$i),1,0,$GLOBALS['TSFE']->id).'">'.($i+1).'</a>').'</span>';
			}
			$PB.='</p>';
		}
        
		$this->caption='';
		for($i=$start;$i<$end;$i++) {
			$query = $GLOBALS['TYPO3_DB']->SELECTquery(
                '*',         // SELECT ...
                'tx_sksimplegallery_pictures',     // FROM ...
                'uid='.$thumb_ids[$i].' and hidden=0 and deleted=0',    // WHERE...
                '',            // GROUP BY...
                '',    // ORDER BY...
                ''            // LIMIT ...
            );
			$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
			$thumb = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			if($thumb) {
			    $this->conf['thumbView.']['file']=$this->uploaddir.$thumb['picture'];
			    $this->conf['thumbView.']['params'] = $this->pi_classParam('image');
			    $this->conf['thumbView.']['altText'] = $thumb['title'];
			    $this->conf['thumbView.']['titleText'] = $thumb['title'];
			    $this->conf['thumbView.']['caption'] = $thumb['description'] ? strip_tags($thumb['description']) : $thumb[$this->config['popupAltDescriptionField']];
			    $this->caption.=$thumb['title']."\n";
			    
                $exif = $this->getExifData($this->uploaddir.$thumb['picture']);
                $markerArray['###EXIF###']=is_array($exif) ? $this->infoExifDiv($exif) : ''; 
			    $markerArray['###THUMB###']=$this->ImageMarker($this->conf['thumbView.'],($this->cObj->data['tx_kjimagelightbox2_imagelightbox2']==1));
			    $imginfo=$GLOBALS['TSFE']->lastImageInfo;
			    $markerArray['###WIDTH###']='style="width:'.$imginfo[0].'px;"';
			    if($this->cObj->data['tx_kjimagelightbox2_imagelightbox2']==0) {
				    if($this->conf['thumbMode']==0) {
					    $markerArray['###THUMB###']=$this->pi_linkTP_keepPIvars($markerArray['###THUMB###'],array('id'=>$this->piVars['id'],'backpid'=>$this->piVars['backpid'],'page'=>$this->piVars['page'],'single'=>$thumb['uid']),1,1,$GLOBALS["TSFE"]->id);
				    }
			    }
			    $markerArray['###THUMBTITLE###']=$thumb['title'];
			    $innercontent.=$this->cObj->substituteMarkerArrayCached($template['item'], $markerArray,array(),array());
			    if($thumb_ids[$i]==$this->piVars['single']) $single=$thumb;
			}
		}
		
		
		$markerArray['###TITLE###']=$row['title'];
		$markerArray['###DESCRIPTION###']=$this->pi_RTEcssText($row['description']);
		$markerArray['###BACKLINK###']=$this->piVars['backpid']>0 ? $this->pi_linkTP_keepPIvars($this->pi_getLL('back'),array(),1,1,$this->piVars['backpid']) : '';
		$markerArray['###PAGEBROWSER###']=$PB; 
		
		
		if(count($single)>0) {
			$this->conf['singleView.']['file']=$this->uploaddir.$single['picture'];
			$this->conf['singleView.']['params'] = $this->pi_classParam('image');
			$this->conf['singleView.']['altText'] = $single['title'];
			$this->conf['singleView.']['titleText'] = $single['title'];
			$this->conf['singleView.']['description'] = $this->pi_RTEcssText($single['description']);
			$markerArray['###SINGLEPICTURE###']=$this->ImageMarker($this->conf['singleView.']);
			$markerArray['###SINGLETITLE###']=$single['title'];
			$markerArray['###SINGLEDESCRIPTION###']=$this->pi_RTEcssText($single['description']);
			$singlecontent.=$this->cObj->substituteMarkerArrayCached($template['single'], $markerArray,array(),$subpartArray);
		}
		
		$subpartArray['###THUMBLIST###']=$innercontent;
		$subpartArray['###SINGLEONE###']=$singlecontent;
		return $this->cObj->substituteMarkerArrayCached($template['total'], $markerArray,$subpartArray,array());
		
	}
	
	function ImageMarker($lconf,$nolinkwrap=false) {
		if(!$nolinkwrap) {
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
		return $this->cObj->IMAGE($lconf);
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
        $d.='Date:'.$exif['make'].'<br />';
        $d.='Model:'.$exif['model'].'<br />';
        $d.='digitalZoom:'.$exif['digitalZoom'].'<br />';
        $d.='exposureMode:'.$exif['exposureMode'].'<br />';
        $d.='exposureTime:'.$exif['exposureTime'].'<br />';
        $d.='flash:'.$exif['flash'].'<br />';
        $d.='focalLength35mmFilm:'.$exif['focalLength35mmFilm'].'<br />';
        $d.='iso:'.$exif['iso'].'<br />';
        $d.='origX:'.$exif['origX'].'<br />';
        $d.='origY:'.$exif['origY'].'<br />';
        $d.='time:'.$exif['time'].'<br />';
        $d.='whiteBalance:'.$exif['whiteBalance'].'<br />';
        
        if($exif['focalLength']) $d.='focalLength:'.$exif['focalLength'].'<br />';
        if($exif['apertureF']) $d.='apertureF:'.$exif['apertureF'].'<br />';
        
        return $d;
    }
    
    
    
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sk_simplegallery/pi1/class.tx_sksimplegallery_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sk_simplegallery/pi1/class.tx_sksimplegallery_pi1.php']);
}

?>
