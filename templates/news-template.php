<?php

function newTemplate($headline, $article, $date, $author, $link, $image){

?>

    <div class="container">
        <div class="article">
            <img src="img/<?php echo $image?>" alt="<?php echo $headline ?>" loading= "lazy">
            <h4 class="headline"><?php echo $headline ?></h4>
            <p class="article-content"><?php echo $article ?></p>
            <div class="meta-info">
                <span><i class="fas fa-calendar-alt"></i> <?php echo date('F j, Y g:i a', strtotime($date)) ?></span>
                <span><i class="fas fa-user"></i> <?php echo $author ?></span>
            </div>
            <div class="btns">
                <button class="btn-read-more" onclick="toggleContent(this)">
                    <i class="fas fa-plus"></i> Read More
                </button>
                <?php $link = "'".$link."'"?>
                <button class="btn-share" onclick="share (<?php echo $link ?>)">
                    <i class="fas fa-share"></i>
                </button>
            </div>
        </div>
        <div class="line"></div>
    </div>

<?php
}
