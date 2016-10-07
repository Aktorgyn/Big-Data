<?php

/* @var $this yii\web\View */

use kartik\export\ExportMenu;

?>
<div class="site-about">
    <h1>Comment Parser</h1>

    <form action="" method="get">
        Enter Number of NEWS that you will parse from Nur.kz
        <br><br>
        <input style="width: 300px;" type="number" name="news" max="100" placeholder="Try 4 news at the start" required>
        <br><br>
        <button class="btn btn-success" type="submit">PAAARSE</button>
        <br>
    </form>
    <p>
        Here is about <span class="h2"><?=count($allComments) ?></span> comments. Choose Your Way To Export. Good Luck ;)
    </p>

    <?php
    echo '<ul>' . ExportMenu::widget([
            'dataProvider' => $comments,
            'columns' => [
                ['class' => 'kartik\grid\SerialColumn'],
                'news_title',
                'author',
                'body',
                'created_at',
                'rate_negative',
                'rate_positive',
            ],
            'fontAwesome' => true,
            'asDropdown' => false,
            'exportConfig' => [
                ExportMenu::FORMAT_PDF => false,
                ExportMenu::FORMAT_EXCEL_X => false
            ]
        ]) . '</ul>';




    ?>
    <br>
</div>
