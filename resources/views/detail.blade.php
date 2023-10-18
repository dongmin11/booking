@extends('layouts.header')
@section('content')

<link rel="stylesheet" href="css/add.css">

<body>
    <div class="container">
    <h2>書籍詳細</h2>
    <form action="{{route('edit')}}" method="get">
        @csrf
        <div>
            <p>書名</p>
            <input type="text" name="BookName" style="background-color:#e9ecef" readonly value="{{$bookInfo->BookName}}">
        </div>

        <div>
            <p>著者</p>
            <input type="text" name="Author" name="Author" style="background-color:#e9ecef" readonly value="{{$bookInfo->Author}}">
        </div>

        <div>
            <p>出版社</p>
            <input type="text" name="Publisher" name="Publisher" style="background-color:#e9ecef" readonly value="{{$bookInfo->Publisher}}">
        </div>

        <div>
            <p>出版日</p>
            <input type="date" name="PublicationDate" style="background-color:#e9ecef" readonly value="{{$bookInfo->PublicationDate}}">
        </div>

        <div>
            <p>備考</p>
            <textarea name="Note" cols="30" rows="10" maxlength="255" style="background-color:#e9ecef; margin-bottom: 20px;" readonly><?php echo $bookInfo->Note; ?></textarea>
        </div>

        <div>
            <p>貸出者</p>
            <input type="text" name="lendingUser" style="background-color:#e9ecef; margin-bottom: 20px;" readonly value="<?php if(isset($lendingUser->name)){echo $lendingUser->name;}else{echo '';} ?>">
        </div>

        <div>
            <p>返却予定日</p>
            <input type="text" name="PublicationDate" style="background-color:#e9ecef; margin-bottom: 20px;" readonly value="<?php if(isset($lending->ReturnExpectedDate)){echo $lending->ReturnExpectedDate;} ?>">
        </div>

        <table>
            <tr>
                <th style="width: 100px;">貸出日</th>
                <th style="width: 100px;">返却日</th>
                <th style="width: 100px;">貸出者</th>
                <th>コメント</th>
            </tr>
            <h4 style="font-weight: bold;">貸出履歴</h4>
            @foreach($lendingInfos as $lendingInfo)
            @if($loop->last)
            <tr style="<?php if($num % 2 == 0){echo 'background-color:#e9ecef;';}
            echo 'border-bottom: 2px solid darkslategray;';
            ?>">
            @else
            <tr <?php if($num % 2 == 0){echo "style=background-color:#e9ecef;";}?>>
            @endif
                <td>{{$lendingInfo->LendingDate}}</td>
                <td><?php if (isset($lendingInfo->ReturnDate)) echo $lendingInfo->ReturnDate; ?></td>
                <td><?php
                echo $lendUserAll[$num]->name;
                $num++;
                ?></td>
                <td>{{$lendingInfo->Note}}</td>
            </tr>
            @endforeach

        </table>

        <input type="hidden" value="{{$_GET['id']}}" name="id">
        <input class="button" type="submit" value="書籍編集" style="margin-right:30px;  background-color:aquamarine; box-shadow:10px 10px 20px; ">
        <a href="/Booking/public/booklist?{{$_SESSION['query_string']}}" class="cancelButton" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;戻る&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
    </form>
    </div>
    


</body>

@endsection
@extends('layouts.footer')