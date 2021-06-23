<?php
 require_once 'config.php';
 require_once ABS_CLASSES_PATH.$dbFile;
 require_once ABS_CONTROLLERS_PATH . 'CreateDbController.php';
 require_once ABS_CONTROLLERS_PATH . 'TicketController.php';
 require_once ABS_GENERAL_PATH . 'formFunctions.php';

?>
<!DOCTYPE html>
<html>
    <head>
            <title>Test GAC: Bienvenue</title>
            <meta http-equiv="Content-Type" content="text/HTML" charset="utf-8" />
            <meta http-equiv="Content-Language" content="fr" />
            <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0" />
            <META NAME="Author" CONTENT="Cédric Von Felten">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
            <link type="text/css" rel="stylesheet" href="css/principal.css">
            <script src="js/jquery-3.6.0.min.js"></script>
    </head>
    <body>
        <section class="section">
            <div class="container">
                <h1 class="title is-1">Bienvenue sur l'application test gac ! </h1>
                
                <div id="div_database" class="box">
                <h2 class="subtitle is-2">Etat des tables en BD</h2>

                    <?php 

                        // 1) Vérification si la base de données a été créée
                        $isBd = true;
                        try {
                            $dbaccess = new DbAccess($dbObj);
                            $handler = $dbaccess->connect();
                            if($handler === false) {
                                throw new ErrorException("Base de données gac_tickets inexistante !");
                            }
                            $dbaccess->close($handler);
                        } catch (Exception $e) {
                            $isBd = false;
                            $messageErreurFinal = "Veuillez créer la base de données en saisissants les commandes" .
                                                " suivantes: <br> CREATE DATABASE IF NOT EXISTS 'gac_tickets'" .
                                                " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;<br>" .
                                                " USE `gac_tickets`;";
                            echo afficherErreur($createDbController->getErrorMessage(), $messageErreurFinal);
                            die();
                        }
                        
                        // 2) Vérification si les tables ont été crées et chaargées

                        $createDbController = new createDbController($dbObj);
                        $isOk = true;
                        echo "<ul>";
                        // Creation et remplissage de la table principale Tickets
                        if($createDbController->initieTableTicket() === false) {
                            $isOk = false;
                        }else{
                            echo "<li>".$createDbController->getOkMessage()."</li>";
                        }
                        // Création de la table Appels
                        if($isOk && $createDbController->initieTableAppels()) {
                            echo "<li>".$createDbController->getOkMessage()."</li>";
                        } else {
                            $isOK = false;
                        }

                        if($isOk && $createDbController->initieTableConnexions()) {
                            echo "<li>".$createDbController->getOkMessage()."</li>";
                        } else {
                            $isOK = false;
                        }
                        echo "</ul>";
                        if(!$isOk) {
                            $messageErreurFinal =   "<div>Problèmes lors de la création des tables nécessaires aux requêtes. " .
                                                    "Essayez de recréer la base et de jouer le dump datas_gac.sql " .
                                                    "Situé dans le répértoire <i>sql</i> avec le fichier " .
                                                    "<i>sql/dump/tickets_appels_201202.csv</i></div>";
                            echo afficherErreur($createDbController->getErrorMessage(), $messageErreurFinal);
                            die();
                        }


                    ?>
                </div>

                
                <!-- 3) Requêtes -->
                <div id="div_queries" class="box">
                    <h2 class="subtitle is-2">Requêtes sur les tables créées</h2>
                    <article class="panel is-primary">
                        <p class="panel-heading">Durée totale réelle des appels effectués après le 15/02/2021</p>
                            <div class="panel-block">
                                <?php
                                $ticketController = new TicketController($dbObj);
                                $dureeTotale = $ticketController->getDureeTotalAppels();
                                if($dureeTotale === false) {
                                    echo $ticketController->getErrorMessage();
                                } else {
                                    echo "Durée total des appels&nbsp;<b>passés</b>: " . $dureeTotale ."h";
                                }
                                ?>
                            </div>
                    </article>
                    <article class="panel is-secondary">
                        <p class="panel-heading">TOP 10 des volumes data facturés en dehors de la tranche 
                        horaire 8h00 - 18h00</p>
                        <div class="panel-block">
                            <?php
                            $topAppels = $ticketController->getHighestDataFactures();
                            if($topAppels === false) {
                                echo $ticketController->getErrorMessage();
                            } else {
                                ?>
                                <table class="table is-striped">
                                    <thead>
                                        <tr>
                                            <th><abbr title="Position">Pos</abbr></th>
                                            <th>N° abonné</th>
                                            <th>Volume Data</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    $i = 1;
                                    foreach($topAppels as $value) {
                                        echo "<tr><th>" . $i . "</th><td>" . $value['num_abonne'] . "</td><td>" . $value['volume_facture'] ."</td></tr>";
                                        $i++;
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            <?php
                            }
                            ?>
                        </div>
                    </article>
                    <article class="panel is-info">
                        <p class="panel-heading">Nombre total de SMS envoyés pour l'ensemble des abonnés</p>
                        <div class="panel-block">
                            <?php
                            $nbTotalSms = $ticketController->quantiteTotaleSms();
                            if($nbTotalSms === false) {
                                echo $ticketController->getErrorMessage();
                            } else {
                                echo "Quantité totale des SMS: " . $nbTotalSms;
                            }

                            $ticketController->fermerConnection();
                            ?>
                        </div>
                    </article>

                </div>
            </div>
                
        </div>
    </body>
</html>
