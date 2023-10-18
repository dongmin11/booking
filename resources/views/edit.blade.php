@extends('layouts.header')
@section('content')

<link rel="stylesheet" href="css/add.css">

<body>
    <div class="container">
    <h2>書籍編集</h2>
    <form action="{{route('update')}}" method="post">
    @csrf
        <div>
            <p>書名</p>
            <input type="text" name="BookName" required maxlength="100" value="{{$bookInfo->BookName}}">
        </div>

        <div>
            <p>著者</p>
            <input type="text" name="Author" name="Author" required maxlength="100" value="{{$bookInfo->Author}}">
        </div>

        <div>
            <p>出版社</p>
            <input type="text" name="Publisher" name="Publisher" required maxlength="100" value="{{$bookInfo->Publisher}}">
        </div>

        <div>
            <p>出版日</p>
            <input type="date" name="PublicationDate" required value="{{$bookInfo->PublicationDate}}"max=<?php echo date('Y-m-d');?>>
        </div>

        <div>
            <p>購入日</p>
            <input type="date" name="PurchaseDate" required value="{{$bookInfo->PurchaseDate}}" max=<?php echo date('Y-m-d');?>>
            @if ($errors->has('PurchaseDate'))
            <span class="error" style="color: red; font-weight:bold; margin-left:10px;">{{ $errors->first('PurchaseDate') }}</span>
            @endif
        </div>

        <div>
            <p>購入者</p>

            <select name="PurchaserName" require>
            @foreach($users as $user)
                <option <?php if($purchaserName == $user['name']){ echo 'selected';} ?> value="<?php if(isset($user['name'])){echo $user['name'];} ?>"><?php if(isset($user['name'])){echo $user['name'];}  
                ?></option>
            @endforeach
            </select>

      </div>

        <div>
            <p>備考</p>
            <textarea name="Note" cols="30" rows="10" maxlength="255" style="margin-bottom: 20px;"><?php echo $bookInfo->Note; ?></textarea>
        </div>

        <input type="hidden" value="{{$_GET['id']}}" name="id">
        <input class="button" type="submit" value="更新" style="margin-right:30px;  background-color:aquamarine; box-shadow:10px 10px 20px; ">
        <a href="/Booking/public/booklist?{{$_SESSION['query_string']}}" class="cancelButton">キャンセル</a>
    </form>
    </div>
    
</body>

@endsection
@extends('layouts.footer')