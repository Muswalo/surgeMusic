<?php
function topSongBanner($title, $artist, $plays, $song,$img, $recordId, $background_image)
{
    $viewsId = uniqid();
?>
    <div style="width: 100%; height: 200px; border-radius: 10px; background: url('../img/<?php echo $background_image ?>') no-repeat center center/cover; display: flex; flex-direction: column; justify-content: space-between; padding: 20px; color: white; position: relative; overflow: hidden;">
        <div style="content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(56, 49, 49, 0.5);"></div>
        <a style="text-decoration: none; color: white; position: relative; z-index: 1;">
            <div>
                <h1 style="font-size: 24px; margin-bottom: 10px; text-transform: uppercase; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?php echo htmlspecialchars(stripslashes($title)); ?> 🔥
                </h1>
                <p style="font-size: 16px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?php echo htmlspecialchars(stripslashes($artist)); ?>
                </p>
            </div>
        </a>
        <div style="display: flex; align-items: center; justify-content: space-between; position: relative; z-index: 1;">
            <span class="buttons" style=" margin-left: -5px">
                <audio src="audio/<?php echo $song ?>" class="song"></audio>

                <button onclick="togglePlay(this, '<?php echo $recordId ?>', '<?php echo $viewsId ?>', {songTitle: '<?php echo addslashes($title) ?>', artistName: '<?php echo addslashes($artist) ?>', album: 'surge music', artwork: '<?php echo $img ?>'})" style="background-color: white; border: none; border-radius: 50%; padding: 10px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                    <i class="fas fa-play" style="font-size: 18px; color: black;"></i>
                </button>
            </span>
            <span style="font-size: 16px;">
                <i class="fas fa-music" style="margin-right: 5px;"></i>
                <span id="<?php echo $viewsId ?>"><?php echo htmlspecialchars(stripslashes($plays)); ?></span>
            </span>
        </div>
    </div>
<?php
}
?>