<?php

/**
 * Ficher contenant la classe Plongeur
 * @author Raphaël Bideau - 3iL
 * @package Modele
 */

/**
 * Classe Plongeur
 */
class Plongeur implements JsonSerializable {
    ///////////////////////
    // Variables Debut //
    ///////////////////////

    /**
     * Id du plongeur
     * @var int
     */
    private $id;

    /**
     * Id du plongeur sur l'application qui synchronise
     * @var int
     */
    private $idDistant;
    
    /**
     * Id de la palanqué à laquel appartient ce plongeur
     * @var int
     */
    private $id_palanque;

    /**
     * Id de la fiche de sécurité à laquel appartient ce plongeur
     * @var int
     */
    private $id_fiche_securite;

    /**
     * Nom du plongeur
     * @var string
     */
    private $nom;

    /**
     * Prénom du plongeur
     * @var string
     */
    private $prenom;

    /**
     * Tableau des aptitudes de ce plongeur
     * @var array
     */
    private $aptitudes;

    /**
     * Numéro de téléphone du plongeur
     * @var string
     */
    private $telephone;

    /**
     * Numéro de téléphone à contacter d'urgence du plongeur
     * @var string
     */
    private $telephone_urgence;

    /**
     * Date de naissance du plongeur, sous forme de chaine de caractères
     * @var string
     */
    private $date_naissance;

    /**
     * Profondeur qu'a réalisé le plongeur (en mètre)
     * @var float
     */
    private $profondeur_realisee;

    /**
     * Durée de plongé réalisé par le plongeur (en seconde)
     * @var int
     */
    private $duree_realisee;

    /**
     * Version du plongeur, pour la synchronisation. Timestamps de dernière modification
     * @var int
     */
    private $version;

    /**
     * Etat du plongeur. Utilisé pour la suppression.
     * Initialisé à false
     * @var boolean
     */
    private $desactive;
    
    //////////////////////
    // Varibables Fin //
    // Constructeur  //
    //////////////////////

    /**
     * Initialise les valeurs à null, avec éventuellement la version spécifié
     * @param int $id
     * @param int $version Optionnel
     */
    public function Plongeur($id, $version = 0) {
        $this->id = $id;
        $this->idDistant = null;
        $this->id_palanque = null;
        $this->id_fiche_securite = null;
        $this->nom = null;
        $this->prenom = null;
        $this->aptitudes = array();
        $this->telephone = null;
        $this->telephone_urgence = null;
        $this->date_naissance = null;
        $this->duree_realisee = null;
        $this->profondeur_realisee = null;
        $this->version = $version != 0 ? $version : time();
        $this->desactive = false;
    }

    ///////////////////////////////
    // Getter and Setter Debut //
    ///////////////////////////////

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }
    
    /**
     * @return int
     */
    public function getIdDistant() {
        return $this->idDistant;
    }

    /**
     * @param int $idDistant
     */
    public function setIdDistant($idDistant) {
        $this->idDistant = $idDistant;
    }

    /**
     * @return int
     */
    public function getIdPalanque() {
        return $this->id_palanque;
    }

    /**
     * @param int $id_palanque
     */
    public function setIdPalanque($id_palanque) {
        $this->id_palanque = $id_palanque;
    }

    /**
     * @return int
     */
    public function getIdFicheSecurite() {
        return $this->id_fiche_securite;
    }

    /**
     * @param int $id_fiche_securite
     */
    public function setIdFicheSecurite($id_fiche_securite) {
        $this->id_fiche_securite = $id_fiche_securite;
    }

    /**
     * @return string
     */
    public function getNom() {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom($nom) {
        $this->nom = $nom;
    }

    /**
     * @return string
     */
    public function getPrenom() {
        return $this->prenom;
    }

    /**
     * @param string $prenom
     */
    public function setPrenom($prenom) {
        $this->prenom = $prenom;
    }

    /**
     * @return array
     */
    public function getAptitudes() {
        return $this->aptitudes;
    }

    /**
     * @param array $aptitudes
     */
    public function setAptitudes($aptitudes) {
        $this->aptitudes = $aptitudes;
    }

    /**
     * @return string
     */
    public function getTelephone() {
        return $this->telephone;
    }

    /**
     * @param array $telephone
     */
    public function setTelephone($telephone) {
        $this->telephone = $telephone;
    }

    /**
     * @return string
     */
    public function getTelephoneUrgence() {
        return $this->telephone_urgence;
    }

    /**
     * @param array $telephone_urgence
     */
    public function setTelephoneUrgence($telephone_urgence) {
        $this->telephone_urgence = $telephone_urgence;
    }

    /**
     * @return string
     */
    public function getDateNaissance() {
        return $this->date_naissance;
    }

    /**
     * @param string $date_naissance
     */
    public function setDateNaissance($date_naissance) {
        $this->date_naissance = $date_naissance;
    }

    /**
     * @return float
     */
    public function getProfondeurRealisee() {
        return $this->profondeur_realisee;
    }

    /**
     * @param float $profondeur_realisee
     */
    public function setProfondeurRealisee($profondeur_realisee) {
        $this->profondeur_realisee = $profondeur_realisee;
    }

    /**
     * @return int
     */
    public function getDureeRealisee() {
        return $this->duree_realisee;
    }

    /**
     * @param int $duree_realisee
     */
    public function setDureeRealisee($duree_realisee) {
        $this->duree_realisee = $duree_realisee;
    }

    /**
     * @return boolean
     */
    public function getDesactive() {
        return $this->desactive;
    }

    /**
     * @param boolean desactive
     */
    public function setDesactive($desactive) {
        $this->desactive = $desactive;
    }

    /**
     * @return int
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Met à jours la version
     */
    public function updateVersion() {
        $this->version = time();
    }

    /////////////////////////////
    // Getter and Setter Fin //
    // Utils Debut
    /////////////////////////////

    /**
     * Permet d'ajouter une aptitude à un plongeur
     * @param  Aptitude $aptitude 
     */
    public function ajouterAptitude(Aptitude $aptitude) {
        $this->aptitudes[] = $aptitude;
    }

    /**
     * Renvoie une string représentant Template
     * Exemple Id: '8', IdFicheSecurite/IdPalanque: 2/4 Nom prenom: 'Dunam Olivia' Fonction: PLONGEUR' Aptitudes: [ PA-40] Version: 0
     * @return string
     */
    public function __toString() {
        $stringAptitudes = "aucune";
        if ($this->aptitudes != null) {
            $stringAptitudes = "[";
            foreach ($this->aptitudes as $aptitude) {
                if (strlen($stringAptitudes) > 1)
                    $stringAptitudes = $stringAptitudes . " ";
                $stringAptitudes = $stringAptitudes . $aptitude->getLibelleCourt();
            }
            $stringAptitudes = $stringAptitudes . "]";
        }

        $string = "Plongeur " . $this->id . ": " . $this->nom . " " . $this->prenom . " DN: " . $this->date_naissance . "Tels: " . $this->telephone . "/" . $this->telephone_urgence . "<br>";
        $string.= "&nbsp;&nbsp;Aptitudes: " . $stringAptitudes . " IdFicheSecurite/IdPalanque: " . $this->id_fiche_securite . "/" . $this->id_palanque . "<br>";
        $string.= "&nbsp;&nbsp;ProfondeurRealise: " . $this->profondeur_realisee . " DureeRealise: " . $this->duree_realisee . " Version: " . $this->version . "<br>";

        return $string;
    }

    /**
     * Serialize ce plongeur en un array acceptable par json_encode
     * @return array 
     */
    public function jsonSerialize() {
        $arrayAptitudes = array();
        foreach ($this->aptitudes as $aptitude) {
            $arrayAptitudes[] = $aptitude;
        }

        return [
            'id' => $this->idDistant,
            'idWeb' => $this->id,
            //'idPalanquee' => null,
            //'idFicheSecurite' => null,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'telephone' => $this->telephone,
            'telephoneUrgence' => $this->telephone_urgence,
            'dateNaissance' => $this->date_naissance,
            'profondeurRealisee' => $this->profondeur_realisee,
            'dureeRealisee' => $this->duree_realisee,
            'version' => $this->version,
            'aptitudes' => $arrayAptitudes,
            'desactive' => $this->desactive ? 'true' : false
        ];
    }

    /////////////////
    // Utils Fin //
    /////////////////
}

?>