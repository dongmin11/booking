@extends('layouts.header')
@section('content')
<link rel="stylesheet" href="css/add.css">

<body>
    <div class="container">
    <h2 id="h2">書籍追加</h2>
    <form action="{{route('create')}}" method="post">
        @csrf
        <div>
            <p>書名</p>
            <input type="text" name="BookName" required maxlength="100" value="{{old('BookName')}}">
        </div>

        <div>
            <p>著者</p>
            <input type="text" name="Author" required maxlength="100" value="{{old('Author')}}">
        </div>

        <div>
            <p>出版社</p>
            <input type="text" name="Publisher" required maxlength="100" value="{{old('Publisher')}}">
        </div>

        <div>
            <p>出版日</p>
            <input type="date" name="PublicationDate" required value="{{old('PublicationDate')}}" max=<?php echo date('Y-m-d'); ?>>
        </div>

        <div>
            <p>購入日</p>
            <input type="date" name="PurchaseDate" required max=<?php echo date('Y-m-d'); ?> value="{{old('PurchaseDate')}}">
            @if ($errors->has('PurchaseDate'))
            <span class="error" style="color: red; font-weight:bold; margin-left:10px;">{{ $errors->first('PurchaseDate') }}</span>
            @endif
        </div>


        <div>
            <p>購入者</p>

            <select name="PurchaserName" required>
                @foreach($users as $user)
                @isset($user['name'])
                <option value="{{ $user['name'] }}" {{ $user['name'] == $UserName ? 'selected' : '' }}>{{ $user['name'] }}</option>
                @else
                <option value=""></option>
                @endif
                @endforeach
            </select>

        </div>

        <div>
            <p>備考</p>
            <textarea name="Note" cols="30" rows="10" maxlength="255" style="margin-bottom: 20px;"></textarea>
        </div>


        <input class="button" type="submit" value="追加" style="margin-right:30px;  background-color:aquamarine; box-shadow:10px 10px 20px; ">
        <a href="/Booking/public/booklist" class="cancelButton"> キャンセル</a>
        <!-- <input class="button" type="submit"  value="キャンセル" style="background-color:crimson; box-shadow:10px 10px 20px;"> -->
    </form>
    </div>
    
</body>

@endsection
@extends('layouts.footer')