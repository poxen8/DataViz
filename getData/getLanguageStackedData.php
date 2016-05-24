﻿<?php

include 'connectdb.php';

$data_text = "date\tenglish\tfrench\titalia\tdeutche\tspanish";

$date = date_create($_POST['dd']);
$maxdate = date_create($_POST['df']);

     $qarrtopics = [];
     $qarrsentiments = [];
    
    // 
    if(intval($_POST['for']) == 1) { $qarrtopics[] = "'For_eu'"; }
    if(intval($_POST['ag']) == 1) { $qarrtopics[] = "'Against_eu'"; }
    if(intval($_POST['none']) == 1) { $qarrtopics[] = "'None'"; }
    
    $qtopics = implode(",", $qarrtopics);
    
    //
    if(intval($_POST['po']) == 1) { $qarrsentiments[] = "'positive'"; }
    if(intval($_POST['neg']) == 1) { $qarrsentiments[] = "'negative'"; }
    if(intval($_POST['neu']) == 1) { $qarrsentiments[] = "'neutral'"; }
    
    $qsentiments = implode(",", $qarrsentiments);

while($date <= $maxdate)
{
    $dater = date_format($date, 'y-M-d');
    $datet = date_format($date, 'Y-m-d');

    $en = $conn->query("SELECT COUNT(*) 
                          FROM TWEET 
                          WHERE TOPIC_TWEET IN (".$qtopics.") 
                          AND DATE_TWEET = '$datet'
                          AND LANGUAGE_TWEET = 'english'
                          AND SENTIMENT_TWEET IN (".$qsentiments.");"
                          )->fetch_row()[0];
                          
    $fr = $conn->query("SELECT COUNT(*) 
                            FROM TWEET 
                            WHERE TOPIC_TWEET IN (".$qtopics.") 
                            AND DATE_TWEET = '$datet'
                            AND LANGUAGE_TWEET = 'french'
                            AND SENTIMENT_TWEET IN (".$qsentiments.");"
                           )->fetch_row()[0];
                           
    $it = $conn->query("SELECT COUNT(*) 
                                FROM TWEET 
                                WHERE TOPIC_TWEET IN (".$qtopics.") 
                                AND DATE_TWEET = '$datet'
                                AND LANGUAGE_TWEET = 'italia'
                                AND SENTIMENT_TWEET IN (".$qsentiments.");"
                                )->fetch_row()[0];
    
    $de = $conn->query("SELECT COUNT(*) 
                                FROM TWEET 
                                WHERE TOPIC_TWEET IN (".$qtopics.") 
                                AND DATE_TWEET = '$datet'
                                AND LANGUAGE_TWEET = 'deutche'
                                AND SENTIMENT_TWEET IN (".$qsentiments.");"
                                )->fetch_row()[0];
                                
    $sp = $conn->query("SELECT COUNT(*) 
                                FROM TWEET 
                                WHERE TOPIC_TWEET IN (".$qtopics.") 
                                AND DATE_TWEET = '$datet'
                                AND LANGUAGE_TWEET = 'spanish'
                                AND SENTIMENT_TWEET IN (".$qsentiments.");"
                                )->fetch_row()[0];

    $total = intval($en)+ intval($fr)+ intval($it)+ intval($de)+ intval($sp);

    $data_text .= "\n".$dater."\t".(100)*intval($en)/intval($total)."\t".(100)*intval($fr)/intval($total)."\t".(100)*intval($it)/intval($total)."\t".(100)*intval($de)/intval($total)."\t".(100)*intval($sp)/intval($total);

    date_add($date, date_interval_create_from_date_string('1 days'));
}

$dfile = fopen(dirname(__DIR__)."/txt_files/datalanguagestacked.txt", "w") or die("Unable to open file!");
fwrite($dfile, $data_text);
fclose($dfile);
$conn->close();

echo 'ready';


?>