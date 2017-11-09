#!/bin/bash

function pause(){
   read -p "Press [Enter] key to continue..."
}

function run(){
   docker exec -it bc.php $1
}


clear
run "php bin/multichain-cli create-address documentregistry"
pause

run "php bin/multichain-cli issue-asset DOCUMENT max 1 documentregistry"
pause

open docs/Blockchain_Meetup_Affiche_v2.pdf
pause

run "php bin/multichain-cli list-assets"
pause

run "php bin/multichain-cli register-document ./docs/Blockchain_Meetup_Affiche_v2.pdf"
pause

run "php bin/multichain-cli validate-document ./docs/Blockchain_Meetup_Affiche_v2.pdf"
pause

open docs/Blockchain_Meetup_Affiche_v2_modified.pdf
pause

run "php bin/multichain-cli validate-document ./docs/Blockchain_Meetup_Affiche_v2_modified.pdf"
pause