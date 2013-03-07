Zendesk
=======

Cakephp 2.3+ Zendesk Plugin

Add the following to your project's Config/bootstrap.php

```php
CakePlugin::loadAll(array('Zendesk'));
```

Add the following to your projects Config/database.php and edit as necessary

```php
public $Zendesk = array(
	'datasource' => 'Zendesk.ZendeskSource',
	'host' => 'yoursubdomain.zendesk.com',
	'apiUser' => 'Your user email addres',
	'apiKey' => 'Your api key'
```