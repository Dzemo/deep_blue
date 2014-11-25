<?php
	require_once("classloader.php");
	require_once("utils/lessmake.php");
	require_once("session.php");
	
	if($connecte){
		$page = "liste_fiches";
		if(isset($_GET['page']) && strlen($_GET['page']) > 0){
			$page = $_GET['page'];
		}
	}
	else{
		$page = "connection";		
	}

	if(isset($_GET['page']) && strcmp($_GET['page'],"initialisation_mot_de_passe") == 0){
		$initialisation_mdp = true;
		$page = "initialisation_mot_de_passe";
	}else{
		$initialisation_mdp = false;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!--
-->
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Deep Blue Project</title>
		<meta name="keywords" content="" />
		<meta name="description" content="" />
		<!-- JQUERY -->
		<script type="text/javascript" language="javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
		<!-- DATATABLES -->
		<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
		<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
		<!-- JQUERY UI -->
		<link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css">
		<script type="text/javascript" language="javascript" src="js/jquery-ui.min.js"></script>
		<script type="text/javascript" language="javascript" src="js/jquery-ui.datepicker-fr.js"></script>
		<!-- SumoSelect -->
		<link rel="stylesheet" type="text/css" href="css/sumoselect.css">
		<script type="text/javascript" language="javascript" src="js/jquery.sumoselect.min.js"></script>
		<!--Bpopup -->
		<script type="text/javascript" language="javascript" src="js/jquery.bpopup.min.js"></script>
		<!-- CSS GENERAL -->
		<link href="css/styles.css" rel="stylesheet" type="text/css" media="all" />
		<!-- JS GENERAL -->		
		<script type="text/javascript" language="javascript" src="js/edition_fiche_securite.js"></script>
	</head>
	<body>
	<div id="wrapper">
	
		<?php 
			///////////////////
			//HEADER DEBUT //
			///////////////////
		?>
		<div id="logo">
			<table width=100% >
				<tr>
					<td>
						<img class="logo-icon" src="images/logo-100.png"><br></td>
					<td>
						<?php
							if($connecte){
								//echo "<span class='welcome'>Bienvenue ".$utilisateur->getPrenom()." ".$utilisateur->getNom()." !</span>";
							}
						?>
					</td>
					<td>
						<table><tr>
						<?php 
							if($connecte && !$initialisation_mdp){
								if($utilisateur->isAdministrateur()){
									?>
										<td><img class="menu-icon" src="images/settings.png"></td><td><a class="header-link" href="index.php?page=administration">Administration</a></td></tr>
									<?php
								}
								?>
									<tr><td><img class="menu-icon" src="images/unlog.png"></td><td><a class="header-link" href="traitement/deconnexion_traitement.php">Deconnexion</a>
								<?php
							}
						?>
						</td></tr></table>
					</td>
				</tr>
			</table>
		</div>
		<?php
			if($connecte && !$initialisation_mdp){
				?>
					<div id='cssmenu'>
						<ul>
							<li class="<?php echo ($page == "liste_fiches" ? "active" : "") ?>">
								<a href='index.php?page=liste_fiches'>
									<table><tr>
											<td><img class="menu-icon" src="images/liste.png"></td>
											<td>Liste des Fiches</td>
										</tr>
									</table>
								</a>
							</li>
							<li class="<?php echo ($page == "creation_fiche" ? "active" : "") ?>">
								<a href='index.php?page=creation_fiche'>
									<table>
										<tr>
											<td><img class="menu-icon" src="images/ajouter.png"></td>
											<td>Ajouter une Fiche</td>
										</tr>
									</table>
								</a>
							</li>
							<li class="<?php echo ($page == "liste_fiches_archivees" ? "active" : "") ?>">
								<a href='index.php?page=liste_fiches_archivees'>
									<table>
										<tr>
											<td><img class="menu-icon" src="images/archive.png"></td>
											<td>Archives</td>
										</tr>
									</table>
								</a>
							</li>
							<li  class="<?php echo ($page == "aide" ? "active" : "") ?> last">
								<a href='index.php?page=aide'>
									<table>
										<tr>
											<td><img class="menu-icon" src="images/aide.png"></td>
											<td>Aide</td>
										</tr>
									</table>
								</a>
							</li>
						</ul>
					</div>
				<?php
			}
		 
			//////////////////////
			// HEADER FIN     //
			//////////////////////
			
		?>
		<div id="content">
			<?php
		
				//////////////////////
				// DEBUT CONTENT  //
				//////////////////////
				
				
				switch($page){
					case "administration":
						if($connecte && $utilisateur->isAdministrateur())
							require_once("page/administration.php");
						else
							require_once("page/liste_fiches.php");
						break;
					case "aide":
						require_once("page/aide.php");
						break;
					case "connection":
						require_once("page/connection.php");
						break;
					case "creation_fiche":
						require_once("page/creation_fiche.php");
						break;
					case "liste_fiches":
						require_once("page/liste_fiches.php");
						break;
					case "liste_fiches_archivees":
						require_once("page/liste_fiches_archivees.php");
						break;
					case "consulter_fiche":
						require_once("page/consulter_fiche.php");
						break;
					case "modification_fiche":
						require_once("page/modification_fiche.php");
						break;
					case "initialisation_mot_de_passe":
						//TODO vérification du droit d'accès à cette page sur la page, peut etre le déplacer ici ?
						require_once("page/initialisation_mot_de_passe.php");
						break;
					default:
						require_once("page/liste_fiches.php");
						break;
				}
				//////////////////
				//FIN CONTENT //
				//////////////////
			?>
		</div>
	<div id="push"></div>
	</div>
	<div id="footer">
		<span>Oxygen © 3iL Rodez - Flavio DEROO | Clément IFRAH | Raphaël BIDEAU</span>
	</div>
	</body>
</html>
