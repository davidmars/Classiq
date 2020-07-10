<?php echo $view->render("./menu")?>
<?php

/** @var \Classiq\Models\Filerecord[] $files */
$files=db()->find("filerecord");
?>
<?php foreach ($files as $file):?>
    <?php if(strlen($file->localPath())>100):?>
        <?php echo $file->localPath()?><br>
        <?php
            $name=basename($file->localPath());
            $path_parts = pathinfo($file->localPath());
            $newName=$path_parts['dirname'];
            $newName.="/".uniqid();
            $newName.=".".$path_parts['extension'];
        //echo "zzz".$name."<br>";
            echo $newName."<br><br>";
            rename($file->localPath(),$newName);
            $file->setFilePath($newName);
            db()->store($file);
        ?>
    <?php endif; ?>

<?php endforeach; ?>
