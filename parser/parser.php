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

class Instruction{
    public $opcode;
    public $args = array();
    private $argsc;

    public function __construct($opcode)
    {
        //TODO: return number of expected args;
        $this->opcode = strtoupper($opcode);

        switch(trim($this->opcode," \n\r\t\v\x00")){
            case ".IPPCODE23":
            case "CREATEFRAME":
            case "PUSHFRAME":
            case "POPFRAME":
            case "RETURN":
            case "BREAK":
                $argsc = 0;
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
                $argsc = 1;
                break;
            case "MOVE":
            case "INT2CHAR":
            case "READ":
            case "TYPE":
                $argsc = 2;
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
                $argsc = 3;
                break;
            default:
            error_log("UNKNOWN INSTRUCTION: " . $this->opcode);
            exit(22);
        }
    }

    public function addArg($arg){
        if(count($args)<= $argc){
            array_push($this->args,$arg);
        }
        else{
            exit(23);
        }
    }

    public function printInstruction(){
        return "OPCODE:  $this->opcode ARGS:" . implode(" ",$this->args) . "\n";
    }

}

class Code {
    public $instructions = array();

    public function printCode(){
        foreach($this->instructions as $inst){
            echo $inst->printInstruction();
        }
    }

    public function parseCode($input){

        //PARSING LINES INTO INSTRUCTIONS LINE BY LINE
        foreach($input as $line){

            //comment check
            if($line == "" || $line[0] == "#"){
                continue;
            }

            array_push($this->instructions,$this->parseInstruction($line));
        }

    }

    private function parseInstruction($line){

        //Seperating the line into an array by the " " divider
        $splicedLine = explode(" ",$line,3);

        //Transforming array into an Argument
        var_dump($splicedLine);
        $instruction = new Instruction($splicedLine[0]);

        foreach($splicedLine as $arg){
            if($arg != $instruction->opcode){
                $instruction->addArg($arg);
            }
        }
        return $instruction;
    }
}


function printHelp(){
    print("printing help yeey");
}


?>