#!/bin/bash

function pause(){
   read -p "Press [Enter] key to continue..."
}

clear
php bin/multichain-cli create-address documentregistry
pause
clear
php bin/multichain-cli issue-asset DOCUMENT max 1 documentregistry
pause
clear
open docs/Blockchain_Meetup_Affiche_v2.pdf
pause
clear
php bin/multichain-cli list-assets
pause
clear
php bin/multichain-cli register-document ./docs/Blockchain_Meetup_Affiche_v2.pdf
pause
clear
php bin/multichain-cli validate-document ./docs/Blockchain_Meetup_Affiche_v2.pdf
pause
clear
open docs/Blockchain_Meetup_Affiche_v2_modified.pdf
pause
clear
php bin/multichain-cli validate-document ./docs/Blockchain_Meetup_Affiche_v2_modified.pdf
pause
clear