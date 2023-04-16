import argparse
import xml.etree.ElementTree as ET
import os


def parse():

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
            print(args.source)
            exit(11)

    elif (args.input):
        if os.path.exists(args.input):
            ##TODO: implementu převod ze souboru do arraye
            print("dawd")
        else:
            exit(11)


def main():
    parse()

# Zkontrolujte, zda soubor byl spuštěn jako skript
if __name__ == "__main__":
    # Zde zavoláme hlavní funkci
    main()
