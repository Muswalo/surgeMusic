<?php

function musicTemplate($title, $artist, $views, $downloads, $song, $link, $img, $recordId)
{
?>
    <div class="musicCont">
        <div class="artWorkCont">
            <img src="img/<?php echo $img ?>" alt="<?php echo htmlspecialchars(stripslashes($title)); ?>" loading="lazy">
        </div>
        <div class="musicItems">

            <span class="songTitle"><?php echo htmlspecialchars(stripslashes($title)); ?></span>
            <span class="artistName"><?php echo htmlspecialchars(stripslashes($artist)); ?></span>
            <span class="musicStats">
                <span><i class="fas fa-eye"></i> <span  class="viewValue" id="<?php $viewsId = uniqid('view_'); echo $viewsId ?>"><?php echo htmlspecialchars(stripslashes($views)); ?></span></span>
                <span><i class="fas fa-arrow-down"></i> <span class="downValue" id="<?php $downId = uniqid('down_'); echo $downId ?>"><?php echo htmlspecialchars(stripslashes($downloads)); ?></span></span>
            </span>
            <span class="buttons">
                <a href="audio/<?php echo $song?>" style="display:none;" class="songDownload" download></a>
                <audio src="audio/<?php echo $song?>" class="song"></audio>
                <span><button onclick="download(this ,'<?php echo $downId ?>', '<?php echo $recordId?>')">Download</button></span>
                <span><button onclick="togglePlay(this, '<?php echo $recordId?>', '<?php echo $viewsId ?>')"><i class="fas fa-play"></i></button></span>
                <span><button onclick="share('<?php echo $link?>');"><i class="fas fa-share"></i></button></span>
            </span>
        </div>
    </div>

<?php
}
?>