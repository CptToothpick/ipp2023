import argparse
import xml.etree.ElementTree as ET
import os

##Argument handling

parser = argparse.ArgumentParser(description='Intepret of XML IPPcode23')
parser.add_argument('--source', metavar='file', type=str, nargs='?', 
                    help='File with a source code')
parser.add_argument('--input', metavar='file', type=str, nargs='?',
                    help='File with inputs')

args = parser.parse_args()

if (args.input==None and args.source==None):
    print("Welp, you didnt specify input or source. Repair that.")
    exit(10)

elif (args.source):
    if os.path.exists(args.source):
        ET.parse(args.source)
    else:
        exit(11)

elif (args.input):
    if os.path.exists(args.input):
        ##TODO: implementu převod ze souboru do arraye
        print("dawd")
    else:
        exit(11)
