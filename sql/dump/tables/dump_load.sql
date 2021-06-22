LOAD DATA INFILE 'H:/Sources/Php/test_gac/dump/tickets_appels_201202.csv' 
IGNORE 
INTO TABLE tickets
FIELDS TERMINATED BY ';' 
OPTIONALLY ENCLOSED BY "'"
LINES TERMINATED BY '\r\n'
(compte,num_facture, num_abonne,@date_time_variable,heure_ticket,duree_volume_reel,duree_volume_facture,type_donnees) -- read one of the field to variable
SET date_ticket = STR_TO_DATE(@date_time_variable, '%d/%m/%Y');