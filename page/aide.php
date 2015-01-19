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
    <div id="<?php echo $questions->getId(); ?>" class="panel-question">
        <table class="panel-text">
            <tr>
                <td class="help-letter-column">Q.<?php echo $questions->getId(); ?></td>
                <td class="help-question help-text-column"><?php echo $questions->getQuestion(); ?></td>
            </tr>
            <tr>
                <td></td>
                <td class="help-tag">tags : <?php echo $questions->getTag(); ?></td>
            </tr>
        </table>
    </div>
    <div class="panel-reponse">
        <table class="panel-text">
            <tr>
                <td class="help-letter-column">R.<?php echo $questions->getId(); ?></td>
                <td class="help-answer help-text-column"><?php echo $questions->getReponse(); ?></td>
            </tr>
            <tr>
                <td></td>
                <td class="help-seealso">
                    <?php if(count($questions->getVoirAussi()) > 0) { ?>                                
                        Voir aussi :
                        <?php foreach ($questions->getVoirAussi() as $seealso) { ?>
                            <a href="#<?php echo $seealso->getId(); ?>"><?php echo $seealso->getQuestion(); ?></a>
                        <?php } 
                    } ?>
                </td>
            </tr>
        </table>
    </div>
    <?php
}
?>