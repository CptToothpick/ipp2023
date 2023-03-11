<?php


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
                $this->argType = "const";
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
                exit(23);
            }
            return;
        }

        $args = explode(" ", $argsIn);
        if(count($args) != $this->argsc){
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
        foreach($this->instructions as $inst){
            echo $inst->__toString();
        }
    }

    public function parseCode($input){

        //PARSING LINES INTO INSTRUCTIONS LINE BY LINE
        foreach($input as $line){

            //Separate comments
            $line = explode("#",$line,2);

            //Separete whitespaces from end
            $line[0] = rtrim($line[0]);

            //skip empty
            if($line[0] == ""){
                continue;
            }

            array_push($this->instructions,$this->parseInstruction($line[0]));
        }

        $temp = array_shift($this->instructions);

        if($temp->opcode != ".IPPCODE23"){
            error_log("MISSING .IPPCODE HEADER");
            exit(21);
        }

    }

    private function parseInstruction($line){

        //Seperating the line into an array by the " " divider
        $splicedLine = explode(" ",$line,2);

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