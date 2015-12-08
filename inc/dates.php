<?php
function get_month_html($year,$month,$monthname)
{ ?>
<li><a href="#" class ="archive-stalker-monthname" data-year="<?=$year?>" data-month="<?=$month?>"><?= $monthname ?></a></li>
<?php }

$prev_year = '';
?>
<div id="archive-stalker-dates-container">
    <a href="#"  id = "archive-stalker-calendar-img-container"><img src ="<?= ARST_PLUGIN_URL_SRC ?>/calendar.png"/></a>
    <ul id="archive-stalker-dates-list">
<?php 
foreach($months as $month){
    $year = $month->year;
   //if year has changed, surround month html with year html
    if($prev_year !== $year){
    if($prev_year != ''){ ?></ul></li><?php } ?>
    <li class="archive-stalker-year-ul">
        <a href="#" class="archive-stalker-year"><?= $year ?></a>
        <ul class="archive-stalker-month-container" style="display:none;">
            <?php get_month_html($year, $month->month, $month->monthname);    
            $prev_year = $year; 
    } else get_month_html($year, $month->month, $month->monthname);
}?>
    </ul>
</div>