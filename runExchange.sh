#!/bin/bash

function pause(){
   read -p "Press [Enter] key to continue..."
}

clear
php bin/multichain-cli create-address fancystartup
pause
clear
php bin/multichain-cli issue-asset FANCY 5000 1 fancystartup
pause
clear
php bin/multichain-cli create-address roderik
pause
clear
php bin/multichain-cli issue-asset EURO 1000000 0.01 roderik
pause
clear
php bin/multichain-cli send EURO 5.5 roderik fancystartup
pause
clear
php bin/multichain-cli exchange FANCY 1000 fancystartup EURO 10000 roderik
