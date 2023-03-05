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

    public function __construct($opcode)
    {
        //TODO: return number of expected args;
        $this->opcode = $opcode;
    }

    public function addArg($arg){
        array_push($this->args,$arg);
    }

    public function printInstruction(){
        return "OPCODE:$this->opcode ARGS:" . implode("",$this->args) . "\n";
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