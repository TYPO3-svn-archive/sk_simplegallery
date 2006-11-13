<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_sksimplegallery_pictures=1
');
t3lib_extMgm::addPageTSConfig('

	# ***************************************************************************************
	# CONFIGURATION of RTE in table "tx_sksimplegallery_pictures", field "description"
	# ***************************************************************************************
RTE.config.tx_sksimplegallery_pictures.description {
  hidePStyleItems = H1, H4, H5, H6
  proc.exitHTMLparser_db=1
  proc.exitHTMLparser_db {
    keepNonMatchedTags=1
    tags.font.allowedAttribs= color
    tags.font.rmTagIfNoAttrib = 1
    tags.font.nesting = global
  }
}
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_sksimplegallery_galleries=1
');
t3lib_extMgm::addPageTSConfig('

	# ***************************************************************************************
	# CONFIGURATION of RTE in table "tx_sksimplegallery_galleries", field "description"
	# ***************************************************************************************
RTE.config.tx_sksimplegallery_galleries.description {
  hidePStyleItems = H1, H4, H5, H6
  proc.exitHTMLparser_db=1
  proc.exitHTMLparser_db {
    keepNonMatchedTags=1
    tags.font.allowedAttribs= color
    tags.font.rmTagIfNoAttrib = 1
    tags.font.nesting = global
  }
}
');

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_sksimplegallery_pi1 = < plugin.tx_sksimplegallery_pi1.CSS_editor
',43);


t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_sksimplegallery_pi1.php','_pi1','list_type',1);

$TYPO3_CONF_VARS['EXTCONF']['cms']['db_layout']['addTables']['tx_sksimplegallery_pictures'][0] = array(
    'fList' => 'picture,title,description',
    'icon' => TRUE
);

$TYPO3_CONF_VARS['EXTCONF']['cms']['db_layout']['addTables']['tx_sksimplegallery_galleries'][0] = array(
    'fList' => 'title,description',
    'icon' => TRUE
);
?>
