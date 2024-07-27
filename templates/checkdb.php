<?php

function checkDb ($conn, $value, $type) {
    $sql = '';
    if ($type == 'music') {

        $sql = "SELECT 'music' as `type`, id,title,artist_name,song,link,plays,downloads,`date`,song_art_work FROM music WHERE title = :val";
    }else if ($type == 'news'){
        $sql = "SELECT 'news' AS type,id,headline,news,`date`,posted_by,link,image FROM news WHERE headline = :val";
    }else {
        return [];
    }
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':val', $value, PDO::PARAM_STR);
    $stmt->execute();
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    return $record;

}