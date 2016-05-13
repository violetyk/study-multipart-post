# study-multipart-post


- [インターネット・プロトコル詳説（4）：MIME（Multipurpose Internet Mail Extensions）～後編 - ＠IT](http://www.atmarkit.co.jp/ait/articles/0104/18/news002.html)
- [Ajaxによるmultipart/postでの画像ファイルアップロード その3 : アシアルブログ](http://blog.asial.co.jp/1378)
- [XMLHttpRequest2 に関する新しいヒント - HTML5 Rocks](http://www.html5rocks.com/ja/tutorials/file/xhr2/)


```
$ brew install php70
$ php -S localhost:8000
```


## マルチパート・メール
- ヘッダの`Content-Type`でマルチパートであることとバウンダリ文字列の宣言
  - `multipart/mixed` 一般的なマルチパート
  - `multipart/alternative` 含まれるパートは同じ内容を示す。HTMLとプレーンテキストメールをセットで送るときなどどちらか片方が表示できればOK名場合を想定
  - `multipart/parallel` 含まれるパートを複合して同時に表示させる音声ファイルと画像や動画ファイルの組み合わせなどを想定
  - `multipart/digest` ニュースやメールを複数含めるとき。メーリングリストなどで複数メールをまとめて送りたい場合など。
- ボディはバウンダリ文字列で区切った複数のパートで構成
- `--バウンダリ文字列`で一つのパートの開始、`--バウンダリ文字列--`で全体のマルチパートの終了

ヘッダ
```
Date: Mon, 19 Feb 2001 04:17:19 +0900
From: "Taro Yamada" < taro@anywhare.ne.jp >
To: hanako@kaisya.co.jp, miho@kigyou.co.jp
Subject: =?ISO-2022-JP?B?GyRCJV4layVBJVEhPCVIJE5OYyRHJDkbKEI=?=
Message-Id: 20010219041709.802C@anywhare.ne.jp
MIME-Version: 1.0
Content-Type: multipart/mixed; boundary="boundary_str"
Content-Transfer-Encoding: 7bit
（空行）
```
ボディ
```
--boundary_str
Content-Type: text/plain; charset="ISO-2022-JP"
Content-Transfer-Encoding: 7bit
（空行）
^[$B$3$s$J$H$3$m$r%G%3!<%I$9$k$J$s$F!#^[(B
^[$B2?$F4qFC$J?M$@!#^[(B
^[$B59$7$+$C$?$i!"5-;v$N46A[$rJT=8It5$IU$G$*4s$;2<$5$$!#BT$C$F$^!<!<$9!#^[(B
--boundary_str
Content-Type: image/gif;
 name="=?ISO-2022-JP?B?GyRCIXcjSSNUJW0lNCVeITwlLxsoQi5naQ==?=
 =?ISO-2022-JP?B?Zg==?="
Content-Disposition: attachment;
 filename="=?ISO-2022-JP?B?GyRCIXcjSSNUJW0lNCVeITwlLxsoQi5naQ==?=
 =?ISO-2022-JP?B?Zg==?="
Content-Transfer-Encoding: base64
（空行）
R0lGODlhcwA4AOYAAP/////++Pj9+/749fn49/H2+eX27+/v7+H07N3z6eXs8dXw5OLl6N/l68/u
4Nfi7Mjs3NXe5sTq2tjb4L3n1bjm0tDY4czY5bTkz67iy8jS3Krgx8zMzKXexMHI1Jrbv7fG1rnE
（中略）
QZJhpFU4ggogyEseqACGj0Pp+dDwhje0IQ6vfFwc4mDSN8RhDTfNHk4ft4aa+rSelPCpScmgVDYM
VYVxGKjjlEqJQAAAOw==
--boundary_str--
```

- インターネットメールは7ビットデータのみをテキストと見なし、それ以外のデータはMINEでのエンコードが必要
- ISO-2022-JPは7bit、シフトJIS、JIS、EUCは8ビット(1バイト)
- ISO-2022-JPで表現できないテキストメールは、テキストファイルをバイナリファイルのように添付ファイルとして扱うようにMIME化してあげる
```
MIME-Version: 1.0
Content-Type: text/plain; charset="SJIS"
Content-Transfer-Encoding: base64
```
```
MIME-Version: 1.0
Content-Type: application/octet-stream
Content-Transfer-Encoding: base64
```
- Subjectやメールアドレスのコメントで非ASCIIテキストを使うにはBase64やQuoted Printableを使う（RFC2047）
```
From: =?ISO-2022-JP?B?GyRCRURDZiQ1JHMbKEI=?= taro@anywhare.co.jp
Subject: =?ISO-2022-JP?B?GyRCJDMkcyRLJEEkTyEiJCo4NTUkJEckORsoQg==?=
 =?ISO-2022-JP?B?GyRCJCshKUtNJGI4NTUkJEo1JCQsJDckShsoQg==?=
 =?ISO-2022-JP?B?GyRCJCQkRyRPJEokJCRHJDkhIxsoQg==?=
```

|記号|意味|
|:--|:--|
|=?|エンコードの開始|
|?=|エンコード終了|
|?|文字コードやエンコード方式、 エンコードデータの区切り|
|`ISO-2022-JP`の部分|文字コード|
|`B`の部分|エンコード方式（B: Base64形式、Q: Quoted Printable形式）|


## 画像のアップロード
- 画像はバイナリ
- Webページではbae64エンコードの`content-Transfer-Encoding`に対応していない
- なので、rrayBufferまたはUint8Array、Blobなどの形式で送る
- XmlHttpRequest2(XHR2)が必要
- XHR2はHTML5ではなく、ブラウザベンダーがそれぞれのプラットフォームに対して加えている改良の一部
- Request Payload
-
```
------WebKitFormBoundaryBa0CUaAUlM8gjJlw
Content-Disposition: form-data; name="my_text1"

sendPost4
------WebKitFormBoundaryBa0CUaAUlM8gjJlw
Content-Disposition: form-data; name="my_text2"

1462780899851
------WebKitFormBoundaryBa0CUaAUlM8gjJlw
Content-Disposition: form-data; name="hogehoge.jpg"; filename="hogehoge.jpg"
Content-Type: image/jpeg


------WebKitFormBoundaryBa0CUaAUlM8gjJlw--
```



