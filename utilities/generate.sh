#!/bin/bash
#Basic generate script
#This will remove any existing output, will recreate the library and install all composer dependencies
#Collect arguments
while getopts ":o:s:" opt; do
  case $opt in
    o) output_folder="$OPTARG"
    ;;
    s) source_yaml_file="$OPTARG"
    ;;
    \?) echo "Invalid option -$OPTARG" >&2
    ;;
  esac
done

#Remove previous generated folder
rm -rf $output_folder;
#generate with async generator
ag $source_yaml_file ./ -o $output_folder
#go to folder
cd $output_folder;
#install all composer dependencies ;)
composer install;
#fix style's with codesniffer
composer fix-style;
#run test suite just in case
composer test;