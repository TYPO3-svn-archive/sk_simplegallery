<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006  <>
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
 * Module extension (addition to function menu) 'Simple Gallery Import' for the 'sk_simplegallery' extension.
 *
 * @author     <>
 */



require_once(PATH_t3lib.'class.t3lib_extobjbase.php');
require_once (PATH_t3lib.'class.t3lib_basicfilefunc.php');
require_once (PATH_t3lib.'class.t3lib_stdgraphic.php');

class tx_sksimplegallery_modfunc1 extends t3lib_extobjbase {

    var $filefunc='';
		
    /**
     * Main method of the module
     *
     * @return    HTML
     */
    function main()    {
            // Initializes the module. Done in this function because we may need to re-initialize if data is submitted!
        global $SOBE,$BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		
        /*
		if(!t3lib_div::_GP('uploadfiles')) 
            $theOutput.='<h3>Upload Images</h3><input type="submit" name="uploadfiles" value="Multi-Upload (upload several images at once)" /><br />'; 
        else
            $theOutput.='<h3>Upload Images</h3><input type="submit" name="import" value="Import (from directory)" /><br />';
        */
         
        $theOutput.=$this->pObj->doc->spacer(15);
        $theOutput.=$this->pObj->doc->section($LANG->getLL("title"),str_replace('|','<br />',$LANG->getLL("importdesc")),0,1);
		
		//load Filefuncs
		$this->fileFunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
		$this->fileFunc->init($GLOBALS['FILEMOUNTS'], $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']);
        
		$imgObj = t3lib_div::makeInstance('t3lib_stdGraphic');
		$imgObj->init();
		$imgObj->mayScaleUp=0;
		$imgObj->tempPath=PATH_site.$imgObj->tempPath;
			
			
		//get data-get
		$data = t3lib_div::_GP('data');
		
		//switch step
		if(t3lib_div::_GP('create1')) {
			#SELECT IMAGES FOR IMPORT
			//check importfolder
			if(substr($data['picpath'],-1)!='/' && $data['picpath']!='') $data['picpath'].='/';
			$importfolder=PATH_site.$data['picpath'];
			$importpathinfo=$this->fileFunc->getTotalFileInfo($importfolder);
			$files=t3lib_div::getFilesInDir($importfolder,'gif,jpg,png');
			
			$out.='<h3>'.$LANG->getLL("imagesinfolder").'</h3><table>';
			foreach($files as $f=>$v) {
				$filepath=$importfolder.$v;
				$imgInfo = $imgObj->getImageDimensions($filepath);
				$pDim = $imgInfo[0].'x'.$imgInfo[1].' pixels';
				$out.='<tr>';
				$out.='<td><input type="checkbox" name="file['.$v.']" value="1" checked="checked" /></td>';
				if($data['showthumbs']) $out.='<td>'.t3lib_BEfunc::getThumbNail($BACK_PATH.'thumbs.php',$filepath,'hspace="5" vspace="5" border="1"','80').'</td>';
				$out.='<td>'.$v.'</td>';
				$out.='<td>'.$pDim.' ('.t3lib_div::formatSize(filesize($importfolder.$v)).'bytes)</td>';
				$out.='</tr>';
			}
			$out.='</table>';
			
			$out.='<input type="hidden" name="data[gal_name]" value="'.$data['gal_name'].'" />
			<input type="hidden" name="data[picpath]" value="'.$data['picpath'].'" />';
			$out.='<input type="radio" name="data[title]" value="1" checked="checked" />&nbsp;&nbsp;'.$LANG->getLL("titlefilename").'<br />
			<input type="radio" name="data[title]" value="2" />&nbsp;&nbsp;'.$LANG->getLL("titlenumbers").'<br />
			'.$LANG->getLL("usepraefix").':&nbsp;&nbsp;<input type="text" name="data[titlepraefix]" value="" />';
			$out.='<br /><br /><input type="submit" name="create2" value="'.$LANG->getLL("importimages").'" />&nbsp;&nbsp;&nbsp;
			<input type="submit" name="create0" value="'.$LANG->getLL("backstep1").'" />';
		} elseif(t3lib_div::_GP('create2')) {
			#DO THE IMPORT
			$files=t3lib_div::_GP('file');
			$galtitle=$data['gal_name']?$data['gal_name']:'***new gallery***'; 
			$picIDs=array();
			$i=1;
			foreach($files as $fname=>$val) {
				$importfolder=PATH_site.$data['picpath'];
				if($val==1) {
					$newfname=$this->fileFunc->getUniqueName($fname,PATH_site.'uploads/tx_sksimplegallery');
					copy($importfolder.$fname,$newfname);
					if($data['title']==1) {
						$title=basename($newfname);
                        $title=substr($title,0,strrpos($title,'.'));
					} else {
						$title=str_pad($i,3,'0',STR_PAD_LEFT);
					}
					$title=trim($data['titlepraefix'].$title);
					$picIDs[]=$this->createGalleryPicRecord(basename($newfname),$title);
					if($i==1) $galpicture=$picIDs[0]; #basename($newfname); 
                    $out.=sprintf($LANG->getLL("importmsg1"),$fname,$title,basename($newfname)).'<br />';
					$i++;
				} 
			}
			//create Gallery Record
			if($i>0) {
				$this->createGalleryRecord($galtitle,implode(',',$picIDs),$galpicture);
				$out.='<br /><b>'.sprintf($LANG->getLL("importmsg2"),$galtitle,$galpicture).'</b><br />';
			} else {
				$out.='<input type="hidden" name="data[gal_name]" value="'.$data['gal_name'].'" />
			<input type="hidden" name="data[picpath]" value="'.$data['picpath'].'" />';
				$out.=$LANG->getLL("noimport");
			}
			$out.='<br /><br /><input type="submit" name="create0" value="'.$LANG->getLL("backstep1").'" />';
		} elseif(t3lib_div::_GP('uploadfiles')) {
            $out.='';
        	
		} else {
			
            $out.=$LANG->getLL("nameofgallery").':<br /><input type="text" name="data[gal_name]"'.$this->pObj->doc->formWidth(30).' value="'.$data['gal_name'].'" /><br />';
			$out.=sprintf($LANG->getLL("imagepath"),PATH_site).'<br /><input type="text" name="data[picpath]"'.$this->pObj->doc->formWidth(30).' value="'.$data['picpath'].'" /><br />';
			$out.='<input type="checkbox" value="1" checked="checked" name="data[showthumbs]" />&nbsp;&nbsp;'.$LANG->getLL("showthumbs").'<br />';
			$out.='<br /><input type="submit" name="create1" value="'.$LANG->getLL("step1").'" />';
		}
	    $theOutput.=$this->pObj->doc->spacer(5);
        $theOutput.=$this->pObj->doc->section($LANG->getLL("importtogallery"),$out,0,1);
        return $theOutput;
    }
	
	function createGalleryPicRecord($fname,$title) {
		global $BE_USER;
		$insert=Array(
			'pid'=>$this->pObj->id,
			'cruser_id'=>$BE_USER->user['uid'],
			'picture'=>$fname,
			'title'=>$title,
			'tstamp'=>time(),
			'crdate'=>time(),
		);
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_sksimplegallery_pictures',$insert);
		return mysql_insert_id();
	}
	function createGalleryRecord($title,$ids,$galpicture) {
		global $BE_USER;
		$insert=Array(
			'pid'=>$this->pObj->id,
			'cruser_id'=>$BE_USER->user['uid'],
			'pictures'=>$ids,
			'galpicture'=>$galpicture,
			'title'=>$title,
			'tstamp'=>time(),
			'crdate'=>time(),
		);
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_sksimplegallery_galleries',$insert);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sk_simplegallery/modfunc1/class.tx_sksimplegallery_modfunc1.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sk_simplegallery/modfunc1/class.tx_sksimplegallery_modfunc1.php']);
}

?>
