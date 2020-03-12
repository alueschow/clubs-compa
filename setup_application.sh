#!/bin/bash

###################
# ABOUT THIS FILE #
###################
# Filename: setup_application.sh

# This script installs CLUBS-Compa.

# Takes one command line argument: the base directory ($BASEDIR) of the application (usually, this should be something like /home/user/clubs-compa)

###################
BASEDIR=$1

sudo composer install
sudo php bin/console doctrine:schema:update --force
sudo sh import_data.sh $BASEDIR
symfony server:start