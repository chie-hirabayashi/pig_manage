        <h1 class="insert_title">母豚の詳細情報</h1>

        <div class="in_content">
            <div class="flag">
                <?php if ($status == 1): ?>
                    <?php $flag = 'flag_on' ?>
                <?php else: ?>
                    <?php $flag = 'flag_off' ?>
                <?php endif; ?>
                
                <h2 class="in_title">個体番号 : <?= h($indivi_num) ?></h2>
                <div class="<?= $flag ?>">
                    <i class="fa-solid fa-flag"></i>
                </div>
            </div>

            <ul class="indivi_info">
                <li>年齢 : <?= h($pig_age) ?> 歳</li>
                <li>直近の回転数 : <?= h($rotate) ?> 回</li>
                <li>出産状況 ▼</li>
            </ul>

            <div class="born_infos">
                <ol class="born_info1">
                <?php foreach ($born_info as $one_info): ?>
                    <li>&ensp;<?= h($one_info['born_day']) ?> : <?= h($one_info['born_num']) ?>頭</li>
                <?php endforeach; ?>
                </ol>

                <ul class="born_info2">
                <?php foreach ($rotate_list as $one_rotate): ?>
                    <li>(<?= h($one_rotate) ?> 回)</li>
                <?php endforeach; ?>
                </ul>
            </div>
            <div>
                <form action="" method="POST">
                    <label for="">フラグ切替 : </label>
                    <input type="hidden" name="watch" value=<?= NOT_WATCH ?> />
                    <input type="checkbox" name="watch" value=<?= WATCH ?> />
                    <input type="submit" name="" value='切替' class="flag-btn" />
                </form>
            </div>
        </div>

