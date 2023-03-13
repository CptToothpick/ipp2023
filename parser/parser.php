<?php

ini_set('display_errors', 'stderr');

// Parameter handling
if($argc > 1){
    if($argv[1] == "--help"){
        printHelp();
    }
    else{
        exit(10);
    }
}

// Input handling

$input = array();
while($line = fgets(STDIN)){
    array_push($input,str_replace("\n","",$line));
}

$code = new Code;
$code->parseCode($input);
$code->printCode();

class Argument{
    public $argType;
    public $dataType;
    public $value;

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

    public function addArgToXML($instXML, $argCNT){
        $argXML = $instXML->addChild("arg" . $argCNT,$this->value);
        //$this->argGetType();
        $argXML->addAttribute("type",$this->argType);
        //$argXML = $this->value;
    }

    public function __toString()
    {
        return "Data:" . $this->dataType . " ArgType:" . $this->argType . " Value:" . $this->value;
    }

    public function getArgType(){
        switch($this->dataType){
            case "Gf":
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

class Instruction{
    public $opcode;
    public $args = array();
    private $argsc;

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
                $this->argsc = 0;
                break;
            case "DEFVAR":
            case "CALL":
            case "PUSHS":
            case "POPS":
            case "WRITE":
            case "LABEL":
            case "JUMP":
            case "EXIT":
            case "DPRINT":
                $this->argsc = 1;
                break;
            case "MOVE":
            case "INT2CHAR":
            case "READ":
            case "TYPE":
                $this->argsc = 2;
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
            case "NOT":
            case "STRI2INT":
            case "CONCAT":
            case "GETCHAR":
            case "SETCHAR":
            case "JUMPIFEQ":
            case "JUMPIFNEQ":
                $this->argsc = 3;
                break;
            default:
            error_log("UNKNOWN INSTRUCTION: " . $this->opcode);
            exit(22);
        }
    }

    public function parseArgs($argsIn){

        if($argsIn == null){
            if($this->argsc != 0){
                error_log("Wrong number of Arguments");
                exit(23);
            }
            return;
        }

        $args = preg_split("/[\s]+/",$argsIn,-1,PREG_SPLIT_NO_EMPTY);

        if(count($args) != $this->argsc){
            error_log("Wrong number of Arguments");
            exit(23);
        }

        foreach($args as $arg){
            $this->addArg($arg);
        }
    }

    public function addArg($argIn){
        $arg = new Argument($argIn);
        array_push($this->args,$arg);
    }

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

class Code {
    
    public $instructions = array();

    public function printCode(){

        $xml = new SimpleXMLElement("<program></program>");
        $xml->addAttribute("language","IPPcode23");

        $order = 0;

        foreach($this->instructions as $inst){
            $inst->addToXML($xml,++$order);
        }

        echo $xml->asXML();

        /*
        foreach($this->instructions as $inst){
            echo $inst->__toString();
        }*/
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

        /*$temp = array_shift($this->instructions);

        if($temp->opcode != ".IPPCODE23"){
            error_log("MISSING .IPPCODE HEADER");
            exit(21);
        }*/

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

function printHelp(){
    print("printing help yeey");
}

?>