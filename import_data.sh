#!/bin/bash

###################
# ABOUT THIS FILE #
###################
# Filename: import_data.sh

# Takes one command line argument: the base directory ($BASEDIR) of the application (usually, this should be something like /home/user/clubs-compa)

# Loads files from $BASEDIR/src/Resources/demo_data into the MySQL database.
# If necessary, change the BASEDIR, MYSQLDIR, DBNAME, DBUSER and MYSQLPASSWORD parameters according to your needs.

# Usage:
# sudo sh import_data.sh $BASEDIR
###################

BASEDIR=$1
# on a server this may be something like this:
# BASEDIR=/var/www/clubs-compa/clubs-compa
DEMODATADIR=$BASEDIR/src/Resources/demo_data

# Database files
MYSQLDIR=/var/lib/mysql-files

# Database credentials
DBNAME="clubs_compa"
DBUSER="clubs_compa"
MYSQLPASSWORD="clubs_compa"

##############################
# NO CHANGES BELOW THIS LINE #
##############################

#############
# FUNCTIONS #
#############

reset_app_users () {
	echo "[USER] Resetting 'User' database ..."
	cd $MYSQLDIR
	rm -f User.csv
	cd $DEMODATADIR
	cp User.csv $MYSQLDIR

	mysql -u "$DBUSER" "-p$MYSQLPASSWORD" "$DBNAME" -e "
	DELETE FROM User;

	LOAD DATA LOCAL INFILE '/var/lib/mysql-files/User.csv'
	INTO TABLE User
	CHARACTER SET utf8mb4
	FIELDS TERMINATED BY '\t'
	ENCLOSED BY '\"'
	LINES TERMINATED BY '\n'
	IGNORE 1 ROWS
	(id,username,password,email,is_active,groups,roles);
	" 2> /dev/null
	
	echo "[USER] Database import finished!"
}

##################################################

demo_comparison_module () {
	# Remove old data from MySQL directory
	cd $MYSQLDIR
	echo "[COMPARISON] Removing old data from $MYSQLDIR ..."
	rm -f ComparisonModule_MainConfiguration.csv
	rm -f ComparisonModule_StandaloneConfiguration.csv
	rm -f ComparisonModule_LiveConfiguration.csv
	rm -f ComparisonModule_Website.csv

	# Copy demo data to MySQL directory
	cd $DEMODATADIR
	echo "[COMPARISON] Copying demo data from $DEMODATADIR to $MYSQLDIR ..."
	cp ComparisonModule_MainConfiguration.csv $MYSQLDIR
	cp ComparisonModule_StandaloneConfiguration.csv $MYSQLDIR
	cp ComparisonModule_LiveConfiguration.csv $MYSQLDIR
	cp ComparisonModule_Website.csv $MYSQLDIR

	# Log into MySQL, delete tables and insert demo data
	echo "[COMPARISON] Deleting MySQL tables ..."
	echo "[COMPARISON] Importing demo data ..."
	mysql -u "$DBUSER" "-p$MYSQLPASSWORD" "$DBNAME" -e "

	DELETE FROM ComparisonModule_MainConfiguration;
	DELETE FROM ComparisonModule_StandaloneConfiguration;
	DELETE FROM ComparisonModule_LiveConfiguration;
	DELETE FROM ComparisonModule_Website;


	LOAD DATA LOCAL INFILE '/var/lib/mysql-files/ComparisonModule_MainConfiguration.csv'
	INTO TABLE ComparisonModule_MainConfiguration
	CHARACTER SET utf8mb4
	FIELDS TERMINATED BY '\t'
	ENCLOSED BY '\"'
	LINES TERMINATED BY '\n'
	IGNORE 1 ROWS
	(id,mode,active_study_category,debug_mode_status);

	LOAD DATA LOCAL INFILE '/var/lib/mysql-files/ComparisonModule_StandaloneConfiguration.csv'
	INTO TABLE ComparisonModule_StandaloneConfiguration
	CHARACTER SET utf8mb4
	FIELDS TERMINATED BY '\t'
	ENCLOSED BY '\"'
	LINES TERMINATED BY '\n'
	IGNORE 1 ROWS
	(id,eval_button_left,eval_button_right,middle_button,url,presentation_mode,presentation_fields,presentation_field_name_1,presentation_field_name_2,presentation_field_name_3,presentation_field_name_4,allow_tie,group_by,group_by_category,randomization,document_order);

	LOAD DATA LOCAL INFILE '/var/lib/mysql-files/ComparisonModule_LiveConfiguration.csv'
	INTO TABLE ComparisonModule_LiveConfiguration
	CHARACTER SET utf8mb4
	FIELDS TERMINATED BY '\t'
	ENCLOSED BY '\"'
	LINES TERMINATED BY '\n'
	IGNORE 1 ROWS
	(id,eval_button_left,eval_button_right,middle_button,randomization,randomization_participation,cookie_name,cookie_expires,participations_per_time_span,time_span,allow_tie,use_base_website,document_order);

	LOAD DATA LOCAL INFILE '/var/lib/mysql-files/ComparisonModule_Website.csv'
	INTO TABLE ComparisonModule_Website
	CHARACTER SET utf8mb4
	FIELDS TERMINATED BY '\t'
	ENCLOSED BY '\"'
	LINES TERMINATED BY '\n'
	IGNORE 1 ROWS
	(id,website_name,website_url,shown,is_base_website);

	" 2> /dev/null
	
	echo "[COMPARISON] Database import finished!"
}

##################################################

demo_assessment_module () {
	# Remove old data from MySQL directory
	cd $MYSQLDIR
	echo "[ASSESSMENT] Removing old data from $MYSQLDIR ..."
	rm -f AssessmentModule_MainConfiguration.csv
	rm -f AssessmentModule_GeneralConfiguration.csv
	rm -f AssessmentModule_MetricConfiguration.csv

	# Copy demo data to MySQL directory
	cd $DEMODATADIR
	echo "[ASSESSMENT] Copying demo data from $DEMODATADIR to $MYSQLDIR ..."
	cp AssessmentModule_MainConfiguration.csv $MYSQLDIR
	cp AssessmentModule_GeneralConfiguration.csv $MYSQLDIR
	cp AssessmentModule_MetricConfiguration.csv $MYSQLDIR

	# Log into MySQL, delete tables and insert demo data
	echo "[ASSESSMENT] Deleting MySQL tables ..."
	echo "[ASSESSMENT] Importing demo data ..."
	mysql -u "$DBUSER" "-p$MYSQLPASSWORD" "$DBNAME" -e "

	DELETE FROM AssessmentModule_MainConfiguration;
	DELETE FROM AssessmentModule_GeneralConfiguration;
	DELETE FROM AssessmentModule_MetricConfiguration;


	LOAD DATA LOCAL INFILE '/var/lib/mysql-files/AssessmentModule_MainConfiguration.csv'
	INTO TABLE AssessmentModule_MainConfiguration
	CHARACTER SET utf8mb4
	FIELDS TERMINATED BY '\t'
	ENCLOSED BY '\"'
	LINES TERMINATED BY '\n'
	IGNORE 1 ROWS
	(id,mode,active_study_category,debug_mode_status,use_metrics);

	LOAD DATA LOCAL INFILE '/var/lib/mysql-files/AssessmentModule_GeneralConfiguration.csv'
	INTO TABLE AssessmentModule_GeneralConfiguration
	CHARACTER SET utf8mb4
	FIELDS TERMINATED BY '\t'
	ENCLOSED BY '\"'
	LINES TERMINATED BY '\n'
	IGNORE 1 ROWS
	(id,url,presentation_mode,presentation_fields,presentation_field_name_1,presentation_field_name_2,presentation_field_name_3,presentation_field_name_4,query_style,query_heading_name,topic_heading_name,document_heading,document_heading_name,group_by,group_by_category,skipping_allowed,skipping_options,skipping_comment,loading_new_document,user_progress_bar,total_progress_bar,rating_heading);

	LOAD DATA LOCAL INFILE '/var/lib/mysql-files/AssessmentModule_MetricConfiguration.csv'
	INTO TABLE AssessmentModule_MetricConfiguration
	CHARACTER SET utf8mb4
	FIELDS TERMINATED BY '\t'
	ENCLOSED BY '\"'
	LINES TERMINATED BY '\n'
	IGNORE 1 ROWS
	(id,limited,max_length,round_precision);

	" 2> /dev/null
	
	echo "[ASSESSMENT] Database import finished!"
}

##################################################

########
# MAIN #
########
reset_app_users
demo_comparison_module
demo_assessment_module