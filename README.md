KNMI client
-----------

This repository contains a very simplistic PHP client for the KNMI [hourly](http://projects.knmi.nl/klimatologie/uurgegevens/selectie.cgi) 
and [daily](http://www.knmi.nl/nederland-nu/klimatologie/daggegevens) endpoints. The response from KNMI is rather
unsuable and this client returns a nice formatted array. Not the best you can get, but better than the original.

# Usage

Since we're using [php-http](http://docs.php-http.org/en/latest/) you're free to choose your own HTTP client library.
For more information on how to pick your favourite client see [the docs](http://docs.php-http.org/en/latest/httplug/users.html).

Installation using latest Guzzle:

...
