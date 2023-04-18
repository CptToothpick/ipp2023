import argparse
import fileinput
import sys
import xml.etree.ElementTree as ET
import os

class Singleton(object):
    _instance = None

    def __new__(class_, *args, **kwargs):
        if not isinstance(class_._instance, class_):
            class_._instance = object.__new__(class_, *args, **kwargs)
        return class_._instance


class Frame:
    variables = {}

class FrameManager(Singleton):
    globalFrame: Frame
    localFrame: Frame
    tempFrame: Frame
    frameStack = []

    def __init__(self):
        self.globalFrame = Frame()
        super().__init__()

    def createFrame(self):
        self.tempFrame = Frame()

    def pushFrame(self):
        self.frameStack.append(self.tempFrame)
        self.tempFrame = None

    def popFrame(self):
        try:
            self.tempFrame = self.frameStack.pop()
        except IndexError:
            exit(55)


instrucitonDictionary = {
    "MOVE" : ["var","symb"],
    "CREATEFRAME" : [],
    "PUSHFRAME" : [],
    "POPFRAME" : [],
    "DEFVAR" : ["var"],
    "CALL" : ["label"],
    "RETURN" : [],

    "PUSHS" : ["symb"],
    "POPS" : ["var"],

    "ADD" : ["var","symb","symb"],
    "SUB" : ["var","symb","symb"],
    "MUL" : ["var","symb","symb"],
    "IDIV" : ["var","symb","symb"],
    "LT" : ["var","symb","symb"],
    "GT" : ["var","symb","symb"],
    "EQ" : ["var","symb","symb"],
    "AND" : ["var","symb","symb"],
    "OR" : ["var","symb","symb"],
    "NOT" : ["var","symb","symb"],
    "INT2CHAR" : ["var","symb"],
    "STRI2INT" : ["var","symb","symb"],

    "READ" : ["var","type"],
    "WRITE" : ["symb"],

    "CONCAT" : ["var","symb","symb"],
    "STRLEN" : ["var","symb"],
    "GETCHAR" : ["var","symb","symb"],
    "SETCHAR" : ["var","symb","symb"],

    "TYPE" : ["var", "symb"],

    "LABEL" : ["label"],
    "JUMP" : ["label"],
    "JUMPIFEQ" : ["label", "symb", "symb"],
    "JUMPIFNEQ" : ["label", "symb", "symb"],
    "EXIT" : ["symb"],

    "DPRINT" : ["symb"],
    "BREAK" : [],
}

class Constant:
    valueType: str
    value = ''

    def __init__(self, valueType, value):
        self.valueType = valueType
        self.value = value

    def getValue(self):
        return self.value

class Variable:
    frame: str
    name: str

    def __init__(self, frame, name):
        self.frame = frame
        self.name = name

    def getValue(self):
        frameManager = FrameManager()
        frameManager.test = "Bobek"
        return self.name

class Argument:
    argType: str
    value: any

class Instruciton:
    order: int
    instructionCode: str
    args = []

    def __init__(self, order:int, instructionCode:str):
        self.order = order
        self.instructionCode = instructionCode

    def addArgument(self, argument):
        self.args.append(argument)


class Code:
    instructions = []

    def parseCode(self, source:ET.ElementTree):
        root = source.getroot()

        frameManager = FrameManager()
        frameManager.test = "Bobek"

        if root.tag != "program" or root.attrib["language"] != "IPPcode23":
            exit(32)

        for instruction in root:
            if instruction.tag != "instruction":
                exit(32)
            instructionObject = Instruciton(int(instruction.attrib["order"]),instruction.attrib["opcode"]) 
            for arg in instruction:
                print(arg.text)
                instructionObject.addArgument(arg)
            
            self.instructions.append(instructionObject)
            

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
            ##TODO: fix \n
            return source, dataInput
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
    code = Code()
    source, input = argumentParse()
    frameManager = FrameManager()
    frameManager.printTest()
    code.parseCode(source)
    frameManager.printTest()

# Zkontrolujte, zda soubor byl spuštěn jako skript
if __name__ == "__main__":
    # Zde zavoláme hlavní funkci
    main()
