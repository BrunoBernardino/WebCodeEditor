## AUTHOR ##

Bruno Bernardino @ http://www.brunobernardino.com

## REQUIREMENTS ##

Connections to the servers are made through SFTP, and you need ssh2 for PHP.

On Debian, the installation is like this:

# apt-get install libssh2-1-dev && pecl install ssh2 channel://pecl.php.net/ssh2-0.11.3

That should be enough.

## SETUP/INSTALL ##

All the settings you need to change/setup to get this working are on inc/functions.php and are commented with "//-- Change This" (except quotes).

The database structure is on bb_code.sql

## NOTES ##

In the add/edit form:
- Clown = Username
- Joke = Password

Though this is optimized to work on a Chromebook, some commented code on js/editor.js will make it work better on Chrome for Mac, for example.

There is no password protection (I'm assuming .htpasswd is enough). 

You can add/edit/delete servers on the simple back-office at /admin.php

The usernames and passwords are encrypted in the database. 

## CREDITS ##

The Highlighter/Editor is CodeMirror ( http://codemirror.net/ ) and only editing modes for CSS, HTML, JS, MySQL, PHP and XML are included (there are a lot more, as well as themes, etc.).

I use Twitter's Bootstrap 2.0 ( http://twitter.github.com/bootstrap/index.html ), and everything about it is available in the app.