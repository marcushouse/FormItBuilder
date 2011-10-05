----------------------
Snippet: FormItBuilder
----------------------
Version: 0.1.5 beta
Created: October 5, 2011
Author: Marcus House

A small framework to assist in quick development of FormIt forms and emails. Using a snippet (with some basic PHP commands) a complex form can be built much faster, and can automatically use the jQuery plugin "Validation" methods
http://bassistance.de/jquery-plugins/jquery-plugin-validation/

FormItBuilders main purpose is to act as a wrapper to simplify much of the FormIt syntax and automatically build forms and email chunks dynamically without the need to duplicate HTML code and FormIt tags. If the output is not 100% appropriate for the job, FormItBuilder can output the raw form HTML to be used and modified as needed (like any FormIt form). Likewise email output can also be output in this same manner.

To use follow the instructions below and check out the example snippet. There will be much more functionality and help information to come.

Need a feature or form element that is not yet supported or documented? Please ask in the forums.

-------------------------
INSTALLATION INSTRUCTIONS
-------------------------

*********
*STEP 1:*
*********
Install the package and create a new snippet for your first form called "FormItBuilder_MyContactForm". Copy the snippet contents from the example in
core/components/formitbuilder/elements/snippets/snippet.formitbuilder.php
into the FormItBuilder_MyContactForm snippet.

*********
*STEP 2:*
*********
Download the latest jQuery from
http://jquery.com/
and jQuery Validator plugin from
http://bassistance.de/jquery-plugins/jquery-plugin-validation/
include them in your modX template as shown in the next step.

*********
*STEP 3:*
*********
Create a basic template with the following

<html>
<head>
<title>[[++site_name]] - [[*pagetitle]]</title>
<base href="[[!++site_url]]" />
<script src="assets/js/jquery-1.6.2.min.js" type="text/javascript"></script>
<script src="assets/js/jquery.validate-1.8.1.min.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="assets/css/stylesheet.css" />
</head>
<body>
[[*content]]
</body>
</html>

*********
*STEP 4:*
*********
Create your stylesheet (sample below)
body,input,select,textarea{font-family:Helvetica,Arial,sans-serif; font-size:13px;}
.form .formSegWrap{    width:450px; overflow:auto;    padding:0px 5px 5px 0px;}
.form .formSegWrap>label{ float:right; width:220px; display:block; padding:3px;}
.form input[type="text"], .form textarea, .form input[type="password"]{    width:210px; }
.form input[type="text"], .form textarea, .form select, .form input[type="password"]{ border:1px solid #a9bbd6;    padding:3px; border-radius:3px;    color:#233156;}
.form input[type="text"]:focus, .form textarea:focus, .form select:focus, .form input[type="password"]:focus{background-color:#f4f7fb; border-color:#4586e7;}
.form textarea{font-size:90%;}

.form textarea.required, .form input[type="text"].required, .form input[type="password"].required{ background-image:url('../components/formitbuilder/images/field_required.png'); background-repeat:no-repeat; background-position:right 2px;}
.form textarea.error, .form input[type="text"].error, .form input[type="password"].error{ background-color:#ffe8e8;    border:1px solid #c04242; color:#c04242; }
.form textarea.valid, .form input[type="text"].valid, .form input[type="password"].valid{ background-image:url('../components/formitbuilder/images/field_valid.png');    background-repeat:no-repeat; background-position:right 2px;}
   
.form input[type="submit"], .form input[type="button"], .form input[type="reset"]{ border:1px solid #a9bbd6; padding:2px; min-width:110px; border-radius:3px; background-color:#b0cde8;cursor:pointer;}
.form input[type="submit"]:hover, .form input[type="button"]:hover ,.form input[type="reset"]:hover{ background-color:#8ca2d9; }
.form .formSegWrap_submit, .form .formSegWrap_reset{ float:left;    width:auto;    margin:10px 10px 10px 0px; }
.form label.mainLabelError{    font-weight:bold; color:#990000;}
.form .errorContainer label.error{    font-size:11px;    display:block;    color:#FF0000;    clear:both;}
.form{ margin:10px; background-color:#e9edf3;    border-radius:5px;    border:1px solid #c8d3e3; padding:10px;    overflow:hidden; width:450px; color:#233156;}

.form hr.formSpltter{ border-top:1px dashed #c8d3e3; border-bottom:none; height:1px; margin:5px 0px 5px 0px;}
.form h2{ font-size:16px; font-weight:bold;    padding:2px 0px 5px 0px;}

.form .checkboxes .formSegWrap{    width:255px;}
.form .errorContainer .error{ display:block; /* for Non jQuery Validate form errors with multiple warnings */ }
.form .process_errors_wrap{    color:#FF0000;}
.form .formSegWrap_staff_performance .radioGroupWrap{float:left;}
.form .radioWrap{clear:both;}
.form .radioWrap label{display:inline-block; padding:2px;}
.form .radioWrap .radioEl{ float:left;    clear:both;}

*********
*STEP 5:*
*********
Create a new content resource using the template above, then add teh following to the content
[[!FormItBuilder_MyContactForm? &outputType=`dynamic`]]

*********
*STEP 6:*
*********
Hit the page from the website and take a look at the output. You should end up with a very similar example as shown in the screenshot.