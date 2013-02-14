<?php
/* ====================
 [BEGIN_COT_EXT]
Code=spam_protection
Name=Spam Protection
Category=security-authentication
Description=Filters spam with your choice of services: Akismet, Defensio or Typepad Anti-Spam
Version=1.0
Date=2013-01-30
Author=tyler@xaez.org
Copyright=
Notes=BSD License
Auth_guests=R
Lock_guests=12345A
Auth_members=RW
Lock_members=
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
service_type=01:callback:spam_protection_get_services():Akismet:Spam service API to use
service_key=02:string:::Spam service API key
filter_comments=03:radio::1:Filter comment spam
filter_forums=04:radio::1:Filter forum spam
maxperpage=05:select:5,10,15,20,25,30,35,40,45,50,65:15:Max spam items to display in administration tool
force_all_as_spam=06:radio::0:Send all items into spam collection no matter what ?
notify_poster=07:radio::0:Notify user when their item is marked as spam ?
use_ajax=08:radio::1:Use AJAX in plugin's administration tool
[END_COT_EXT_CONFIG]
==================== */


?>