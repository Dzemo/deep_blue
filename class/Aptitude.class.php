<?php

/**
 * Ficher contenant la classe Aptitude
 * @author Raphaël Bideau - 3iL
 * @package Modele
 */

/**
 * Classe Aptitude représentent les aptitudes que peut avoir un directeur de plongé, un moniteur
 * ou un plongeur 
 */
class Aptitude implements JsonSerializable {
    ///////////////////////
    // Variables Debut //
    ///////////////////////

    /**
     * Id de l'aptitude
     * @var int
     */
    private $id;

    /**
     * Libelle raccourci de l'aptitude
     * Exemple: PA-12
     * @var string
     */
    private $libelle_court;

    /**
     * Libelle complet de l'aptitudes
     * Exemple: Plongé autonome 12m
     * @var string
     */
    private $libelle_long;

    /**
     * Version de l'aptitude, utilisé pour la synchronisation (timestamp)
     * @var int
     */
    private $version;

    /**
     * Profondeur en metre maximale à laquel le plongeur/moniteur ayant cette aptitude peut plongé 
     * dans le cadre d'une plongé technique (sous réserve qu'il dispose aussi des aptitudes
     * supplémentaire éventuellement nécessaire dans le cadre du plongé au nitrox par exemple)
     * @var int
     */
    private $technique_max;

    /**
     * Profondeur en metre maximale à laquel le plongeur/moniteur ayant cette aptitude peut plongé 
     * dans le cadre d'une plongé encadrée (sous réserve qu'il dispose aussi des aptitudes
     * supplémentaire éventuellement nécessaire dans le cadre du plongé au nitrox par exemple)
     * @var int
     */
    private $encadree_max;

    /**
     * Profondeur en metre maximale à laquel le plongeur/moniteur ayant cette aptitude peut plongé 
     * dans le cadre d'une plongé autonome (sous réserve qu'il dispose aussi des aptitudes
     * supplémentaire éventuellement nécessaire dans le cadre du plongé au nitrox par exemple)
     * @var int
     */
    private $autonome_max;

    /**
     * Profondeur en metre maximale à laquel le plongeur/moniteur ayant cette aptitude peut plongé 
     * dans le cadre d'une plongé au nitrox
     * @var int
     */
    private $nitrox_max;

    /**
     * Profondeur en metre maximale à laquel le plongeur ayant cette aptitude peut s'ajouter à une
     * palanqué au dessus de la limite de plongeur (sous réserve qu'il dispose aussi des aptitudes
     * supplémentaire éventuellement nécessaire dans le cadre du plongé au nitrox par exemple)
     * @var string
     */
    private $ajout_max;

    /**
     * Profondeur en metre maximale à laquel le plongeur ayant cette aptitude peut enseigner
     * à une palanqué utilisant de l'air
     * @var string
     */
    private $enseignement_air_max;

    /**
     * Profondeur en metre maximale à laquel le plongeur ayant cette aptitude peut enseigner 
     * à une palanqué utilisant du nitrox (sous réserve qu'il dispose aussi des aptitudes
     * supplémentaire éventuellement nécessaire dans le cadre du plongé au nitrox par exemple)
     * @var string
     */
    private $enseignement_nitrox_max;

    /**
     * Profondeur en metre maximale à laquel le plongeur ayant cette aptitude peut encarder
     * une palanqué (sous réserve qu'il dispose aussi des aptitudes supplémentaire éventuellement 
     * nécessaire dans le cadre du plongé au nitrox par exemple)
     * @var string
     */
    private $encadremement_max;

    /**
     * Indique si cette aptitude peut concerner les plongeurs
     * @var boolean
     */
    private $pour_plongeur;
    
    /**
     * Indique si cette aptitude peut concerner les moniteurs
     * @var boolean
     */
    private $pour_moniteur;
    
    //////////////////////
    // Varibables Fin //
    // Constructeur   //
    //////////////////////
    /**
     * Initialise l'id et éventuellement la version. Les profondeurs maximales sont initialisé à 0 et le reste à null
     * @param int $id
     * @param int $version Optionnel
     */
    public function Aptitude($id, $version = 0) {
        $this->id = $id;
        $this->libelle_long = null;
        $this->libelle_court = null;
        $this->technique_max = 0;
        $this->encadree_max = 0;
        $this->autonome_max = 0;
        $this->nitrox_max = 0;
        $this->ajout_max = 0;
        $this->enseignement_air_max = 0;
        $this->enseignement_nitrox_max = 0;
        $this->encadremement_max = 0;
        $this->version = $version != 0 ? $version : time();
        $this->pour_moniteur = true;
        $this->pour_plongeur = true;
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
     * @return string
     */
    public function getLibelleCourt() {
        return $this->libelle_court;
    }

    /**
     * @param string $libelle_court
     */
    public function setLibelleCourt($libelle_court) {
        $this->libelle_court = $libelle_court;
    }

    /**
     * @return string
     */
    public function getLibelleLong() {
        return $this->libelle_long;
    }

    /**
     * @param string $libelle_long
     */
    public function setLibelleLong($libelle_long) {
        $this->libelle_long = $libelle_long;
    }

    /**
     * @return int
     */
    public function getTechniqueMax() {
        return $this->technique_max;
    }

    /**
     * @param int $technique_max
     */
    public function setTechniqueMax($technique_max) {
        $this->technique_max = $technique_max;
    }

    /**
     * @return int
     */
    public function getEncadreeMax() {
        return $this->encadree_max;
    }

    /**
     * @param int $encadree_max
     */
    public function setEncadreeMax($encadree_max) {
        $this->encadree_max = $encadree_max;
    }

    /**
     * @return int
     */
    public function getAutonomeMax() {
        return $this->autonome_max;
    }

    /**
     * @param int $autonome_max
     */
    public function setAutonomeMax($autonome_max) {
        $this->autonome_max = $autonome_max;
    }

    /**
     * @return int
     */
    public function getNitroxMax() {
        return $this->nitrox_max;
    }

    /**
     * @param int $nitrox_max
     */
    public function setNitroxMax($nitrox_max) {
        $this->nitrox_max = $nitrox_max;
    }

    /**
     * @return int
     */
    public function getAjoutMax() {
        return $this->ajout_max;
    }

    /**
     * @param int $ajout_max
     */
    public function setAjoutMax($ajout_max) {
        $this->ajout_max = $ajout_max;
    }

    /**
     * @return int
     */
    public function getEnseignementAirMax() {
        return $this->enseignement_air_max;
    }

    /**
     * @param int $enseignement_air_max
     */
    public function setEnseignementAirMax($enseignement_air_max) {
        $this->enseignement_air_max = $enseignement_air_max;
    }

    /**
     * @return int
     */
    public function getEnseignementNitroxMax() {
        return $this->enseignement_nitrox_max;
    }

    /**
     * @param int $enseignement_nitrox_max
     */
    public function setEnseignementNitroxMax($enseignement_nitrox_max) {
        $this->enseignement_nitrox_max = $enseignement_nitrox_max;
    }

    /**
     * @return int
     */
    public function getEncadrementMax() {
        return $this->encadremement_max;
    }

    /**
     * @param int $encadremement_max
     */
    public function setEncadrementMax($encadremement_max) {
        $this->encadremement_max = $encadremement_max;
    }

    /**
     * @return int
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Met la version au timestamps courant
     */
    public function updateVersion() {
        $this->version = time();
    }

    /**
     * @return boolean
     */
    public function isPourPlongeur() {
        return $this->pour_plongeur;
    }
    
    /**
     * @param boolean $pour_plongeur
     */
    public function setPourPlongeur($pour_plongeur) {
        $this->pour_plongeur = $pour_plongeur;
    }

    /**
     * @return boolean
     */
    function isPourMoniteur() {
        return $this->pour_moniteur;
    }

     /**
     * @param boolean $pour_moniteur
     */
    public function setPourMoniteur($pour_moniteur) {
        $this->pour_moniteur = $pour_moniteur;
    }

            
    /////////////////////////////
    // Getter and Setter Fin //
    // Utils Debut
    /////////////////////////////
  
    /**
     * Fonction static qui transfrome un tableau d'Aptitude en chaine de caractère contenenant 
     * les ids des aptitudes séparé par un ;
     * Utilisé pour le stockage/récupération d'aptitude en BDD
     * @param  array $aptitudes
     * @return string
     */
    public static function AptitudesArrayToAptitudesString($aptitudes) {
        if ($aptitudes == null || count($aptitudes) == 0) {
            return "";
        }
        $stringAptitudes = "";
        foreach ($aptitudes as $aptitude) {
            if ($stringAptitudes != "") {
                $stringAptitudes .=";";
            }
            $stringAptitudes .= $aptitude->getId();
        }
        return $stringAptitudes;
    }

    /**
     * Fonction qui transforme une chaine de caractère contenant des ids d'aptitudes séparé par
     * un ; en un tableau d'entier contenant les ids de ces aptitudes
     * Utilisé pour le stockage/récupération d'aptitude en BDD
     * @param  string $aptitudesString
     * @return array
     */
    public static function aptitudesStringToAptitudesIdsArray($aptitudesString) {
        return array_map("intval", explode(";", $aptitudesString));
    }

    /**
     * Transforme un tableau d'aptitude en un tableau javascript contenant les id sous forme de chaine de caractere
     * Exemple: ["1","2"]
     * Utilisé dans printFormFicheSecurite.php pour générer les sources dans autocomplete
     * @param  array $aptitudes 
     * @return string
     */
    public static function toJsIdArray($aptitudes) {
        $result = "[";
        if ($aptitudes != null) {
            foreach ($aptitudes as $aptitude) {
                if ($result != "[") {
                    $result .= ", ";
                }
                $result .= "\"" . $aptitude->getId() . "\"";
            }
        }
        return $result . "]";
    }

    /**
     * Transforme un tableau d'aptitude en une chaine de caractere javascripte contenant les libelle des atptitudes
     * Exemple: "PA-20, P-1"
     * Utilisé dans printFormFicheSecurite.php pour générer les sources dans autocomplete
     * @param  array $aptitudes 
     * @return string
     */
    public static function toJsLibelle($aptitudes) {
        $result = "\"";
        if ($aptitudes != null) {
            foreach ($aptitudes as $aptitude) {
                if ($result != "\"") {
                    $result .= ", ";
                }
                $result .= $aptitude->getLibelleCourt();
            }
        }
        return $result . "\"";
    }

    /**
     * Transforme un tableau d'aptitudes en une chaine de caractère contenant le libelle de ces aptitudes séparé par une virgule
     * Exemple  	PA-40, PA-60 	
     * @param  array $aptitudes 
     * @return strin            
     */
    public static function toLibelleString($aptitudes) {
        if ($aptitudes == null || count($aptitudes) == 0) {
            return "";
        }
        $stringAptitudes = "";
        foreach ($aptitudes as $aptitude) {
            if ($stringAptitudes != "") {
                $stringAptitudes .=", ";
            }
            $stringAptitudes .= $aptitude->getLibelleCourt();
        }
        return $stringAptitudes;
    }

    /**
     * Renvoie une string représentant l'Aptitude
     * Exemple Id: 1 LibelleCourt: PA-12 LibelleLong: Plongé autonome 12m Version: 0
     * @return string
     */
    public function __toString() {
        $string = "Aptitude " . $this->id . ": <strong>" . $this->libelle_court . "</strong> (" . $this->libelle_long . ")<br>";
        if ($this->technique_max > 0) {
            $string .= "&nbsp;&nbsp;Technique max = " . $this->technique_max . "<br>";
        }
        if ($this->encadree_max > 0) {
            $string .= "&nbsp;&nbsp;Encadree max = " . $this->encadree_max . "<br>";
        }
        if ($this->autonome_max > 0) {
            $string .= "&nbsp;&nbsp;Autonome max = " . $this->autonome_max . "<br>";
        }

        if ($this->nitrox_max > 0) {
            $string .= "&nbsp;&nbsp;Nitrox max = " . $this->nitrox_max . "<br>";
        }
        if ($this->ajout_max > 0) {
            $string .= "&nbsp;&nbsp;Ajout max = " . $this->ajout_max . "<br>";
        }

        if ($this->enseignement_air_max > 0) {
            $string .= "&nbsp;&nbsp;Enseignement air max = " . $this->enseignement_air_max . "<br>";
        }
        if ($this->enseignement_nitrox_max > 0) {
            $string .= "&nbsp;&nbsp;Enseignement nitrox max = " . $this->enseignement_nitrox_max . "<br>";
        }
        if ($this->encadremement_max > 0) {
            $string .= "&nbsp;&nbsp;Encadrement max = " . $this->encadremement_max . "<br>";
        }
        
        if($this->pour_moniteur && $this->pour_plongeur){
            $string .= "&nbsp;&nbsp;Concerne moniteur et plongeur" ;
        }
        else if($this->pour_moniteur){
            $string .= "&nbsp;&nbsp;Concerne moniteur" ;
        }
        else if($this->pour_plongeur){
            $string .= "&nbsp;&nbsp;Concerne plongeur" ;
        }
        else{
            $string .= "&nbsp;&nbsp;Concerne ni moniteur ni plongeur" ;
        }

        return $string;
    }

    /**
     * Serialize cette aptitude en un array acceptable par json_encode
     * @return array 
     */
    public function jsonSerialize() {
        return [
            'idWeb' => $this->id,
            'libelleCourt' => $this->libelle_court,
            'libelleLong' => $this->libelle_long,
            'techniqueMax' => $this->technique_max,
            'encadreeMax' => $this->encadree_max,
            'autonomeMax' => $this->autonome_max,
            'nitroxMax' => $this->nitrox_max,
            'ajoutMax' => $this->ajout_max,
            'enseignementAirMax' => $this->enseignement_air_max,
            'enseignementNitroxMax' => $this->enseignement_nitrox_max,
            'encadrementMax' => $this->encadremement_max,
            'version' => $this->version,
            'pourMoniteur' => $this->pour_moniteur ? 'true' : 'false',
            'pourPlongeur' => $this->pour_plongeur ? 'true' : 'false'
        ];
    }

    /////////////////
    // Utils Fin //
    /////////////////
}