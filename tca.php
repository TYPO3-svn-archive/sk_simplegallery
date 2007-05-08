<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_sksimplegallery_pictures"] = Array (
	"ctrl" => $TCA["tx_sksimplegallery_pictures"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,starttime,endtime,fe_group,title,description,picture,downloads"
	),
	"feInterface" => $TCA["tx_sksimplegallery_pictures"]["feInterface"],
	"columns" => Array (
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages',-1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => Array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
				),
				'foreign_table' => 'tx_sksimplegallery_pictures',
				'foreign_table_where' => 'AND tx_sksimplegallery_pictures.pid=###CURRENT_PID### AND tx_sksimplegallery_pictures.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (		
			'config' => Array (
				'type' => 'passthrough'
			)
		),
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"starttime" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.starttime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"default" => "0",
				"checkbox" => "0"
			)
		),
		"endtime" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.endtime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"checkbox" => "0",
				"default" => "0",
				"range" => Array (
					"upper" => mktime(0,0,0,12,31,2020),
					"lower" => mktime(0,0,0,date("m")-1,date("d"),date("Y"))
				)
			)
		),
		"fe_group" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.fe_group",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("", 0),
					Array("LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login", -1),
					Array("LLL:EXT:lang/locallang_general.xml:LGL.any_login", -2),
					Array("LLL:EXT:lang/locallang_general.xml:LGL.usergroups", "--div--")
				),
				"foreign_table" => "fe_groups"
			)
		),
		"title" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:sk_simplegallery/locallang_db.xml:tx_sksimplegallery_pictures.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"description" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:sk_simplegallery/locallang_db.xml:tx_sksimplegallery_pictures.description",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => Array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		"picture" => Array (		
			"exclude" => 0,		
            "l10n_mode" => 'exclude',
			"label" => "LLL:EXT:sk_simplegallery/locallang_db.xml:tx_sksimplegallery_pictures.picture",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => $GLOBALS["TYPO3_CONF_VARS"]["GFX"]["imagefile_ext"],	
				"max_size" => 5000,	
				"uploadfolder" => "uploads/tx_sksimplegallery",
				"show_thumbs" => 1,	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
        "downloads" => Array (        
        "exclude" => 1,
        "l10n_mode" => 'exclude',        
        "label" => "LLL:EXT:ddd/locallang_db.xml:tx_sksimplegallery_pictures.downloads",        
        "config" => Array (
            "type"     => "input",
            "size"     => "8",
            "max"      => "8",
            "eval"     => "int",
            "checkbox" => "0",
            "range"    => Array (
                "upper" => "1000000",
                "lower" => "0"
            ),
            "default" => 0
        )
    ),

	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, title;;;;2-2-2, description;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_sksimplegallery/rte/];3-3-3, picture,downloads")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "starttime, endtime, fe_group")
	)
);



$TCA["tx_sksimplegallery_galleries"] = Array (
	"ctrl" => $TCA["tx_sksimplegallery_galleries"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,starttime,endtime,fe_group,title,description,pictures,galpicture"
	),
	"feInterface" => $TCA["tx_sksimplegallery_galleries"]["feInterface"],
	"columns" => Array (
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages',-1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => Array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
				),
				'foreign_table' => 'tx_sksimplegallery_galleries',
				'foreign_table_where' => 'AND tx_sksimplegallery_galleries.pid=###CURRENT_PID### AND tx_sksimplegallery_galleries.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (		
			'config' => Array (
				'type' => 'passthrough'
			)
		),
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"starttime" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.starttime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"default" => "0",
				"checkbox" => "0"
			)
		),
		"endtime" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.endtime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"checkbox" => "0",
				"default" => "0",
				"range" => Array (
					"upper" => mktime(0,0,0,12,31,2020),
					"lower" => mktime(0,0,0,date("m")-1,date("d"),date("Y"))
				)
			)
		),
		"fe_group" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.xml:LGL.fe_group",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("", 0),
					Array("LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login", -1),
					Array("LLL:EXT:lang/locallang_general.xml:LGL.any_login", -2),
					Array("LLL:EXT:lang/locallang_general.xml:LGL.usergroups", "--div--")
				),
				"foreign_table" => "fe_groups"
			)
		),
		"title" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:sk_simplegallery/locallang_db.xml:tx_sksimplegallery_galleries.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
		"description" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:sk_simplegallery/locallang_db.xml:tx_sksimplegallery_galleries.description",		
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => Array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		"pictures" => Array (		
			"exclude" => 0,		
            "l10n_mode" => 'exclude',
			"label" => "LLL:EXT:sk_simplegallery/locallang_db.xml:tx_sksimplegallery_galleries.pictures",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "tx_sksimplegallery_pictures",	
				"size" => 10,	
				"minitems" => 0,
				"maxitems" => 500,
			)
		),
		"galpicture" => Array (		
			"exclude" => 0,		
            "l10n_mode" => 'exclude',
			"label" => "LLL:EXT:sk_simplegallery/locallang_db.xml:tx_sksimplegallery_galleries.galpicture",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "db",	
				"allowed" => "tx_sksimplegallery_pictures",	
				"show_thumbs" => 1,	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
        "altgalpicture" => Array (		
			"exclude" => 0,		
            "l10n_mode" => 'exclude',
			"label" => "LLL:EXT:sk_simplegallery/locallang_db.xml:tx_sksimplegallery_galleries.altgalpicture",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => $GLOBALS["TYPO3_CONF_VARS"]["GFX"]["imagefile_ext"],	
				"max_size" => 1000,	
				"uploadfolder" => "uploads/tx_sksimplegallery",
				"show_thumbs" => 1,	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
        
	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, title;;;;2-2-2, description;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_sksimplegallery/rte/];3-3-3, pictures, galpicture,altgalpicture")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "starttime, endtime, fe_group")
	)
);

$TCA["tx_sksimplegallery_ecards"] = array (
    "ctrl" => $TCA["tx_sksimplegallery_ecards"]["ctrl"],
    "interface" => array (
        "showRecordFieldList" => "hidden,sender,sendermail,receiver,receivermail,pic,subject,message"
    ),
    "feInterface" => $TCA["tx_sksimplegallery_ecards"]["feInterface"],
    "columns" => array (
        'hidden' => array (        
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array (
                'type'    => 'check',
                'default' => '0'
            )
        ),
        "sender" => Array (        
            "exclude" => 0,        
            "label" => "LLL:EXT:sk_simplegallery/locallang_db.xml:tx_sksimplegallery_ecards.sender",        
            "config" => Array (
                "type" => "input",    
                "size" => "30",
            )
        ),
        "sendermail" => Array (        
            "exclude" => 1,        
            "label" => "LLL:EXT:sk_simplegallery/locallang_db.xml:tx_sksimplegallery_ecards.sendermail",        
            "config" => Array (
                "type" => "input",    
                "size" => "30",
            )
        ),
        "recipient" => Array (        
            "exclude" => 1,        
            "label" => "LLL:EXT:sk_simplegallery/locallang_db.xml:tx_sksimplegallery_ecards.recipient",        
            "config" => Array (
                "type" => "input",    
                "size" => "30",
            )
        ),
        "recipientmail" => Array (        
            "exclude" => 0,        
            "label" => "LLL:EXT:sk_simplegallery/locallang_db.xml:tx_sksimplegallery_ecards.recipientmail",        
            "config" => Array (
                "type" => "input",    
                "size" => "30",
            )
        ),
        "pic" => Array (        
            "exclude" => 0,        
            "label" => "LLL:EXT:sk_simplegallery/locallang_db.xml:tx_sksimplegallery_ecards.pic",        
            "config" => Array (
                "type" => "group",    
                "internal_type" => "db", 
                "show_thumbs" => 1,	   
                "allowed" => "tx_sksimplegallery_pictures",    
                "size" => 1,    
                "minitems" => 0,
                "maxitems" => 1,
            )
        ),
        "subject" => Array (        
            "exclude" => 1,        
            "label" => "LLL:EXT:sk_simplegallery/locallang_db.xml:tx_sksimplegallery_ecards.subject",        
            "config" => Array (
                "type" => "input",    
                "size" => "80",
            )
        ),
        "message" => Array (        
            "exclude" => 1,        
            "label" => "LLL:EXT:sk_simplegallery/locallang_db.xml:tx_sksimplegallery_ecards.message",        
            "config" => Array (
                "type" => "text",
                "cols" => "50",    
                "rows" => "10",
            )
        ),
        
    ),
    "types" => array (
        "0" => array("showitem" => "hidden;;1;;1-1-1, sender, sendermail, recipient, recipientmail, pic, subject, message")
    ),
    "palettes" => array (
        "1" => array("showitem" => "")
    )
);

?>
