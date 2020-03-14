# CLUBS Compa

Copyright (c) 2020 Andreas Lüschow

License: MIT License

## About
CLUBS Compa is a fully-functional web application for
           
* comparing documents or search engine websites in an A/B test,
* classifying documents, images, etc.,
* assessing retrieval quality of search engine results.

CLUBS Compa was developed during the <a href="https://www.clubs-project.eu" target="_blank">CLUBS project</a>.

## Installation
The application uses the <a href="https://symfony.com/" target="_blank">Symfony</a> PHP framework (version 5.0)
and their template engine <a href="https://twig.symfony.com/" target="_blank">Twig</a>.
PHP dependencies are maintained using <a href="https://getcomposer.org/" target="_blank">Composer</a> and
Database integration is handled by a Symfony third-party library called <a href="https://symfony.com/doc/current/doctrine.html" target="_blank">Doctrine</a>.
CSS is mainly based on <a href="https://getbootstrap.com/" target="_blank">Bootstrap</a>.

You can find a lot of information about most of these components in the <a href="https://symfony.com/doc/current/index.html" target="_blank">Symfony documentation.</a>

Follow these steps to make CLUBS Compa available on your computer:
* Install __Composer__ and __Symfony__.
* Clone the repository from GitHub to a project directory and <code>cd</code> into this directory.
* Set up a new MySQL Database: user, password and database name are all _clubs_compa_.
* Run <code>sudo sh setup_application.sh $FULL_PATH_TO_PROJECT</code> (e.g., _/home/andreas/clubs-compa_). This will install all dependencies and initialize the database.
* Start the local PHP server with <code>symfony server:start</code>.
* Go to <code>localhost:8000/admin</code> and log in by using the default credentials "admin" and "password".

More documentation can be found inside the application.