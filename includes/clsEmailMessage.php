<?php
	
	// Used to send email message
	class EmailMessage {	
		var $strSendTo;	
		var $strSubject;
		var $strEmailMessage;
		
		public function EmailMessage() { }
		
		// getListingTitle
		//	Parameter: intListingID: the id as an integer
		//  Returns:   the title corresponding to the listing id
		private function getListingBTitle($intListingBID) {
			$sql_query1 = sprintf("SELECT `title` FROM `cbwire`.`listings_b` list
						WHERE list.`id`=%s and list.`deleted`=0;",
						mysql_real_escape_string($intListingBID));
			$result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), "clsEmailMessage7", false);							
						
			if ($result_row = mysql_fetch_assoc($result1)) {		
				if (!is_null($result_row['title'])) { return $result_row['title']; }
			}	
			
			return "";
		}
                
                public function sendAdminPublished($inListingBID, $inListingID) {   
                    if ($this->getListingEnteredByAdmin($inListingID)) {
                        return $this->sendPromoInformation($inListingID);
                    } else {
                        return $this->sendNonregPublished($inListingBID, $inListingID);
                    }
                }
                
                private function sendNonregPublished($inListingBID, $inListingID) {
                    $this->strSendTo = $this->getNonregEmail($inListingBID);		
                    $strTitle = $this->getListingTitle($inListingID);
                    $this->strSubject = $strTitle . " on cbwire.ca";

                    $this->strEmailMessage = "                
                            Thank you for sharing <b>" . $this->getListingTitle($inListingID) . "</b> on CB Wire.<br><br>

                            To see your listing, follow the link:<br>
                            <a href=\"http://www.cbwire.ca?in=" . $inListingID . "\">www.cbwire.ca?in=" . $inListingID . "</a><br><br>

                            Enjoy!<br>
                            The cbwire.ca team

                    ";
                    
                    
                    return $this->SendEmail();
                }
                
                public function newFAQ($strEmail, $strQuestion) {                    
                    $this->strSendTo = "admin@cbwire.ca";
                    $this->strSubject = "New FAQ from web";
                    $this->strEmailMessage = " 
                            New FAQ Posted By:
                            " . $strEmail . "
                            Question: 
                            " . $strQuestion . "
                        ";
                    
                    return $this->SendEmail();
                }
                
                public function alertAdminNewListing($inListingID) {
                    $this->strSendTo = "admin@cbwire.ca";		
                    $strTitle = $this->getListingTitle($inListingID);
                    $this->strSubject = "New Listing: " . $strTitle;

                    $this->strEmailMessage = "                
                            " . $this->getListingTitle($inListingID) . "</b> has just been published on CB Wire.<br><br>

                            To see the listing, follow the link:<br>
                            <a href=\"http://www.cbwire.ca?in=" . $inListingID . "\">www.cbwire.ca?in=" . $inListingID . "</a><br><br>

                            Enjoy!<br>
                            The cbwire.ca team

                    ";
                    
                    
                    return $this->SendEmail();                    
                }
		
                public function sendPublished($inListingID) {
                    $this->strSendTo = $this->getListingEmail($inListingID);		
                    $strTitle = $this->getListingTitle($inListingID);
                    $this->strSubject = $strTitle . " on cbwire.ca";

                    $this->strEmailMessage = "                
                            Thank you for sharing <b>" . $this->getListingTitle($inListingID) . "</b> on CB Wire.<br><br>

                            To see your listing, follow the link:<br>
                            <a href=\"http://www.cbwire.ca?in=" . $inListingID . "\">www.cbwire.ca?in=" . $inListingID . "</a><br><br>

                            Enjoy!<br>
                            The cbwire.ca team

                    ";
                    
                    
                    return $this->SendEmail();
                }
		
		public function sendPromoInformation($inListingID) {			
				$this->strSendTo = "michelle@cbwire.ca";				
				$strTitle = $this->getListingTitle($inListingID);
				$this->strSubject = $strTitle . " on cbwire.ca";
				
				$this->strEmailMessage = "                                
Hi,<br><br>

CBWire.ca is Conception Bay North's online bulletin board.  
It offers comprehensive event, business and attraction listings for the entire region.<br><br>

" . $strTitle . " has just been added to CBWire.ca :<br>
<a href=\"http://www.cbwire.ca?in=" . $inListingID . "\">http://www.cbwire.ca?in=" . $inListingID . "</a><br><br>

Let me know if you have any problem with this.
Feel free to send along any more 
information you would like added, or
any more listings that you think might be of interest to Conception Bay North residents or visitors.  
<big><b>You can add, edit and search for free.</b></big><br><br>

Be sure to keep up to date by clicking <big><b>Like</b></big> on our
<big><b><a href=\"http://www.facebook.com/cbwire.ca\">Facebook page</a></b></big>.  
And don't hesitate to contact me if you have any questions.<br><br>

Thank you!<br>
Michelle Gallant<br>
<a href=\"mailto:michelle@cbwire.ca\">michelle@cbwire.ca</a>
	";	
				return $this->SendEmail();
		}
		
		public function listingRejection($inReason, $inListingBID) {
			$this->strSendTo = $this->getListingBEmail($inListingBID);
			$this->strSubject = "Your CB Wire Listing needs your attention";
			
			$this->strEmailMessage = "
Thank you for sharing your listing with CB Wire.<br><br>

The CB Wire team RETURNED the listing for you to modify.<br><br>
<b>" . $this->getListingBTitle($inListingBID) . "</b><br>
<i>" . $inReason . "</i>
<br><br>

Feel free to make appropriate changes and resubmit your listing.  <br><br>

We truly appreciate your interest and hope to see your listing resubmitted soon!<br><br>

To login and make changes, follow the link:<br>
<a href=\"http://www.cbwire.ca/login.php\">www.cbwire.ca/login.php</a><br><br>

The cbwire.ca team

";		
			return $this->SendEmail();
	
		}
		
		public function alertAdmin($inListingBID) {		
			$this->strSendTo = "admin@cbwire.ca";
			$this->strSubject = "CB Wire Listing Needs Approval";
						
			$this->strEmailMessage = "
                            The following requires an approval:<br><br>
                            <b>" . $this->getListingBTitle($inListingBID) . "</b>
                            <br><br>
                            <a href=\"http://www.cbwire.ca/login.php\">www.cbwire.ca/login.php</a><br><br>

                        ";					
			return $this->SendEmail();
		}
                
                public function sendRegistrationInformation($inUserID) {
                    $this->strSendTo = $this->getEmailAddress($inUserID);
                    $this->strSubject = "New cbwire.ca registration";
                    $this->strEmailMessage = "
Thank you for registering with cbwire.ca!<br><br>
You can now log in using your email address and the password you set at the time of registration.
<br><br>                            
If this account was set up in error, please forward this message to admin@cbwire.ca for investigation.
<br><br>

Thank you!<br>
The cbwire.ca team";
                    
                    return $this->SendEmail();
                }
                              
		public function forgotPassword($inEmail, $inNewPassword) {
			$this->strSendTo = $inEmail;
			$this->strSubject = "Your cbwire.ca password";
			$this->strEmailMessage = "
Your cbwire.ca password has been reset.<br><br>

Your new password is:<br>
" . $inNewPassword . "<br><br>

Please click on the link below to login.<br>
<a href=\"http://www.cbwire.ca/login.php\">www.cbwire.ca/login.php</a>
<br><br>

Enjoy!<br>
The cbwire.ca team

";			
			return $this->SendEmail();
		}
		
		/*public function changeEmailAddress($inEmail, $inActivationCode) {
			$this->strSendTo = $inEmail;
			$this->strSubject = "Update your cbwire.ca email address";
			$this->strEmailMessage = "
To update your CB Wire email address, click the link below or copy and paste the address into your browser (e.g. Internet Explorer)<br><br>
<a href=\"http://www.cbwire.ca/activate_email.php?actcode=" . $inActivationCode . "\">www.cbwire.ca/activate_email.php?actcode=" . $inActivationCode . "</a><br><br>

Enjoy!<br>
The cbwire.ca team

";			
			return $this->SendEmail();
		}*/		
		
		public function contactClassified($inUserID, $inName, $inEmail, $inMessage, $inPhone, $inListingID) {
			if ($inName == "") { $inName = "- - -"; }
			if ($inEmail == "") { $inEmail = "- - -"; }
			if ($inPhone == "") { $inPhone = "- - -"; }
			
			$strListingTitle = $this->getListingTitle($inListingID);
			$this->strSendTo = $this->getClassifiedEmail($inListingID);
			$this->strSubject = "Message regarding CB Wire Classified Listing";
			$this->strEmailMessage = "
<br><br>
You have a received a message regarding your CB Wire Classified Listing<br>
<b>" . $strListingTitle . "</b><br> 
( <a href=\"http://www.cbwire.ca/classifieddetails.php?id=" . $inListingID . "\">www.cbwire.ca?in=" . $inListingID . "</a>)<br><br>
<div class=\"msghighlight\">
<b>Message Details</b><br><br>
<i>Name:</i> " . $inName . "<br>
<i>Phone Number:</i> " . $inPhone . "<br>
<i>Email Address:</i> " . $inEmail . "<br><br>
<i>Message:</i> <br>
" . $inMessage . "
    </div>

<br><br>
Please forward any questionable messages to <a href=\"mailto:admin@cbwire.ca\">admin@cbwire.ca</a><br><br>
Enjoy!<br>
The cbwire team
			"; 
			
			return $this->SendEmail();
		}	
		
		public function contactCBWire($inUserID, $inName, $inEmail, $inMessage, $inPhone) {
			if ($inName == "") { $inName = "- - -"; }
			if ($inEmail == "") { $inEmail = "- - -"; }
			if ($inPhone == "") { $inPhone = "- - -"; }
			if ($inMessage == "") { $inMessage = "- - -"; }
			
			$this->strSendTo = "contact@cbwire.ca";
			$this->strSubject = "CB Wire Contact";
			$this->strEmailMessage = "
From the contact form on CB Wire:<br><br>

Name: " . $inName . "<br>
Phone Number: " . $inPhone . "<br>
Email Address: " . $inEmail . "<br>
Message: <br>
" . $inMessage . "
			"; 
			
			return $this->SendEmail();
		}
		
		// getListingTitle
		//	Parameter: intListingID: the id as an integer
		//  Returns:   the title corresponding to the listing id
		private function getListingTitle($intListingID) {
			$sql_query1 = sprintf("SELECT `title` FROM `cbwire`.`listings` list
						WHERE list.`id`=%s and list.`deleted`=0;",
						mysql_real_escape_string($intListingID));
			$result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), "clsEmailMessage2", false);
						
			if ($result_row = mysql_fetch_assoc($result1)) {		
				if (!is_null($result_row['title'])) { return $result_row['title']; }
			}	
			
			return "";
		}
		
		// getListingEnteredByAdmin
		//	Parameter: intListingID: the id as an integer
		//  Returns:   true if this listing was entered by an admin; false otherwise
		private function getListingEnteredByAdmin($intListingID) {
			$sql_query1 = sprintf("SELECT u.`admin` FROM `cbwire`.`listings` list
						INNER JOIN `cbwire`.`users` u on u.`id` = list.`userid`
						WHERE list.`id`=%s and list.`deleted`=0;",
						mysql_real_escape_string($intListingID));
			$result1 = mysql_query($sql_query1) or log_error($sql_query1, mysql_error(), "clsEmailMessage1", false);				
						
			if ($result_row = mysql_fetch_assoc($result1)) {		
				if (!is_null($result_row['admin'])) { 
					if ($result_row['admin'] == 1) { return true; }
				}
			}	
                        
			return false;
		}
		
	/*	private function getDetailsPage($strSection) {
			switch ($strSection) {
				case "att":
					return "attractiondetails.php";
					break;
				case "act": 
					return "activitydetails.php";
					break;
				case "dir":
					return "directorydetails.php";
					break;
				case "cla":
					return "classifieddetails.php";
					break;
				default:
					return "directorydetails.php";
			}
			
			return "";
		} */
		
		// getEmailAddress
		//  Parameter: intUserID: the user id as an interger
		//	Returns:   email address for this user
		private function getListingEmail($intListingID) {
			$strEmail = "";
			
			$sql_query = sprintf("SELECT u.`email` from `cbwire`.`listings` l
									inner join `cbwire`.`users` u on l.`userid`=u.`id`
									where l.`id`=%s;", 
									mysql_real_escape_string($intListingID));
			$result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), "clsEmailMessage3", false);

			if ($result_row = mysql_fetch_assoc($result)) {
				if (!is_null($result_row['email'])) { return $result_row['email']; }
			}

			return "";
		}
		
                
                private function getNonregEmail($inListingBID) {
			$sql_query = sprintf("SELECT l.`useremail` from `cbwire`.`listings_b` l
									where l.`id`=%s;", 
									mysql_real_escape_string($inListingBID));
			$result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), "clsEmailMessage56", false);

			if ($result_row = mysql_fetch_assoc($result)) {
				if (!is_null($result_row['useremail'])) { return $result_row['useremail']; }
			}

			return "";                    
                }
                
		// getEmailAddress
		//  Parameter: intUserID: the user id as an interger
		//	Returns:   email address for this user
		private function getListingBEmail($intListingBID) {
			$sql_query = sprintf("SELECT u.`email` from `cbwire`.`listings_b` l
									inner join `cbwire`.`users` u on l.`userid`=u.`id`
									where l.`id`=%s;", 
									mysql_real_escape_string($intListingBID));
			$result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), "clsEmailMessage4", false);

			if ($result_row = mysql_fetch_assoc($result)) {
				if (!is_null($result_row['email'])) { return $result_row['email']; }
			}

			return "";
		}
		
		// getClassifiedEmail
		//  Parameter: intUserID: the user id as an interger
		//	Returns:   email address for this user
		private function getClassifiedEmail($intListingID) {
			$sql_query = sprintf("SELECT `email` from `cbwire`.`contact` 
									where `listingsid`=%s;", 
									mysql_real_escape_string($intListingID));
			$result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), "clsEmailMessage5", false);

			if ($result_row = mysql_fetch_assoc($result)) {
				if (!is_null($result_row['email'])) { return $result_row['email']; }
			}

			return "";
		}
		
		// getEmailAddress
		//  Parameter: intUserID: the user id as an interger
		//	Returns:   email address for this user
		private function getEmailAddress($intUserID) {
			$sql_query = sprintf("SELECT `email` from `cbwire`.`users` where `id`=%s;", $intUserID);
			$result = mysql_query($sql_query) or log_error($sql_query, mysql_error(), "clsEmailMessage6", false);
								
			if ($result_row = mysql_fetch_assoc($result)) {
				if (!is_null($result_row['email'])) { return $result_row['email']; }
			}
			
			return "";
		}
		
		private function formatEmailMessage() {
			$this->strEmailMessage = "	
							<!doctype html public \"-//W3C//DTD HTML 4.0 Transitional//EN\">
        <html lang=\'en\'>
                <head>	
                        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=9\" /> 
                        <meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\">
                        <META http-equiv=\"Content-Style-Type\" content=\"text/css;charset=utf-8\">		
                </head>
        <body bgcolor=\"#FFFFFF\">
        <style>
        <!--
        body {
            background: #FFFFFF url(\"http://www.cbwire.ca/rewrite/imgs/bg_fade.png\") repeat-x fixed;
            font-family: sans-serif; 
            padding: 10px 0px 10px 0px;
        }
        
        img {
            padding: 10px;
            margin: 10px;
        }

        .container {
            display: block;
            position: relative;
            margin: auto;
            text-align: left;
            background-color: white;
            padding: 10px;
            border: 1px solid #B6B6B6;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
        }
        
        .msghighlight {
            border: 1px solid #B6B6B6;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
            -moz-box-shadow: 4px 4px 5px #888888;
            -webkit-box-shadow: 4px 4px 5px #888888;
            box-shadow: 4px 4px 5px #888888; 
            padding: 10px;
        }
        
        .maincell {
            display: inline-block;
            border-width: 0px;
            padding: 5px;
            width: 100%;
        }        

        a {
            text-decoration: none;
            color: #000099;
        }

        a:hover {
            text-decoration: underline;
            color: #cd0a0a;
        }
        

        .subject {
            position: relative;
            border-width: 0px 0px 3px 0px;    
            border-color: #F0F0F0;
            border-style: solid;    
            color: #000000;
            font-size: 2.3em;
            font-weight: bold;
            padding: 5px 5px 5px 5px;
            margin: 10px 10px 10px 10px;
        }
        
        .line {
            position: relative;
            padding: 0px 0px 0px 0px;
            margin: 10px 10px 15px 0px;
            border-width: 0px 0px 3px 0px;    
            border-color: #F0F0F0;
            border-style: solid;    
            color: #000000;
            font-size: 2.3em;
            font-weight: bold;
            height: 2px;
            width: 85%;        
        }

        .nobr { white-space: nowrap; }
        
        .footer {
            width: 100%;
            padding-top: 20px;
            font-size: 0.9em;
        }
        .footer a {
            color: #888888;
        }
        -->
        </style>
        <div class=\"container\">
            <div class=\"maincell\">										
                <div class=\"subject\">" . $this->strSubject . "</div>
                " . $this->strEmailMessage  . "<br><br>
                <div class=\"footer\">           
                    <a href=\"http://www.facebook.com/cbwire.ca\" target=\"_blank\">Like Us On Facebook</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href=\"https://twitter.com/cbwire\" target=\"_blank\">Follow @cbwire On Twitter</a>
                </div>
                
            </div>
         </div>
                                                                                             
</body>
</html>";
		}
                
                private function heal($str) {
                        $injections = array('/(\n+)/i',
                        '/(\r+)/i',
                        '/(\t+)/i',
                        '/(%0A+)/i',
                        '/(%0D+)/i',
                        '/(%08+)/i',
                        '/(%09+)/i'
                        );
                        $str= preg_replace($injections,'',$str);
                        return $str;
                }

		private function SendEmail() {
			try {	
				$this->formatEmailMessage();
				$strHeaders = 'From: admin@cbwire.ca' . "\r\n" .
								'Reply-To: admin@cbwire.ca' . "\r\n" .
								'X-Mailer: PHP/' . phpversion() . "\r\n" .
								'Content-type: text/html' . "\r\n";
		
				if (mail($this->heal($this->strSendTo), $this->heal($this->strSubject), $this->strEmailMessage, $strHeaders)) {                                    
					return true;
				} else {
					// Logging class initialization
                                        include_once "clsLogging.php";
					$log = new Logging();
					// write message to the log file
					//$log->lwrite($this->strSendTo . " :: " . $this->strSubject . " :: " . $this->strEmailMessage);
					
					return false;
				}				
			} catch (Exception $e) {
					// Logging class initialization   
                                        include_once "clsLogging.php";
					$log = new Logging();   
					// write message to the log file   
					$log->lwrite($this->strSendTo . " :: " . $this->strSubject . " :: " . $this->strEmailMessage);
					
					return false;
			} 
		}
		
		// Used in recording errors func.inc.php : function arrayToString($inArray)
		public function print_log() {
			return "clsEmailMessage";
		}
	}
	
?>