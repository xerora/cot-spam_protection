
## What this plugin does

- Filters comment, forum and other spam with your choice of services (Akismet, Defensio, Typepad Anti-Spam). Comment and forum spam filtering is disabled by default. ( It is not recommended to use non-custom adapters for filtering if you use plugins that alter the extension you are filtering spam on. This could have unexpected results unless you know the specifics.)
- Provides an administration tool to manage items marked as spam.
- Allows you to filter spam in your extensions.
- Add your own section adapters to enable your extensions to use the spam moderation queue when filtering spam.

## Installation

1. Decide which spam service you wish to use. Register at the service you choose to use and obtain an API key.
	- Akismet: http://akismet.com
	- Defensio: http://defensio.com
	- Typepad Anti-Spam: http://antispam.typepad.com
2. Download, unpack and upload the spam_protection plugin folder to your plugin directory.
3. Install the plugin in the administration panel.
4. Go into the plugin's configurations in the admin panel and select your spam service and enter your API key.
5. Check to see that everything is configured to your preference.
6. The rest is up to your spam service and you can find all spam items caught in Administration -> Other -> Spam Protection

## Adding spam filtering to your extensions

An example of what it would look like to add spam filtering to your extension:

```PHP
if(cot_plugin_active('spam_protection'))
{
	require_once cot_incfile('spam_protection', 'plug');
	$spam_data = array(
		'content' => $msg['text'],
		'authorname' => $usr['name'],
		'authoremail' => $usr['profile']['user_email'],
		'authorid' => $usr['id'],
		'authorip' => $usr['ip'],
		'date' => $sys['now'],
		'section' => 'guestbook',
		'data' => array(
			'guestbook' => $msg
		), 
	);
	$spam_check_result = spam_protection_check($spam_data);
	if($spam_check_result['is_spam'])
	{
		// Item was returned as spam
		spam_protection_queue_add($spam_data); // Add to the moderation queue if you want. Not required.
	}
}
```

See __spam_protection_check__ below for more options to pass.

## Available Tags

### forums.posts.tpl

###### Block: FORUMS_POSTS_ROW
* `{SP_MARK_AS_SPAM_URL}`: The URL for for sending false negatives back to the spam service and deleting the item.
* `{SP_MARK_AS_SPAM_LINK}`: A resource that is formatted into a link for marking an item as spam.

### comments.tpl

###### Block: COMMENTS_ROW
* `{SP_MARK_AS_SPAM_URL}`: The URL for for sending false negatives back to the spam service and deleting the item.
* `{SP_MARK_AS_SPAM_LINK}`: A resource that is formatted into a link for marking an item as spam.

## Section adapters

Section adapters allow you to add your own spam filtered items to the moderation queue. 

To enable queue moderation on your custom filtered items, simply:

- Create a file named after the section you assigned your items to (e.g `'section' => 'guestbook'` would be guestbook.php) and place it
in adapters/sections. Go to the admin panel and clear the internal cache on `sp_available_sections`. You will now see your items that have been marked for spam in the admin tool of this plugin.
- To be able to take action on the items in the queue, you must define the functions spam_protection_queue_ham(array $item, $service, array $data) for the Mark as Ham action and spam_protection_queue_spam(array $item, $service, array $data) for the Mark as Spam action in your adapter. You don't have to define all the params if you don't want, but they will be sent in that order. You will need to write how you want to deal with an item when you mark it for an action. Items will be ran through these functions one at a time. Refer to the "Available callback functions" section in the README for further information on possible options.

For further information, you can refer to the comments.php adapter as it is a simple example.

## Available callback functions in section adapters

A list of functions that will get used at the time you mark an item for spam or ham in the moderation queue if defined. 

#### spam_protection_queue_ham(array $item, $service, array $data)
 
Used on each item one at a time when you use the action 'Mark as Ham' in the moderation queue.

* `$item`: An array of the spam_protection table row data for the item.
* `$service`: The service object that contains the spam data already setup.
* `$data`: An array of data from sp_data row in spam_protection. Contains data that was with held when item was sent to spam queue.

#### spam_protection_queue_spam(array $item, $service, array $data)

Used on each item one at a time when you use the action 'Mark as Spam' in the moderation queue. 

* `$item`: An array of the spam_protection table row data for the item.
* `$service`: The service object that contains the spam data already setup.
* `$data`: An array of data from sp\_data row in spam\_protection. Contains data that was withheld when item was sent to spam queue.

#### spam_protection_queue_after($type)

Ran after the whole queue is marked. 

* `$type`: Either 'spam' or 'ham'

#### spam_protection_queue_before($type)

Ran before the queue is marked. 

* `$type`: Either 'spam' or 'ham'

#### spam_protection_check_data($type, $data)

Ran before the item is marked and can be used to validate/change the data before being marked.

* `$type`: Either 'spam' or 'ham'.
* `$data`: An array of spam data from the spam_protection database table for the item being marked.

## Available service adapter functions

Functions that are available from any spam service that you use and can be used anywhere you include this plugin.

#### spam_protection_service_connection()

Used to create a new service object for use with the spam service

* `return`: The service object that has been created.

##### spam_protection_service_setup($service, array $data)

Used to load the service object with spam data.

* `$service`: The service object
* `$data`: An array of the spam data to be checked / used
* `return`: Service object with spam data loaded

#### spam_protection_service_validate_key([$service])

Validate the spam service API key entered for the service.

* `$service`: _optional_, A service object. One will be created if not passed.
* `return`: A boolean whether the key is valid or not.

#### spam_protection_service_submit_ham($service)

Submit false positives to the spam service in order to make it more knowledgeable in the future.

* `$service`: A service object with spam data loaded into it.
* `return`: A boolean whether the submit was completed or not.

#### spam_protection_service_submit_spam($service)

Submit false negatives to the spam service in order to make it more knowledgeable in the future.

* `$service`: A service object with spam data loaded into it.
* `return`: A boolean whether the submit was completed or not.

```PHP
$service = spam_protection_service_connection();
$service = spam_protection_service_setup($service, array(
	'content' => $row['text'],
	'authorname' => $row['author'],
	'authorid' => $row['authorid'],
	'authorip' => $row['authorip'],
	'date' => $row['date'],
));
spam_protection_service_submit_spam($service);
```

#### spam_protection_queue_add($data)

Send spam data to moderation queue for further review. Items marked as spam can still be false positive.

* `$data`: An array of data. See __spam_protection_check__ for options for `$data`

#### spam_protection_queue_remove($id)

Remove item from moderation queue. Just deletes the record from the spam_protection database table.

* `$id`: The integer for the item in spam_protection database table.

#### spam_protection_check($data)

Send data to the spam service for validation.

* `$data`: An array of data to validate against.
	
	__Options__ 
	* `content`: The body of text you need to review.
	* `authorname`: The name of the author.
	* `authoremail`: The email address of the author.
	* `authorid`: The Cotonti user id of the author.
	* `authorip`: The IP address of the author.
	* `referrer`: The request referrer.
	* `date`: The current date timestamp at the time of the posting.
	* `section`: A simple name or plugin name that is a "file safe" name. This will be your section adapter name if you send the item to the moderation queue.
	* `subsection`: Provide some sort of unqiue identifier for taking special actions on an item in a section when marking as ham/spam in the moderation queue. (eg. section is 'forum' and subsection could be 'post')
	* `data`: An array in which you store data that is withheld from being submitted into the database. This data can be used to restore your item when you mark it as ham in the moderation queue.
* `return`: An array with a key 'is_spam' that is a boolean stating whether item is spam or not.

```PHP
$spam_data = array(
	'content' => 'blahblah viagra blahblah',
	'authorname' => 'viagra-user',
	'authoremail' => 'viagra@drugs.com'
);
$result = spam_protection_check($spam_data);
if($result['is_spam'])
{
	// is spam
}
else
{
	// not spam
}
```

## Complete removal

The following is to completely remove this plugin.
 
1. Uninstall the plugin in the Cotonti plugin administration
2. Delete the plugin folder "spam_protection" in your plugins folder
3. Delete the database table ( usually cot\_spam_protection ) in phpMyAdmin or the likes

## Notes

- You can NOT enable multiple services for filtering an item. You can only have one spam filtering service running.
- You can rename the spam_protection database table by defining it in your config.php:

	```$db_spam_protection = 'cot_spam_protection';```

	Note: Make sure the table exists. You must ensure you renamed your database table if you have already installed the plugin.
	This plugin creates the database table when installed through the administration panel. You can just reinstall the plugin after
	defining the new table name in the config but ensure the old table is removed as it is not needed.

- Spam filtering is disabled when NO API key is present in the plugin configuration. Your site will continue working as normal if your API key is not present or not valid, but spam services will not work.