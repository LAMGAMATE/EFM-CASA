<?php

    // QST 1
    abstract class Vehicule
    {
        protected $code;
        protected $nbPlaces;
        protected $capacite;

        function __construct($a, $b, $c)
        {
            $this->code = $a;
            $this->nbPlaces = $b;
            $this->capacite = $c;

        }

    // QST 2
        abstract function afficher();
    
    // QST 3
        abstract function total();

    }


    // QST 4
    class Bateau extends Vehicule
    {
        private $coleur;
        private $prix; 

        function __construct($code, $nbPlaces, $capacite, $coleur, $prix)
        {
            $this->code = $code;
            $this->nbPlaces = $pnbPlacesrix;
            $this->capacite = $capacite;
            $this->coleur = $coleur;
            $this->prix = $prix;
        }
    }

    // QST 5
    function afficher()
    {
        echo (
            "Code : ". $this->code.
            "Nombre de Places : ". $this->nbPlaces.
            "Capacite : ". $this->capacite.
            "Couleur : ". $this->coleur.
            "Prix : ". $this->prix
        );
    }

    // QST 6
    function total()
    {
        return $this->prix * $this->nbPlaces;
    }

    // QST 7 
    $o = new Bateau(1, 23, 50, 'rouge', 70);
    $o->afficher();
    echo $o->total();


############ PARTIE PRATIQUE (26 PTS) #############

    // QST 1 conxDB.php
    $host = "localhost";
    $dbname = "conxDB";
    $username = "root";
    $password = "";

    try{
        $conn = new PDO("mysql:host=$host; dbname=$dbname", $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]);

    }
    catch(PDOException $e)
    {
        die($e);
    }

?>

    <!-- QST 2 connEmp.php -->
    <?php
    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
        $host = "localhost";
        $dbname = "tp_revision";
        $username = "root";
        $password = "root";
    
        try{
            $conn = new PDO("mysql:host=$host; dbname=$dbname", $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]);
    
            $stm = $conn->prepare("SELECT * FROM users WHERE email = :a AND password = :b");

            $stm->bindParam(':a', $_POST['login']);
            $pwd = md5($_POST['password']);
            $stm->bindParam(':b', $pwd);

            $stm->execute();

            $record = $stm->fetchAll(PDO::FETCH_OBJ);

            if(count($record)>0)
            {
                session_start();
                
                $_SESSION['authUser'] = $record[0];
                header('Location: sinscire.php');
            }
            else
                echo "Les infos de connexion sont erronees";
    
        }
        catch(PDOException $e)
        {
            die($e);
        }
    }
    else
    {

    ?>
    <html>
        <head>
        </head>
        <body>
            <form action="connEmp.php" method="post">
                <label for="">Nom d'utilisateur</label>
                <input type="text" name="login" id=""><br><br>
                <label for="">Mot de passe</label>
                <input type="password" name="password" id=""><br><br>
                <button type="submit">Se connecter</button>
            </form>
        </body>
    </html>

    <?php
        }
    ?>

    <?php
    // QST 3 
    // menu.php

    session_start();

    if(!isset($_SESSION['authUser']))
    {
        header('location: connEmp.php');
    }

    echo (
        "<ul>".
            "<li><a href='sinscrire.php'>S'inscrire</a></li>".
            "<li><a href='listeVoyages.php'>Liste voyages</a></li>".
            "<li><a href='seDeonnecter.php'>Se deconnecter</a></li>".
            "<li>". $_SESSION['authUser']->nomClient ." ". $_SESSION['authUser']->prenom ."</li>".
        "</ul>"
    );


    // QST 4
    require("menu.php");
    require("conxDB.php");

    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $stm3 = $conn->prepare("SELECT codeDesc FROM DescriptionVoyage WHERE villeD = :villeD AND villeA = : villeA");
        
        $stm3->bindParam(':villd', $_POST['departCity']);
        $stm3->bindParam(':villA', $_POST['destinCity']);
    
        $stm3->execute();

        $result = $stm3->fetchAll(PDO::FETCH_OBJ);

        $codeDesc = $result[0]->codeDesc;

        $stm4 = $conn->prepare("SELECT codeVoyage FROM Voyage WHERE codeDesc = :codeDesc");
        $stm4->bindParam(':codeDesc', $codeDesc);

        $stm4->execute();

        $result = $stm4->fetchAll(PDO::FETCH_OBJ);

        $codeVoyage = $result[0]->codeVoyage;

        $stm5 = $conn->prepare("INSERT INTO Inscription VALUES(:codeEmp, :codeVoyage, :nbrePers, :dateVoy)");
        $stm5->bindParam(':codeEmp', $_SESSION['authUser']->idClient);
        $stm5->bindParam(':codeVoyage', $codeVoyage);
        $stm5->bindParam(':nbrePers', $_POST['nbPerson']);
        $stm5->bindParam(':dateVoy', $_POST['date']);

        $stm5->execute();
    }
    else
    {

        $stm1 = $conn->prepare("SELECT DISTINCT(villeD) FROM DescriptionVoyage");
        $stm2 = $conn->prepare("SELECT DISTINCT(villeA) FROM DescriptionVoyage");
        
        $stm1->execute();
        $stm2->execute();
        
        $departCities = $stm1->fetchAll(PDO::FETCH_OBJ);
        $destinCities = $stm2->fetchAll(PDO::FETCH_OBJ);

    ?>
    <html>
        <head>
        </head>
        <body>
            <form action="sinscrire.php" method="post">
                <label for="">Ville de depart</label>
                <select name="departCity" id="">

                    <?php 
                        foreach($departCities as $r)
                        {
                            echo "<option value='". $r->villeD ."'>". $r->villeD ."</option>";
                        }
                    ?>

                </select><br><br>

                <label for="">Ville d'arrivee</label>
                <select name="destinCity" id="">

                    <?php 
                        foreach($destinCities as $r)
                        {
                            echo "<option value='". $r->villeA ."'>". $r->villeA ."</option>";
                        }
                    ?>

                </select><br><br>
                <label for="">date de voyage</label>
                <input type="date" name="date" id=""><br><br>
                <label for="">Nombre de personnes</label>
                <input type="text" name="nbPerson" id=""><br><br>
                <button type="submit">S'inscrire</button>
            </form>
        </body>
    </html>

    <?php

    }


    // QST 5 listeIns.php
    require("menu.php");
    require("conxDB.php");

    $stm1 = $conn->prepare("SELECT codeInsc, dateVoy, nbrePers, count(nbrePers * prixTicket) as total FROM Inscription i, Boyage v WHERE i.codeVoyage = v.codeVoyage AND codeEmp = :codeEmp AND dateVoy = :dateVoy");
    
    $stm1->bindParam(':codeEmp', $_SESSION['authUser']->idClient);
    $stm1->bindParam(':dateVoye', $_GET['dateVoy']);
    
    $stm1->execute();

    $result = $stm1->fetchAll(PDO::FETCH_OBJ);

    // 1er echo pour le champs de filtre par date
    echo (
        "<form action='' method='GET'>".
            "<input type='date' name='dateVoy'>".
            "<button type='submit'>Filtrer</button>".
        "</form>"
    );

    // 2eme echo pour la liste d'inscription
    echo(
        "<table>".
                "<thead>".
                "<th>CodeIbscr</th>".
                "<th>DateVoy</th>".
                "<th>NbrePers</th>".
                "<th>Total</th>".
                "<th>Action</th>".
            "</thead>".
            "<tbody>"
    );

        
    foreach($result as $r)
    {
        echo (
            "<tr>".
                "<td>". $r->codeInscr ."</td>".
                "<td>". $r->dateVoy ."</td>".
                "<td>". $r->nbrePers ."</td>".
                "<td>". $r->total ."</td>".
                "<td><a href='affichage.php?id='". $r->codeInscr ."'>Afficher</a></td>".
            "</tr>"
        );

    }

    echo "</tbody></table>";


    // QST 6 affichage.php
    require("menu.php");
    require("conxDB.php");

    // Ici il faut la jointure entre 3 table pour recuperer toutes les infos
    $stm1 = $conn->prepare("SELECT dateVoy, villeD, villeA, heureDepar, duree FROM Inscription i, Voyage v, DescriptionVoyage dv WHERE i.codeVoyage = v.codeVoyage AND dv.codeDesc = v.codeDesc and i.codeDesc = :id");
    
    $stm1->bindParam(':id', $_GET['id']);
    
    $stm1->execute();

    $result = $stm1->fetchAll(PDO::FETCH_OBJ);

    // C'est pour calculer l'heure d'arrive qui egal heureDepart + duree
    $time = new DateTime($result[0]->heureD);
    $time->add(new DateInterval('PT' . $result[0]->duree . 'M'));

    echo(

        "Date de voyage : ". $result[0]->dateVoy.
        "Vile de depart : ". $result[0]->villeD.
        "Ville d'arrivee : ". $result[0]->villeA.
        "Heure de depart : ". $result[0]->heureD.
        "Heure d'arrivee : ". $time
    );



    // QST 7 seDeconnecter.php
    session_start();
    
    session_destroy();

    header('location: connEmp.php');

    ?>




    



 






?>