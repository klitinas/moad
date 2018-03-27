#!/bin/bash

cd /var/www/loris/tools

# Demographics
echo -e "\n\n\nDemographics..."
php CouchDB_Import_Demographics.php confirm
echo -e "\n\n\nDone with Demographics."

# Instruments
echo -e "\n\n\nInstruments..."
php CouchDB_Import_Instruments.php confirm
echo -e "\n\n\nDone with Instruments.\n\n"
