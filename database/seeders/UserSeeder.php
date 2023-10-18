<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        function generateEmail($length)
        {
            $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';
            $charLength = strlen($characters);

            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charLength - 1)];
            }
            return $randomString . '@com';
        }

        $Name = [
            "太郎",
            "花子",
            "一郎",
            "麻美",
            "次郎",
            "美香",
            "夏樹",
            "さくら",
            "健太",
            "奈緒",
            "雅彦",
            "ゆり",
            "悠太",
            "結衣",
            "和也",
        ];


        $Birth = [
            "2023-01-15", "2022-08-23", "2021-11-07", "2023-04-30", "2022-06-12",
            "2021-09-28", "2023-03-19", "2022-05-02", "2021-12-11", "2023-02-08",
            "2022-07-05", "2021-10-17", "2023-05-25", "2022-04-09", "2021-08-06",
        ];
        $Note = [
            "メモ1の内容",
            "メモ2の内容",
            "メモ3の内容",
            "メモ4の内容",
            "メモ5の内容",
            "メモ6の内容",
            "メモ7の内容",
            "メモ8の内容",
            "メモ9の内容",
            "メモ10の内容",
            "メモ11の内容",
            "メモ12の内容",
            "メモ13の内容",
            "メモ14の内容",
            "メモ15の内容",
        ];

        for ($i = 0; $i < count($Name); $i++) {
            User::create([
                'Name' => $Name[$i],
                'email' => generateEmail(10),
                'Birth' => $Birth[$i],
                'Note' => $Note[$i],
                'password' => Hash::make('aaaa1111'),
                'CreateUserID' => 1,
            ]);
        }
    }
}
