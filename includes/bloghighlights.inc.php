
        <h1><a href="http://blog.cbwire.ca">blog.cbwire.ca</a></h1>
        
                          <div class="blogDate">Thu, 29 Nov 2012 </div>
                          <div class="blogTitle"><a href="http://cbwire.ca/blog/2012/11/29/youve-come-to-the-wrong-place/">You’ve come to the wrong place</a></div>
                          <div class="blogDescription">If you’re looking for Walk In Clinics, you’ve come to the wrong place. You may&nbsp;have noticed there are no medical clinics listed on cbwire.&nbsp;&nbsp;That’s because they don’t want to be listed.&nbsp; Or at least one that contacted me doesn’t want … <a href="http://cbwire.ca/blog/2012/11/29/youve-come-to-the-wrong-place/"></a></div>
                          
                          <div class="blogDate">Tue, 02 Oct 2012 </div>
                          <div class="blogTitle"><a href="http://cbwire.ca/blog/2012/10/01/recent-updates/">Recent Updates</a></div>
                          <div class="blogDescription">You may have noticed a few changes to the site lately. new styles for textboxes and dropdowns 
                              moved the login panel so it is always  … <a href="http://cbwire.ca/blog/2012/10/01/recent-updates/"></a></div>
        <?php      
  /*  Below worked locally but not on prod   
            $xml=("http://blog.cbwire.ca/feed/"); 
            global $item_title, $item_link, $item_description, $item_date;
            $xmlDoc = new DOMDocument(); 
            $xmlDoc->load($xml);             
            $x=$xmlDoc->getElementsByTagName('item'); 
            
            for ($i=0; $i<2; $i++) {
                $item_title[$i] = $x->item($i)->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
                $item_link[$i] = $x->item($i)->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
                $item_description[$i] = $x->item($i)->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;
                $item_description[$i] = str_replace("Continue reading <span class=\"meta-nav\">&#8594;</span>", "", $item_description[$i]);
                $item_date[$i] = $x->item($i)->getElementsByTagName('pubDate')->item(0)->childNodes->item(0)->nodeValue;               
                $item_date[$i] = substr($item_date[$i], 0, 17);
            }
            
            for ($i=0; $i<2; $i++) {
            //    if (($item_title[$i] <> "") && ($item_link[$i] <> "") && ($item_description[$i] <> "") && ($item_date[$i] <> "")) {
                    echo sprintf("
                          <div class=\"blogDate\">%s</div>
                          <div class=\"blogTitle\"><a href=\"%s\">%s</a></div>
                          <div class=\"blogDescription\">%s</div>
                          ", $item_date[$i], $item_link[$i], $item_title[$i], $item_description[$i]);                           
            //    }
            }
   */     
        ?>