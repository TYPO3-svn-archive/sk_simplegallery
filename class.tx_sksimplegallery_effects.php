<?php

class tx_sksimplegallery_effects {
  
  function listEffects($params, $conf) {
     $effects= unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sk_simplegallery']); 
     $effects=explode('|',$effects['effectPresets']);
     
     #debug($effects);   
     
     for($i=0;$i<count($effects);$i=$i+2) {
        $params['items'][$i+1][0]=$effects[$i];
        $params['items'][$i+1][1]=$effects[$i+1];
     }
     
  
  }
}
?>
