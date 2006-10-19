<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Steffen Kamper <steffen@sk-typo3.de>
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
 * Module 'Softlinks' for the 'sk_softlinks' extension.
 *
 * @author	Steffen Kamper <steffen@sk-typo3.de>
 */



	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require_once("conf.php");
require_once($BACK_PATH."init.php");
require_once($BACK_PATH."template.php");
$LANG->includeLLFile("EXT:sk_simplegallery/mod1/locallang.xml");
require_once(PATH_t3lib."class.t3lib_scbase.php");
require_once (PATH_t3lib.'class.t3lib_basicfilefunc.php');
require_once (PATH_t3lib.'class.t3lib_stdgraphic.php');
require_once (PATH_t3lib.'class.t3lib_tceforms.php'); 

$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

class tx_sksimplegallery_module1 extends t3lib_SCbase {
	var $pageinfo;

	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		parent::init();

		/*
		if (t3lib_div::_GP("clear_all_cache"))	{
			$this->include_once[]=PATH_t3lib."class.t3lib_tcemain.php";
		}
		*/
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig()	{
		global $LANG;
		$this->MOD_MENU = Array (
			"function" => Array (
				"1" => $LANG->getLL("function1"),
			)
		);
		parent::menuConfig();
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	[type]		...
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

        
        
		if (($this->id && $access) || ($BE_USER->user["admin"] && !$this->id))	{

            
            //load Filefuncs
		    $this->fileFunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
		    $this->fileFunc->init($GLOBALS['FILEMOUNTS'], $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']);
            
		    $imgObj = t3lib_div::makeInstance('t3lib_stdGraphic');
		    $imgObj->init();
		    $imgObj->mayScaleUp=0;
		    $imgObj->tempPath=PATH_site.$imgObj->tempPath;
			 
            #$this->content.=$this->elementBrowser();
			    
		    //get data-get
		    $this->data = t3lib_div::_GP('data');
            $this->post=t3lib_div::_GP('tx_sksoftlinks');     
            
            debug($this->post);
            if($this->post['browse']) {
                return $this->elementBrowser();
            }    
        
				// Draw the header.
			$this->doc = t3lib_div::makeInstance("mediumDoc");
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form action="" method="POST">';

				// JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
				</script>
			';
			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = 0;
				</script>
			';

			$headerSection = $this->doc->getHeader("pages",$this->pageinfo,$this->pageinfo["_thePath"])."<br />".$LANG->sL("LLL:EXT:lang/locallang_core.xml:labels.path").": ".t3lib_div::fixed_lgd_pre($this->pageinfo["_thePath"],50);

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->section("",$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,"SET[function]",$this->MOD_SETTINGS["function"],$this->MOD_MENU["function"])));
			$this->content.=$this->doc->divider(5);


			// Render content:
			$this->moduleContent();


			// ShortCut
			if ($BE_USER->mayMakeShortcut())	{
				$this->content.=$this->doc->spacer(20).$this->doc->section("",$this->doc->makeShortcutIcon("id",implode(",",array_keys($this->MOD_MENU)),$this->MCONF["name"]));
			}

			$this->content.=$this->doc->spacer(10);
		} else {
				// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance("mediumDoc");
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent()	{

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	void
	 */
	function moduleContent()	{
        global $LANG;
		
        switch((string)$this->MOD_SETTINGS["function"])	{
			case 1:
                $content= $this->importFromDirectory();
			    $this->content.=$this->doc->section("Import from Directory:",$content,0,1);  	
			break;
			
		}
	}
    
    function importFromDirectory() {
        global $LANG;
        
        
         $test='<a href="#" onclick="setFormValueOpenBrowser(\'file\',\'data[tx_skplants_products][0][picture]|||gif,jpg,jpeg,tif,bmp,pcx,tga,png,pdf,ai|\'); return false;"><img src="../typo3conf/ext/daimi_skin/icons/gfx/insert3.gif" width="15" height="15" border="0"  alt="Durch Dateien browsen" title="Durch Dateien browsen" /></a>';
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
			
            $out.=$LANG->getLL("nameofgallery").':<br /><input type="text" name="data[gal_name]"'.$this->doc->formWidth(30).' value="'.$data['gal_name'].'" />'.$test.'<br />';
			$out.=sprintf($LANG->getLL("imagepath"),PATH_site).'<br /><input type="text" name="data[picpath]"'.$this->doc->formWidth(30).' value="'.$data['picpath'].'" /><br />';
			$out.='<input type="checkbox" value="1" checked="checked" name="data[showthumbs]" />&nbsp;&nbsp;'.$LANG->getLL("showthumbs").'<br />';
			$out.='<br /><input type="submit" name="create1" value="'.$LANG->getLL("step1").'" />';
		}
	    $theOutput.=$this->doc->spacer(5);
        $theOutput.=$this->doc->section($LANG->getLL("importtogallery"),$out,0,1);
        return $theOutput;
    }
    
    
    
    ################################################
    #####  some Helpers  ###########################
    ################################################
    
    function elementBrowser() {
        $config['itemFormElName'] = 'data[' . $cfg['uid'] . '][values][' .
        $languid . '][0]';
        $config['fieldConf']['config']['internal_type'] = 'file';
        $config['fieldConf']['config']['allowed'] = $cfg['allowed'];
        $config['fieldConf']['config']['disallowed'] = $cfg['disallowed'];
        $config['fieldConf']['config']['size'] = $cfg['files'];
        $config['fieldConf']['config']['show_thumbs'] = 0;
        $config['fieldConf']['config']['uploadfolder'] = 'uploads/prodb/' .
        $cfg['uploadpath'];
        $config['itemFormElValue'] = $data[0] ? $data[0] : ' ';

        $form = t3lib_div::makeInstance('t3lib_TCEforms');
        $form->initDefaultBEMode();


        $popform = $form->getSingleField_typeGroup( 'tx_bgmprodb_record', 'upload',
        array(), $config );

        
                                    
        $out =  $form->JStop();
        $out .= $popform;
        $out .= $form->JSbottom();
        return $out;

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
    
    function loadTS($pageUid) {
            $sysPageObj = t3lib_div::makeInstance('t3lib_pageSelect');
            $rootLine = $sysPageObj->getRootLine($pageUid);
            $TSObj = t3lib_div::makeInstance('t3lib_tsparser_ext');
            $TSObj->tt_track = 0;
            $TSObj->init();
            $TSObj->runThroughTemplates($rootLine);
            $TSObj->generateConfig();
            $this->conf = $TSObj->setup['plugin.']['tx_sksoftlinks_pi1.'];
    }
        
        
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sk_softlinks/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sk_softlinks/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_sksimplegallery_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>
