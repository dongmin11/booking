@extends('layouts.header')
@section('content')
<link rel="stylesheet" href="css/userLendHistory.css">

<div id="custom-dialog" class="dialog">
    <div class="dialog-content">
        <h2>返却しますか？</h2>
        <p><?php echo '返却日時<br>'; ?></p>
        <input id="dateInput" style="display: block; margin:0 auto 20px; padding:3px" type="date" required value="<?php echo date('Y-m-d') ?>" min="<?php echo date('Y-m-d') ?>" max=<?php echo date('Y-m-d', strtotime('+1 month')); ?>>
        <button style="margin-right: 10px;" id="return-dialog">返却</button>
        <button style="margin-left: 10px;" id="close-dialog">閉じる</button>
    </div>
</div>

<body>
    <div class="container">
        <div class="center">
            <h2 id="h2" style="margin-bottom: 20px;"><a href="/Booking/public/booklist" style="text-decoration: none; color:black">ユーザー貸出履歴</a></h2>

            @csrf
            <div class="item">
                <form action="{{route('userLendHistory')}}" method="get">
                    <select name="selectedUser" id="" style="border:1px solid #dee2e6; padding:3px">
                        @foreach($users as $user)
                        <option value="{{$user}}" <?php if (isset($_GET['selectedUser']) && $user == $_GET['selectedUser']) {
                                                        echo 'selected';
                                                    } ?>>{{$user}}</option>
                        @endforeach
                    </select>
                    <span style="margin-right: 20px; ">表示中</span>
                    <input type="submit" value="変更" style="border:1px solid #dee2e6; padding:3px 5px;">
                </form>
            </div>



            @if(session('message'))
            <div class="alert alert-success" style="width: 500px; margin:0 auto; padding:5px;">
                <p style="text-align: center; margin:0; padding:0;">{{ session('message') }}</p>
            </div>
            @endif

            @isset($message)
            <h2 style="text-align: center;">{{$message}}</h2>
            @endisset
            <table style="margin-top:20px">
                <tr style="border-bottom: 2px solid darkslategray;">
                    <th style="width: 95px;">@sortablelink('bookName','書名')<?php if (isset($_GET["direction"]) && $_GET["sort"] == 'bookName') {
                                                                                if ($_GET["direction"] == 'desc') {
                                                                                    echo '▲';
                                                                                } elseif ($_GET["direction"] == 'asc') {
                                                                                    echo '▼';
                                                                                }
                                                                            } ?></th>
                    <th class="head">@sortablelink('Author','貸出日')<?php if (isset($_GET["direction"]) && $_GET["sort"] == 'Author') {
                                                                        if ($_GET["direction"] == 'desc') {
                                                                            echo '▲';
                                                                        } elseif ($_GET["direction"] == 'asc') {
                                                                            echo '▼';
                                                                        }
                                                                    } ?></th>
                    <th class="head">@sortablelink('Publisher','返却日')<?php if (isset($_GET["direction"]) && $_GET["sort"] == 'Publisher') {
                                                                            if ($_GET["direction"] == 'desc') {
                                                                                echo '▲';
                                                                            } elseif ($_GET["direction"] == 'asc') {
                                                                                echo '▼';
                                                                            }
                                                                        } ?></th>
                    <th class="head" style="width: 60px;">@sortablelink('LendingDate','貸出日数')<?php if (isset($_GET["direction"]) && $_GET["sort"] == 'LendingDate') {
                                                                                                    if ($_GET["direction"] == 'desc') {
                                                                                                        echo '▲';
                                                                                                    } elseif ($_GET["direction"] == 'asc') {
                                                                                                        echo '▼';
                                                                                                    }
                                                                                                } ?></th>
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
                    <td>{{$bookInfo->BookName}}</td>
                    <td>{{$bookInfo->LendingDate}}</td>
                    <td>@if(isset($bookInfo->ReturnDate))
                        {{$bookInfo->ReturnDate}}
                    </td>
                    @else
                    貸出中
                    @endif
                    <td>@if(isset($bookInfo->DaysBetween))
                        {{$bookInfo->DaysBetween}}日
                    </td>
                    @endif
                </tr>
                <?php $num++; ?>
                @endforeach

            </table>
            @if(count($bookInfos)<=10 && count($lendingUser)>10) <div class="allUser">
                    <a href="<?php if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
                                    echo $_SERVER['REQUEST_URI'] . '&count=all';
                                } else {
                                    echo $_SERVER['REQUEST_URI'] . '?count=all';
                                } ?>" class="allUserButton">全件表示</a>
                </div>
                @endif


        </div>
    </div>

    @endsection
    @extends('layouts.footer')