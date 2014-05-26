# Talks

## About Talks

Talks is a web application project to manage talks sessions.
Originally developed as an internal competition for Parrot S.A. by Alvaro Moran, copyright 2014, it was later dropped, the company preferred a different solution. 

I still think this one is a cleaner solution, so I decided to share it anyway.
This web application is based on [Laravel](http://laravel.com/), a php framework to develop MVC web applications. It uses [Twitter Bootstrap](http://getbootstrap.com/) for the UI and few other projects for the interface.

## Features

- List of available talks as main page
- Users management (creation, edit) with different rights levels
- Talks management (creation, edit, deletion), with attachments
- Users can subscribe to an event
- Managers can approve/refuse a user to attend to a talk
- Developed with simplicity in mind

## Composer

You need to install composer in order to bring up the application. To do so just type:

	php -r "readfile('https://getcomposer.org/installer');" | php

This will install the composer package manager. Go on installing the talks packages:	

	./composer.phar install


## Setup

These Setup comments were written with Debian in mind.
Beware of setting the correct permisisons. For example, if Talks resides in a directory called `talks`:

	chown -R root:www-data talks
	cd talks
	chmod -R gu+w app/storage
	
Apache should be configured in order to point to the public folder. For example, your `/etc/apache2/sites-available/talks` could look like this one:

	<VirtualHost *:80>
	DocumentRoot /var/www/talks/public
	<Directory />
		Options FollowSymLinks
		AllowOverride All
	</Directory>
	</VirtualHost>

Another issue could be the apache rewrite module. On Debian, you could run:

	a2enmod rewrite
	service apache2 restart
	
Finally, you should configure your `app/config/app.php`.

For database configuration edit `app/config/database.php` with the database information needed.
To bing up a database you should do:

	php artisan migrate:install
	php artisan migrate:refresh --seed
	
This will setup Sessions' tables and an administrator user called admin with password set to "password" and few more test data.


## GRR Integration

[GRR](http://grr.mutualibre.org/) is a room reservation system quite popular in France.  
In order to activate GRR integration, set `use_grr` to `true` in your application configuration (`app/config/app.php`) and set up the GRR connection settings in the same file.

The integration assumes that GRR has a `Talk` area type (it could seed it) and the Room have all different names.
Creating, modifying and deleting talks will make the relevant changes to the GRR database.

Do not edit the reservation if you want Talks to be able to recognise the talks related entries.

## Languages

Talks supports the following languages: English, French.

If you want to change language you can set it in the `app/config/app.php` file. If you want to modify any text, just check the `app/lang` directory.
