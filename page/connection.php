<form id="form_connection" method="post" action="traitement/connexion_traitement.php">
	<table>
		<tr>
			<td class="align_right"></td>
			<td class="align_left">
				<span class="connection_erreur">
					<?php
						if(isset($_GET['msg'])){
							if($_GET['msg'] == "connection_champs_manquants"){
								echo "Veuillez remplir tout les champs";
							}
							else if($_GET['msg'] == "connection_identifiants_invalides"){
								echo "Identifiants invalide";
							}
						}
					?>
				</span>
			</td>
		</tr>
		<tr>
			<td class="align_right"><label for="login">Login </label></td>
			<td class="align_left"><input type="text" id="login" name="login" /></td>
		</tr>
		<tr>
			<td class="align_right"><label for="mdp">Mot de passe </label></td>
			<td class="align_left"><input type="password" id="mdp" name="mdp"/></td>
		</tr>
		<tr>
			<td></td>
			<td class="align_left"><button type="submit">Connexion</button></td>
		</tr>
		<tr>
			<td></td>
			<td class="align_left"><button onclick="$('#form_mdp_oublie').toggle()" type="button">Mot de passe oublié ?</button></td>
		</tr>
	</table>	
</form>
<?php
	if(isset($_GET['msg']) && strpos($_GET['msg'], "reinitialisation") !== false)
		$display_reinitialisation = true;
	else
		$display_reinitialisation = false;
?>
<form 	id="form_mdp_oublie" 
		method="post" 
		action="traitement/reinitialiser_mdp_envoie_traitement.php" 
		<?php if(!$display_reinitialisation) echo 'style="display:none"';?>>
	<span class="titre">Reçevoir un lien de réinitialisation de mon mot de passe</span><br>
	<span class="reinitialisation_succes">
		<?php
			if(isset($_GET['msg']) && $_GET['msg'] == "reinitialisation_succes"){
				echo "Un e-mail contenant un lien de réinitialisation de votre mot de passe à été envoyé à votre addresse e-mail";
			}
		?>
	</span>
	<table>
		<tr>
			<td class="align_right"></td>
			<td class="align_left">
				<span class="reinitialisation_erreur">
					<?php
						if(isset($_GET['msg'])){
							if($_GET['msg'] == "reinitialisation_champs_manquants"){
								echo "Veuillez remplir tout les champs";
							}
							else if($_GET['msg'] == "reinitialisation_mail_erreur"){
								echo "Une erreur est survenu lors de l'envoi de l'e-mail";
							}
						}
					?>
				</span>
			</td>
		</tr>
		<tr>
			<td class="align_right"><label for="login">Login </label></td>
			<td class="align_left"><input type="text" id="login" name="login" /></td>
		</tr>
		<tr>
			<td></td>
			<td class="align_left"><button type="submit">Envoyer</button></td>
		</tr>
	</table>	
</form>