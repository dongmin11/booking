<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserLoginController extends Controller
{
    public function login()
    {
        session_start();
        session_destroy();
        return view('Auth.login');
    }

    public function UserLogin(Request $request)
    {
        $message = "";
        $email = $request->email;
        $password = $request->password;

        //入力されたメールアドレスのレコード取得
        $user = User::where('email', 'LIKE', "%{$email}%");

        //入力されたメールアドレスが存在しない場合
        $user_email = User::where('email', 'LIKE', "%{$email}%")->first();
        if (!$user_email) {
            $EmailMessage = "登録されたメールアドレスが存在しません";
            return view('auth.login')->with(compact('EmailMessage'));
        }else{
            $user->update([
                'login_try' => time()
            ]);
        }

        //最後にログイン試行した時間取得
        $login_try = User::where('email', 'LIKE', "%{$email}%")->value('login_try');

        $now_time = time();

        //最後にログイン試行した時間と現在時間の差分取得
        $time_difference = $now_time - $login_try;

        //3時間経過でパスコード試行回数リセット
        if ($time_difference > 10800) {
            $user->update([
                'passLock' => 0
            ]);
        }


        //パスワード入力の試行回数が6回以上&&試行から5分経過していない場合
        if (($time_difference < 300) && ($user->value('passLock') == 6)) {
            $user->update([
                'login_try' => time()
            ]);
            $fatalMessage = "パスワード間違いの上限回数に達しました。5分後に再度実行してください";
            return view('auth.login')->with(compact('fatalMessage'));
        }


        if (!password_verify($password, $user->value('password'))) {
            $PassMessage = "パスワードが違います";

            $pass_Lock = $user->value('passLock');
            $pass_Lock++;

            $user->update([
                'passLock' => $pass_Lock,
            ]);

            //入力されたパスワードが合っているか判定
            if (User::where('email', 'LIKE', "%{$email}%")->value('passLock') == 6) {
                User::where('email', 'LIKE', "%{$email}%")->update([
                    'login_try' => time()
                ]);
                $fatalMessage = "パスワード間違いの上限回数に達しました。5分後に再度実行してください";
                return view('auth.login')->with(compact('fatalMessage'));
            }

            return view('auth.login')->with(compact('PassMessage'));
        }


        session_start();
        $_SESSION['userID'] = $user->value('ID');

        User::where('email', 'LIKE', "%{$email}%")->update([
            'login_time' => time()
        ]);

        return redirect()->route('booklist');
    }

    //ログアウト処理
    public function logout()
    {
        session_start();
        session_destroy();
        return redirect()->route('login');
    }

    //登録画面
    public function regist()
    {
        return view('auth.register');
    }

    //登録処理
    public function UserRegister(Request $request)
    {
        //単一のメールアドレスであるか確認
        $rules = [
            'email' => ['string', 'email', 'max:100', 'unique:users'],
            'password' => ['min:8', 'max:20']
        ];

        $customMessages = [
            'email.unique' => 'このメールアドレスはすでに利用されています',
            'password.min' => 'パスワードは8文字以上で入力してください',
        ];

        $this->validate($request, $rules, $customMessages);

        //確認用パスワードと一致されているか確認
        if (!($request->password == $request->password_confirmation)) {
            $message = 'パスワードが一致しません';
            return view('auth.register', compact('message'));
        }

        //ユーザ登録
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        session_start();
        $_SESSION['username'] = $request->name;

        return redirect()->route('booklist');
    }
}
