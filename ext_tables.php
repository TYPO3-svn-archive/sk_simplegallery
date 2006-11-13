<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::allowTableOnStandardPages("tx_sksimplegallery_pictures");


t3lib_extMgm::addToInsertRecords("tx_sksimplegallery_pictures");

$TCA["tx_sksimplegallery_pictures"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:sk_simplegallery/locallang_db.xml:tx_sksimplegallery_pictures',		
		'label' => 'title',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'versioningWS' => TRUE, 
        "thumbnail" => "picture", 
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',	
		'transOrigPointerField' => 'l18n_parent',	
		'transOrigDiffSourceField' => 'l18n_diffsource',	
		"default_sortby" => "ORDER BY crdate",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",	
			"starttime" => "starttime",	
			"endtime" => "endtime",	
			"fe_group" => "fe_group",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_sksimplegallery_pictures.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, starttime, endtime, fe_group, title, description, picture",
	)
);


t3lib_extMgm::allowTableOnStandardPages("tx_sksimplegallery_galleries");


t3lib_extMgm::addToInsertRecords("tx_sksimplegallery_galleries");

$TCA["tx_sksimplegallery_galleries"] = Array (
	"ctrl" => Array (
		'title' => 'LLL:EXT:sk_simplegallery/locallang_db.xml:tx_sksimplegallery_galleries',		
		'label' => 'title',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'versioningWS' => TRUE, 
        "thumbnail" => "galpicture", 
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',	
		'transOrigPointerField' => 'l18n_parent',	
		'transOrigDiffSourceField' => 'l18n_diffsource',	
		"default_sortby" => "ORDER BY crdate",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",	
			"starttime" => "starttime",	
			"endtime" => "endtime",	
			"fe_group" => "fe_group",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_sksimplegallery_galleries.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, starttime, endtime, fe_group, title, description, pictures, galpicture",
	)
);


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform,tx_kjimagelightbox2_imagelightbox2,tx_kjimagelightbox2_imageset';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1','FILE:EXT:'.$_EXTKEY.'/flexform.xml');

t3lib_extMgm::addPlugin(Array('LLL:EXT:sk_simplegallery/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","Simple Gallery Setup");
t3lib_extMgm::addStaticFile($_EXTKEY,'pi1/static/css/','Simple Gallery CSS');

if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_sksimplegallery_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_sksimplegallery_pi1_wizicon.php';

if (TYPO3_MODE=="BE")    {
    include_once(t3lib_extMgm::extPath($_EXTKEY).'class.tx_sksimplegallery_effects.php'); 
    
   /* t3lib_extMgm::insertModuleFunction(
        "web_func",        
        "tx_sksimplegallery_modfunc1",
        t3lib_extMgm::extPath($_EXTKEY)."modfunc1/class.tx_sksimplegallery_modfunc1.php",
        "LLL:EXT:sk_simplegallery/locallang_db.xml:moduleFunction.tx_sksimplegallery_modfunc1",
        "wiz"    
    );*/
    t3lib_extMgm::addModule("web","txsksimplegalleryM1","",t3lib_extMgm::extPath($_EXTKEY)."mod1/");    
    
}


?>
