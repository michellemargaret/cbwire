function uniqueId() { return new Date().getTime(); }

$(document).ready(function(){    

    /* Set defaults / initial values */
       
    var dateSectionCount = 0;
    var strDateSection = '';
    
    var contactSectionCount = 0;
    var strContactSection = "";
    
    var whereSectionCount = 0;
    var strWhereSection = "";
    
    var strRemoveSection = "";
    
    var currentCategory = "";
    
    var currentLiveLinkSearch = "";
    var currentLiveLinkText = "";
    
   
    
    function openLoadingDiv() {
        if ($("#divLoading").css("display") == "none") {
            var varTop = ($(window).height()/2) + $(window).scrollTop() - 50;
            var varLeft = ($(window).width()/2) + $(window).scrollLeft() - 50;
            $("#divLoading").css("top", varTop).css("left", varLeft);
            $("#divLoading").fadeIn("fast");
        }
    }
    
    function closeLoadingDiv() {        
        if ($("#divLoading").css("display") != "none") {
            $("#divLoading").fadeOut("fast");   
        }
    }
    
     
   if ($("#update_listing").length > 0){  
       
        $.get("editincludes/remove_section.php", function(data) {
            strRemoveSection = data;
        });        
            
        $("#txtExpiry").change(function() {
            var txtExpiry = $(this);
            var txtExpiryErr = $("#txtExpiryErr");
            txtExpiry.removeClass("txt_alert");
            txtExpiryErr.html("");
            if ($(this).val() != "") {
                if (!isDate(txtExpiry.val())) {
                    txtExpiryErr.html("Expiry Date " + txtExpiry.val() + " is not a proper date. Please use a proper future date with format YYYY-MM-DD.");
                    txtExpiryErr.addClass("txt_alert");
                }
            }
        });

        // Image gallery        
        $.get("editincludes/add_pictures.php?uid=" + uniqueId(), function(data) {
            $("#select_picture").html(data);

            $(".savedPicture").click(function() {
                var clickedPicture = $(this).attr("id").substring(12, $(this).attr("id").length);
                $(".savedPicture").removeClass("selectedPicture");
                $("#savedPicture" + clickedPicture).addClass("selectedPicture");
                $("#txtPictureID").val(clickedPicture);

                $("#pictureDisplayBox").empty();
                $("#pictureDisplayBox").append($("#savedPicture" + clickedPicture).html());      
                $("#pictureDisplayBox").css("height", $("#savedPicture" + clickedPicture + " img").css("height"));
                $("#pictureDisplayBox").css("width", $("#savedPicture" + clickedPicture + " img").css("width"));
                $("#removeImage").css("display", "inline");

                return false;
            });
        });

        $("#txtExpiry").datepicker({dateFormat: 'yy-mm-dd'});

        // Add new section when Add button is clicked
        $("#link_add_date").click(function() {                    
            insertDateSection("", true);

            return false;
        });

        $("#link_add_contact").click(function() {
            insertContactSection();

            return false;
        }); 

        $("#link_add_location").click(function() {        
            insertWhereSection();

            return false;
        }); 

        $("#removeImage").click(function() {
            if (confirm("Are you sure you want to remove this picture?")) {
                $(this).css("display", "none");
                $("#pictureDisplayBox").empty();
                $("#pictureMessage").empty();
                $("#txtPictureID").val("");
            }

            return false;
        });        
    
        $("#txtDescription").bind("paste", function() {        
            checkCharCount();
        });

        $("#txtDescription").bind("keyup", function() {        
            checkCharCount();
        });

        // Validate Title
        $("#txtTitle").change(function() {          
            if ($(this).val() == "") {
                $(this).addClass("txt_alert");
                $("#txtTitleErr").text("Title is required");
            } else {
                $(this).removeClass("txt_alert");
                $("#txtTitleErr").text("");
            }
        });

        // Validate url
 /*       $("#txtWebsite").change(function() {  
            var urlregex = new RegExp(/^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i); 
            var inText = $(this).val();

            if ((inText.substring(0, 7) != "http://") && (inText.substring(0, 8) != "https://") && (inText != "")) {           
            inText = "http://" + inText;
            }

            if ((urlregex.test(inText)) || (inText == "") || (inText == "http://")) {
                    $(this).removeClass("txt_alert");
                    $("#txtWebsiteErr").text("");
            }else{
                    $(this).addClass("txt_alert");
                    $("#txtWebsiteErr").text("Not a valid website");
            }
        }); */
        
        function saveListing(inType) {     
            $("#overlay_back").css("height", $("#maincell").outerHeight() + 380).css("width", $(window).width() + $(window).scrollLeft()).css("display", "block");
            openLoadingDiv();
            
            $.ajax({  
                url: "editincludes/save_listing.php",  
                type: "POST",  
                data: $("#update_listing").serialize(),  
                success: function(data) { 
                    if ((data.length > 6) && (data.substring(0,7) == "success")) { 
                       // $.post("includes/recorderror.php", { page: "save_listing:success", message: data } );
                        if (inType == "user") {
                            alert("Thank you!  Your listing has been saved.  Be sure to publish it when you are ready for the public to see it.");
                            $("#jquery_done").submit();
                        } else if (inType == "publish") {
                            publishListing();
                        } else { 
                             alert("Thank you!  Your listing has been saved.  You will be notified by email when it has been approved.");
                             $("#jquery_done").submit();
                        } 
                    } else {                          
                        closeLoadingDiv();
                        $.post("includes/recorderror.php", { page: "save_listing:error", message: data } );
                        alert("\nThere was an error.  Please try again.  If this error continues, please contact michelle@cbwire.ca");
                        $("#overlay_back").fadeOut(200, function() {            
                            $("#overlay_back").css("width", "0").css("height", "0").css("display", "none");
                        });
                    }
                },
                error: function(jqXHR, exception) {  
                    closeLoadingDiv();
                    $("#overlay_back").fadeOut(200, function() {            
                            $("#overlay_back").css("width", "0").css("height", "0").css("display", "none");
                    });
                    $.post("includes/recorderror.php", { page: "save_listing:error2", message: jqXHR.status + ", " + jqXHR.responseText + ", " + exception + " , "  + jqXHR.responseText } );
                    alert("There was an error.  Please try again.  If this error continues, please contact michelle@cbwire.ca");                        
                }
            });
            
            return;
        }
        
        function deleteListing() {
            $("#overlay_back").css("height", $("#maincell").outerHeight() + 380).css("width", $(window).width() + $(window).scrollLeft()).css("display", "block");
            openLoadingDiv();
            $.ajax({  
                url: "editincludes/delete_listing.php",  
                type: "POST",  
                data: $("#update_listing").serialize(),  
                success: function(data) {  
                    if ((data.length > 6) && (data.substring(0,7) == "success")) { 
                        $.post("includes/recorderror.php", { page: "delete_listing:success", message: data } );
                        alert("Your listing has been deleted.");
                        $("#jquery_done").submit();
                    } else { 
                        closeLoadingDiv();
                        $.post("includes/recorderror.php", { page: "delete_listing:error", message: data } );
                        alert("There was an error.  Please try again.  If this error continues, please contact michelle@cbwire.ca");
                        $("#overlay_back").fadeOut(200, function() {            
                            $("#overlay_back").css("width", "0").css("height", "0").css("display", "none");
                        });
                    }
                },
                error: function(jqXHR, exception) {  
                    closeLoadingDiv();
                    $.post("includes/recorderror.php", { page: "delete_listing:error2", message: jqXHR.status + ", " + jqXHR.responseText + ", " + exception + " , "  + jqXHR.responseText } );
                        alert("There was an error.  Please try again.  If this error continues, please contact michelle@cbwire.ca");
                        $("#overlay_back").fadeOut(200, function() {            
                            $("#overlay_back").css("width", "0").css("height", "0").css("display", "none");
                        });
                }
            });
            return;
        }
                
        function publishListing() {
            $.ajax({  
                url: "editincludes/publish_listing.php",  
                type: "POST",  
                data: $("#update_listing").serialize(),  
                success: function(data) {     
                    if ((data.length > 6) && (data.substring(0,7) == "success")) { 
                      //  $.post("includes/recorderror.php", { page: "publish_listing:success", message: data } );
                        alert("Thank you!  Your listing has been published.");
                         $("#jquery_done").submit();
                    } else { 
                        closeLoadingDiv();
                        $.post("includes/recorderror.php", { page: "publish_listing:error", message: data } );
                        alert("There was an error.  Please try again.  If this error continues, please contact michelle@cbwire.ca");
                        $("#overlay_back").fadeOut(200, function() {            
                            $("#overlay_back").css("width", "0").css("height", "0").css("display", "none");
                        });
                    }
                },
                error: function(jqXHR, exception) { 
                    $.post("includes/recorderror.php", { page: "publish_listing:error2", message: jqXHR.status + ", " + jqXHR.responseText + ", " + exception + " , "  + jqXHR.responseText } ); 
                    closeLoadingDiv();
                    alert("There was an error.  Please try again.  If this error continues, please contact michelle@cbwire.ca");
                    $("#overlay_back").fadeOut(200, function() {            
                        $("#overlay_back").css("width", "0").css("height", "0").css("display", "none");
                    });
                }

            });
            return;
        }
        
        
        function liveLinkSearch(clickedSection) {            
            var searchFor = encodeURIComponent($("#txtLink" + clickedSection).val());
            currentLiveLinkSearch = "#liveLinkSearch" + clickedSection;
            currentLiveLinkText = "#txtLink" + clickedSection;

            if (searchFor == "") {
                closeLiveLinkSearch(clickedSection, false);
            } else {
                $("#overlay_back").css("height", $("#maincell").outerHeight() + 380).css("width", $(window).width() + $(window).scrollLeft()).css("display", "block");
                $("#txtLink" + clickedSection).css("z-index", "50");
                // Display results within current search results div

                $("#liveLinkSearchResults" + clickedSection).load("editincludes/live_link_search.php?in=" + searchFor + "&uid=" + uniqueId());
                //   "Within current parent div"                "find search results div"
                $("#liveLinkSearch" + clickedSection).css("display", "block").css("z-index", "50");
                $("#txtLinkID" + clickedSection).val("");
            }
        }
        
        function closeLiveLinkSearch(clickedSection, clearTextField) {
            $("#liveLinkSearch" + clickedSection).css("display", "none");
            if (clearTextField == true) {
                $("#txtLink" + clickedSection).val("");
            }
            $("#txtLink" + clickedSection).css("z-index", "1");
            $("#overlay_back").fadeOut(200, function() {            
                $("#overlay_back").css("width", "0").css("height", "0").css("display", "none");
            });    
            return false;
        }
        
        $("#btnSave").click(function(){  
            if (confirm("Are you sure you're ready to save this listing?")) {
                saveListing("public");
            } else {
                
            }
            return false;
        });

        $("#btnPublish").click(function(){  
            if (confirm("Are you sure you're ready to publish this listing?")) {
                saveListing("publish");                    
            } 
            
            return false;
        });

        $("#btnSaveForLater").click(function(){                    
            saveListing("user");
                       
            return false;
        });
                

        $("#btnDelete").click(function(){  
            if (confirm("Are you sure you want to delete this listing?")) {
                deleteListing();
            } else {
                
            }
            return false;
        });

        $("#btnCancel").click(function() {
            document.location.href = "update_listing_pre.php";
            return false;
        });

        $("#btnCancel2").click(function() {
            document.location.href = "yourinfo.php";
            return false;
        });
        
        
        // Initialize
        insertCategorySection();
        
        var requestContact = $.ajax({  
            url: "editincludes/getExistingIDs.php?in=contact&uid=" + uniqueId(),  
            type: "GET", 
            success: function(data) {     
                var arrIDs = data.split(",");
                for(i = 0; i < arrIDs.length; i++){
                    insertContactSection(arrIDs[i]);
                }
            },
            error: function(jqXHR, exception) {  
                $.post("includes/recorderror.php", { page: "requestContact:error", message: jqXHR.status + ", " + jqXHR.responseText + ", " + exception + " , "  + jqXHR.responseText } );
            }

        });        
        
        var requestLocation = $.ajax({  
            url: "editincludes/getExistingIDs.php?in=location&uid=" + uniqueId(),  
            type: "GET", 
            success: function(data) {     
                var arrIDs = data.split(",");
                for(i = 0; i < arrIDs.length; i++){
                    insertWhereSection(arrIDs[i]);
                }
            },
            error: function(jqXHR, exception) {  
                $.post("includes/recorderror.php", { page: "requestLocation:error", message: jqXHR.status + ", " + jqXHR.responseText + ", " + exception + " , "  + jqXHR.responseText } );
            }

        });     
        
        var requestDate = $.ajax({  
            url: "editincludes/getExistingIDs.php?in=date&uid=" + uniqueId(),  
            type: "GET", 
            success: function(data) {     
                var arrIDs = data.split(",");
                for(i = 0; i < arrIDs.length; i++){
                    insertDateSection(arrIDs[i], false);
                }
            },
            error: function(jqXHR, exception) {  
                $.post("includes/recorderror.php", { page: "requestDate:error", message: jqXHR.status + ", " + jqXHR.responseText + ", " + exception + " , "  + jqXHR.responseText } );
            }

        });  
        
        initializeCategories(); 
        
        $("#txtTitle").focus();
       
   }
     
    
   if ($("#update_listing_pre").length > 0){
        // Validate email        
        $("#txtEmailPre").change(function() {  
            var urlregex = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i); 
            var inText = $(this).val();

            if ((urlregex.test(inText)) || (inText == "")) {
                $(this).removeClass("txt_alert");
                $("#txtEmailErrPre").text("");
            }else{
                $(this).addClass("txt_alert");
                $("#txtEmailErrPre").text("Not a valid email");
            }
        });

        $("#pre_select_buttons a").click(function() {
            $("#add_pre a").each(function(){
                $(this).removeClass("button_click");
            })

            $(this).addClass("button_click");
            $("#listingtype").val($(this).html());

            return false;
        });


        $("#btnPreGo").click(function() {
            var urlregex = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i); 
            var inEmail = $("#txtEmailPre").val();

            var errMessage = '';

            if (!(urlregex.test(inEmail))) {
                errMessage += 'A valid email is required. \n';
            }

            if ($("#listingtype").val() == '') {
                errMessage += 'A listing type must be selected. \n';
            }

            if (errMessage != '') {
                alert(errMessage);
                return false;            
            } else {                
                $("#overlay_back").css("height", $("#container").outerHeight() + 380).css("width", $(window).width() + $(window).scrollLeft()).css("display", "block");
                openLoadingDiv();

                $.ajax({  
                    url: "editincludes/save_pre.php",  
                    type: "POST",  
                    data: $("#update_listing_pre").serialize(),  
                    success: function(data) {     
                        if (data == "success") { 
                            $("#query_update").submit(); 
                        } else { 
                            closeLoadingDiv();
                            $.post("includes/recorderror.php", { page: "save_pre", message: data } );
                            alert("There was an error.  Please try again.  If this error continues, please contact michelle@cbwire.ca");
                            $("#overlay_back").fadeOut(200, function() {            
                                $("#overlay_back").css("width", "0").css("height", "0").css("display", "none");
                            });
                        }
                    },
                    error: function(jqXHR, exception) {  
                        closeLoadingDiv();
                        $.post("includes/recorderror.php", { page: "save_pre:error2", message: jqXHR.status + ", " + jqXHR.responseText + ", " + exception + " , "  + jqXHR.responseText } );
                        alert("There was an error.  Please try again.  If this error continues, please contact michelle@cbwire.ca");
                        $("#overlay_back").fadeOut(200, function() {            
                            $("#overlay_back").css("width", "0").css("height", "0").css("display", "none");
                        });
                    }
                });  
                     
                
                return false;
            }
        });
        
        $("#txtEmailPre").focus();
    }
    
    
    function insertCategorySection() {
        $.get("editincludes/add_categories.php", function(data) {
            $("#add_categories").append(data);            
            
         //   $("#divSubCategoryBox").hide();
            $("#divSelectedCategoryBox").hide();
         //   $("#aRemoveCategory").hide();
         //   $("#aSelectCategory").hide();

            $("#divMainCategory a").click(function() {
                $("#divSubCategoryBox").stop(true, true);
                $("#divSubCategoryBox").html("Loading...");
                var clickedCategory = $(this).attr("id").substring(8, $(this).attr("id").length);
                currentCategory = $(this).text();
                $("#divMainCategory a").each(function() {
                    $(this).removeClass("selected");
                });
                $(this).addClass("selected");
                $("#divSubCategoryBox").fadeOut("fast", function() {
                    $("#divSubCategoryBox").load("editincludes/get_subcategories.php?in=" + clickedCategory, function() { 
                        $("#divSubCategoryBox a").each(function() {
                            var subCategory = $(this).attr("id");
                            var alreadyThere = false;
                            $("#divSelectedCategoryBox a").each(function() {
                                if ($(this).attr("id") == subCategory) {
                                    alreadyThere = true;
                                }
                            });
                            if (alreadyThere == true) {
                                 $(this).hide();
                            }
                        });
                        $("#divSubCategoryBox a").click(function() {
                            var a = $(this);

                            if (a.hasClass("selected")) {
                                a.removeClass("selected");
                            } else {
                                a.addClass("selected");
                            }

                            $("#aSelectCategory").show();
                            $("#aRemoveCategory").show();
                            $("#divSelectedCategoryBox").show();

                            return false;
                        });
                     });                    
                }).fadeIn("slow");

                return false;
            });

            $("#aSelectCategory").click(function() {
                var someSelected = false;
                $("#divSubCategoryBox a").each(function() {
                    if ($(this).hasClass("selected")) {                        
                        $("#divSelectedCategoryBox").append($(this));
                        
                        // Track which categories chosen
                        $("#selectedCategories").val($("#selectedCategories").val() + "x" + $(this).attr("id").substring(8, $(this).attr("id").length) + "x");
                        $(this).removeClass("selected");
                        $(this).text(currentCategory + ' >> ' + $(this).text());
                        someSelected = true;
                    }            
                });

                if (!(someSelected)) {
                    alert("Please choose a sub-category to add.");
                }

                return false;
            });

            $("#aRemoveCategory").click(function() {
                var someSelected = false;
                $("#divSelectedCategoryBox a").each(function() {
                    if ($(this).hasClass("selected")) {
                        if ($(this).text().indexOf(currentCategory + ' >>') != -1) {
                            $(this).text($(this).text().replace(currentCategory + ' >> ', ''));
                            $("#divSubCategoryBox").append($(this));
                            
                            // Track which category removed
                            $("#selectedCategories").val($("#selectedCategories").val().replace("x" + $(this).attr("id").substring(8, $(this).attr("id").length) + "x", ""));
                        } else {
                            $(this).remove();
                        }
                        $(this).removeClass("selected");
                        someSelected = true;
                    }
                });

                if (!(someSelected)) {
                    alert("Please choose a category under Your Selections to remove.");
                }

                return false;
            });
        })
    }
    
    function insertContactSection(inID) {
        $.get("editincludes/add_contact.php?in=" + inID + "&uid=" + uniqueId(), function(data) {
            strContactSection = data;
            contactSectionCount = contactSectionCount+1;
            if (contactSectionCount > 1) {
               $("#add_contacts").append(strRemoveSection.replace(/secnum/g, "add_contact" + contactSectionCount));                   
               $("#removeadd_contact" + contactSectionCount + " a").click(function() {
                    var clickedSection = $(this).parent().attr("id").substring(6, $(this).parent().attr("id").length);
                    removeSection(clickedSection);
                    var clickedSectionNumber = $(this).parent().attr("id").substring(17, $(this).parent().attr("id").length);
                    $("#contactSections").val($("#contactSections").val().replace("x" + clickedSectionNumber + "x", ""));
                    return false;
                });
            }
            $("#add_contacts").append(strContactSection.replace(/secnum/g, contactSectionCount));
            $("#contactSections").val($("#contactSections").val() + "x" + contactSectionCount + "x");
            $("#txtLink" + contactSectionCount).bind('keyup', function() {
                var clickedSection = $(this).attr("id").substring(7, $(this).parent().attr("id").length);
                liveLinkSearch(clickedSection);
            }).bind('paste', function() {
                var clickedSection = $(this).attr("id").substring(7, $(this).parent().attr("id").length);
                liveLinkSearch(clickedSection);
            });

            $("#closeLiveLinkSearch" + contactSectionCount).click(function() {
                var clickedSection = $(this).attr("id").substring(19, $(this).attr("id").length);
                return closeLiveLinkSearch(clickedSection, true);
            });

            $("#liveLinkSearchResults" + contactSectionCount + " > a").live('click', function(){                
                var clickedSection = $(this).parent().attr("id").substring(21, $(this).parent().attr("id").length);
                
                var clickedItemID = $(this).attr('id');
                if (clickedItemID.length > 8) {
                    clickedItemID = clickedItemID.substring(8, clickedItemID.length);
                    $("#txtLink" + clickedSection).val($(this).text());
                    $("#txtLinkID" + clickedSection).val(clickedItemID);
                }
                return closeLiveLinkSearch(clickedSection, false);
            });
            
            $("#txtOtherCommunity" + contactSectionCount).attr("disabled", "true").addClass("txt_other_disabled");
            $("#labelOther" + contactSectionCount).addClass("label_disabled");
            $("#ddlCommunity" + contactSectionCount).change(function() {                
                var clickedSection = $(this).attr("id").substring(12, $(this).attr("id").length);

                if ($(this).val() == "0") {
                    $("#txtOtherCommunity" + clickedSection).attr("disabled", "").removeClass("txt_other_disabled");
                    $("#labelOther" + clickedSection).removeClass("label_disabled");
                } else {
                    $("#txtOtherCommunity" + clickedSection).val("");
                    $("#txtOtherCommunity" + clickedSection).attr("disabled", "disabled").addClass("txt_other_disabled");
                    $("#labelOther" + clickedSection).addClass("label_disabled");
                }
            });
            
            
            // Validate email
            $("#txtEmail" + contactSectionCount).change(function() {  
                var urlregex = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i); 
                var inText = $(this).val();

                if ((urlregex.test(inText)) || (inText == "")) {
                        $(this).removeClass("txt_alert");
                        $("#txtEmailErr" + contactSectionCount).text("");
                }else{
                        $(this).addClass("txt_alert");
                        $("#txtEmailErr" + contactSectionCount).text("Not a valid email");
                }
            });

            // Validate phone
            $("#txtPhone" + contactSectionCount).change(function() {
                var valid = false;

                var inText = new String($(this).val());
                
                inText = inText.replace(/[^0-9]/g, ''); 

                if (inText.length >= 7) {
                    if (inText.charAt(0) == 1) { inText = inText.substring(1, inText.length); }
                    if (inText.substring(0, 3) == "709") { inText = inText.substring(3, inText.length); }

                    if (inText.length >= 7) {
                        if (inText.length == 7) {
                        $(this).val("(709) " + inText.substring(0, 3) + "-" + inText.substring(3,7));
                        }
                        valid = true;
                    }
                } else if ($(this).val() == "") {
                    valid = true;
                }

                if (valid == true) {
                        $(this).removeClass("txt_alert");
                        $("#txtPhoneErr" + contactSectionCount).text("");
                }else{
                        $(this).addClass("txt_alert");
                        $("#txtPhoneErr" + contactSectionCount).text("Not a valid phone number");
                }
            });
            
            if (contactSectionCount > 1) {
                $("#txtContactName" + contactSectionCount).focus();
            }
        });
    }   
    
    
    
    function insertWhereSection(inID) {
        $.get("editincludes/add_where.php?in=" + inID + "&uid=" + uniqueId(), function(data) {
            strWhereSection = data;
            whereSectionCount = whereSectionCount+1;
            if (whereSectionCount > 1) {
               $("#add_locations").append(strRemoveSection.replace(/secnum/g, "add_location" + whereSectionCount));                   
               $("#removeadd_location" + whereSectionCount + " a").click(function() {
                    var clickedSection = $(this).parent().attr("id").substring(6, $(this).parent().attr("id").length);
                    removeSection(clickedSection);
                    var clickedSectionNumber = $(this).parent().attr("id").substring(18, $(this).parent().attr("id").length);
                    $("#locationSections").val($("#locationSections").val().replace("x" + clickedSectionNumber + "x", ""));

                    return false;
                });
            }
            $("#add_locations").append(strWhereSection.replace(/secnum/g, whereSectionCount));
            $("#locationSections").val($("#locationSections").val() + "x" + whereSectionCount + "x");
            $("#txtLinkW" + whereSectionCount).bind('keyup', function() {
                var clickedSection = $(this).attr("id").substring(8, $(this).parent().attr("id").length);
                liveLinkSearch("W" + clickedSection);
            }).bind('paste', function() {
                var clickedSection = $(this).attr("id").substring(8, $(this).parent().attr("id").length);
                liveLinkSearch("W" + clickedSection);
            });            

            $("#closeLiveLinkSearchW" + whereSectionCount).click(function() {
                var clickedSection = $(this).attr("id").substring(20, $(this).attr("id").length);                
                return closeLiveLinkSearch("W" + clickedSection, true);
            });

            $("#liveLinkSearchResultsW" + whereSectionCount + " > a").live('click', function(){                
                var clickedSection = $(this).parent().attr("id").substring(22, $(this).parent().attr("id").length);
               
                $("#liveLinkSearchW" + clickedSection).css("display", "none");

                var clickedItemID = $(this).attr('id');
                if (clickedItemID.length > 8) {
                    clickedItemID = clickedItemID.substring(8, clickedItemID.length);
                    $("#txtLinkW" + clickedSection).val($(this).text());
                    $("#txtLinkIDW" + clickedSection).val(clickedItemID);
                }
                
                return closeLiveLinkSearch("W" + clickedSection, false);
            });
            
            $("#txtOtherCommunityW" + whereSectionCount).attr("disabled", "true").addClass("txt_other_disabled");
            $("#labelOtherW" + whereSectionCount).addClass("label_disabled");
            $("#ddlCommunityW" + whereSectionCount).change(function() {                
                var clickedSection = $(this).attr("id").substring(13, $(this).attr("id").length);

                if ($(this).val() == "0") {
                    $("#txtOtherCommunityW" + clickedSection).attr("disabled", "").removeClass("txt_other_disabled");
                    $("#labelOtherW" + clickedSection).removeClass("label_disabled");
                } else {
                    $("#txtOtherCommunityW" + clickedSection).val("");
                    $("#txtOtherCommunityW" + clickedSection).attr("disabled", "disabled").addClass("txt_other_disabled");
                    $("#labelOtherW" + clickedSection).addClass("label_disabled");
                }
            });            
            
            if (whereSectionCount > 1) { 
                $("#txtLinkW" + whereSectionCount).focus();
            }
        });
    }      
    
    function insertDateSection(inID, setFocus) {
        $.get("editincludes/add_date.php?in=" + inID + "&uid=" + uniqueId(), function(data) {            
            strDateSection = data;
            dateSectionCount = dateSectionCount+1;
            if (dateSectionCount > 1) {
                $("#add_dates").append(strRemoveSection.replace(/secnum/g, "add_date" + dateSectionCount));
                $("#copydate" + (dateSectionCount-1) + " a").show();
                   
                $("#removeadd_date" + dateSectionCount + " a").click(function() {
                    var clickedSection = $(this).parent().attr("id").substring(6, $(this).parent().attr("id").length);
                    removeSection(clickedSection);                   

                    var arrIDs = $("#dateSections").val().split("xx"); 
                    var i = arrIDs.length-1;
                    arrIDs[i] = arrIDs[i].replace("x", "");
                    if (("add_date" + arrIDs[i] == clickedSection) && (i > 1)) {
                        var lastSection = arrIDs[i-1];
                        lastSection = lastSection.replace("x", "");
                        $("#copydate" + (lastSection) + " a").hide();
                    }
                    
                    var clickedSectionNumber = $(this).parent().attr("id").substring(14, $(this).parent().attr("id").length);
                    $("#dateSections").val($("#dateSections").val().replace("x" + clickedSectionNumber + "x", ""));                    
                                 
                    return false;
                });
            } else {
                $("#add_dates").append("<div class='remove_section'></div>");
            }
            $("#add_dates").append(strDateSection.replace(/secnum/g, dateSectionCount));
            
            if ($("#ddlRecurrance" + dateSectionCount).val() == "none") {
                $("#txtExpiry" + dateSectionCount).attr("disabled", "disabled").addClass("txt_date_disabled");
                $("#label_until" + dateSectionCount).addClass("label_disabled");
            } else {                
                $("#txtExpiry" + dateSectionCount).attr("disabled", "").removeClass("txt_date_disabled");
                $("#label_until" + dateSectionCount).removeClass("label_disabled");
            }
            $("#copydate" + (dateSectionCount) + " a").hide();
            $("#dateSections").val($("#dateSections").val() + "x" + dateSectionCount + "x");
            $("#txtStartDate" + dateSectionCount).datepicker({dateFormat: 'yy-mm-dd'});
            $("#txtExpiry" + dateSectionCount).datepicker({dateFormat: 'yy-mm-dd'});   
             
             $("#ddlRecurrance" + dateSectionCount).change(function() {
                    var clickedSection = $(this).attr("id").substring(13, $(this).attr("id").length);

                    if ($(this).val() == "none") {
                        $("#txtExpiry" + clickedSection).val("");
                        $("#txtExpiry" + clickedSection).attr("disabled", "disabled").addClass("txt_date_disabled");
                        $("#label_until" + clickedSection).addClass("label_disabled");
                    } else {
                        $("#txtExpiry" + clickedSection).attr("disabled", "").removeClass("txt_date_disabled");
                        $("#txtExpiry" + clickedSection).change(function() {
                            var clickedSection = $(this).attr("id").substring(9, $(this).attr("id").length);                            
                        });
                        $("#label_until" + clickedSection).removeClass("label_disabled");
                    }
                    
                    generateDateSample(clickedSection);
             });
             $("#txtStartDate" + dateSectionCount + ", #txtStartTime" + dateSectionCount).change(function() {
                    var clickedSection = $(this).attr("id").substring(12, $(this).attr("id").length);
                    generateDateSample(clickedSection);                
             });
             $("#ddlStartAM" + dateSectionCount + ", #txtEndTime" + dateSectionCount).change(function() {
                    var clickedSection = $(this).attr("id").substring(10, $(this).attr("id").length);
                    generateDateSample(clickedSection);                
             });
             $("#txtExpiry" + dateSectionCount).change(function() {
                    var clickedSection = $(this).attr("id").substring(9, $(this).attr("id").length);
                    generateDateSample(clickedSection);                
             });
             $("#ddlEndAM" + dateSectionCount ).change(function() {
                    var clickedSection = $(this).attr("id").substring(8, $(this).attr("id").length);
                    generateDateSample(clickedSection);                
             });
             $("#copydate" + (dateSectionCount-1) + " a").click(function(){
                 var clickedSection = $(this).parent().attr("id").substring(8, $(this).parent().attr("id").length);
                 var nextSection = 0;
                 var arrIDs = $("#dateSections").val().split("xx");
                    for(i = 0; i < arrIDs.length; i++){
                        arrIDs[i] = arrIDs[i].replace("x", "");
                        if ((arrIDs[i] == clickedSection) && (i < (arrIDs.length-1))) {
                            nextSection = arrIDs[i+1];
                            nextSection = nextSection.replace("x", "");
                        }
                    }              
                 
                 $("#txtStartDate" + nextSection).val($("#txtStartDate" + clickedSection).val());
                 $("#txtExpiry" + nextSection).val($("#txtExpiry" + clickedSection).val());
                 $("#txtStartTime" + nextSection).val($("#txtStartTime" + clickedSection).val());
                 $("#txtEndTime" + nextSection).val($("#txtEndTime" + clickedSection).val());
                 $("#ddlEndAM" + nextSection).val($("#ddlEndAM" + clickedSection).val());
                 $("#ddlStartAM" + nextSection).val($("#ddlStartAM" + clickedSection).val());
                 $("#ddlRecurrance" + nextSection).val($("#ddlRecurrance" + clickedSection).val());
                 
                 if ($("#ddlRecurrance" + nextSection).val() == "none") {
                        $("#txtExpiry" + nextSection).val("");
                        $("#txtExpiry" + nextSection).attr("disabled", "disabled").addClass("txt_date_disabled");
                        $("#label_until" + nextSection).addClass("label_disabled");
                    } else {
                        $("#txtExpiry" + nextSection).attr("disabled", "").removeClass("txt_date_disabled");
                        $("#label_until" + nextSection).removeClass("label_disabled");
                    }
                 
                 return false;
             });     
            
            if ((dateSectionCount > 1) && (setFocus == true)) {
                $("#txtStartDate" + dateSectionCount).focus();
            } else if (setFocus == false) {
                $("#txtTitle").focus();
            }
        });
    }
    
    function generateDateSample(inSecNum) {
        var dateSample = '';        
        var errMessage = '';
	var strParams="";
	// Trim spaces off title: replace(/^\s\s*/, '').replace(/\s\s*$/, ''). 
	var txtstartdate = encodeURIComponent($("#txtStartDate" + inSecNum).val().replace(/^\s\s*/, '').replace(/\s\s*$/, ''));	
	var txtstarttime = encodeURIComponent($("#txtStartTime" + inSecNum).val().replace(/^\s\s*/, '').replace(/\s\s*$/, ''));	
	var txtendtime = encodeURIComponent($("#txtEndTime" + inSecNum).val().replace(/^\s\s*/, '').replace(/\s\s*$/, ''));	
	var txtenddate = encodeURIComponent($("#txtExpiry" + inSecNum).val().replace(/^\s\s*/, '').replace(/\s\s*$/, ''));	
        	
	var ddlstartam = $("#ddlStartAM" + inSecNum).val();
	var ddlendam = $("#ddlEndAM" + inSecNum).val();
	
	var ddlrecursive = $("#ddlRecurrance" + inSecNum).val();
	
	strParams += "txtStartDate=" + txtstartdate + "&txtStartTime=" + txtstarttime + "&txtEndTime=" + txtendtime;
	strParams += "&ddlStartAM=" + ddlstartam + "&ddlEndAM=" + ddlendam + "&ddlRecursive=" + ddlrecursive + "&txtExpiry=" + txtenddate;

        if (txtstartdate == '') {
             errMessage += "On Date is required. ";
             $("#txtStartDate" + inSecNum).addClass("txt_alert");
        } else if(!isDate(txtstartdate)) {
            errMessage += "On Date " + $("#txtStartDate" + inSecNum).val() + " is not a proper date. ";
             $("#txtStartDate" + inSecNum).addClass("txt_alert");
        } else {            
             $("#txtStartDate" + inSecNum).removeClass("txt_alert");
        }
        
        
        $("#txtStartTime" + inSecNum).removeClass("txt_alert");
        if (txtstarttime != '') {
            if (!isTime($("#txtStartTime" + inSecNum).val())) {
                errMessage += "From Time " + $("#txtStartTime" + inSecNum).val() + " is not a proper time. ";                
                $("#txtStartTime" + inSecNum).addClass("txt_alert");
            }
        }
        
        $("#txtEndTime" + inSecNum).removeClass("txt_alert");
        if (txtendtime != '') {
            if (!isTime($("#txtEndTime" + inSecNum).val())) {
                errMessage += "To Time " + $("#txtEndTime" + inSecNum).val() + " is not a proper time. ";          
                $("#txtEndTime" + inSecNum).addClass("txt_alert");
            } else if (txtstarttime == '') {
                errMessage += "From Time is required when an To Time is entered. ";          
                $("#txtStartTime" + inSecNum).addClass("txt_alert");
            }
        }
        
        $("#txtExpiry" + inSecNum).removeClass("txt_alert");
        if (txtenddate != '') {
             if (!isDate(txtenddate)) {
                errMessage += "Until Date " + $("#txtExpiry" + inSecNum).val() + " is not a proper date. ";          
                $("#txtExpiry" + inSecNum).addClass("txt_alert");
            }
        }
        
        if (errMessage == '') {
            $("#txtDateErr" + inSecNum).html("");           
            $("#txtDateSample" + inSecNum).load("includes/sample_when.inc.php?ondate=" + txtstartdate + "&fromtime=" + txtstarttime + "&totime=" + txtendtime + "&onam=" + ddlstartam + "&toam=" + ddlendam + "&recursive=" + ddlrecursive + "&until=" + txtenddate);
        } else {
            $("#txtDateSample" + inSecNum).html("");
            $("#txtDateErr" + inSecNum).html(errMessage);
        }
    }
    
    function removeSection(inSection) {
        if (confirm("Are you sure you want to remove this section?  Any information entered will permanently removed.")) {
            $("#" + inSection).remove();
            $("#remove" + inSection).remove();
        }
    }
        
    
    
    /*               ***********                  */
    /*               Validation                   */
    /*               ***********                  */
    function checkCharCount(){ 
        textfield = document.getElementById('txtDescription');    
        var charCount = 500 - textfield.value.length;
            if (charCount < 0) {
                    document.getElementById("charCounter").innerHTML = "* Description is too long. Remove " + (0-charCount) + " characters *";
            } else {
                    document.getElementById("charCounter").innerHTML = charCount;
            }
    }
    

    String.prototype.trim = function () {
        return this.replace(/^\s*/, "").replace(/\s*$/, "");
    }

    function isDate(value) {
        try {
            //Change the below values to determine which format of date you wish to check. It is set to dd/mm/yyyy by default.
            var DayIndex = 2;
            var MonthIndex = 1;
            var YearIndex = 0;

            value = value.replace(/-|\./g, "/"); 
            var SplitValue = value.split("/");
            var OK = true;
            if (!(SplitValue[DayIndex].length == 1 || SplitValue[DayIndex].length == 2)) {
                OK = false;
            }
            if (OK && !(SplitValue[MonthIndex].length == 1 || SplitValue[MonthIndex].length == 2)) {
                OK = false;
            }
            if (OK && SplitValue[YearIndex].length != 4) {
                OK = false;
            }
            if (OK) {
                var Day = parseInt(SplitValue[DayIndex], 10);                
                var Month = parseInt(SplitValue[MonthIndex], 10);
                var Year = parseInt(SplitValue[YearIndex], 10);

                if (OK = (Year >= new Date().getFullYear())) {
                    if (OK = (Month <= 12 && Month > 0)) {
                        var LeapYear = (((Year % 4) == 0) && ((Year % 100) != 0) || ((Year % 400) == 0));
                        
                        if (Month == 2) {
                            OK = LeapYear ? Day <= 29 : Day <= 28;
                        }
                        else {
                            if ((Month == 4) || (Month == 6) || (Month == 9) || (Month == 11)) {
                                OK = (Day > 0 && Day <= 30);
                            }
                            else {
                                OK = (Day > 0 && Day <= 31);
                            }
                        }
                    }
                } 
                
                if (!(OK)) {
                
                }
            } else {
            
            }
            return OK;
        }
        catch (e) {            
            return false;
        }
    }
    

    function isTime(value) {
        try {
            var HourIndex = 0;
            var MinuteIndex = 1;
 
            var SplitValue = value.split(":");
            var OK = true;
            if (OK && !(SplitValue[HourIndex].length == 1 || SplitValue[HourIndex].length == 2)) {
                OK = false;
            }
            if (OK && !(SplitValue[MinuteIndex].length == 2)) {
                OK = false;
            }
            if (OK) {
                var Hour = parseInt(SplitValue[HourIndex], 10);                
                var Minute = parseInt(SplitValue[MinuteIndex], 10);

                if (OK = (Hour <= 12 && Hour > 0)) {
                    OK = (Minute <= 59 && Minute >= 0);
                }
                
                if (!(OK)) {
                     
                }
            } else {
                
            }
            return OK;
        }
        catch (e) {            
            return false;
        }
    }    
       
    $("button").hover(function(){
        $(this).addClass("button_hover");
    }, function(){
        $(this).removeClass("button_hover");
    });
    
    $("button").click(function(){
        $(this).addClass("button_click");
    });
    
    $(".section").show(2000, function() {
        $(this).css("display", "inline-block");    
    });
    
    $("#overlay_back").click(function() {  
        $("#overlay_back").fadeOut(200, function() {            
            $("#overlay_back").css("width", "0").css("height", "0").css("display", "none");
        });    
        
        if (currentLiveLinkSearch != "") {
            $(currentLiveLinkSearch).css("display", "none");
            $(currentLiveLinkText).val("").css("z-index", "1");
        }
    });
})