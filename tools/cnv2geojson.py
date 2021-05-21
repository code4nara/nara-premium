#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import os
import sys
import re
import argparse
#
import csv
import codecs
import pandas
# for geojson 
import json
import geojson
# SEE; https://python-geojson.readthedocs.io/en/latest/

DEFAULT_INPUT  = "../nara-premium-geocoded.csv"
DEFAULT_OUTPUT = "../nara-premium.geojson"

# --------------------------------------
#  Read CSV file 
# --------------------------------------
def readCSV( csvFile, encode="utf-8" ):
    csvnames = ['name','sort','fulladdress','zip','address','Longitude','Latitude']
    
    try:
        inputCSV = pandas.read_csv( csvFile, encoding=encode, header=0, names=csvnames )
    except Exception as e:
        sys.stderr.write( "EE  CSVFileRead: %s \n"%( e.args ) )
        sys.exit()
        
    return( inputCSV )

# --------------------------------------
#  Write GeoJSON
# --------------------------------------
def writeGeoJson( OutFile, feature_collection):
    # open a file with write mode
    with open( OutFile, 'w', encoding="utf-8") as gjfile:
        gjfile.write(json.dumps(feature_collection,ensure_ascii=False,indent=2))

# --------------------------------------
# parse Point CSV
# --------------------------------------
def parsePoint( inputCSV ) :
    myFeatures = []

    # Check Column Name
    LineCN = {'name','sort','zip','address','Longitude','Latitude'}

    for cn in LineCN:
        if cn not in inputCSV.columns:
            print( "EE  CSV Column ERROR : %s Not Found."%( cn ) )
            sys.exit()
            
    for index, row in inputCSV.iterrows():
        # Set Properties except geometory
        myProperties= {}
        for col in inputCSV.columns:
            value=row[col]
            if col == "Longitude":
                lon = float( value );
                continue;
            if col == "Latitude":
                lat = float( value );
                continue;
            if col == "Altitude":
                # Ignore Altitude
                continue;

            # Check NaN -> ""
            if value != value :
                value = ""
            if col == "No" and type(value) is float :
                myProperties[col]=str(int(value))
            else:
                myProperties[col]=str(value)

        myPoint = geojson.Point([lon, lat])
        # Append Point Data 
        myFeatures.append(geojson.Feature(id=index, properties=myProperties, geometry=myPoint  ))

    myFeatureCollection = geojson.FeatureCollection(myFeatures)

    return myFeatureCollection

# --------------------------------------
# Main Function
# --------------------------------------
def main( args ):
    print( "\n  Convert Script from CSV to GeoJSON for NARA-Premium Shop list\n")

    # Raed CSV
    inputCSV = readCSV( args.inputcsv , "utf-8" )

    if args.debug :
        print( "II  Found Records: %d in %s" %( len(inputCSV), args.inputcsv ) , flush=True)
        print( inputCSV )

    # Parse Point Data
    geoJson = parsePoint( inputCSV )
    if geoJson == False:
        print( "\nEE  Parse Error in Convert from %s \n" %( args.inputcsv ) , flush=True)
        sys.exit()
        
    # Save GeoJson
    writeGeoJson( args.outgeojson, geoJson )

    print( "II  Convert Finished,  GeoJSON: %s\n" %(  args.outgeojson ) , flush=True)

# --------------------------------------
# Args Parser 
# --------------------------------------
if __name__=='__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument('-i', '--inputcsv' , help='Input CSV filename', default=DEFAULT_INPUT )
    parser.add_argument('-o', '--outgeojson', help='Output GeoJson filename', default=DEFAULT_OUTPUT )
    parser.add_argument('-d', '--debug', help='Set Debug Mode', action='store_true' )

    args = parser.parse_args()
    main( args )
    
