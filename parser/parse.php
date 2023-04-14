<?php
/*
IPP 2023 - část 1.

Author: Martin Laštovica - xlasto03
*/

//WARNING SETTINGS
ini_set('display_errors', 'stderr');

//PARAMETER HANDLING
if($argc > 1){
    if($argv[1] == "--help"){
        printHelp();
        exit(0);
    }
    else{
        exit(10);
    }
}

//INPUT HANDLING
$input = array();
while($line = fgets(STDIN)){
    array_push($input,str_replace("\n","",$line));
}

$code = new Code;
$code->parseCode($input);
$code->printCode();


//Argument class
//Serves as a reprasintation of an argument in code
//@param $argType - type of Argument (var, label, etc)
//@param $dataType - type of Data or Frame (nil, bool, etc... for vars GF,LF,TF)
//@param $value - actual value of an argument
class Argument{
    public $argType;
    public $dataType;
    public $value;

    //CONSTRUCTOR
    //@param $arg - string representation of an argument
    public function __construct($arg){
        $splicedArg = explode("@",$arg,2);

        switch(count($splicedArg)){
            case 1:
                $this->dataType = "label";
                $this->value = $splicedArg[0];
                break;
            case 2:
                $this->dataType = $splicedArg[0];
                $this->value = $splicedArg[1];
                break;
        }

        $this->getArgType();
    }

    //Adds argument to a given XML tree
    public function addArgToXML($instXML, $argCNT){
        $argValue = "";
        if($this->argType == "var"){
           $argValue = $this->dataType."@".$this->value;
        }
        else{
            $argValue = $this->value;
        }

        //proper string XML representation
        if($this->argType == "string"){
            $argXML = $instXML->addChild("arg" . $argCNT, htmlspecialchars($argValue));
        }
        else{
            $argXML = $instXML->addChild("arg" . $argCNT, $argValue);
        }

        $this->getArgType();
        $argXML->addAttribute("type",$this->argType);
    }

    //DEBUG function
    public function __toString()
    {
        return "Data:" . $this->dataType . " ArgType:" . $this->argType . " Value:" . $this->value;
    }

    //saves the proper type of argument based on DataType
    public function getArgType(){
        switch($this->dataType){
            case "GF":
            case "LF":
            case "TF":
                $this->argType = "var";
                break;
            case "int":
            case "bool":
            case "string":
            case "nil":
                $this->argType = $this->dataType;
                break;
            case "label":
                $this->argType = "label";
                break;
        }
    }

}


//Instruction class
//Serves as a representation of an instruction in code
//@param $opcode - instruction opcode
//@param $args - array of intruction arguments
//@param $argst - type of expected arguments
class Instruction{
    public $opcode;
    public $args = array();
    private $argst;

    public function __construct($opcode)
    {
        $this->opcode = strtoupper($opcode);

        switch(trim($this->opcode)){
            case ".IPPCODE23":
            case "CREATEFRAME":
            case "PUSHFRAME":
            case "POPFRAME":
            case "RETURN":
            case "BREAK":
                $this->argst = array();
                break;
            case "DEFVAR":
            case "POPS":
                $this->argst = array("var");
                break;
            case "CALL":
            case "LABEL":
            case "JUMP":
                $this->argst = array("label");
                break;
            case "PUSHS":
            case "WRITE":
            case "EXIT":
            case "DPRINT":
                $this->argst = array("symb");
                break;
            case "MOVE":
            case "STRLEN":
            case "TYPE":
            case "NOT":
            case "INT2CHAR":
                $this->argst = array("var","symb");
                break;
            case "READ":
                $this->argst = array("var","type");
                break;
            case "ADD":
            case "SUB":
            case "MUL":
            case "IDIV":
            case "LT":
            case "GT":
            case "EQ":
            case "AND":
            case "OR":
            case "STRI2INT":
            case "CONCAT":
            case "GETCHAR":
            case "SETCHAR":
                $this->argst = array("var","symb","symb");
                break;
            case "JUMPIFEQ":
            case "JUMPIFNEQ":
                $this->argst = array("label","symb","symb");
                break;
            default:
            error_log("UNKNOWN INSTRUCTION: " . $this->opcode);
            exit(22);
        }
    }

    //parses arg from string
    public function parseArgs($argsIn){

        if($argsIn == null){
            if(count($this->argst) != 0){
                error_log("Wrong number of Arguments");
                exit(23);
            }
            return;
        }

        $args = preg_split("/[\s]+/",$argsIn,-1,PREG_SPLIT_NO_EMPTY);

        if(count($args) != count($this->argst)){
            error_log("Wrong number of Arguments");
            exit(23);
        }
        
        $argNum = 0;

        foreach($args as $arg){
            $this->addArg($arg, $this->argst[$argNum++]);
        }
    }

    //adds argument to an instruction
    public function addArg($argIn, $argType){
        $arg = new Argument($argIn);
        if($argType == "symb"){
            if($arg->argType == "label"){
                exit(23);
            }
        }
        elseif($argType == "type"){
            if($arg->value != "string" && $arg->value != "int" && $arg->value != "bool" && $arg->value != "nil"){
                exit(23);
            }
        }
        else{
            if($arg->argType != $argType){
                exit(23);
            }
        }
        array_push($this->args,$arg);
    }

    //adds Instructiob to a given XML
    public function addToXML($xml, $order){
        $instXML = $xml->addChild("instruction");
        $instXML->addAttribute("order", $order);
        $instXML->addAttribute("opcode", $this->opcode);

        $argCNT = 0;

        foreach($this->args as $arg){
            $arg->addArgToXML($instXML,++$argCNT);
        }

    }

    public function __toString(){
        return "OPCODE:  $this->opcode ARGS:" . $this->argsToString() . "\n";
    }

    private function argsToString(){
        $args = "";
        
        foreach($this->args as $arg){
            $args .= $arg->__toString();
        }

        return $args;
    }

}


//class CODE
//Representation of CODE
//@param instructions - array of intructions
class Code {
    
    public $instructions = array();

    public function printCode(){

        $xml = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?>"."<program></program>\n");
        $xml->addAttribute("language","IPPcode23");

        $order = 0;

        foreach($this->instructions as $inst){
            $inst->addToXML($xml,++$order);
        }

        echo $xml->asXML();
    }

    public function parseCode($input){
        $hasHeader = false;

        //PARSING LINES INTO INSTRUCTIONS LINE BY LINE
        foreach($input as $line){

            //Separate comments
            $line = explode("#",$line,2);

            //Separete whitespaces from end
            $line[0] = rtrim($line[0]);
            $line[0] = ltrim($line[0]);

            //skip empty
            if($line[0] == ""){
                continue;
            }

            if(!$hasHeader){
                if(strtoupper($line[0]) == ".IPPCODE23"){
                    $hasHeader = true;
                    continue;
                }
                else{
                    error_log("MISSING .IPPCODE HEADER");
                    exit(21);
                }
            }

            array_push($this->instructions,$this->parseInstruction($line[0]));
        }
    }

    private function parseInstruction($line){

        //Seperating the line into an array by the " " divider
        $splicedLine = preg_split("/[\s]+/",$line,2,PREG_SPLIT_NO_EMPTY);

        //Transforming array into an Argument
        $instruction = new Instruction($splicedLine[0]);

        count($splicedLine)==1?array_push($splicedLine,null):null;
        $instruction->parseArgs($splicedLine[1]);
        
        return $instruction;
    }
}

//helper function for printing help
function printHelp(){
    print("\n\nHELP:" . "\n\nThis program reads IPPCode23 from STDIN and translates it to its XML representation on STDOUT"."\n\nCLI: '--help' - prints this"."\n\nRETURN CODES:"."\n\n21 - Missing IPPCODE23 header"."\n22 - Unknown isntruction"."\n23 - Unknown Lexical or Syntax Error\n\n");
}

?>