<?php
	//Vérification du droit d'acceder à la page
	
	//soit un utilisateur est connecté -> premiere connection, il faut bien vérifier que mdp == login
	//soit par un lien de réinitialisation -> vérifier que le lien est valide
	//si réinitalisation, mettre dans la session le login de l'utilisateur initialisation_mdp_login
	//sinon on quitte
	$acces_legitime = false;

	//soit un utilisateur est connecté -> premiere connection, il faut bien vérifier que mdp == login
	if($connecte && $utilisateur->getMotDePasse() == md5($utilisateur->getLogin())){
		$reinitialisation = false;
		$acces_legitime = true;
	}
	//soit par un lien de réinitialisation -> vérifier que le lien est valide
	//si réinitalisation, mettre dans la session le login de l'utilisateur initialisation_mdp_login
	else if(isset($_GET['jeton'])){

		$crypter = new Crypter();
		$jeton = $crypter->decrypte($_GET['jeton']);
		$jetonArray = explode('+', $jeton);
		
		if(count($jetonArray) == 2){
			$reinitialisation = true;
			$acces_legitime = true;

			if(intval($jetonArray[1]) >= time() - $GLOBALS['duree_validite_lien_reinitialisation']){
				$jeton_expire = false;
				$_SESSION['initialisation_mdp_login'] = $jetonArray[0];
			}
			else{
				$jeton_expire = true;
			}
		}
	}

	//Si pas d'accès légitime on quitte
	if(!$acces_legitime){
		header('Location: '.$GLOBALS['dns'].'index.php');
		die();
	}
	else if(!$reinitialisation || !$jeton_expire){
		//Soit c'est pas une réinitialisation, soit faut que le jeton soit valide
		?>
			<div class="titre"><?php echo ($reinitialisation ? "Réi" : "I" );?>nitialisation de votre mot de passe</div>
			<form id="form_initialisation_mdp" method="post" action="traitement/initialisation_mdp_traitement.php">
				<intput type="hidden" value="<?php echo $reinitialisation;?>" name="reinitialisation">

				<?php
					if(!$reinitialisation){
						?>
							<p>
								Lors de votre première connexion, il vous est recommandé de mettre à jours votre mot de passe
							</p>
						<?php
					}
					else{
						?>
							<p>
								Vous avez demandé à reçevoir un lien de réinitialisation de votre mot de passe
							</p>
						<?php
					}
				?>
				<table>
					<tr>
						<td class="align_right"></td>
						<td class="align_left">
							<span class="initialisation_mdp_erreur">
								<?php
									if(isset($_GET['msg'])){
										if($_GET['msg'] == "initialisation_champs_manquants"){
											echo "Veuillez remplir tout les champs";
										}
										else if($_GET['msg'] == "initialisation_champs_differents"){
											echo "Les deux mots de passe saisis ne sont pas identiques";
										}
										else if($_GET['msg'] == "initialisation_mdp_defaut"){
											echo "Veuillez saisir un mot de passe différent du mot de passe par défaut";
										}
									}
								?>
							</span>
						</td>
					</tr>
					<tr>
						<td class="align_right"><label for="mdp">Mot de passe </label></td>
						<td class="align_left"><input type="password" id="mdp" name="mdp"/></td>
					</tr>
					<tr>
						<td class="align_right"><label for="mdp">Confirmation </label></td>
						<td class="align_left"><input type="password" id="mdp_confirme" name="mdp_confirme"/></td>
					</tr>
					<tr>
						<td></td>
						<td class="align_left"><button type="submit">Envoyer</button></td>
					</tr>
				</table>	
			</form>
		<?php
	}
	else{
		?>
			<div id="form_initialisation_mdp">
				<p>
					Ce lien de réinitialisation de mot de passe a expiré.
				</p>
			</div>
		<?php
	}
?>