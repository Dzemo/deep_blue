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
			<div class="panel-question">
				<table class="panel-text">
					<tr>
						<td class="help-letter-column">Q.<?php echo $questions->getId();?></td>
						<td class="help-question help-text-column"><?php echo $questions->getQuestion();?></td>
					</tr>
				</table>
			</div>
			<div class="panel-reponse">
				<table class="panel-text">
					<tr>
						<td class="help-letter-column">R.<?php echo $questions->getId();?></td>
						<td class="help-answer help-text-column"><?php echo $questions->getReponse();?></td>
					</tr>
				</table>
			</div>
		<?php
	}
?>