<?php

require_once("nusoap.php");

$server = new soap_server;

$server->configureWSDL('WSFondoComune', "http://servergennari.ddns.net:2587/dbSegreteria/"); // <-- diamo un nome al webservice ed impostiamo il nostro namespace
$server->wsdl->schemaTargetNamespace = "http://servergennari.ddns.net:2587/dbSegreteria/"; // <-- impostiamo il nostro namespace anche come target dello schema WSDL (come abbiamo fatto nell'esempio WSDL)

/*
$server->wsdl->addComplexType( // <-- aggiungiamo al WSDL un tipo complesso (li abbiamo già visti in precedenza)
    'anag', // <- con il primo argomento impostiamo il nome
    'complexType', // <-- con il secondo il tipo, naturalmente complesso ;)
    'struct', // <-- indichiamo a NuSOAP il tipo php che useremo per questo elemento
    'all', //<-- qui impostiamo l'indicatore di ordine
    '', // <-- attraverso questo argomento si può impostare una restrizione ma noi non ne abbiamo bisogno
    array( // <-- con questo array inseriamo gli elementi child (figli) che faranno parte dell'elemento utente
    'nome' => array('name'=>'nome','type'=>'xsd:string'), // N.B. in NuSOAP il namespace per i tipi base è xsd, nei nostri esempi precedenti noi avevamo usato xs
    'cognome' => array('name'=>'cognome','type'=>'xsd:string'),
    )
);
*/

/*----------------------Funzioni per la tabella Persone-----------------*/

$server->register( 
    'getPeopleById', //<- decidiamo il nome da dare all'operazione
    array('id'=>'xsd:int'), //<-questo array contiene gli elementi da ricevere in input per l'operazione, come chiave il nome dell'elemento da ricevere ed il suo tipo come valore
    array('return'=>'xsd:string'), //<-- stessa cosa per questo array che invece rappresenta l'output dell'operazione. n.b. in NuSOAP i tipi da noi creati vengono inseriti nel namespace "tns"
    "http://servergennari.ddns.net:2587/dbSegreteria/",
    false,false,false,
    "Descrivo L'azione svolta dalla mia funzione" // <-- ancora una volta specifichiamo il namespace

);

function getPeopleById($id) { //<- n.b. il nome della funzione dev'essere uguale al nome che abbiamo registrato in precedenza per l'operazione
    //qui per questo esempio rimane poco da fare... all'interno della funzione puoi fare quello che ti serve, a seconda dello scopo che devi raggiungere
    //Per questo esempio è sufficiente far ritornare qualcosa alla funzione ;)
	    $myconn = mysql_connect("localhost:3306", "SegreteriaCLU", "CLUParmaSegreteria") or die("Errore di connessione a mySQL");


            mysql_select_db("dbSegreteria", $myconn) or die("Errore di connessione al database");

            $query = "SELECT * FROM Persone WHERE _id=".$id;
            $result = mysql_query($query, $myconn) or die("Errore di esecuzione della query");
            if(!($nome = @mysql_result($result, 0, "nome")) or
	    	!($cognome = @mysql_result($result, 0, "cognome"))){
	    	$ret = "errore";
	    }else{
	    	$ret = $nome."#".$cognome;
            }
	    mysql_close($myconn);

            return $ret;
}

$server->register(
        'peopleScroll',
        array('row'=>'xsd:int'),
        array('return'=>'xsd:string'),
        "http://servergennari.ddns.net:2587/dbSegreteria/",
        false,false,false,
        "La funzione prende in ingresso un dato intero \"row\" e ritorna una stringa formattata come \"_id#Nome#Cognome\" contenente il nome e il cognome della persona alla riga indicata dal valore passato in ingresso, la funzione ritornerà \"endtable\" alla fine della tabella.."
);


function peopleScroll($row){
        $myconn = mysql_connect("localhost:3306", "SegreteriaCLU", "CLUParmaSegreteria") or die("Errore di connessione a mySQL");

            mysql_select_db("dbSegreteria", $myconn) or die("Errore di connessione al database");

            $query = "SELECT * FROM Persone LIMIT ".($row-1).",1";
            $result = mysql_query($query, $myconn) or die("Errore di esecuzione della query");

            if(!($id = @mysql_result($result, 0, "_id")) or
                !($nome = @mysql_result($result, 0, "nome")) or
                !($cognome = @mysql_result($result, 0, "cognome"))){
                $ret = "endtable";
            }else{
                $ret = $id."#".$nome."#".$cognome;
            }
            mysql_close($myconn);

            return $ret;
}

$server->register(
        'addPerson',
        array('person'=>'xsd:string'),
        array('return'=>'xsd:string'),
        "http://servergennari.ddns.net:2587/dbSegreteria/",
        false,false,false,
        "La funzione prende in ingresso una stringa contenente nome e cognome della persona da aggiungere \"Nome Cognome\", la funzione poi divide nome e cognome e li inserisce all'interno della tabella Persone, lasciando a SQL il compito di assegnargli un id, la funzione ritornerà \"exist\" se il nome utente esiste già."
);

function addPerson($person){
            $myconn = mysql_connect("localhost:3306", "SegreteriaCLU", "CLUParmaSegreteria") or die("Errore di connessione a mySQL");

            mysql_select_db("dbSegreteria", $myconn) or die("Errore di connessione al database");

            $anagrafica = explode(" ", $person);
            $nome = $anagrafica[0];
            $cognome = $anagrafica[1];

            $query = "SELECT nome, cognome FROM Persone WHERE nome=\"$nome\" and cognome=\"$cognome\"";
            $mysql = mysql_query($query, $myconn);
	    $result = @mysql_result($mysql, 0);
        if($result==""){
        $query = "INSERT INTO Persone (nome,cognome) VALUES (\"$nome\",\"$cognome\")";
        $result = mysql_query($query, $myconn);
        }else{
        $result = "exist";
        }
            mysql_close($myconn);

            return $result;
}

$server->register(
        'removePerson',
        array('person'=>'xsd:string'),
        array('return'=>'xsd:string'),
        "http://servergennari.ddns.net:2587/dbSegreteria/",
        false,false,false,
        "La funzione prende in ingresso una stringa contenente nome e cognome della persona da rimuovere \"Nome Cognome\", la funzione poi divide nome e cognome e li rimuove dalla tabella Persone, la funzione ritornerà \"!exist\" se il nome utente non esiste."
);

function removePerson($person){
            $myconn = mysql_connect("localhost:3306", "SegreteriaCLU", "CLUParmaSegreteria") or die("Errore di connessione a mySQL");

            mysql_select_db("dbSegreteria", $myconn) or die("Errore di connessione al database");

            $anagrafica = explode(" ", $person);
            $nome = $anagrafica[0];
            $cognome = $anagrafica[1];

            $query = "SELECT nome, cognome FROM Persone WHERE nome=\"$nome\" and cognome=\"$cognome\"";
            $mysql = mysql_query($query, $myconn);
	    $result = @mysql_result($mysql, 0);
        if($result==""){
            $result = "!exist";
        }else{
            $query = "DELETE FROM Persone WHERE nome=\"$nome\" AND cognome=\"$cognome\"";
            $result = mysql_query($query, $myconn);
        }
        mysql_close($myconn);

        return $result;
}

/*-----------------------------Funzioni per la tabella Transaction------------------------------*/

$server->register(
    'transactionScroll',
    array('row'=>'xsd:int'),
    array('return'=>'xsd:string'),
    "http://servergennari.ddns.net:2587/dbSegreteria/",
    false,false,false,
    "La funzione prende in ingresso un dato intero \"row\" e ritorna una stringa formattata come \"_id#userId#cash#mese#anno\" contenente i dati completi della transazione alla riga indicata dal valore passato in ingresso, la funzione ritornerà \"endtable\" alla fine della tabella.."
);

function transactionScroll($row){
	    $myconn = mysql_connect("localhost:3306", "SegreteriaCLU", "CLUParmaSegreteria") or die("Errore di connessione a mySQL");

            mysql_select_db("dbSegreteria", $myconn) or die("Errore di connessione al database");

            $query = "SELECT * FROM Transazioni LIMIT ".($row-1).",1";
            $result = mysql_query($query, $myconn) or die("Errore di esecuzione della query");

	    if(!($id = @mysql_result($result, 0, "_id")) or 
		!($userId = @mysql_result($result, 0, "userId")) or 
		!($cash = @mysql_result($result, 0, "cash")) or
            	!($mese = @mysql_result($result, 0, "mese")) or
		!($anno = @mysql_result($result, 0, "anno"))){
		$ret = "endtable";
	    }else{
		$ret = $id."#".$userId."#".$cash."#".$mese."#".$anno;
	    }
            mysql_close($myconn);

            return $ret;
}

$server->register(
        'selectByPerson',
        array('person'=>'xsd:String', 'row'=>'xsd:int'),
        array('return'=>'xsd:string'),
        "http://servergennari.ddns.net:2587/dbSegreteria/",
        false,false,false,
        "prende in ingresso un dato \"person\" di tipo stringa, contenente \"Nome Cognome\" della persona inserita, tramite i quali fa una ricerca nella vista MergeData dalla quale ritorna i valori \"cash#mese#anno\" della persona indicata, per ottenere tutti i valori occore inserire anche un intero \"row\" contenente la riga da stampare, la funzione ritornerà \"endtable\" alla fine della tabella."
);

function selectByPerson($person, $row){
            $myconn = mysql_connect("localhost:3306", "SegreteriaCLU", "CLUParmaSegreteria") or die("Errore di connessione a mySQL");

            mysql_select_db("dbSegreteria", $myconn) or die("Errore di connessione al database");

        $anagrafica = explode(" ", $person);
        $nome = $anagrafica[0];
        $cognome = $anagrafica[1];

        $query = "SELECT cash, mese, anno FROM MergeData where nome=\"".$nome."\" and cognome=\"".$cognome."\"";
            $result = mysql_query($query, $myconn) or die("Errore di esecuzione della query");

            if(!($cash = @mysql_result($result, $row, "cash")) or
                !($mese = @mysql_result($result, $row, "mese")) or
                !($anno = @mysql_result($result, $row, "anno"))){
                $ret = "endtable";
            }else{
                $ret = $cash."#".$mese."#".$anno;
            }
            mysql_close($myconn);

            return $ret;
}

$server->register(
        'addTransaction',
        array('person'=>'xsd:string', 'data'=>'xsd:string'),
        array('return'=>'xsd:string'),
        "http://servergennari.ddns.net:2587/dbSegreteria/",
        false,false,false,
        "La funzione prende in ingresso una stringa contenente nome e cognome della persona da aggiungere \"Nome Cognome\", la funzione poi divide nome e cognome e li inserisce all'interno della tabella Persone, lasciando a SQL il compito di assegnargli un id, la funzione ritornerà \"exist\" se il nome utente esiste già."
);

function addTransaction($person, $data){
            $myconn = mysql_connect("localhost:3306", "SegreteriaCLU", "CLUParmaSegreteria") or die("Errore di connessione a mySQL");

            mysql_select_db("dbSegreteria", $myconn) or die("Errore di connessione al database");

            $anagrafica = explode(" ", $person);
            $nome = $anagrafica[0];
            $cognome = $anagrafica[1];

            $tran = explode(" ", $data);
            $cash = $tran[0];
            $mese = $tran[1];
            $anno = $tran[2];

            /*Aggiungere controllo su mese e anno*/

            $query = "SELECT _id FROM Persone WHERE nome=\"$nome\" and cognome=\"$cognome\"";
            $mysql = mysql_query($query, $myconn);
            $result = @mysql_result($mysql, 0);
            if(!$result==""){
                $query = "INSERT INTO Transazioni (userId, cash, mese, anno) 
                            VALUES (\"$result\",\"$cash\",\"$mese\",\"$anno\")";
                $result = mysql_query($query, $myconn);
            }else{
                $result = "!exist";
            }
            mysql_close($myconn);

            return $result;
}

$server->register(
        'removeTransaction',
        array('person'=>'xsd:string', 'data'=>'xsd:string'),
        array('return'=>'xsd:string'),
        "http://servergennari.ddns.net:2587/dbSegreteria/",
        false,false,false,
        "La funzione prende in ingresso una stringa contenente nome e cognome della persona da rimuovere \"Nome Cognome\", la funzione poi divide nome e cognome e li rimuove dalla tabella Persone, la funzione ritornerà \"!exist\" se il nome utente non esiste."
);

function removeTransaction($person, $data){
            $myconn = mysql_connect("localhost:3306", "SegreteriaCLU", "CLUParmaSegreteria") or die("Errore di connessione a mySQL");

            mysql_select_db("dbSegreteria", $myconn) or die("Errore di connessione al database");

            $anagrafica = explode(" ", $person);
            $nome = $anagrafica[0];
            $cognome = $anagrafica[1];

            $tran = explode(" ", $data);
            $cash = $tran[0];
            $mese = $tran[1];
            $anno = $tran[2];

            $query = "SELECT _id FROM Persone WHERE nome=\"$nome\" and cognome=\"$cognome\"";
            $mysql = mysql_query($query, $myconn);
            $userId = @mysql_result($mysql, 0);

            $query = "SELECT _id FROM Transazioni WHERE userId=\"$userId\" AND cash=\"$cash\" AND mese=\"$mese\" AND anno=\"$anno\"";
            $mysql = mysql_query($query, $myconn);
            $id = @mysql_result($mysql, 0);
            if($userId=="" or $id==""){
                $result = "!exist";
            }else{
                $query = "DELETE FROM Transazioni WHERE _id=\"$id\" AND userId=\"$userId\" AND cash=\"$cash\" AND mese=\"$mese\" AND anno=\"$anno\"";
                $result = mysql_query($query, $myconn);
            }
            mysql_close($myconn);

        return $result;
}

/*-----------------------------Funzioni per la vista MergeData------------------------------*/

$server->register(
        'mergeScroll',
        array('row'=>'xsd:int'),
        array('return'=>'xsd:string'),
        "http://servergennari.ddns.net:2587/dbSegreteria/"
);

function mergeScroll($row){
            $myconn = mysql_connect("localhost:3306", "SegreteriaCLU", "CLUParmaSegreteria") or die("Errore di connessione a mySQL");

            mysql_select_db("dbSegreteria", $myconn) or die("Errore di connessione al database");

            $query = "SELECT * FROM MergeData LIMIT ".($row-1).",1";
            $result = mysql_query($query, $myconn) or die("Errore di esecuzione della query");

            if(!($nome = @mysql_result($result, 0, "nome")) or
                !($cognome = @mysql_result($result, 0, "cognome"))or
		!($cash = @mysql_result($result, 0, "cash")) or
                !($mese = @mysql_result($result, 0, "mese")) or
                !($anno = @mysql_result($result, 0, "anno"))){
                $ret = "endtable";
            }else{
                $ret = $nome."#".$cognome."#".$cash."#".$mese."#".$anno;
            }
            mysql_close($myconn);

            return $ret;
}

$server->register(
    'getElement',
    array('row'=>'xsd:int', 'element'=>'xsd:string', 'cond'=>'xsd:string'),
    array('return'=>'xsd:string'),
    "http://servergennari.ddns.net:2587/dbSegreteria/",
    false,false,false,
    "La funzione prende in ingresso un dato intero \"row\" e ritorna una stringa formattata come \"_id#userId#cash#mese#anno\" contenente i dati completi della transazione alla riga indicata dal valore passato in ingresso, la funzione ritornerà \"endtable\" alla fine della tabella.."
);

function getElement($row, $element,$cond){
        $myconn = mysql_connect("localhost:3306", "SegreteriaCLU", "CLUParmaSegreteria") or die("Errore di connessione a mySQL");

            mysql_select_db("dbSegreteria", $myconn) or die("Errore di connessione al database");
            if ($cond==null) {
                if ($element=="anno") {
                    $query = "SELECT DISTINCT anno FROM Transazioni LIMIT ".($row-1).",1";
                }else if ($element=="mese") {
                    $query = "SELECT DISTINCT mese FROM Transazioni LIMIT ".($row-1).",1";
                }else if ($element=="cash"){
                    $query = "SELECT DISTINCT mese FROM Transazioni LIMIT ".($row-1).",1";
                }
            }else{
                if ($element=="anno") {
                    $query = "SELECT DISTINCT anno FROM Transazioni WHERE $cond LIMIT ".($row-1).",1";
                }else if ($element=="mese") {
                    $query = "SELECT DISTINCT mese FROM Transazioni WHERE $cond LIMIT ".($row-1).",1";
                }else if ($element=="cash"){
                    $query = "SELECT DISTINCT mese FROM Transazioni WHERE $cond LIMIT ".($row-1).",1";
                }
            }
            
            
            $result = mysql_query($query, $myconn) or die("Errore di esecuzione della query");

            if(!($obtained = @mysql_result($result, 0))){
                $ret = "endtable";
            }else{
                $ret = $obtained;
            }
            mysql_close($myconn);

            return $ret;
}

$server->register(
    'populateCount',
    array('row'=>'xsd:int', 'mese'=>'xsd:string'),
    array('return'=>'xsd:string'),
    "http://servergennari.ddns.net:2587/dbSegreteria/",
    false,false,false,
    "La funzione prende in ingresso un dato intero \"row\" e ritorna una stringa formattata come \"_id#userId#cash#mese#anno\" contenente i dati completi della transazione alla riga indicata dal valore passato in ingresso, la funzione ritornerà \"endtable\" alla fine della tabella.."
);

function populateCount($row, $mese){
        $myconn = mysql_connect("localhost:3306", "SegreteriaCLU", "CLUParmaSegreteria") or die("Errore di connessione a mySQL");

            mysql_select_db("dbSegreteria", $myconn) or die("Errore di connessione al database");
            
            $query = "SELECT DISTINCT nome, cognome, cash FROM Transazioni JOIN Persone WHERE Persone._id=Transazioni.userId and Transazioni.mese=\"$mese\" LIMIT ".($row-1).",1";
            $result = mysql_query($query, $myconn) or die("Errore di esecuzione della query");

            if(!($nome = @mysql_result($result, 0, "nome")) or
                !($cognome = @mysql_result($result, 0, "cognome")) or
                !($cash = @mysql_result($result, 0, "cash"))){
                $ret = "endtable";
            }else{
                $ret = $nome."#".$cognome."#".$cash;
            }
            mysql_close($myconn);

            return $ret;
}

$server->register(
    'PCMonth',
    array('row'=>'xsd:int', 'mese'=>'xsd:string', 'anno'=>'xsd:string'),
    array('return'=>'xsd:string'),
    "http://servergennari.ddns.net:2587/dbSegreteria/",
    false,false,false,
    "La funzione prende in ingresso un dato intero \"row\" e ritorna una stringa formattata come \"_id#userId#cash#mese#anno\" contenente i dati completi della transazione alla riga indicata dal valore passato in ingresso, la funzione ritornerà \"endtable\" alla fine della tabella.."
);

function PCMonth($row, $mese, $anno){
            $myconn = mysql_connect("localhost:3306", "SegreteriaCLU", "CLUParmaSegreteria") or die("Errore di connessione a mySQL");

            mysql_select_db("dbSegreteria", $myconn) or die("Errore di connessione al database");
            
            $query = "SELECT nome, cognome, cash FROM MergeData WHERE mese=\"$mese\" AND anno=\"$anno\" LIMIT ".($row-1).",1";
            $result = mysql_query($query, $myconn) or die("Errore di esecuzione della query");

            if(!($nome = @mysql_result($result, 0, "nome")) or
                !($cognome = @mysql_result($result, 0, "cognome")) or
                !($cash = @mysql_result($result, 0, "cash"))){
                $ret = "endtable";
            }else{
                $ret = $nome."#".$cognome."#".$cash;
            }
            mysql_close($myconn);

            return $ret;
}

$server->register(
    'MonthSum',
    array('mese'=>'xsd:string', 'anno'=>'xsd:string'),
    array('return'=>'xsd:string'),
    "http://servergennari.ddns.net:2587/dbSegreteria/",
    false,false,false,
    "La funzione prende in ingresso un dato intero \"row\" e ritorna una stringa formattata come \"_id#userId#cash#mese#anno\" contenente i dati completi della transazione alla riga indicata dal valore passato in ingresso, la funzione ritornerà \"endtable\" alla fine della tabella.."
);

function MonthSum($mese, $anno){
            $myconn = mysql_connect("localhost:3306", "SegreteriaCLU", "CLUParmaSegreteria") or die("Errore di connessione a mySQL");

            mysql_select_db("dbSegreteria", $myconn) or die("Errore di connessione al database");
            
            $query = "SELECT SUM(cash) FROM MergeData WHERE mese=\"$mese\" AND anno=\"$anno\"";
            $result = mysql_query($query, $myconn) or die("Errore di esecuzione della query");

            $ret = @mysql_result($result, 0);
            mysql_close($myconn);

            return $ret;
}

$server->service(file_get_contents("php://input"));

exit();
?>
