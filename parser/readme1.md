# Implementační dokumentace k 1. úloze do IPP 2022/2023
## Jméno a příjmení: Martin Laštovica
## Login: xlasto03

### Poupnost programu

Program prve načte argumenty a zpracujeje (podle zadání). Následně zpracuje vstup postupným načítáním všech řádků až do EOF a jejich ukládáním do proměnné. Dále si vytvoří novou instanci třídy CODE, do které tento array řádků vloží. Třída Code jej zpracuje a program následně zavolá metodu printCode třídy CODE, římž vypíše na STDOUT reprezentaci kódu v XML.

### Třídy:
#### Code

Reprezentuje celkový kód programu. Třída při zavolání funkce parseCode transformuje vstupní pole řádků na array třídy Instukcí

#### Instruction

Reprezentuje instrukci. Funkce a parametry jsou popsané ve zdrojovém souboru

#### Argument

Reprezentuje Argument. Funkce a parametry jsou popsané ve zdrojovém souboru
