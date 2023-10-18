/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});

import ExampleComponent from './components/ExampleComponent.vue';
app.component('example-component', ExampleComponent);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

app.mount('#app');

const h2 = document.getElementById('h2');

const bookList = document.getElementById('bookList');

const bookAdd = document.getElementById('bookAdd')

//foreach文内でレコードの数分生成された返却ボタンの要素
// const returnButtons = document.querySelectorAll('.returnButton');

//ダイアログ要素
// const customDialog = document.getElementById('custom-dialog');

const bookLend = document.getElementById('bookLend');

const userLend = document.getElementById('userLend');


//選択されている画面に合わせてヘッダーを強調
function removeOpacity() {

    if (h2.textContent == bookList.textContent) {
        bookList.style.opacity = '1';
    }

    if (h2.textContent == bookAdd.textContent) {
        bookAdd.style.opacity = 1;
    }

    if (h2.textContent == userLend.textContent) {
        userLend.style.opacity = 1;
    }

    if (h2.textContent == bookLend.textContent) {
        bookLend.style.opacity = 1;
    }
}
removeOpacity();

const returnButtons = document.querySelectorAll('.returnButton');
const dialog = document.getElementById('custom-dialog');
const overlay = document.getElementById('overlay');

returnButtons.forEach(Button => {
    Button.addEventListener('click', () => {
        returnButtons.forEach(button => {
            button.disabled = true;
        })

        overlay.style.display = 'block';
        setTimeout(() => {
            dialog.style.opacity = 1;
        }, 100);
        dialog.style.display = 'block';


        const return_dialog = document.getElementById('return-dialog');
        const close_dialog = document.getElementById('close-dialog');
        const bookID = Button.getAttribute('data-book-id');

        return_dialog.addEventListener('click', () => {
            const dateInpupt = document.getElementById('dateInput');
            window.location.href = "/Booking/public/return?id=" + bookID + "&returnDate=" + dateInpupt.value;
        });

        close_dialog.addEventListener('click', () => {
            overlay.style.display = 'none';
            dialog.style.opacity = '0';
            setTimeout(() => {
                dialog.style.display = 'none';
                returnButtons.forEach(button => {
                    button.disabled = false;
                })
                Button.disabled = false;
            }, 1000);
        });
    });
});



//更新、追加ごメッセージのフェードアウト処理
document.addEventListener("DOMContentLoaded", function () {
    setTimeout(() => {
        var alertDiv = document.querySelector('.alert.alert-success');
        if (alertDiv) {
            setTimeout(function () {
                alertDiv.style.transition = "opacity 1s"; // フェードアウトのアニメーション
                alertDiv.style.opacity = "0";
                setTimeout(function () {
                    alertDiv.style.display = "none";
                }, 500); // フェードアウトが完了したら非表示にする
            }, 1000); // 5秒後にフェードアウトを開始
        }
    })
});

const stars = document.querySelectorAll('.star'); // クラス名を指定

stars.forEach(star => {
    const userID = star.getAttribute('userID');
    const bookID = star.getAttribute('bookID');

    star.addEventListener('click', () => {
        const isColored = star.getAttribute('data-isColored') === 'true';
        console.log(bookID);
        if (isColored) {
            // 色をクリア
            star.style.color = '';
            star.style.opacity = '10%';
            star.setAttribute('data-isColored', 'false');
            updateFavorite(false, userID, bookID);
        } else {
            // 色をランダムに変更
            star.style.color = 'orange';
            star.style.opacity = '100%';
            star.setAttribute('data-isColored', 'true');
            updateFavorite(true, userID, bookID);
        }
    });
});


function updateFavorite(isColored, userID, bookID) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update-favorite", true);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    xhr.setRequestHeader("X-CSRF-TOKEN", csrfToken);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log(xhr.responseText); // レスポンスをコンソールに出力
        } else {
            console.error("リクエストエラー:" + xhr.status)
        }
    }
    const requestData = "isColored=" + isColored.toString() + "&userID=" + userID + "&bookID=" + bookID;
    xhr.send(requestData);
}
