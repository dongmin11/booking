@extends('layouts.header')
@section('content')
<link rel="stylesheet" href="css/booklist.css">

<div id="custom-dialog" class="dialog">
    <div class="dialog-content">
        <h2>返却しますか？</h2>
        <p><?php echo '返却日時<br>'; ?></p>
        <input id="dateInput" style="display: block; margin:0 auto 20px; padding:3px" type="date" required value="<?php echo date('Y-m-d') ?>" min="<?php echo date('Y-m-d') ?>" max=<?php echo date('Y-m-d', strtotime('+1 month')); ?>>
        <button style="margin-right: 10px;" id="return-dialog">返却</button>
        <button style="margin-left: 10px;" id="close-dialog">閉じる</button>
    </div>
</div>
<meta name="csrf-token" content="{{ csrf_token() }}">

<body>
    <div class="container">
        <div id="overlay"></div>
        <div class="center">
            <h2 id="h2" style="margin-bottom: 20px;"><a href="/Booking/public/booklist" style="text-decoration: none; color:black">書籍一覧</a></h2>

            @csrf
            <div class="item">
                <form action="/Booking/public/booklist" method="get">
                    <p class="right">検索:<input name="key" style="border: 1px solid #dee2e6;" type="text" value=<?php if (isset($_GET['key'])) {
                                                                                                                    echo $_GET['key'];
                                                                                                                } ?>></p>
                </form>

                @if(session('message'))
                <div id="success" class="alert alert-success" style="margin-left:500px; padding:10px; display:inline;">
                    {{ session('message') }}
                </div>
                @endif
                <form action="/Booking/public/booklist">
                    @isset($_GET['key'])
                    <input type="hidden" name="key" value="{{$_GET['key']}}">
                    @endisset
                    <select name="indexNum" style="width:50px; border:1px solid #dee2e6;">
                        <option value="5" <?php if (isset($_GET['indexNum']) && $_GET['indexNum'] == 5) {
                                                echo 'selected';
                                            } ?>>5</option>
                        <option value="10" <?php if (isset($_GET['indexNum']) && $_GET['indexNum'] == 10) {
                                                echo 'selected';
                                            } elseif (!isset($_GET['indexNum'])) {
                                                echo 'selected';
                                            } ?>>10</option>
                        <option value="15" <?php if (isset($_GET['indexNum']) && $_GET['indexNum'] == 15) {
                                                echo 'selected';
                                            } ?>>15</option>
                        <option value="20" <?php if (isset($_GET['indexNum']) && $_GET['indexNum'] == 20) {
                                                echo 'selected';
                                            } ?>>20</option>
                        <option value="30" <?php if (isset($_GET['indexNum']) && $_GET['indexNum'] == 30) {
                                                echo 'selected';
                                            } ?>>30</option>
                        <option value="50" <?php if (isset($_GET['indexNum']) && $_GET['indexNum'] == 50) {
                                                echo 'selected';
                                            } ?>>50</option>
                    </select>
                    件表示
                    <input type="submit" value="適応" style="border:1px solid #dee2e6;" id="teki">
                </form>
            </div>





            @isset($message)
            <h2 style="text-align: center;">{{$message}}</h2>
            @endisset
            <table style="margin-top:20px">
                <tr style="border-bottom: 2px solid darkslategray;">
                    <th class="favorite">

                    </th>
                    <th class="head">@sortablelink('BookName','書名')<?php if (isset($_GET["direction"]) && $_GET["sort"] == 'BookName') {
                                                                        if ($_GET["direction"] == 'desc') {
                                                                            echo '▲';
                                                                        } elseif ($_GET["direction"] == 'asc') {
                                                                            echo '▼';
                                                                        }
                                                                    } ?></th>
                    <th class="head">@sortablelink('Author','著者名')<?php if (isset($_GET["direction"]) && $_GET["sort"] == 'Author') {
                                                                        if ($_GET["direction"] == 'desc') {
                                                                            echo '▲';
                                                                        } elseif ($_GET["direction"] == 'asc') {
                                                                            echo '▼';
                                                                        }
                                                                    } ?></th>
                    <th class="head">@sortablelink('Publisher','出版社')<?php if (isset($_GET["direction"]) && $_GET["sort"] == 'Publisher') {
                                                                            if ($_GET["direction"] == 'desc') {
                                                                                echo '▲';
                                                                            } elseif ($_GET["direction"] == 'asc') {
                                                                                echo '▼';
                                                                            }
                                                                        } ?></th>
                    <th class="head">@sortablelink('PublicationDate','出版日')<?php if (isset($_GET["direction"]) && $_GET["sort"] == 'PublicationDate') {
                                                                                if ($_GET["direction"] == 'desc') {
                                                                                    echo '▲';
                                                                                } elseif ($_GET["direction"] == 'asc') {
                                                                                    echo '▼';
                                                                                }
                                                                            } ?></th>
                    <th class="head">@sortablelink('PurchaseDate','購入日')<?php if (isset($_GET["direction"]) && $_GET["sort"] == 'PurchaseDate') {
                                                                            if ($_GET["direction"] == 'desc') {
                                                                                echo '▲';
                                                                            } elseif ($_GET["direction"] == 'asc') {
                                                                                echo '▼';
                                                                            }
                                                                        } ?></th>
                    <th style="width: 95px;">@sortablelink('ReturnDate','貸出状態')<?php if (isset($_GET["direction"]) && $_GET["sort"] == 'ReturnDate') {
                                                                                    if ($_GET["direction"] == 'desc') {
                                                                                        echo '▲';
                                                                                    } elseif ($_GET["direction"] == 'asc') {
                                                                                        echo '▼';
                                                                                    }
                                                                                } ?></th>
                    <th style="width: 95px;">書籍編集</th>
                    <th style="width: 95px;">貸出</th>
                    <th style="width: 95px;">返却</th>
                    <th style="width: 95px;">詳細</th>
                </tr>

                @foreach($bookInfos as $bookInfo)
                @if($loop->last)
                <tr style="<?php if ($num % 2 == 1) {
                                echo 'background-color:#e9ecef;';
                            }
                            echo 'border-bottom: 2px solid darkslategray;'; ?>">
                    @else
                <tr <?php if ($num % 2 == 1) {
                        echo "style=background-color:#e9ecef;";
                    } ?>>
                    @endif
                    <td class="favorite"><span userID="{{$userID}}" bookID="{{$bookInfo->bookID}}" class="star" <?php if (isset($bookInfo->favoriteFlg)) {
                                                                                                                if ($bookInfo->favoriteFlg == 1) {
                                                                                                                    echo "style='color:orange; opacity:100%;'";
                                                                                                                }
                                                                                                            } ?>>★</span></td>
                    <td>{{$bookInfo->BookName}}</td>
                    <td>{{$bookInfo->Author}}</td>
                    <td>{{$bookInfo->Publisher}}</td>
                    <td><?php $param = $bookInfo->PublicationDate;
                        $param = explode('-', $param);
                        echo $param[0] . '年' . $param[1] . '月'; ?></td>
                    <td><?php $param = $bookInfo->PurchaseDate;
                        $param = explode('-', $param);
                        echo $param[0] . '年' . $param[1] . '月' . $param[2] . '日'; ?></td>
                    <td><?php if (!($bookInfo->ReturnDate) && ($bookInfo->LendingUserID)) {
                            echo '&nbsp;&nbsp;貸出中<br>&nbsp;（' . $bookInfo->latestLendingUser . '）';
                        } ?></td>
                    <td><?php echo "<a href='/Booking/public/edit?id={$bookInfo->bookID}' style='text-decoration: none; color:black;'><button style='border-color:#dee2e6;'>書籍編集</button></a>"; ?></td>
                    <td style="width: 98px;">
                        <?php if (!($bookInfo->ReturnDate) && ($bookInfo->LendingUserID)) {
                            echo "<a href='/Booking/public/editLendInfo?id={$bookInfo->bookID}' style='text-decoration: none; color:black;'><button style='border-color:#dee2e6;'>貸出編集</button></a>";
                        } else {
                            echo "<a href='/Booking/public/lendInfo?id={$bookInfo->bookID}' style='text-decoration: none; color:black;'><button style='border-color:#dee2e6; width:72px; '>貸出</button></a>";
                        } ?>
                    <td style="width: 72px;"> <?php if (!($bookInfo->ReturnDate) && ($bookInfo->LendingUserID)) {
                                                    echo "<button  class='returnButton' data-book-id='{$bookInfo->bookID}' style='border-color:#dee2e6; width:72px;'>返却</button>";
                                                } ?></td>
                    <input type="hidden" value="{{$bookInfo->bookID}}" id="bookID">
                    <td style="width: 70px;"><a href='/Booking/public/detail?id={{$bookInfo->bookID}}' style='text-decoration: none; color:black;'><button value="aaa" name="a" style="margin: 0; border-color:#dee2e6; width:72px">詳細</button></a></td>

                </tr>
                <?php $num++; ?>
                @endforeach

                <?php $createThNum =  $now * $indexNum - $count; ?>
                @if($createThNum <=$indexNum && $createThNum>=0 && $indexNum - $createThNum < 5) @for($i=0;$i<$createThNum;$i++) <tr <?php if ($num % 2 == 1) {
                                                                                                                                            echo "style=background-color:#e9ecef;";
                                                                                                                                        } ?>>

                        </tr>
                        <?php $num++; ?>
                        @endfor
                        @endif


            </table>
        </div>


        <!-- ページネーション処理 -->
        @if(count($bookInfos) > 0)
        {{$count}}件中
        {{$indexNum*($now-1)+1}}件から

        @if(($now == 1) && ($count - $indexNum * ($now) > 0))

        {{$indexNum}}件まで表示

        @elseif($count - $indexNum * ($now) > 0)

        {{$now * $indexNum}}件まで表示

        @else

        {{$count}}件まで表示

        @endif
        @endif


    </div>

    @isset($max_page)

    <div style="text-align: center;">
        <!-- ページ数によって表示の仕方を変える
        検索結果が0件だった場合                                                                                                                            -->
        @if($max_page > 0)
        <a class="page_link" href="{{$url}}1">＜＜&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
        @if($now > 1)
        <a class="page_link" href="{{$url.$now-1}}">＜&nbsp;&nbsp;&nbsp;</a>
        @endif

        @for($i = max(1, $now - 1); $i <= min($max_page, $now + 1); $i++) @if($i==$now) <p class="page">{{$i}}</p>
            @else
            <a class="page_link" href="{{$url.$i}}">{{$i}}</a>
            @endif
            @endfor

            @if($now < $max_page) <a class="page_link" href="{{$url.$now+1}}">&nbsp;&nbsp;&nbsp;＞</a>
                @endif
                <a class="page_link" href="{{$url.$max_page}}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;＞＞</a>
                @endif

                @endisset
                @endsection
                @extends('layouts.footer')