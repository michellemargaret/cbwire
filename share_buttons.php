<?php function include_share_buttons($thisURL, $strTitle) { ?>
                        <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td><script>
                                    function fbs_click() {
                                        u='<?php echo $thisURL; ?>';
                                        t=document.title;
                                        window.open('http://www.facebook.com/sharer.php?u='+encodeURIComponent(u)+'&t='+encodeURIComponent(t),'sharer','toolbar=0,status=0,width=626,height=436');
                                        return false;
                                    }
                                    </script>
                                    <a href="http://www.facebook.com/share.php?u=<?php echo $thisURL; ?>" onclick="return fbs_click()" target="_blank"><img src="imgs/facebook_share.jpg" alt="Share on Facebook" border="0" /></a>
                                </td>
                                <td width="10">&nbsp;</td>
                                <td valign="top">
                                    <div id="twitterblock"></div>
                                    <script>
                                        var url = '<?php echo $thisURL; ?>';  
                                        jQuery("#twitterblock").html('<a href="https://twitter.com/share" class="twitter-share-button" data-url="' + url +'" data-text="<?php echo addslashes($strTitle); ?> - check it out" data-hashtags="cbwire">Tweet</a>');
                                    </script>                                
                                    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                                </td>
                            </tr>
                        </table>
<?php } ?>