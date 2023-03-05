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

$input = '';
while($line = fgets(STDIN)){
    $input .= $line;
}

echo $input[1];


function printHelp(){
    print("printing help yeey");
}


?>