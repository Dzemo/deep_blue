<?php
   /**
     * Ficher contenant la classe Crypter
     * @author Raphaël Bideau - 3iL
     * @package Dao
     */
    
    /**
     * Classe permettent de crypter et decrypter des messages textes. Utilisé pour la création de token de réinitialisation de mot de passe
     */
    class Crypter {
        private $securekey, $iv;
        function Crypter() {
            $this->securekey = hash('sha256',"ultimate cards kernite rebozo spelling fuzee",TRUE);
            $this->iv = mcrypt_create_iv(32);
        }
        /**
         * Crypte un message
         * @param  string $message le message décrypté
         * @return string          le message crypté
         */
        function crypte($message) {
            return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->securekey, $message, MCRYPT_MODE_ECB, $this->iv));
        }
        /**
         * Décrypte un message
         * @param  string $message le message crypté
         * @return string          le message décrypté
         */
        function decrypte($message) {
            return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->securekey, base64_decode($message), MCRYPT_MODE_ECB, $this->iv));
        }
    }
?>