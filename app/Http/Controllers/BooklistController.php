<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\booklendinginfo;
use App\Models\booklist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\favorite;

class BooklistController extends Controller
{
    public $userName;
    public function makeSQL($userID){
        return "SELECT
        b.ID AS bookID
        , BookName
        , Author
        , Publisher
        , PublicationDate
        , PurchaseDate
        , b.Note AS bookNote
        , bl.LendingUserID
        , u.name AS latestLendingUser
        , u.Note AS userNote
        , f.favoriteFlg
        , bl.ID AS lendingID
        , bl.LendingDate
        , bl.ReturnExpectedDate
        , bl.ReturnDate
        , bl.Note AS lendingNote
        , u.ID AS userID 
    From
        Booklists AS b 
        LEFT JOIN ( 
            SELECT
                ID
                , bookID
                , MAX(ID) AS latestLendingInfo 
            FROM
                booklendingInfos 
            GROUP BY
                bookID
        ) AS x 
            ON x.bookID = b.ID 
        LEFT JOIN ( 
            SELECT
                ID
                , BookID
                , LendingUserID
                , LendingDate
                , ReturnExpectedDate
                , ReturnDate
                , Note 
            FROM
                booklendingInfos
        ) AS bl 
            ON bl.BookID = b.ID 
            AND bl.ID = x.latestLendingInfo 
        LEFT JOIN users AS u 
            ON u.ID = bl.LendingUserID
        LEFT JOIN(
        SELECT
        userID,
        favoriteBookID,
        favoriteFlg
        FROM favorites
        WHERE userID = $userID
        )AS f
        ON b.ID = f.favoriteBookID
         ";
    }



    //一覧画面表示
    public function booklist(Request $request)
    {
        session_start();
        //ログインチェック
        if (!isset($_SESSION['userID'])) {
            return redirect()->route('login');
        }
        $userID = $_SESSION['userID'];

        $time_difference = $this->loginCheck();

        // 時間差が3時間以上の場合の処理
        if ($time_difference >= 3 * 3600) { // 3時間は秒単位で計算されています
            $message = '再度ログインしてください';
            unset($_SESSION['userID']);
            return redirect()->route('login')->with('message', $message);
        }


        //レコード表示件数設定
        if (isset($_GET['indexNum'])) {
            $indexNum = $_GET['indexNum'];
        } else {
            //初期値は10件
            $indexNum = 10;
        }

        $_SESSION['query_string'] = $_SERVER['QUERY_STRING'];
        $num = 1;
        $UserName = User::where('ID', $_SESSION['userID'])->first()->name;

        if (isset($_GET['key'])) {
            $key = $_GET['key'];
            $key = mb_convert_kana($key, 'nr');

            $bookInfos = DB::table('booklists AS i')
                ->select(
                    'i.ID AS bookID',
                    'i.BookName',
                    'i.Author',
                    'i.Publisher',
                    'i.PublicationDate',
                    'i.PurchaseDate',
                    'i.Note',
                    'b.BookID',
                    'b.LendingDate AS bookLendDate',
                    'b.ReturnExpectedDate AS bookReturnexpect',
                    'b.ReturnDate AS ReturnDate',
                    'b.LendingUserID',
                    'users.name AS latestLendingUser',
                    'f.favoriteBookID',
                    'f.favoriteFlg'
                )
                ->leftJoin(DB::raw('(SELECT
                    `ID`,
                    `BookID`,
                    `LendingUserID`,
                    `LendingDate`,
                    `ReturnExpectedDate`,
                    `ReturnDate`
                FROM
                    booklendinginfos
                WHERE
                    `ReturnDate` IS NULL) AS b'), 'i.ID', '=', 'b.BookID')
                ->leftjoin(DB::raw("(
                        SELECT 
                        `userID`,
                        `favoriteBookID`,
                        `favoriteFlg`
                    FROM
                        favorites
                    WHERE
                        userID = $userID
                    )AS f"), 'i.ID', '=', 'f.favoriteBookID')
                ->leftJoin('users', 'b.LendingUserID', '=', 'users.ID')
                ->whereRaw("i.BookName LIKE ? OR i.Author LIKE ? OR i.Publisher LIKE ?", ['%' . $key . '%', '%' . $key . '%', '%' . $key . '%'])
                ->orderBy('i.ID', 'desc')
                ->get();
            $bookInfos = $bookInfos->toArray();
        } else {
            //ソートされていない場合id降順に表示
            $sql = $this->makeSQL($userID);
            $bookInfos = DB::select("$sql ORDER BY bookID DESC");
        }

        $count = count($bookInfos);

        // ソート機能
        if (isset($_GET['direction'])) {
            $bookInfos = $this->OriginalSort($userID);
        }

        //レコード数
        $categorys_num = count($bookInfos);

        //10ずつ表示
        $max_page = ceil($categorys_num / $indexNum);

        //すでにページングされている場合
        if (!isset($_GET['page_id'])) {
            $now = 1;
            //ページングされていない場合
        } else {
            $now = $_GET['page_id'];
        }

        //ページング機能
        $param = $this->paging($indexNum, $bookInfos, $now, $max_page);
        $url = $param[0];
        $bookInfos = $param[1];

        $booklendinginfo = new booklendinginfo();

        return view('booklist', compact('bookInfos', 'count', 'url', 'max_page', 'now', 'UserName', 'num', 'booklendinginfo', 'indexNum', 'userID'));
    }


    //追加画面
    public function add()
    {
        session_start();

        //ログインされていなければログイン画面へリダイレクト
        if (!isset($_SESSION['userID'])) {
            return redirect()->route('login');
        }

        $time_difference = $this->loginCheck();

        if ($time_difference >= 3 * 3600) { // 3時間は秒単位で計算されています
            $message = '再度ログインしてください';
            unset($_SESSION['userID']);
            return redirect()->route('login')->with('message', $message);
        }

        $UserName = User::where('ID', $_SESSION['userID'])->value('name');

        //ユーザーレコード全取得
        $users = User::where('DeleteFlg', 0)->get();

        //配列に変換して先頭を空白
        $users = $users->toArray();
        array_unshift($users, '');

        return view('add', compact('UserName', 'users'));
    }

    //追加処理
    public function create(Request $request)
    {
        //ヴァリデーション作成
        $rules = [
            'PurchaseDate' => "after:{$request->PublicationDate}"
        ];
        $messages = [
            'PurchaseDate.after' => '購入日は出版日より後の日付を選択して下さい'
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // バリデーションに失敗した場合
            return redirect()->back()->withErrors($validator)->withInput();
        }

        session_start();

        //購入者のUserID取得
        $userID = User::where('name', 'LIKE', "%{$request->PurchaserName}%")->pluck('ID')->first();

        //ログイン中のユーザー名取得
        $UserName = $_SESSION['username'];

        //ログインユーザーID取得
        $loginUserID = User::where('name', $UserName)->first('ID');

        //追加完了メッセージ
        $message = '追加完了しました';

        //レコード追加
        booklist::create([
            'BookName' => mb_convert_kana($request->BookName, 'nr'),
            'Author' => mb_convert_kana($request->Author, 'nr'),
            'Publisher' => mb_convert_kana($request->Publisher, 'nr'),
            'PublicationDate' => $request->PublicationDate,
            'PurchaseDate' => $request->PurchaseDate,
            'CreateUserID' => $loginUserID->ID,
            'PurchaserID' => $userID,
            'Note' => $request->Note,
        ]);

        return redirect()->route('booklist')->with('message', $message);
    }

    //編集画面
    public function edit(Request $request)
    {
        session_start();

        $time_difference = $this->loginCheck();
        if ($time_difference >= 3 * 3600) { // 3時間は秒単位で計算されています
            $message = '再度ログインしてください';
            unset($_SESSION['userID']);
            return redirect()->route('login')->with('message', $message);
        }

        //排他機能
        $_SESSION['LockVer'] = booklist::where('id', $_GET['id'])->first('LockVer')->LockVer;

        //ログイン中のユーザー名取得
        $UserName = User::where('ID', $_SESSION['userID'])->value('name');

        //ユーザーレコード全取得
        $users = User::where('DeleteFlg', 0)->get();

        //選択されたレコード取得
        $bookInfo = booklist::find($_GET['id']);

        //購入者レコード取得
        $param = User::find($bookInfo->PurchaserID);

        $purchaserName = $param->name;

        return view('edit', compact('bookInfo', 'UserName', 'users', 'purchaserName'));
    }

    //編集処理
    public function update(Request $request)
    {
        session_start();

        //排他機能楽観ロック
        $LockVer = booklist::where('id', $request->id)->first('LockVer')->LockVer;
        if (!($LockVer == $_SESSION['LockVer'])) {
            $message = '更新に失敗しました';
            return redirect()->route('booklist')->with('message', $message);
        }

        //ヴァリデーション作成
        $rules = [
            'PurchaseDate' => "after:{$request->PublicationDate}"
        ];
        $messages = [
            'PurchaseDate.after' => '購入日は出版日より後の日付を選択して下さい'
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // バリデーションに失敗した場合
            return redirect()->back()->withErrors($validator)->withInput();
        }

        //選択された購入者のレコードを取得
        $param = User::where('name', 'LIKE', "%{$request->PurchaserName}%")->first();
        $userID = $param->ID;
        $booklist = new booklist();
        $message = '更新が完了しました';
        $UserName = $_SESSION['username'];
        $loginUser = User::where('name', $UserName)->first();

        booklist::where('id', $request->id)->update(['LockVer' => 0]);
        //選択された値でレコード更新
        $booklist->where('id', $request->id)->update([
            'BookName' => mb_convert_kana($request->BookName, 'nr'),
            'Author' => mb_convert_kana($request->Author, 'nr'),
            'Publisher' => mb_convert_kana($request->Publisher, 'nr'),
            'PublicationDate' => $request->PublicationDate,
            'PurchaseDate' => $request->PurchaseDate,
            'PurchaserID' => $userID,
            'Note' => mb_convert_kana($request->Note),
            'UpdateUserID' => $loginUser->ID
        ]);

        $LockVer++;
        booklist::where('id', $request->id)->update(['LockVer' => $LockVer]);

        return redirect()->route('booklist')->with('message', $message);
    }

    //貸出情報編集
    public function editLendInfo()
    {
        session_start();

        $time_difference = $this->loginCheck();
        if ($time_difference >= 3 * 3600) { // 3時間は秒単位で計算されています
            $message = '再度ログインしてください';
            unset($_SESSION['userID']);
            return redirect()->route('login')->with('message', $message);
        }

        //排他機能
        $LockVer = booklendinginfo::where('BookID', $_GET['id'])->where('ReturnDate', Null)->first()->LockVer;
        $_SESSION['LockVer'] = $LockVer;

        //ログイン中のユーザー名取得
        $UserName = User::where('ID', $_SESSION['userID'])->value('name');

        //ユーザーレコード全取得
        $users = User::where('DeleteFlg', 0)->get();

        //選択されたレコード取得
        $bookInfo = booklist::find($_GET['id']);

        //貸出情報取得
        $lendUser = booklendinginfo::where('BookID', $_GET['id'])->where('ReturnDate', Null)->first();
        $lendUserName = User::where('ID', $lendUser->LendingUserID)->first()->name;

        return view('editLendInfo', compact('bookInfo', 'UserName', 'users', 'lendUser', 'lendUserName'));
    }

    public function updateLendInfo(Request $request)
    {
        session_start();

        $time_difference = $this->loginCheck();
        if ($time_difference >= 3 * 3600) { // 3時間は秒単位で計算されています
            $message = '再度ログインしてください';
            unset($_SESSION['userID']);
            return redirect()->route('login')->with('message', $message);
        }

        $LockVer = booklendinginfo::where('BookID', $request->id)->where('ReturnDate', Null)->first()->LockVer;
        if ($_SESSION['LockVer'] !== $LockVer) {
            $message = '貸出に失敗しました';
            return redirect()->route('booklist')->with('message', $message);
        }

        //ログイン中のユーザー名取得
        $UserName = User::where('ID', $_SESSION['userID'])->value('name');

        //選択された貸出者のレコードを取得
        $param = User::where('name', 'LIKE', "%{$request->LenderName}%")->first();
        $LendinguserID = $param->ID;

        //更新者（ログイン者）情報を取得
        $item = User::where('name', 'LIKE', "%{$UserName}%")->first();
        $loginUserID = $item->ID;

        //貸出情報追加
        booklendinginfo::where('bookID', $request->id)->where('ReturnDate', Null)->update([
            "LendingUserID" => $LendinguserID,
            "LendingDate" => $request->LendingDate,
            "ReturnExpectedDate" => $request->ReturnExpectedDate,
            "UpdateUserID" => $loginUserID,
            "Note" => $request->Note
        ]);

        $LockVer++;
        booklendinginfo::where('BookID', $request->id)->where('returnDate', Null)->update(['LockVer' => $LockVer]);

        $message = '貸出情報編集完了';

        return redirect()->route('booklist')->with('message', $message);
    }

    //貸出画面
    public function lendInfo()
    {
        session_start();
        unset($_SESSION['lendUpdate_at']);
        unset($_SESSION['bookUpdate_at']);


        //排他機能
        $record = booklendinginfo::where('BookID', $_GET['id'])->first();
        // 貸出履歴が存在する場合
        if (isset($record)) {
            booklendinginfo::where('BookID', $_GET['id'])->orderBy('updated_at', 'desc')
                ->first()->update(['LockVer' => 1]);
            //Lockverを変更した後に該当レコードの更新情報を取得
            $latestRecord = booklendinginfo::where('BookID', $_GET['id'])->orderBy('updated_at', 'desc')
                ->first();
            $_SESSION['lendUpdate_at'] = $latestRecord->updated_at;
        } else {
            // 貸出履歴が存在しない場合の処理
            booklist::where('id', $_GET['id'])->update(['LockVer' => 1]);
            $param = booklist::where('id', $_GET['id'])->first();
            $_SESSION['bookUpdate_at'] = $param->updated_at;
        }
        //ログイン中のユーザー名取得
        $UserName = User::where('ID', $_SESSION['userID'])->value('name');

        //ユーザーレコード全取得
        $users = User::where('DeleteFlg', 0)->get();

        //選択されたレコード取得
        $bookInfo = booklist::find($_GET['id']);
        return view('lendInfo', compact('bookInfo', 'UserName', 'users'));
    }

    //貸出処理
    public function lend(Request $request)
    {
        session_start();

        //貸出履歴がある場合
        if (isset($_SESSION['lendUpdate_at'])) {
            //レコード更新差分確認用に最後に更新された該当レコード情報取得
            $latestRecord = booklendinginfo::where('BookID', $request->id)->orderBy('updated_at', 'desc')->first('updated_at');
            //セッションで保存されている更新履歴と最新の更新履歴が違っていた場合
            if (!($latestRecord->updated_at == $_SESSION['lendUpdate_at'])) {
                $message = '貸出に失敗しました';
                booklendinginfo::where('BookID', $request->id)->orderBy('updated_at', 'desc')->first()->update(['LockVer' => 0]);
                return redirect()->route('booklist')->with('message', $message);
            }
        }

        //貸出履歴が無い場合
        if (isset($_SESSION['bookUpdate_at'])) {
            //レコード更新差分確認用に最後に更新された該当レコード情報取得
            $update_at = booklist::where('id', $request->id)->first('updated_at');
            booklist::where('id', $request->id)->update(['LockVer' => 0]);
            // /セッションで保存されている更新履歴と最新の更新履歴が違っていた場合
            if (!($update_at->updated_at == $_SESSION['bookUpdate_at'])) {
                $message = '更新に失敗しました';
                return redirect()->route('booklist')->with('message', $message);
            }
        }
        //ログイン中のユーザー名取得
        $UserName = User::where('ID', $_SESSION['userID'])->value('name');

        //選択された貸出者のレコードを取得
        $param = User::where('name', 'LIKE', "%{$request->LenderName}%")->first();
        $LendinguserID = $param->ID;

        //更新者（ログイン者）情報を取得
        $item = User::where('name', 'LIKE', "%{$UserName}%")->first();
        $loginUserID = $item->ID;

        //貸出情報追加
        booklendinginfo::create([
            "BookID" => $request->id,
            "LendingUserID" => $LendinguserID,
            "LendingDate" => $request->LendingDate,
            "ReturnExpectedDate" => $request->ReturnExpectedDate,
            "CreateUserID" => $loginUserID,
            "Note" => $request->Note
        ]);

        $message = '貸出完了';

        return redirect()->route('booklist')->with('message', $message);
    }

    //詳細画面
    public function detail(Request $request)
    {
        session_start();

        $time_difference = $this->loginCheck();
        if ($time_difference >= 3 * 3600) { // 3時間は秒単位で計算されています
            $message = '再度ログインしてください';
            unset($_SESSION['userID']);
            return redirect()->route('login')->with('message', $message);
        }

        //ログイン中のユーザー名取得
        $UserName = User::where('ID', $_SESSION['userID'])->value('name');
        $lendingUser = "";
        $num = 0;

        //選択されたレコード取得
        $bookInfo = booklist::find($_GET['id']);

        //貸出履歴情報取得
        $lendingInfos = booklendinginfo::where('BookID', 'LIKE', $_GET['id'])->orderBy('id', 'desc')->get();

        //まだ返却がされていない貸出情報を取得
        $lending = $lendingInfos->where('ReturnDate', null)->first();
        //貸出者名取得
        if (isset($lending->LendingUserID)) {
            $lendingUser = User::find($lending->LendingUserID);
            $lendingUser->name;
        }

        //貸出情報一覧の貸出者名をすべて取得
        $lendUserAll = [];
        for ($i = 0; $i < count($lendingInfos); $i++) {
            array_push($lendUserAll, User::where('ID', $lendingInfos[$i]->LendingUserID)->first('name'));
        }



        return view('detail', compact('bookInfo', 'UserName', 'lendingUser', 'lendingInfos', 'lendUserAll', 'num', 'lending'));
    }

    public function return()
    {
        $message = "返却が完了しました";
        $bookInfo = booklist::find($_GET['id']);
        $booklendinginfo = new booklendinginfo();

        //返却処理
        $param = $booklendinginfo->where('BookID', '=', $bookInfo->ID)->where('ReturnDate', '=', null)->first();
        if ($param == null) {
            $message = 'すでに返却済みです';
            return redirect()->route('booklist')->with('message', $message);
        }
        $booklendinginfo->where('BookID', '=', $bookInfo->ID)->where('ReturnDate', '=', null)->update([
            'Returndate' => $_GET['returnDate']
        ]);

        return redirect()->route('booklist')->with('message', $message);
    }

    public function practice(Request $request)
    {
        return view('practice');
    }

    public function bookLendHistory(Request $request)
    {
        //ログインチェック
        session_start();
        if (!isset($_SESSION['userID'])) {
            return redirect()->route('login');
        }

        $time_difference = $this->loginCheck();
        if ($time_difference >= 3 * 3600) { // 3時間は秒単位で計算されています
            $message = '再度ログインしてください';
            unset($_SESSION['userID']);
            return redirect()->route('login')->with('message', $message);
        }

        //表示件数設定
        $indexNum = 10;
        if (isset($_GET['indexNum'])) {
            $indexNum = $request->indexNum;
        }
        $num = 1;
        $UserName = User::where('ID', $_SESSION['userID'])->value('name');

        //本ごとに貸出履歴取得
        $bookInfos = DB::select("SELECT
        i.ID
       , i.BookName
       , i.Author 
       , b.lendingUserID
       ,u.name AS userName
       ,lending_counts.lendCount
    FROM
       booklists i
       LEFT JOIN(SELECT
       `ID`                                        -- ID
       , `BookID`                                  -- BookID
       , `LendingUserID`                           -- LendingUserID
       , `LendingDate`                             -- LendingDate
       , `ReturnExpectedDate`                      -- ReturnExpectedDate
       , `ReturnDate`                              -- ReturnDate
    FROM
       booklendinginfos
    WHERE
       `ReturnDate` IS NULL)  AS b
           ON i.ID = b.BookID
    LEFT JOIN (
        SELECT
            BookID,
            COUNT(ID) AS lendCount
        FROM
            booklendinginfos
        GROUP BY
            BookID
    ) AS lending_counts
        ON i.ID = lending_counts.BookID
       LEFT JOIN users u
           ON b.lendingUserID = u.ID
        WHERE lending_counts.lendCount >0
           ORDER BY
     LendCount DESC
     LIMIT $indexNum OFFSET 0;");


        return view('bookLendHistory', compact('bookInfos', 'UserName', 'num'));
    }

    public function userLendHistory(Request $request)
    {

        //ログインチェック
        session_start();
        if (!isset($_SESSION['userID'])) {
            return redirect()->route('login');
        }

        $time_difference = $this->loginCheck();
        if ($time_difference >= 3 * 3600) { // 3時間は秒単位で計算されています
            $message = '再度ログインしてください';
            unset($_SESSION['userID']);
            return redirect()->route('login')->with('message', $message);
        }

        $num = 1;
        $UserName = User::where('ID', $_SESSION['userID'])->value('name');

        //ログイン中のユーザーID取得
        $UserID = User::where('name', $UserName)->pluck('id')->first();
        //ユーザー選択された場合
        if (isset($request->selectedUser)) {
            $UserID = User::where('name', $_GET['selectedUser'])->pluck('id')->first();
        }
        //表示するユーザーのレコード件数取得
        $lendingUser = booklendinginfo::where('LendingUserID', $UserID)->get();
        $count = count($lendingUser);

        //「全件表示」押下された場合は該当ユーザーのレコード全件表示
        if ($count > 10 && isset($_GET['count'])) {
            $count = count($lendingUser);
            //デフォルトは10件
        } elseif ($count > 10) {
            $count = 10;
        }

        //ソート機能
        $sort = 'LendingDate';
        $direction = 'DESC';

        if (isset($_GET['sort'])) {
            $sort = $_GET['sort'];
        }

        if (isset($_GET['direction'])) {
            $direction = $_GET['direction'];
        }

        //ユーザー別書籍貸出履歴取得
        $bookInfos = DB::select("SELECT
        u.ID,
        u.name,
        bli.LendingDate,
        bli.ReturnDate,
        DATEDIFF(bli.ReturnDate, bli.LendingDate) AS DaysBetween,
        b.BookName
    FROM
        booklists AS b
    LEFT JOIN booklendinginfos AS bli
    ON bli.BookID = b.ID
        LEFT JOIN users AS u
    ON bli.LendingUserID = u.ID
    WHERE u.ID = $UserID
    ORDER BY $sort $direction
    LIMIT $count OFFSET 0
    ");

        $users = User::pluck('name');

        return view('userLendHistory', compact('bookInfos', 'UserName', 'num', 'users', 'lendingUser'));
    }



    //ページング機能
    public function paging($indexNum, $bookInfos, $now, $max_page)
    {
        //現在のページ位置
        if ($now == "") {
            $now = "1";
        }

        $start_no = ($now - 1) * $indexNum;

        //現在のレコード開始位置から指定の数だけレコードを区切る
        $bookInfos = array_slice($bookInfos, $start_no, $indexNum, true);
        $url = strpos($_SERVER['REQUEST_URI'], '?');

        //urlに？がついていない場合
        if ($url == false) {
            $url = 'http://localhost/Booking/public/booklist?page_id=';
            return [$url, $bookInfos];
        } else {
            //すでにページネート機能使われている状態の場合該当urlを除く
            for ($i = 1; $i <= $max_page; $i++) {
                if (mb_strpos($_SERVER['REQUEST_URI'], 'page_id=')) {
                    $_SERVER['REQUEST_URI'] = str_replace('&page_id=' . $i, '', $_SERVER['REQUEST_URI']);
                }
            }

            //url作成
            $last_string = substr($_SERVER['REQUEST_URI'], -1);
            if ($last_string == '?') {
                $url = $_SERVER['REQUEST_URI'] . 'page_id=';
                return [$url, $bookInfos];
            } else {
                $url = $_SERVER['REQUEST_URI'] . '&page_id=';
                return [$url, $bookInfos];
            }
        }
    }

    //ソート機能
    public function OriginalSort($userID)
    {
        //ソート条件取得
        $direction = $_GET['direction'];
        $sql = $this->makeSQL($userID);

        if (isset($_GET['key']) && ($_GET['sort'])) {
            $key = $_GET['key'];
            $key = mb_convert_kana($key, 'nr');
            $sort = $_GET['sort'];
            $bookInfos = db::select("$sql WHERE `BookName` LIKE '%$key%' OR `Author` LIKE '%$key%' OR `Publisher` LIKE '%$key%' ORDER BY $sort $direction");
        } elseif ($_GET['sort']) {
            $sort = $_GET['sort'];
            $bookInfos = db::select("$sql ORDER BY $sort $direction");
        } else {
            $bookInfos = db::select("$sql ORDER BY id DESC");
        }
        return $bookInfos;
    }

    //ログインチェック機能
    public function loginCheck()
    {
        // ユーザーのログイン時刻を取得し、Unixタイムスタンプに変換
        $login_time = User::where('ID', $_SESSION['userID'])->value('login_time'); // ユーザーが存在するか確認が必要

        // 現在の時刻をUnixタイムスタンプに変換
        $now_time = time();

        // 時間差を計算
        $time_difference = $now_time - $login_time;

        return $time_difference;
    }

    public function timeoutSession($time_difference)
    {
        if ($time_difference >= 3 * 3600) { // 3時間は秒単位で計算されています
            $message = '再度ログインしてください';
            unset($_SESSION['userID']);
            return redirect()->route('login')->with('message', $message);
        }
    }

    public function updateFavorite(Request $request)
    {
        // // POSTリクエストからisColoredの値を取得
        $isColored = $request->input('isColored');
        $userID = $request->input('userID');
        $bookID = $request->input('bookID');

        if ($isColored == 'true') {
            // 既存のレコードがあれば削除
            $existingRecord = Favorite::where('userID', $userID)->where('favoriteBookID', $bookID)->first();
            if (isset($existingRecord)) {
                $existingRecord->delete();
            }
            // // データベースを更新
            favorite::create([
                'userID' => $userID,
                'favoriteBookID' => $bookID,
                'favoriteFlg' => 1
            ]);
            return response()->json(['message' => 'created']);
        } elseif ($isColored == 'false') {
            favorite::where('userID', $userID)->where('favoriteBookID',$bookID)->update([
                'favoriteFlg' => 0
            ]);
            return response()->json(['message' => 'updated']);
        }
    }
}
