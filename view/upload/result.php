<sectipon class="part">
    <?php if(!empty($error)): ?>
        <h2 class=part__title>Uploaden is voltooid met fout</h2>
        <p class="info-box error"><?php echo $error; ?>
    <?php else: ?>
        <?php if(!empty($success)): ?>
            <h2 class=part__title>Uploaden is voltooid</h2>
            <p class="info-box success"><?php echo $success; ?>
        <?php endif; ?>
        <p>Er konden <strong> <?php echo count($error_lines); ?> berichten niet rechtstreeks opgeslagen worden </strong> wegens een foutief formaat. Hun inhoud werd automatisch toegevoegd aan het vorige bericht.</p>
        <?php if(count($error_lines) > 0): ?>
            <ul class="list">
            <?php foreach($error_lines as $line): ?>
                <li><?php echo $line; ?></li>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <p>Er werden <strong> <?php echo count($parsed_lines); ?> berichten opgeslagen </strong> in de database.</p>
        <a href="index.php?page=conversation" >Bekijk de resultaten</a>
    <?php endif; ?>
</section>