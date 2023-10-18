@extends('layouts.header')
@section('content')

<link rel="stylesheet" href="css/add.css">

<body>
    <div class="container">
    <h2>貸出情報編集</h2>
    <form action="{{route('updateLendInfo')}}" method="post">
    @csrf
        <div>
            <p>書名</p>
            <input type="text" name="BookName" required maxlength="100" style="background-color:#e9ecef" readonly value="{{$bookInfo->BookName}}">
        </div>

        <div>
            <p>著者</p>
            <input type="text" name="Author" name="Author" required maxlength="100" style="background-color:#e9ecef" readonly value="{{$bookInfo->Author}}">
        </div>

        <div>
            <p>出版社</p>
            <input type="text" name="Publisher" name="Publisher" required maxlength="100" style="background-color:#e9ecef" readonly value="{{$bookInfo->Publisher}}">
        </div>

        <div>
            <p>出版日</p>
            <input type="date" name="PublicationDate" style="background-color:#e9ecef" required readonly value="{{$bookInfo->PublicationDate}}">
        </div>

        <div>
            <p>備考</p>
            <textarea cols="30" rows="10" maxlength="255" style="background-color:#e9ecef" readonly style="margin-bottom: 20px;"><?php echo $bookInfo->Note; ?></textarea>
        </div>

        <input type="hidden" value="{{$_GET['id']}}" name="id">
        <input class="button" type="submit" value="確定" style="margin-right:30px;  background-color:aquamarine; box-shadow:10px 10px 20px; ">
        <a class="cancelButton" href="/Booking/public/booklist?{{$_SESSION['query_string']}}">キャンセル</a>
        
        <div>
        <p>貸出者</p>
        <select name="LenderName" require>
            @foreach($users as $user)
                <option <?php if($lendUserName == $user['name']){ echo 'selected';} ?> value="<?php if(isset($user['name'])){echo $user['name'];} ?>"><?php if(isset($user['name'])){echo $user['name'];}  
                ?></option>
            @endforeach
            </select>
        </div>

        <div>
            <p>貸出日</p>
            <input type="date" name="LendingDate" required readonly value="{{$lendUser->LendingDate}}">
        </div>

        <div>
            <p>返却予定日</p>
            <input type="date" name="ReturnExpectedDate" value="{{$lendUser->ReturnExpectedDate}}" required min="<?php echo date('Y-m-d');?>" max="<?php echo date('Y-m-d',strtotime('+1 month'));?>">
        </div>

        <div>
            <p>備考</p>
            <textarea name="Note" cols="30" rows="10" maxlength="255"  style="margin-bottom: 20px;">{{$lendUser->Note}}</textarea>
        </div>
        
    </form>
    </div>
    
</body>

@endsection
@extends('layouts.footer')