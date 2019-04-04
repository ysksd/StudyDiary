<body>
<header id="header">
    <div class="site-width header-flex">
        <h1>Study Diary</h1>
        <nav>
            <?php if(empty($_SESSION['user_id'])) { ?>
                <ul id="nav">
                    <li><a href="" id="signup-open">新規登録</a></li>
                    <li><a href="" id="modal-open">ログイン</a></li>
                </ul>
            <?php }else{ ?>
                <ul id="pc-nav">
                    <li><a href="logout.php">ログアウト</a></li>
                    <li><a href="mypage.php">マイページ</a></li>
                    <li><a href="diary.php">日記投稿</a></li>
                    <li><a href="form.php">お問い合わせ</a></li>
                    <li><a href="withdraw.php">退会</a></li>
                </ul>
                <ul id="sp-nav">
                    <li class="ac-menu"><span class="ac-icon">三</span>
                        <ul class="ac-list">
                            <li><a href="logout.php">ログアウト</a></li>
                            <li><a href="mypage.php">マイページ</a></li>
                            <li><a href="diary.php">日記投稿</a></li>
                            <li><a href="form.php">お問い合わせ</a></li>
                            <li><a href="withdraw.php">退会</a></li>
                        </ul>
                    </li>
                </ul>
            <?php } ?>
        </nav>
    </div>
</header>