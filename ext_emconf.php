<?php

########################################################################
# Extension Manager/Repository config file for ext: "sk_simplegallery"
#
# Auto generated 09-05-2007 00:34
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Simple Gallery',
	'description' => 'Simple Picture-Gallery',
	'category' => 'plugin',
	'author' => 'Steffen Kamper',
	'author_email' => 'info@sk-typo3.de',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 1,
	'createDirs' => 'uploads/tx_sksimplegallery/rte/,uploads/tx_sksimplegallery/ecards/',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.0.8',
	'constraints' => array(
		'depends' => array(
			'php' => '3.0.0-0.0.0',
			'typo3' => '3.8.0-4.2.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'kj_imagelightbox2' => '',
		),
	),
	'_md5_values_when_last_written' => 'a:89:{s:9:"ChangeLog";s:4:"11ba";s:10:"README.txt";s:4:"9fa9";s:36:"class.tx_sksimplegallery_effects.php";s:4:"106f";s:21:"ext_conf_template.txt";s:4:"1cf8";s:12:"ext_icon.gif";s:4:"f130";s:17:"ext_localconf.php";s:4:"f401";s:14:"ext_tables.php";s:4:"3470";s:14:"ext_tables.sql";s:4:"8b4c";s:12:"flexform.xml";s:4:"ef01";s:34:"icon_tx_sksimplegallery_ecards.gif";s:4:"8e0f";s:37:"icon_tx_sksimplegallery_galleries.gif";s:4:"f130";s:36:"icon_tx_sksimplegallery_pictures.gif";s:4:"a70e";s:13:"locallang.xml";s:4:"b10b";s:16:"locallang_db.xml";s:4:"6e19";s:7:"tca.php";s:4:"22d8";s:14:"doc/manual.sxw";s:4:"c43d";s:19:"doc/wizard_form.dat";s:4:"edcf";s:20:"doc/wizard_form.html";s:4:"e125";s:46:"modfunc1/class.tx_sksimplegallery_modfunc1.php";s:4:"a2fb";s:22:"modfunc1/locallang.xml";s:4:"3892";s:14:"pi1/ce_wiz.gif";s:4:"f130";s:36:"pi1/class.tx_sksimplegallery_pi1.php";s:4:"0a4c";s:44:"pi1/class.tx_sksimplegallery_pi1_wizicon.php";s:4:"5938";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"8e69";s:17:"pi1/template.html";s:4:"e890";s:24:"pi1/static/constants.txt";s:4:"b16b";s:24:"pi1/static/editorcfg.txt";s:4:"704f";s:20:"pi1/static/setup.txt";s:4:"e022";s:24:"pi1/static/css/setup.txt";s:4:"660a";s:16:"res/example1.css";s:4:"a712";s:46:"res/PHP_JPEG_Metadata_Toolkit_1.11/COPYING.txt";s:4:"fdaf";s:43:"res/PHP_JPEG_Metadata_Toolkit_1.11/EXIF.php";s:4:"633b";s:53:"res/PHP_JPEG_Metadata_Toolkit_1.11/EXIF_Makernote.php";s:4:"0d60";s:48:"res/PHP_JPEG_Metadata_Toolkit_1.11/EXIF_Tags.php";s:4:"b2ac";s:53:"res/PHP_JPEG_Metadata_Toolkit_1.11/Edit_File_Info.php";s:4:"fac4";s:61:"res/PHP_JPEG_Metadata_Toolkit_1.11/Edit_File_Info_Example.php";s:4:"d50d";s:46:"res/PHP_JPEG_Metadata_Toolkit_1.11/Example.php";s:4:"11d3";s:43:"res/PHP_JPEG_Metadata_Toolkit_1.11/IPTC.php";s:4:"8c37";s:43:"res/PHP_JPEG_Metadata_Toolkit_1.11/JFIF.php";s:4:"45ba";s:43:"res/PHP_JPEG_Metadata_Toolkit_1.11/JPEG.php";s:4:"e52d";s:42:"res/PHP_JPEG_Metadata_Toolkit_1.11/PIM.php";s:4:"ce1b";s:58:"res/PHP_JPEG_Metadata_Toolkit_1.11/Photoshop_File_Info.php";s:4:"97e0";s:52:"res/PHP_JPEG_Metadata_Toolkit_1.11/Photoshop_IRB.php";s:4:"b7c1";s:50:"res/PHP_JPEG_Metadata_Toolkit_1.11/PictureInfo.php";s:4:"a4d3";s:50:"res/PHP_JPEG_Metadata_Toolkit_1.11/TIFFExample.php";s:4:"f7db";s:54:"res/PHP_JPEG_Metadata_Toolkit_1.11/Toolkit_Version.php";s:4:"2adf";s:46:"res/PHP_JPEG_Metadata_Toolkit_1.11/Unicode.php";s:4:"efd3";s:54:"res/PHP_JPEG_Metadata_Toolkit_1.11/Write_File_Info.php";s:4:"63ef";s:42:"res/PHP_JPEG_Metadata_Toolkit_1.11/XML.php";s:4:"6854";s:42:"res/PHP_JPEG_Metadata_Toolkit_1.11/XMP.php";s:4:"da53";s:53:"res/PHP_JPEG_Metadata_Toolkit_1.11/get_JFXX_thumb.php";s:4:"3074";s:54:"res/PHP_JPEG_Metadata_Toolkit_1.11/get_casio_thumb.php";s:4:"66e2";s:53:"res/PHP_JPEG_Metadata_Toolkit_1.11/get_exif_thumb.php";s:4:"5c25";s:56:"res/PHP_JPEG_Metadata_Toolkit_1.11/get_minolta_thumb.php";s:4:"4627";s:51:"res/PHP_JPEG_Metadata_Toolkit_1.11/get_ps_thumb.php";s:4:"98f4";s:49:"res/PHP_JPEG_Metadata_Toolkit_1.11/pjmt_utils.php";s:4:"9f54";s:43:"res/PHP_JPEG_Metadata_Toolkit_1.11/test.jpg";s:4:"5b34";s:68:"res/PHP_JPEG_Metadata_Toolkit_1.11/documentation/Camera_List_1.0.pdf";s:4:"d82d";s:61:"res/PHP_JPEG_Metadata_Toolkit_1.11/documentation/changes.html";s:4:"27c8";s:63:"res/PHP_JPEG_Metadata_Toolkit_1.11/documentation/css_terms.html";s:4:"8e5a";s:74:"res/PHP_JPEG_Metadata_Toolkit_1.11/documentation/edit_write_file_info.html";s:4:"23bb";s:61:"res/PHP_JPEG_Metadata_Toolkit_1.11/documentation/example.html";s:4:"2619";s:62:"res/PHP_JPEG_Metadata_Toolkit_1.11/documentation/examples.html";s:4:"c0e6";s:58:"res/PHP_JPEG_Metadata_Toolkit_1.11/documentation/exif.html";s:4:"cd3c";s:59:"res/PHP_JPEG_Metadata_Toolkit_1.11/documentation/index.html";s:4:"b0dc";s:59:"res/PHP_JPEG_Metadata_Toolkit_1.11/documentation/intro.html";s:4:"3b4e";s:58:"res/PHP_JPEG_Metadata_Toolkit_1.11/documentation/jfif.html";s:4:"6e47";s:58:"res/PHP_JPEG_Metadata_Toolkit_1.11/documentation/jpeg.html";s:4:"8cfd";s:63:"res/PHP_JPEG_Metadata_Toolkit_1.11/documentation/photoshop.html";s:4:"bca3";s:73:"res/PHP_JPEG_Metadata_Toolkit_1.11/documentation/photoshop_file_info.html";s:4:"43bf";s:66:"res/PHP_JPEG_Metadata_Toolkit_1.11/documentation/picture_info.html";s:4:"dcc0";s:58:"res/PHP_JPEG_Metadata_Toolkit_1.11/documentation/style.css";s:4:"c8bc";s:65:"res/PHP_JPEG_Metadata_Toolkit_1.11/documentation/tiffexample.html";s:4:"da6f";s:58:"res/PHP_JPEG_Metadata_Toolkit_1.11/documentation/todo.html";s:4:"8e93";s:57:"res/PHP_JPEG_Metadata_Toolkit_1.11/documentation/xmp.html";s:4:"afa5";s:56:"res/PHP_JPEG_Metadata_Toolkit_1.11/Makernotes/Pentax.php";s:4:"f288";s:54:"res/PHP_JPEG_Metadata_Toolkit_1.11/Makernotes/agfa.php";s:4:"7945";s:55:"res/PHP_JPEG_Metadata_Toolkit_1.11/Makernotes/canon.php";s:4:"d4b0";s:55:"res/PHP_JPEG_Metadata_Toolkit_1.11/Makernotes/casio.php";s:4:"6453";s:55:"res/PHP_JPEG_Metadata_Toolkit_1.11/Makernotes/epson.php";s:4:"98e1";s:58:"res/PHP_JPEG_Metadata_Toolkit_1.11/Makernotes/fujifilm.php";s:4:"c139";s:64:"res/PHP_JPEG_Metadata_Toolkit_1.11/Makernotes/konica_minolta.php";s:4:"77d3";s:57:"res/PHP_JPEG_Metadata_Toolkit_1.11/Makernotes/kyocera.php";s:4:"dd65";s:55:"res/PHP_JPEG_Metadata_Toolkit_1.11/Makernotes/nikon.php";s:4:"cca5";s:57:"res/PHP_JPEG_Metadata_Toolkit_1.11/Makernotes/olympus.php";s:4:"d6d8";s:59:"res/PHP_JPEG_Metadata_Toolkit_1.11/Makernotes/panasonic.php";s:4:"6b71";s:55:"res/PHP_JPEG_Metadata_Toolkit_1.11/Makernotes/ricoh.php";s:4:"c3d7";s:54:"res/PHP_JPEG_Metadata_Toolkit_1.11/Makernotes/sony.php";s:4:"c610";}',
	'suggests' => array(
	),
);

?>
