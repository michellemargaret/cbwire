    <h1>News Headlines</h1>

    <div class="newsSection"><a href="http://www.cbncompass.ca/" target="_blank">The Compass</a></div>
        <?php  
        
        /*** The Compass **/        
            global $item_title, $item_link, $item_description;
            $xml=("http://www.cbncompass.ca/Rss/c/887/News"); 
            $xmlDoc = new DOMDocument(); 
            $xmlDoc->load($xml);             
            $x=$xmlDoc->getElementsByTagName('item'); 
            
            for ($i=0; $i<3; $i++) {
                if (gettype($x->item($i)) == "object") {
                    $item_title[$i] = $x->item($i)->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
                    $item_link[$i] = $x->item($i)->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;               
                }
            }
            
            for ($i=0; $i<3; $i++) {
                echo sprintf("
                      <div class=\"newsTitle\"><a href=\"%s\" target=\"_blank\">%s</a></div>
                      ", $item_link[$i], $item_title[$i]); 
            }
            
            unset($item_title);
            unset($item_link);
            unset($item_description);
       ?>
        
        <div class="newsSection"><a href="http://www.cbc.ca/nl/" target="_blank">CBC Newfoundland</a></div>
        <?php
        /** CBC **/ 
            global $item_title2, $item_link2, $item_description2;
            $xml=("http://rss.cbc.ca/lineup/canada-newfoundland.xml");
            
            $xmlDoc = new DOMDocument(); 
            $xmlDoc->load($xml);       
            $x=$xmlDoc->getElementsByTagName('item'); 
            $match_count = 0;
            $searchString = "#bay roberts|bishops cove|capital costs|brigus|bristols hope|bryants cove|carbonear|clarkes beach|coleys point|cupids|harbour grace|makinsons|north river|port de grave|shearstown|south river|spaniards bay|tilton|upper island cove|victoria|conception bay north|cbn|veterans memorial highway#i";
                
            try {
                
        
                for ($i=0; $i<15; $i++) {
                    if (gettype($x->item($i)) == "object") {
                        $strTitle = $x->item($i)->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
                        $strDescription = $x->item($i)->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;

                        if ((preg_match($searchString, $strTitle)) || (preg_match($searchString, $strDescription))) {             
                            $item_title[$match_count] = $strTitle;
                            $item_link[$match_count] = $x->item($i)->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
                            $match_count++;
                        }
                    }
                }            

                $i = 0;
                while (($i<15) && ($match_count < 3)) {                    
                    if (gettype($x->item($i)) == "object") {
                        $strTitle = $x->item($i)->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
                        $strDescription = $x->item($i)->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;

                        if ((preg_match($searchString, $strTitle)) || (preg_match($searchString, $strDescription))) {  
                            // Do nothing
                        } else {           
                            $item_title[$match_count] = $strTitle;
                            $item_link[$match_count] = $x->item($i)->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
                            $match_count++;                    
                        }
                    }
                    $i++;
                }

                for ($i=0; $i<3; $i++) {
                    echo sprintf("
                          <div class=\"newsTitle\"><a href=\"%s\" target=\"_blank\">%s</a></div>
                          ", $item_link[$i], $item_title[$i]); 
                }
            } catch (Exception $e) {
                
            }
            unset($item_title);
            unset($item_link);
            unset($item_description);
        ?>