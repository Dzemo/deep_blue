<?php

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "config.php");
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "classloader.php");

// Définition de la version de l'API. Nombre entier.
// Cette version doit être augmenter en cas de modification du fonctionnement de cette API de synchronisation
// Cette version est comparé avec la version envoyé par l'application mobile, si les versions ne correspondent pas un message 
// d'erreur correspondent est envoyé
$api_version = 1;


if (!isset($_POST['api_version'])) {
    printResponse("older-version");
    die();
} else {
    $mobile_version = intval(filter_input(INPUT_POST, 'api_version', FILTER_VALIDATE_INT));
    
    if ($api_version > $mobile_version) {
        printResponse("older-version");
        die();
    } else if ($api_version < $mobile_version) {
        printResponse("newer-version");
        die();
    }

    //Les deux version sont identiques
}

//Si pas de données json, on repond 'no-data'
if (!isset($_POST['data'])) {
    printResponse("no-data");
    die();
}

//Tableau contenant les données envoyées par l'application
$arrayRequest = getRequestData();

//Tableau contenant les données envoyées en réponse
$arrayResponse = array('utilisateurs' => null,
    'fichesSecurite' => array(),
    'aptitudes' => array(),
    'embarcations' => array(),
    'sites' => array(),
    'moniteurs' => array(),
    'fichesOk' => array(),
    'historiquesOk' => array(),
);

//////////////////////////////////////////////////
//Traitement de la version max des utilisateur //
//////////////////////////////////////////////////
if (isset($arrayRequest['utilisateurMaxVersion'])) {
    $utilisateurMaxVersion = intval($arrayRequest['utilisateurMaxVersion']);
    $utilisateurs = UtilisateurDao::getFromVersion($utilisateurMaxVersion);
    $arrayResponse['utilisateurs'] = $utilisateurs;
}

///////////////////////////////////////////////////
//Récupération de l'utilisateur qui synchronise //
///////////////////////////////////////////////////
$utilisateurSynch = null;
if (isset($arrayRequest['utilisateurLogin']) && strlen($arrayRequest['utilisateurLogin']) > 0) {
    $utilisateurSynch = UtilisateurDao::getbyLogin($arrayRequest['utilisateurLogin']);

    //Ajout d'un historique pour indiquée que l'utilisateur à synchroniser son appareil
    $historique = new Historique($utilisateurSynch->getLogin(), time(), null);
    $historique->setSource(Historique::sourceSynchronize);
    $historique->setCommentaire("Synchronisation de l'appareil");
    $historique = HistoriqueDao::insert($historique);
}

//Si l'utilisateur est null (existe pas) ou est désactivé, on arrete la
if ($utilisateurSynch == null || !$utilisateurSynch->getActif()) {
    printResponse(json_encode($arrayResponse));
    die();
}

///////////////////////////////////////////////////////////////////////////////
//Enregistrement des fiches de sécurité si présente et de leurs historiques //
///////////////////////////////////////////////////////////////////////////////
$arrayResponseFicheHistorique = enregistreFichesEtHistoriqueAvecJson($arrayRequest, $utilisateurSynch);
$arrayResponse['fichesOk'] = $arrayResponseFicheHistorique['fichesOk'];
$arrayResponse['historiquesOk'] = $arrayResponseFicheHistorique['historiquesOk'];


/////////////////////////////////////////////////////////////
// Envoi des aptitudes, embarcations, moniteurs et sites  //
/////////////////////////////////////////////////////////////
//Envoi des nouvelles aptitudes (aptitudeMaxVersion)
if (isset($arrayRequest['aptitudeMaxVersion'])) {
    $aptitudeMaxVersion = intval($arrayRequest['aptitudeMaxVersion']);
    $aptitudes = AptitudeDao::getFromVersion($aptitudeMaxVersion);
    //Comme le tableau des aptitudes renvoyé par le dao est indexé par leur id, il faut le renvoyer dans un tableau non indexé
    if ($aptitudes != null) {
        $aptitudesResponse = array();
        foreach ($aptitudes as $aptitude) {
            $aptitudesResponse[] = $aptitude;
        }
        $arrayResponse['aptitudes'] = $aptitudesResponse;
    }
}

//Envoi des nouvelles embarcations (embarcationMaxVersion)
if (isset($arrayRequest['embarcationMaxVersion'])) {
    $embarcationMaxVersion = intval($arrayRequest['embarcationMaxVersion']);
    $arrayResponse['embarcations'] = EmbarcationDao::getFromVersion($embarcationMaxVersion);
}

//Envoi des nouveaux moniteurs (moniteurMaxVersion)
if (isset($arrayRequest['moniteurMaxVersion'])) {
    $moniteurMaxVersion = intval($arrayRequest['moniteurMaxVersion']);
    $arrayResponse['moniteurs'] = MoniteurDao::getFromVersion($moniteurMaxVersion);
}

//Envoi des nouveaux sites (siteMaxVersion)
if (isset($arrayRequest['siteMaxVersion'])) {
    $siteMaxVersion = intval($arrayRequest['siteMaxVersion']);
    $sites = SiteDao::getFromVersion($siteMaxVersion);
    //Comme le tableau des sites renvoyé par le dao est indexé par leur id, il faut le renvoyer dans un tableau non indexé
    if ($sites != null) {
        $sitesResponse = array();
        foreach ($sites as $site) {
            $sitesResponse[] = $site;
        }
        $arrayResponse['sites'] = $sitesResponse;
    }
}

//////////////////////////////////////////////////////
//Récupération des fiches modifiés sur le téléphone //
//////////////////////////////////////////////////////
if (isset($arrayRequest['fichesSecuriteEnCours'])) {
    $arrayFichesEnCoursJson = $arrayRequest['fichesSecuriteEnCours'];

    for ($i = 0; $i < count($arrayFichesEnCoursJson); $i++) {
        $ficheJson = $arrayFichesEnCoursJson[$i];

        //Récupération des informations de la fiche
        $ficheJsonToObjectResult = ficheJsonToObject($ficheJson, false);
        $erreurRecuperationFiche = $ficheJsonToObjectResult['erreurRecuperationFiche']; 
        $ficheDistant = $ficheJsonToObjectResult['fiche'];

        //Traitement de la fiche récupéré
        if ($ficheDistant->getId() > 0) {
            //Si la fiche à un id de set, il s'agit d'une fiche qui a déjà été synchronisé, donc il faut merge ses information avec les informations de la fiche local
            $ficheLocal = FicheSecuriteDao::getbyId($ficheDistant->getId(), true);
            if ($ficheLocal != null) {
                $mergeFiche = mergeFiche($ficheDistant, $ficheLocal);
                $mergeFicheUpdated = FicheSecuriteDao::update($mergeFiche);

                //Ajout de l'historique
                if($mergeFicheUpdated != null){
                    $historique = new Historique($utilisateurSynch->getLogin(), time(), $mergeFicheUpdated->getId());
                    $historique->setSource(Historique::sourceSynchronize);
                    $historique->setCommentaire("Synchronisation de la fiche (Récupération d'information depuis l'application mobile)");
                    HistoriqueDao::insert($historique);

                    //Ajout de la fiche au fiches renvoyé
                    $arrayResponse['fichesSecurite'][] = $mergeFicheUpdated;
                } else {
                    error_log("Erreur lors de l'enregistrement de la fiche ".$mergeFiche->getId()." ($erreurRecuperationFiche)");
                }
            }
        } else {
            //Si la fiche n'a pas d'id, il s'agit d'une fiche créée sur le téléphone, il faut donc l'enregistrer et la renvoyer avec les id web set
            $ficheDistant->setEtat(FicheSecurite::etatSynchronise);
            $ficheEnregistrer = FicheSecuriteDao::insert($ficheDistant);
            
            //Ajout de l'historique
            $historique = new Historique($utilisateurSynch->getLogin(), time(), $ficheEnregistrer->getId());
            $historique->setSource(Historique::sourceSynchronize);
            $historique->setCommentaire("Enregistrement d'une fiche créée sur l'application mobile");
            HistoriqueDao::insert($historique);
                
            //Ajout de la fiche au fiches renvoyé
            $arrayResponse['fichesSecurite'][] = $ficheEnregistrer;
        }
    }
}

///////////////////////////////////////////////////////////////
//Récupération et envoie des nouvelles fiches de sécuritées //
///////////////////////////////////////////////////////////////
//Récupération des paramètres de récupération des fiches (synchRetrieveLength)
if (isset($arrayRequest['synchRetrieveLength'])) {
    $synchRetrieveLength = intval($arrayRequest['synchRetrieveLength']);
} else {
    $synchRetrieveLength = 0; //TODO config
}
//Calcul du max timestamps pour la récupération des fiches en fonction de synchRetrieveLength
$timestampsHeureActuel = time() % (24 * 60 * 60);
$timestampsJourActuel = time() - $timestampsHeureActuel;
$minTimestamps = $timestampsJourActuel;
$maxTimestamps = $timestampsJourActuel + (24 * 60 * 60 * ($synchRetrieveLength + 1));

//Récupération des paramètres de récupération des fiches (synchRetrieveTypeAll)
if (isset($arrayRequest['synchRetrieveTypeAll'])) {
    $synchRetrieveTypeAll = intval($arrayRequest['synchRetrieveTypeAll']);
} else {
    $synchRetrieveTypeAll = false; //TODO mettre une valeur par défaut dans le fichier de config ??
}
//Récupération du moniteur associé à l'utilisateur courant si il existe et synchRetrieveTypeAll = false
$idMoniteurAssocie = null;
if ($synchRetrieveTypeAll == false && $utilisateurSynch->getMoniteurAssocie() != null) {
    $idMoniteurAssocie = $utilisateurSynch->getMoniteurAssocie()->getId();
}

//Récupération des nouvelles fiches (sans tenir compte de ficheSecuriteMaxVersion qui sert plus) et ajout dans les fiches renvoyé si elles n'y sont pas déjà
if (isset($arrayRequest['ficheSecuriteMaxVersion'])) {
    $ficheSecuriteMaxVersion = intval($arrayRequest['ficheSecuriteMaxVersion']);
    $nouvellesFiches = FicheSecuriteDao::getFromVersionIdDpTimestamps(0, $idMoniteurAssocie, $minTimestamps, $maxTimestamps);
    
    if($nouvellesFiches != null){
        //Ajout des nouvelles fiches dans les fiches renvoyé si elles ne sont pas déjà présente
        foreach($nouvellesFiches as $nouvelleFiche){
            $ficheDejaPresente = false;
            foreach ($arrayResponse['fichesSecurite'] as $ficheSecuriteDejaPresente) {
               if($nouvelleFiche->getId() == $ficheSecuriteDejaPresente->getId()){
                   $ficheDejaPresente = true;
               }
            }

            if(!$ficheDejaPresente){        
                //Met à jours l'état de la fiche et ajout dans les fiches renvoyés
                $nouvelleFiche = FicheSecuriteDao::updateEtat($nouvelleFiche, FicheSecurite::etatSynchronise);
                $arrayResponse['fichesSecurite'][] = $nouvelleFiche;

                //Enregistrement de l'historique de syncrhonisation de la fiche
                $historique = new Historique($utilisateurSynch->getLogin(), time(), $nouvelleFiche->getId());
                $historique->setSource(Historique::sourceSynchronize);
                $historique->setCommentaire("Synchronisation de la fiche");
                HistoriqueDao::insert($historique);
            }
        }    
    }
}

/////////////////////////
//Envoi de la réponse //
/////////////////////////
printResponse(json_encode($arrayResponse));

///////////////////////////////
//Fin de la synchronisation //
///////////////////////////////



function getRequestData() {
    return json_decode($_POST['data'], true);
}

function printResponse($response) {
    echo $response;
}

/**
 * Merge les informations d'une fiche distante et d'une fiche locale, en gardant les informations de la version la plus élevé
 * @param FicheSecurite $ficheDistante  Fiche issue de l'application mobile qui synchronise
 * @param FicheSecurite $ficheLocale    Fiche issue de la base de données du serveur
 * @return \FicheSecurite
 */
function mergeFiche(FicheSecurite $ficheDistante, FicheSecurite $ficheLocale) {
    if ($ficheDistante == null) {
        return $ficheLocale;
    } else if ($ficheLocale == null) {
        return $ficheDistante;
    }

    //Les deux fiches ont le même id
    $mergedFiche = new FicheSecurite($ficheDistante->getId(), max($ficheLocale->getVersion(), $ficheDistante->getVersion()));
    $mergedFiche->setEtat(FicheSecurite::etatSynchronise);
    $mergedFiche->setIdDistant($ficheDistante->getIdDistant());

    //Merge des informations de la fiche
    if ($ficheDistante->getVersion() > $ficheLocale->getVersion()) {
        //Les informations de la fiche distante sont plus récentes, on récupère celle ci
        $mergedFiche->setDirecteurPlonge($ficheDistante->getDirecteurPlonge());
        $mergedFiche->setEmbarcation($ficheDistante->getEmbarcation());
        $mergedFiche->setTimestamp($ficheDistante->getTimestamp());
        $mergedFiche->setSite($ficheDistante->getSite());
        $mergedFiche->setDesactive($ficheDistante->getDesactive());
    } else {
        //Les informations de la fiche locale sont plus récentes, on récupère celle ci
        $mergedFiche->setDirecteurPlonge($ficheLocale->getDirecteurPlonge());
        $mergedFiche->setEmbarcation($ficheLocale->getEmbarcation());
        $mergedFiche->setTimestamp($ficheLocale->getTimestamp());
        $mergedFiche->setSite($ficheLocale->getSite());
        $mergedFiche->setDesactive($ficheLocale->getDesactive());
    }

    //Merge des palanquées
    //On récupère toute les palanquées distantes : celle qui n'ont pas d'id sont rajouté, celle qui on un id sont comparé avec les palanquées locale    
    foreach ($ficheDistante->getPalanques() as $palanqueeDistante) {
        
        if ($palanqueeDistante->getId() < 0) {
            $mergedFiche->ajouterPalanquee($palanqueeDistante);
        } else {
            //La palanquée distante existe aussi localement, il faut merge leurs informations
            foreach($ficheLocale->getPalanques() as $palanqueeLocale){
                if($palanqueeLocale->getId() == $palanqueeDistante->getId()){
                    $mergedFiche->ajouterPalanquee(mergePalanquee($palanqueeDistante, $palanqueeLocale, $mergedFiche->getId()));              
                }
            }
        }
    }
    
    //Parcours des palanquées pour mettre à jours les numéro si besoin
    $palanquees = $mergedFiche->getPalanques();
    $numeroUtilise = array(); //Tableau contenant les numéro de palanquées déjà utilisé
    for($i = 0; $i < count($palanquees) ; $i++){
        if(array_key_exists($palanquees[$i]->getNumero(), $numeroUtilise)){
            $nouveauNumeroTrouve = false;
            $tentativeNumero = 1;
            while(!$nouveauNumeroTrouve){
                if(!array_key_exists($tentativeNumero, $numeroUtilise)){
                    //On a trouvé un nouveau numéro libre pour cette palanquee
                    $palanquees[$i]->setNumero($tentativeNumero);
                    $nouveauNumeroTrouve = true;
                    $numeroUtilise[$tentativeNumero] = true;
                } else{
                    $tentativeNumero++;
                }
            }
        } else {
            //Ce numéro n'était pas utilisé, on l'ajoute
            $numeroUtilise[$palanquees[$i]->getNumero()] = true;
        }
    }
    $mergedFiche->setPalanques($palanquees);
    
    //enfin, on ajoute les palanquée locale qui ne sont pas présente dans les palanquée distante
    foreach ($ficheLocale->getPalanques() as $palanqueeLocale) {
        $palanqueeDejaPresente = false;

        //Parcours des palanquée déjà présente en comparant les id
        foreach ($mergedFiche->getPalanques() as $mergedPalanquee) {
            if ($mergedPalanquee->getId() > 0 && $mergedPalanquee->getId() == $palanqueeLocale->getId()) {
                $palanqueeDejaPresente = true;
            }
        }
        if (!$palanqueeDejaPresente) {
            $mergedFiche->ajouterPalanquee($palanqueeLocale);
        }
    }

    return $mergedFiche;
}

/**
 * Merge les informations de la palanquée et leurs plongeurs en conservent les information de la palanquées avec la version la plus élevé et
 * en comparant les plongeurs pour merge les plongeurs commun et ajouter les plongeurs qui manquant
 * Renvoie la palanquée mergé.
 * @param Palanque  $palanqueeDistante  Palanquée issue de l'application mobile qui synchronise
 * @param Palanque  $palanqueeLocale    Palanquée issue de la base de données du serveur
 * @param int       $idFicheSecurite    Id de la fiche de sécurité à laquel appartient cette palanquée
 * @return \Palanque
 */
function mergePalanquee(Palanque $palanqueeDistante,Palanque $palanqueeLocale, $idFicheSecurite){
    if($palanqueeDistante === null){
        return $palanqueeLocale;
    }
    else if($palanqueeLocale === null){
        return $palanqueeDistante;
    }
    
    //Les deux palanquees ont le même id
    $mergedPalanquee = new Palanque($palanqueeDistante->getId(), max($palanqueeDistante->getVersion(), $palanqueeLocale->getVersion()));
    $mergedPalanquee->setIdFicheSecurite($idFicheSecurite);
    $mergedPalanquee->setIdDistant($palanqueeDistante->getIdDistant());
    
    //Merge des informations de la palanquee
    if($palanqueeDistante->getVersion() > $palanqueeLocale->getVersion()){
        $mergedPalanquee->setNumero($palanqueeDistante->getNumero());
        $mergedPalanquee->setTypeGaz($palanqueeDistante->getTypeGaz());
        $mergedPalanquee->setTypePlonge($palanqueeDistante->getTypePlonge());
        $mergedPalanquee->setProfondeurPrevue($palanqueeDistante->getProfondeurPrevue());
        $mergedPalanquee->setDureePrevue($palanqueeDistante->getDureePrevue());
        $mergedPalanquee->setHeure($palanqueeDistante->getHeure());
        $mergedPalanquee->setProfondeurRealiseeMoniteur($palanqueeDistante->getProfondeurRealiseeMoniteur());
        $mergedPalanquee->setMoniteur($palanqueeDistante->getMoniteur());
        $mergedPalanquee->setDureeRealiseeMoniteur($palanqueeDistante->getDureeRealiseeMoniteur());
        $mergedPalanquee->setDesactive($palanqueeDistante->getDesactive());
    } else {
        $mergedPalanquee->setIdFicheSecurite($idFicheSecurite);
        $mergedPalanquee->setNumero($palanqueeLocale->getNumero());
        $mergedPalanquee->setTypeGaz($palanqueeLocale->getTypeGaz());
        $mergedPalanquee->setTypePlonge($palanqueeLocale->getTypePlonge());
        $mergedPalanquee->setProfondeurPrevue($palanqueeLocale->getProfondeurPrevue());
        $mergedPalanquee->setDureePrevue($palanqueeLocale->getDureePrevue());
        $mergedPalanquee->setHeure($palanqueeLocale->getHeure());
        $mergedPalanquee->setProfondeurRealiseeMoniteur($palanqueeLocale->getProfondeurRealiseeMoniteur());
        $mergedPalanquee->setMoniteur($palanqueeLocale->getMoniteur());
        $mergedPalanquee->setDureeRealiseeMoniteur($palanqueeLocale->getDureeRealiseeMoniteur());
        $mergedPalanquee->setDesactive($palanqueeLocale->getDesactive());
    }
    
    //Merge des plongeurs
    //On récupère tout les plongeurs distants : ceux qui n'ont pas d'id sont rajouté, ceux qui on un id sont comparé avec les plongeurs locaux
    foreach($palanqueeDistante->getPlongeurs() as $plongeurDistant){
        if($plongeurDistant->getId() < 0){
            $mergedPalanquee->ajouterPlongeur($plongeurDistant);
        } else {
            //Le plongeur distant existe aussi localement, il faut merge ses informations avec le plongeur local correspondent
            foreach($palanqueeLocale->getPlongeurs() as $plongeurLocal){
                if($plongeurLocal->getId() == $plongeurDistant->getId()){
                    $mergedPlongeur = new Plongeur($plongeurDistant->getId(), max($plongeurDistant->getVersion(), $plongeurLocal->getVersion()));                       
                    $mergedPlongeur->setIdFicheSecurite($idFicheSecurite);
                    $mergedPlongeur->setIdPalanque($mergedPalanquee->getId());
                    $mergedPlongeur->setIdDistant($plongeurDistant->getIdDistant());
                    
                    //Merge des informations du plongeur
                    if($plongeurDistant->getVersion() > $plongeurLocal->getVersion()){ 
                        $mergedPlongeur->setNom($plongeurDistant->getNom());
                        $mergedPlongeur->setPrenom($plongeurDistant->getPrenom());
                        $mergedPlongeur->setAptitudes($plongeurDistant->getAptitudes());
                        $mergedPlongeur->setTelephone($plongeurDistant->getTelephone());
                        $mergedPlongeur->setTelephoneUrgence($plongeurDistant->getTelephoneUrgence());
                        $mergedPlongeur->setDateNaissance($plongeurDistant->getDateNaissance());
                        $mergedPlongeur->setProfondeurRealisee($plongeurDistant->getProfondeurRealisee());
                        $mergedPlongeur->setDureeRealisee($plongeurDistant->getDureeRealisee());
                        $mergedPlongeur->setDesactive($plongeurDistant->getDesactive());
                    } else {
                        $mergedPlongeur->setNom($plongeurLocal->getNom());
                        $mergedPlongeur->setPrenom($plongeurLocal->getPrenom());
                        $mergedPlongeur->setAptitudes($plongeurLocal->getAptitudes());
                        $mergedPlongeur->setTelephone($plongeurLocal->getTelephone());
                        $mergedPlongeur->setTelephoneUrgence($plongeurLocal->getTelephoneUrgence());
                        $mergedPlongeur->setDateNaissance($plongeurLocal->getDateNaissance());
                        $mergedPlongeur->setProfondeurRealisee($plongeurLocal->getProfondeurRealisee());
                        $mergedPlongeur->setDureeRealisee($plongeurLocal->getDureeRealisee());
                        $mergedPlongeur->setDesactive($plongeurLocal->getDesactive());
                    }
                    
                    $mergedPalanquee->ajouterPlongeur($mergedPlongeur);
                    
                    //Fin du merge des plongeurs
                }
            }
        }
        
        //Fin du parcours des plongeurs distant
    }
    
    //Enfin, on ajoute les plongeurs locaux qui ne sont pas déjà présent
    foreach($palanqueeLocale->getPlongeurs() as $plongeurLocal){
        $plongeurDejaPresent = false;

        //Parcours des palanquée déjà présente en comparant les id
        foreach ($mergedPalanquee->getPlongeurs() as $mergedPlongeur) {
            if ($mergedPlongeur->getId() > 0 && $mergedPlongeur->getId() == $plongeurLocal->getId()) {
                $plongeurDejaPresent = true;
            }
        }
        if (!$plongeurDejaPresent) {
            $mergedPalanquee->ajouterPlongeur($plongeurLocal);
        }
    }
    
    return $mergedPalanquee;
}

/**
 * Récupère les fiches et les historiques dans le tableau contenant la request json et les enregistres
 * Renvoi un tableau contenant de boolean ['fichesOk', 'historiquesOk'] indiquant le succès de l'enregistrement
 * @param  array $arrayRequest     
 * @param  Utilisateur $utilisateurSynch 
 * @return array
 */
function enregistreFichesEtHistoriqueAvecJson($arrayRequest, $utilisateurSynch) {

    //Enregistre le résultat de la synchronisation des fiches et historiques, retourné a la fin
    $arrayResponseFicheHistorique = array('fichesOk' => array(), 'historiquesOk' => array());

    //Tableau mappant les id des fiches local avec l'id json, pour récupérer les historiques
    $arrayMapIdsFiche = array();

    if ($utilisateurSynch == null) {
        // Si pas d'utilisateur on ne pourra pas enregistrer les fiches donc on annule ici
        return $arrayResponseFicheHistorique;
    }

    //Enregistrement des fiches de sécurité validés si présente et de leurs historiques
    if (isset($arrayRequest['fichesSecuriteValidees'])) {
        $arrayFichesValideesJson = $arrayRequest['fichesSecuriteValidees'];

        for ($i = 0; $i < count($arrayFichesValideesJson); $i++) {
            $ficheJson = $arrayFichesValideesJson[$i];

            //Récupération des informations de la fiche
            $ficheJsonToObjectResult = ficheJsonToObject($ficheJson, true);
            $erreurRecuperationFiche = $ficheJsonToObjectResult['erreurRecuperationFiche']; 
            $ficheSecurite = $ficheJsonToObjectResult['fiche'];

            //Si la fiche a bien été récupérée on l'enregistre et on ajoute son id distant au tableau des fiches récupérés
            if (strlen($erreurRecuperationFiche) == 0) {
                $arrayResponseFicheHistorique['fichesOk'][] = $ficheSecurite->getIdDistant();

                //Mise à jours de l'état à archive
                $ficheSecurite->setEtat(FicheSecurite::etatArchive);

                //Enregistrement de la fiche
                if ($ficheSecurite->getId() > 0) {
                    $ficheSecurite = FicheSecuriteDao::update($ficheSecurite);
                    $commentaireHistorique = "Archivage de la fiche";
                } else {
                    $ficheSecurite = FicheSecuriteDao::insert($ficheSecurite);
                    $commentaireHistorique = "Archivage de la fiche (fiche créée depuis l'application mobile')";
                }

                //Enregistrement de l'historique de la fiche
                $historique = new Historique($utilisateurSynch->getLogin(), time(), $ficheSecurite->getId());
                $historique->setSource(Historique::sourceSynchronize);
                $historique->setCommentaire($commentaireHistorique);
                $historique = HistoriqueDao::insert($historique);

                //Map de l'id de la fiche issue de l'application avec l'id local, pour l'enregistrement des historiques
                $idFicheSecuriteJson = $ficheSecurite->getIdDistant();
                $arrayMapIdsFiche[$idFicheSecuriteJson] = $ficheSecurite->getId();
            } else {
                error_log($erreurRecuperationFiche);
            }
        }
    }

    //Récupération des historiques
    if (isset($arrayRequest['historiques'])) {

        for ($i = 0; $i < count($arrayRequest['historiques']); $i++) {
            $historiqueJson = $arrayRequest['historiques'][$i];
            $historiqueBienRecuperee = true;

            //Récupération de l'id distant de l'historique
            if (isset($historiqueJson['idHistorique'])) {
                $idHistorique = $historiqueJson['idHistorique'];
            } else {
                $historiqueBienRecuperee = false;
            }

            //Récupération du login
            if (isset($historiqueJson['loginUtilisateur'])) {
                $loginHistorique = $historiqueJson['loginUtilisateur'];
            } else {
                $historiqueBienRecuperee = false;
            }

            //Récupération du timestamp
            if (isset($historiqueJson['timestamp'])) {
                $timestampHistorique = $historiqueJson['timestamp'];
            } else {
                $historiqueBienRecuperee = false;
            }

            //Récupération de la fiche dans le tableau contenant les ids distant des fiches déjà récupérées
            if (isset($historiqueJson['idFicheSecurite'])) {
                if (array_key_exists($historiqueJson['idFicheSecurite'], $arrayMapIdsFiche)) {
                    $idFicheSecuriteHistorique = $arrayMapIdsFiche[$historiqueJson['idFicheSecurite']];
                } else {
                    //Historique associé à une fiche qui n'a pas été récupérés dont on le récupère pas
                    $historiqueBienRecuperee = false;
                }
            } else {
                //Historique qui n'est pas associé à une fiche
                $idFicheSecuriteHistorique = null;
            }

            //Récupération du commentaire
            if (isset($historiqueJson['commentaire'])) {
                $commentaireHistorique = $historiqueJson['commentaire'];
            } else {
                $historiqueBienRecuperee = false;
            }

            if ($historiqueBienRecuperee) {
                //Enregistrement de l'historique
                $historique = new Historique($loginHistorique, $timestampHistorique, $idFicheSecuriteHistorique);
                $historique->setSource(Historique::sourceMobile);
                $historique->setCommentaire($commentaireHistorique);
                HistoriqueDao::insert($historique);

                $arrayResponseFicheHistorique['historiquesOk'][] = $idHistorique;
            }

            //Fin de la récupération des historiques
        }
    }

    return $arrayResponseFicheHistorique;
}

/**
 * Transforme un tableau json contenant une fiche de sécurité en un objet php FicheSecurite
 * @param array     $ficheJson                  Le tableau json contenant les informations de la fiche à récupérer
 * @return \FicheSecurite
 */
function ficheJsonToObject($ficheJson) {

    $erreurRecuperationFiche = "";
    
    //Récupération de l'id
    if (isset($ficheJson['idWeb']) && intval(isset($ficheJson['idWeb'])) > 0) {
        $idFiche = intval($ficheJson['idWeb']);
    } else {
        $idFiche = -1;
    }

    //Récupération de la version
    if (isset($ficheJson['version']) && intval(isset($ficheJson['version'])) > 0) {
        $versionFiche = intval($ficheJson['version']);
    } else {
        $versionFiche = time();
    }

    $ficheSecurite = new FicheSecurite($idFiche, $versionFiche);

    //Récupération de l'id distant
    if (isset($ficheJson['id']) && intval(isset($ficheJson['id'])) > 0) {
        $ficheSecurite->setIdDistant(intval($ficheJson['id']));
    } else {
        $erreurRecuperationFiche .= ";id distant absent";
    }
    
    //Récupération de l'embarcation
    if (isset($ficheJson['embarcation']) && isset($ficheJson['embarcation']['idWeb'])) {
        $ficheSecurite->setEmbarcation(EmbarcationDao::getById($ficheJson['embarcation']['idWeb']));
    } else {
        $erreurRecuperationFiche .= ";embarcation absente";
    }

    //Récupération du directeur de plongée
    if (isset($ficheJson['directeurPlonge']) && isset($ficheJson['directeurPlonge']['idWeb'])) {
        $ficheSecurite->setDirecteurPlonge(MoniteurDao::getById($ficheJson['directeurPlonge']['idWeb']));
    } else {
        $erreurRecuperationFiche .= ";directeurPlonge absente";
    }

    //Récupération des palanquees
    if (isset($ficheJson['palanquees'])) {
        $palanquees = array();
        for ($j = 0; $j < count($ficheJson['palanquees']); $j++) {
            $palanqueeJson = $ficheJson['palanquees'][$j];

            $palanqueeJsonToObjectResult = palanqueeJsonToObject($palanqueeJson, $ficheSecurite->getId());
            $palanquee =  $palanqueeJsonToObjectResult['palanquee'];
            $erreurRecuperationFiche .= $palanqueeJsonToObjectResult['erreurRecuperationFiche'];

            $palanquees[] = $palanquee;

            //Fin de la récupération des palanquées
        }

        $ficheSecurite->setPalanques($palanquees);
    }

    //Récupération du timestamp
    if (isset($ficheJson['timestamp'])) {
        $ficheSecurite->setTimestamp($ficheJson['timestamp']);
    } else {
        $erreurRecuperationFiche .= ";timestamp absent";
    }
    
    //Récupération de l'eventuelle suppression de la fiche
    if (isset($ficheJson['desactive'])) {
        $ficheSecurite->setDesactive($ficheJson['desactive'] == "true" ? true : false);
    }

    //Récupération du site
    if (isset($ficheJson['site'])) {
        if (isset($ficheJson['site']['idWeb']) && intval($ficheJson['site']['idWeb']) > 0) {
            $ficheSecurite->setSite(SiteDao::getById($ficheJson['site']['idWeb']));
        } else {
            $site = new Site(null, $ficheJson['site']['version']);
            $site->setNom($ficheJson['site']['nom']);
            $site->setCommentaire($ficheJson['site']['commentaire']);
            $ficheSecurite->setSite($site);
        }
    } else {
        $erreurRecuperationFiche .= ";site absent";
    }

    return ['fiche' => $ficheSecurite, 'erreurRecuperationFiche' => $erreurRecuperationFiche];
}

/**
 * Transforme un tableau json contenant une palanquee en un objet php Palanque
 * @param array     $palanqueeJson              Le tableau json contenant les informations de la palanquée à récupérer
 * @param int       $idFicheSecurite            L'id (web) de la fiche de sécurité à laquel appartient cette palanquee
 * @return array Un tableau associatif contenant la palanquee dans 'palanquee' et les eventuelles erreurs dans 'erreurRecuperationFiche'
 */
function palanqueeJsonToObject($palanqueeJson, $idFicheSecurite) {
    $erreurRecuperationFiche = "";
    
    //Récupération de l'id
    if (isset($palanqueeJson['idWeb']) && intval(isset($palanqueeJson['idWeb'])) > 0) {
        $idPalanquee = intval($palanqueeJson['idWeb']);
    } else {
        $idPalanquee = -1;
    }

    //Récupération de la version
    if (isset($palanqueeJson['version']) && intval(isset($palanqueeJson['version'])) > 0) {
        $versionPalanquee = intval($palanqueeJson['version']);
    } else {
        $versionPalanquee = time();
    }

    $palanquee = new Palanque($idPalanquee, $versionPalanquee);
    $palanquee->setIdFicheSecurite($idFicheSecurite);

    //Récupération de l'id distant
    if (isset($palanqueeJson['id']) && intval(isset($palanqueeJson['id'])) > 0) {
        $palanquee->setIdDistant(intval($palanqueeJson['id']));
    } else {
        $erreurRecuperationFiche .= ";id distant absent pour la palanquee ";
    }
    
    //Récupération du numéro
    if (isset($palanqueeJson['numero'])) {
        $palanquee->setNumero($palanqueeJson['numero']);
    } else {
        $erreurRecuperationFiche .= ";numero de palanquee absent";
    }

    //Récupération du type de gaz
    if (isset($palanqueeJson['typeGaz'])) {
        $palanquee->setTypeGaz($palanqueeJson['typeGaz']);
    } else {
        $erreurRecuperationFiche .= ";typeGaz absent pour la palanquee numero " . $palanquee->getNumero();
    }

    //Récupération du type de plongée
    if (isset($palanqueeJson['typePlonge'])) {
        $palanquee->setTypePlonge($palanqueeJson['typePlonge']);
    } else {
        $erreurRecuperationFiche .= ";typePlonge absent pour la palanquee numero " . $palanquee->getNumero();
    }

    //Récupération de la profondeur prévue
    if (isset($palanqueeJson['profondeurPrevue'])) {
        $palanquee->setProfondeurPrevue($palanqueeJson['profondeurPrevue']);
    } else {
        $erreurRecuperationFiche .= ";profondeurPrevue absent pour la palanquee numero " . $palanquee->getNumero();
    }

    //Récupération de la durée prévue
    if (isset($palanqueeJson['dureePrevue'])) {
        $palanquee->setDureePrevue($palanqueeJson['dureePrevue']);
    } else {
        $erreurRecuperationFiche .= ";dureePrevue absent pour la palanquee numero " . $palanquee->getNumero();
    }

    //Récupération de l'heure'
    if (isset($palanqueeJson['heure'])) {
        $palanquee->setHeure($palanqueeJson['heure']);
    } else {
        $erreurRecuperationFiche .= ";heure absent pour la palanquee numero " . $palanquee->getNumero();
    }

    //Récupération de la profondeur réalisée par le moniteur
    if (isset($palanqueeJson['profondeurRealiseeMoniteur'])) {
        $palanquee->setProfondeurRealiseeMoniteur($palanqueeJson['profondeurRealiseeMoniteur']);
    } else {
        $erreurRecuperationFiche .= ";profondeurRealiseeMoniteur absent pour la palanquee numero " . $palanquee->getNumero();
    }

    //Récupération de la durée réalisée par le moniteur
    if (isset($palanqueeJson['dureeRealiseeMoniteur'])) {
        $palanquee->setDureeRealiseeMoniteur($palanqueeJson['dureeRealiseeMoniteur']);
    } else {
        $erreurRecuperationFiche .= ";dureeRealiseeMoniteur absent pour la palanquee numero " . $palanquee->getNumero();
    }
    
    //Récupération du moniteur
    if (isset($palanqueeJson['moniteur']) && isset($palanqueeJson['moniteur']['idWeb'])) {
        $palanquee->setMoniteur(MoniteurDao::getById($palanqueeJson['moniteur']['idWeb']));
    }
    
    //Récupération de l'eventuelle desactivation
    if (isset($palanqueeJson['desactive'])) {
        $palanquee->setDesactive($palanqueeJson['desactive'] == "true" ? true : false);
    }

    //Récupération des plongeurs
    if (isset($palanqueeJson['plongeurs'])) {
        $plongeurs = array();
        for ($k = 0; $k < count($palanqueeJson['plongeurs']); $k++) {
            $plongeurJson = $palanqueeJson['plongeurs'][$k];

            $plongeurJsonToObjectResult = plongeurJsonToObject($plongeurJson, $palanquee->getNumero());
            $erreurRecuperationFiche .= $plongeurJsonToObjectResult['erreurRecuperationFiche'];
            
            $plongeur = $plongeurJsonToObjectResult['plongeur'];
            $plongeur->setIdFicheSecurite($idFicheSecurite);
            $plongeur->setIdPalanque($palanquee->getId());

            $plongeurs[] = $plongeur;

            //Fin de la récupération des plongeurs
        }

        $palanquee->setPlongeurs($plongeurs);
    }

    return ['palanquee' => $palanquee, 'erreurRecuperationFiche' => $erreurRecuperationFiche];
}

/**
 * Transforme un tableau json contenant un plongeur en un objet php Plongeur
 * @param array     $plongeurJson               Le tableau json contenant les informations du plongeur à récupérer
 * @param int       $numeroPalanquee            Le numéro de la palanquée à laquel appartient ce plongeur
 * @return array Un tableau associatif contenant le plongeur dans 'plongeur' et les eventuelles erreurs dans 'erreurRecuperationFiche'
 */
function plongeurJsonToObject($plongeurJson, $numeroPalanquee) {

    $erreurRecuperationFiche = "";
    
    //Récupération de l'id
    if (isset($plongeurJson['idWeb']) && intval(isset($plongeurJson['idWeb'])) > 0) {
        $idPlongeur = intval($plongeurJson['idWeb']);
    } else {
        $idPlongeur = -1;
    }

    //Récupération de la version
    if (isset($plongeurJson['version']) && intval(isset($plongeurJson['version'])) > 0) {
        $versionPlongeur = intval($plongeurJson['version']);
    } else {
        $versionPlongeur = time();
    }

    $plongeur = new Plongeur($idPlongeur, $versionPlongeur);

    //Récupération de l'id distant
    if (isset($plongeurJson['id']) && intval(isset($plongeurJson['id'])) > 0) {
        $plongeur->setIdDistant(intval($plongeurJson['id']));
    } else {
        $erreurRecuperationFiche .= ";id plongeur distant absent pour la palanquee numero " . $numeroPalanquee;
    }
    
    //Récupération du nom
    if (isset($plongeurJson['nom'])) {
        $plongeur->setNom($plongeurJson['nom']);
    } else {
        $erreurRecuperationFiche .= ";nom du plongeur absent pour la palanquee numero " . $numeroPalanquee;
    }

    //Récupération du prénom
    if (isset($plongeurJson['prenom'])) {
        $plongeur->setPrenom($plongeurJson['prenom']);
    } else {
        $erreurRecuperationFiche .= ";prenom du plongeur absent pour la palanquee numero " . $numeroPalanquee;
    }

    //Récupération des aptitudes
    if (isset($plongeurJson['aptitudes'])) {
        for ($l = 0; $l < count($plongeurJson['aptitudes']); $l++) {
            $aptitudeJson = $plongeurJson['aptitudes'][$l];

            //Récupération de l'id
            if (isset($aptitudeJson['idWeb'])) {
                $aptitude = AptitudeDao::getById($aptitudeJson['idWeb']);
            }

            $plongeur->ajouterAptitude($aptitude);
        }
    } else {
        $erreurRecuperationFiche .= ";aptitudes du plongeur absent pour la palanquee numero " . $numeroPalanquee;
    }

    //Récupération du téléphone
    if (isset($plongeurJson['telephone'])) {
        $plongeur->setTelephone($plongeurJson['telephone']);
    } else {
        $erreurRecuperationFiche .= ";telephone du plongeur absent pour la palanquee numero " . $numeroPalanquee;
    }

    //Récupération du téléphone d'urgence
    if (isset($plongeurJson['telephoneUrgence'])) {
        $plongeur->setTelephoneUrgence($plongeurJson['telephoneUrgence']);
    } else {
        $erreurRecuperationFiche .= ";telephoneUrgence du plongeur absent pour la palanquee numero " . $numeroPalanquee;
    }

    //Récupération de la date de naissance
    if (isset($plongeurJson['dateNaissance'])) {
        $plongeur->setDateNaissance($plongeurJson['dateNaissance']);
    } else {
        $erreurRecuperationFiche .= ";dateNaissance du plongeur absent pour la palanquee numero " . $numeroPalanquee;
    }

    //Récupération de la profondeur réalisée
    if (isset($plongeurJson['profondeurRealisee'])) {
        $plongeur->setProfondeurRealisee($plongeurJson['profondeurRealisee']);
    } else {
        $erreurRecuperationFiche .= ";profondeurRealisee du plongeur absent pour la palanquee numero " . $numeroPalanquee;
    }

    //Récupération de la durée réalisée
    if (isset($plongeurJson['dureeRealisee'])) {
        $plongeur->setDureeRealisee($plongeurJson['dureeRealisee']);
    } else {
        $erreurRecuperationFiche .= ";dureeRealisee du plongeur absent pour la palanquee numero " . $numeroPalanquee;
    }
    
    if (isset($plongeurJson['desactive'])) {
        $plongeur->setDesactive($plongeurJson['desactive'] == "true" ? true : false);
    }

    return ['plongeur' => $plongeur, 'erreurRecuperationFiche' => $erreurRecuperationFiche];
}
