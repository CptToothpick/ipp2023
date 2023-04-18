import argparse
import fileinput
import sys
import xml.etree.ElementTree as ET
import os

class Code:
    instructions = {}

class argParser(argparse.ArgumentParser):
    def parse_args(self):
        try:
            return super().parse_args()      
        except BaseException:
            exit(11)

def argumentParse():

##Argument handling

    parser = argParser(description='Intepret of XML IPPcode23')
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
            source = ET.parse(args.source)
            dataInput = []
            for line in sys.stdin:
                dataInput.append(line)
            print(dataInput)
            ##TODO: fix \n
        else:
            print(args.source)
            exit(11)

    elif (args.input):
        if os.path.exists(args.input):
            ##TODO: implementu prevod ze souboru do arraye
            print("dawd")
        else:
            exit(11)


def main():
    argumentParse()

# Zkontrolujte, zda soubor byl spuštěn jako skript
if __name__ == "__main__":
    # Zde zavoláme hlavní funkci
    main()
