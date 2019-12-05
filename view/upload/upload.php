<ul class="status">
    <?php foreach($_SESSION['upload-success'] as $success): ?>
        <li><?php echo $success; ?>
    <?php endforeach; ?>
</ul>
<p>
    <?php 
        if(!empty($_SESSION['upload-next'])){
            echo $_SESSION['upload-next'];
        }
    ?>
</p>
<p>
    <?php 
        if(!empty($_SESSION['error'])){
            echo $_SESSION['error'];
        }
    ?>
</p>