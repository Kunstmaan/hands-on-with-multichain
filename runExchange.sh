#!/bin/bash

function pause(){
   read -p "Press [Enter] key to continue..."
}

function run(){
   docker exec -it bc.php $1
}

clear
run "php bin/multichain-cli create-address fancystartup"
pause

run "php bin/multichain-cli issue-asset FANCY 5000 1 fancystartup"
pause

run "php bin/multichain-cli create-address roderik"
pause

run "php bin/multichain-cli issue-asset EURO 1000000 0.01 roderik"
pause

run "php bin/multichain-cli send EURO 5.5 roderik fancystartup"
pause
run "php bin/multichain-cli exchange FANCY 1000 fancystartup EURO 10000 roderik"
