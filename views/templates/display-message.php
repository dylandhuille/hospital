<!-- Affichage d'un message d'erreur personnalis√© -->
<?php if(isset($message)) {?>
    
    <div class="alert alert-warning" role="alert">
        <?= nl2br($message)?>
    </div>

<?php } ?>