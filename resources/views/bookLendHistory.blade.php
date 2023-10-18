@extends('layouts.header')
@section('content')
<link rel="stylesheet" href="css/bookLendHistory.css">

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
            <h2 id="h2" style="margin-bottom: 20px;"><a href="/Booking/public/booklist" style="text-decoration: none; color:black">書籍別貸出ランキング</a></h2>

            @csrf
            <div class="item">
                <form action="/Booking/public/bookLendHistory">
                    <select name="indexNum" style="width:50px; border:1px solid #dee2e6;">
                        <option value="10" <?php if (isset($_GET['indexNum']) && $_GET['indexNum'] == 10) {
                                                echo 'selected';
                                            } elseif (!isset($_GET['indexNum'])) {
                                                echo 'selected';
                                            } ?>>10</option>
                        <option value="20" <?php if (isset($_GET['indexNum']) && $_GET['indexNum'] == 20) {
                                                echo 'selected';
                                            } ?>>20</option>
                        <option value="30" <?php if (isset($_GET['indexNum']) && $_GET['indexNum'] == 30) {
                                                echo 'selected';
                                            } ?>>30</option>
                    </select>
                    件表示
                    <input type="submit" value="適応" style="border:1px solid #dee2e6;" id="teki">
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
                    <th style="width: 50px;">順位</th>
                    <th class="head">書名</th>
                    <th class="head">著者名</th>
                    <th class="head">貸出状況</th>
                    <th style="width: 95px;">貸出回数</th>


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
                    <td>{{$num}}</td>
                    <td>{{$bookInfo->BookName}}</td>
                    <td>{{$bookInfo->Author}}</td>
                    <td><?php if (($bookInfo->LendingUserID)) {
                            echo '&nbsp;&nbsp;貸出中<br>&nbsp;（' . $bookInfo->userName . '）';
                        } ?></td>
                    <td>{{$bookInfo->lendCount}}</td>



                </tr>
                <?php $num++; ?>
                @endforeach

            </table>
        </div>
    </div>

    @endsection
    @extends('layouts.footer')