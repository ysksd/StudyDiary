<div id="sidevar">
    <form action="" method="get" class="search-area">
        <div class="title">
            <h3 class="content-title"><i class="fas fa-search search"></i>カテゴリー</h3>
        </div>
        <div class="select-box">
            <select>
                <option value="0" <?php if(!empty(getFormData('c_id', true)) == 0){ echo 'selected'; } ?>>選択してください</option>
                <?php foreach($dbCategoryData as $key => $val) { ?>
                <option value="<?php echo $val['id']; ?>" <?php if(getFormData('c_id', true) == $val['id']) { echo 'selected'; } ?> >
                    <?php echo $val['name']; ?>
                </option>
                <?php } ?>
            </select>
        </div>
        <input type="submit" value="検索">
    </form>
</div>
