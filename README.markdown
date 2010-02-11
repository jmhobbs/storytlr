# What is Storytlr? #

<blockquote>Storytlr is an open source lifestreaming and micro blogging platform. You can use it for a single user or it can act as a host for many people all from the same installation.</blockquote>

And...

<blockquote># Import your web 2.0 life: Pick your sources and they will appear as a lifestream directly on your site. We currently support the following services: Delicious, Digg, Disqus, Flickr, Google Reader, Identi.ca / Laconi.ca, Last.fm favorites, Picasa, Qik, RSS Feeds, Seesmic, StumbleUpon, Tumblr, Twitpic pictures in Twitter tweets, Twitter, Vimeo, Youtube Favorites</blockquote>

# More information #

You can find more information about Storytlr online at <http://storytlr.googlecode.com/>

# What else is in this fork? #

This fork is a working copy that  includes several patches not present in 0.9.2.  Eventually most of these changes will find their way into core.

* A tool to rename users (dangerous if not handled properly)
* Plugins built in for...
  * [Github](http://github.com/)
  * [goodreads](http://goodreads.com/)
  * [Foursquare](http://www.foursquare.com/)
  * [Stack Overflow](http://www.stackoverflow.com/)
  * [Twitter favorites](http://www.twitter.com/)
  * [Google Buzz](http://buzz.google.com/)
* Ability to delete items that are from other sources (not just ones from Storytlr itself)
* tidy is replaced by [htmLawed](http://code.google.com/p/htmlawed/)
* Misc fixes for issues reported on the Google group
* Misc fixes from other forks, esp. paths courtesy of [Stuart Herbert](http://github.com/stuartherbert)

# Installation #

1. Clone the repo to where you want it
2. Edit /protected/config/config.ini
3. Load /protected/install/database.sql
4. Sign in with user "admin" password "storytlr"
5. Change your password
6. Install sources

# Switch From 0.9.2 Core #

1. Clone the repo over your source (or out of tree and switch them)
2. Load /protected/install/migration-0.9.2.sql
3. Check your /protected/config/config.ini
4. Enjoy

# Tracking This Fork #

Once you have switched to this fork it will update quickly. There are a migration SQL files in /protected/install/migration-*.sql that should always be safe to apply to your database, they only alter or create as needed.

So, the update steps are:

1. Run <tt>git pull origin master</tt>
2. Import the latest migration SQL file (or all of them to be safe)

# Help! #

To get help for _this specific_ fork, you can contact me on twitter @jmhobbs

For general Storytlr help you can try the Storytlr community at <http://storytlr.googlecode.com/> or in #storytlr on [freenode](http://freenode.net/).
