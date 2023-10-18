<!DOCTYPE html>
<html>
<head>
    <title>星マークの色変更と表示/非表示</title>
    <style>
        .star {
            font-size: 24px;
            cursor: pointer;
            color: while;
            opacity: 20%;
        }
    </style>
</head>
<body>
    <span class="star" onclick="toggleStarColor(this)">★</span>

    <script>
        const star = document.querySelector('.star'); // クラス名を指定
        let isColored = false; // 色が設定されているかどうかを示すフラグ

        function toggleStarColor(starElement) {
            if (isColored) {
                // 色をクリア
                starElement.style.color = '';
                star.style.opacity = '10%';
                isColored = false;
            } else {
                // 色をランダムに変更
                star.style.color = 'orange';
                star.style.opacity = '100%';
                isColored = true;
            }
        }
    </script>
</body>
</html>
