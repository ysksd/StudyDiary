window.addEventListener('DOMContentLoaded', 
function() {
// ログインモーダル
    if(document.querySelector('#modal-open')) {
            document.querySelector('#modal-open').addEventListener('click',function(e) {
            e.preventDefault();
            var modal = document.querySelector('#modal');
            modal.style.display = 'block';

            var modalClose = document.querySelector('#m-close');
            modalClose.addEventListener('click',function() {
                modal.style.display ='none';
            });
        });
    }

// 新規登録モーダル
    if(document.querySelector('#signup-open')) {
        document.querySelector('#signup-open').addEventListener('click',function(e) {
            e.preventDefault();
            var signup = document.querySelector('#signup-modal');
            signup.style.display ='block';

            var signupClose = document.querySelector('#s-close');
            signupClose.addEventListener('click',function() {
                signup.style.display ='none';
            });
        });
    }

    // アコーディオン
    if(document.querySelector('.ac-icon')) {
        document.querySelector('.ac-icon').addEventListener('click',function(){
            var list = document.querySelector('.ac-list');
            list.classList.toggle('ac-active');
        });
    }

    // 文字数カウント
    if(document.querySelector('#count-area')) {
        document.querySelector('#count-area').addEventListener('keyup',function() {
            var count = this.value.length;
            var counter = document.querySelector('.counter');
            counter.innerText = count;
        });
    }

    // 画像プレビュー
    if(document.querySelector('.input-file')) {
        document.querySelector('.input-file').addEventListener('change', function(e) {
            var file = e.target.files[0];
            var fileReader = new FileReader();
            fileReader.onload = function() {
                var dataUrl = this.result;
                var preview = document.querySelector('.prev-img');
                preview.src = dataUrl;
                preview.style.display ='block';
            }
            fileReader.readAsDataURL(file);
        });
    }

},false);

$(function(){
// お気に入り
var $like = $('#js-click-like') || null;
var likeProductId = $like.data('productid') || null;
if(likeProductId !== undefined && likeProductId !== null) {
    $like.on('click',function(){
        var $this = $(this);
        $.ajax({
            type: "POST",
            url : "ajaxLike.php",
            data: {productId : likeProductId}
        }).done(function( data ){
            $this.toggleClass('active');
        }).fail(function( msg ){
            console.log('Ajax Error');
        });
    });
}

// 投稿削除
var $del = $('#js-click-delete') || null;
var delProductId = $del.data('productid') || null;
if(delProductId !== undefined && delProductId !== null) {
    $del.on('click',function(){
        if(confirm('本当に削除しますか？'))  {
            $.ajax({
                type: "POST",
                url : "ajaxDelete.php",
                data: {productId : delProductId}
            }).done(function( data ){
                console.log('投稿を削除しました。');
            }).fail(function( msg ){
                console.log('Ajax Error');
            });
        }else{
            return false;
        }
    });
}

});