----------------------
Snippet: FormItBuilder
----------------------
Version: 0.1.3 beta1
Created: September 4, 2011
Author: Marcus House

A small framework to assist in quick development of FormIt forms and emails. Using a snippet (with some basic PHP commands) a complex form can be built much faster, and can automatically use the jQuery plugin "Validation" methods
http://bassistance.de/jquery-plugins/jquery-plugin-validation/

FormItBuilders main purpose is to act as a wrapper to simplify much of the FormIt syntax and automatically build forms and email chunks dynamically without the need to duplicate HTML code and FormIt tags. If the output is not 100% appropriate for the job, FormItBuilder can output the raw form HTML to be used and modified as needed (like any FormIt form). Likewise email output can also be output in this same manner.

To use check out the see examples. There will be much more functionality and help information to come.

Need a feature or form element that is not yet supported or documented? Please ask in the forums.

-------------------------
Installation Instructions
-------------------------
STEP 1:
-------
Install this package :)

STEP 2:
Add jQuery javascript
http://jquery.com/
and jQuery Validation javascript
http://bassistance.de/jquery-plugins/jquery-plugin-validation/
to your html head like so
<script src="assets/js/jquery.min.js" type="text/javascript"></script> 
<script src="assets/js/jquery.validate-1.8.1.min.js" type="text/javascript"></script>

STEP 3:
Create a resource and add this snippet code
[[!FormItBuilder_MyContactForm? &outputType=`dynamic`]]

STEP 4:
Add CSS to style your form
(See example CSS at rtfm.modx.com)

STEP 5:
Test the example form.

STEP 6:
Copy the "FormItBuilder_MyContactForm" snippet and create your own form.
Future updates will overwrite this example snippet. So be sure to make your own.
Full documentation on creating your own form snippet is underway.
Visit rtfm.modx.com for the latest documentation.