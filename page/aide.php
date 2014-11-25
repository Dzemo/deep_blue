<?php
// Creation d'un panel clickable qui fait apparaître la réponse à la question
// Autant de panel que de questions
// Les vidéos de présentations seront sur le panel de tout en haut
?>
<?php 
	// Récupération de la fiche de sécurité en cours
	$aide = AideDao::getAll();
	foreach ($aide as $questions) {
		?>
			<div class="panel-form">
				<?php echo $questions->getQuestion();?>
			</div>
			<div class="panel-reponse">
				<?php echo $questions->getReponse();?>
			</div>
		<?php
	}
?>