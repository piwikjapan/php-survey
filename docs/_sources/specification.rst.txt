.. -*- coding: utf-8; -*-
.. CySec documentation master file, created by
   sphinx-quickstart on Wed Jun 20 09:57:41 2018.
   You can adapt this file completely to your liking, but it should at least
   contain the root `toctree` directive.

.. |nbsp| unicode:: 0xA0
   :trim:

.. meta::
   :robots: noindex

====
仕様
====

データベース
============

.. _database:

テーブル accounts
-----------------

解答者管理テーブルです。

.. csv-table::
   :header: 名前, データ型, 説明
   :widths: 10, 5, 30

   id, int(11), ユニークキー
   email, varchar(255), メールアドレス（ただしチェック無し: 単なる文字列でも受け入れる）
   password, varchar(255), md5 でハッシュ化されたパスワード
   has_voted, tinyint(1), すでに投票完了しているとき 1 まだであれば 0

テーブル answers
----------------

各人の解答を格納します。

.. csv-table::
   :header: 名前, データ型, 説明
   :widths: 10, 5, 30

   id, int(11), ユニークーキー
   account_id, int(11), 誰の答えか（accounts テーブルの id）
   option_id, int(11), 答えた選択肢（options の id）

テーブル options
----------------

.. csv-table::
   :header: 名前, データ型, 説明
   :widths: 10, 5, 30

   id, int(11), ユニークーキー
   survey_id, int(11), 質問事項（surveys テーブルの id）
   text, varchar(255), survey_id 中の選択肢

テーブル surveys
----------------

.. csv-table::
   :header: 名前, データ型, 説明
   :widths: 10, 5, 30

   idd, int(11), ユニークーキー
   question, varchar(255), 質問事項
   type, varchar(32), Yes No の質問であれば single、複数選択肢は multiple

関数の呼ばれ方
==============

アンケート調査アプリケーションの各プログラムは、ディレクトリ毎に役目が与えられています。
ここで簡単に説明しておきますので、プログラムを読んでみるとよいでしょう。

**controllers**

 画面遷移を管理し、次にどのテンプレートテンプレート呼び出すべきかが定義されています。またテンプレートに引き渡すデータの下ごしらえをします。

**core**

 index.php より最初に呼び出され、データベース接続と一体になったセッション管理を行います。

**css**

 テンプレートから呼び出される css ファイル群です。

**images**

 テンプレートから呼び出される画像です。

**models**

 プログラムからデータベースにどのようにデータを格納するかを定義してあります。アンケート調査アプリケーションにおけるビジネスロジックといったところでしょうか。

**views**

 テンプレート群です。 controllers から引き渡されるデータを代入すれば html として出力できます。
 アンケート調査アプリケーションは残念ながら、本来 controller が担うべき画面遷移のロジックがテンプレート中に組み込まれており、
 テンプレートが多少混沌としています。

ログイン時の遷移
----------------

ログイン時に index.php から主要な関数が呼ばれる順序をまとめてみました。

.. sequencediagram::

   title php-survey: First login diagram
   index -> core\nApplication: $application->run()
   core\nApplication -> controllers\nLoginController: $application->controller->run()
   alt database query
     controllers\nLoginController -> +models\nAccount: Account::findByCredentials
     models\nAccount -> -controllers\nLoginController:  login or\n"already_voted" or\n"invalid_credentials"
   end
   controllers\nLoginController -> controllers\nBaseController: parent::render()
   controllers\nBaseController -> views\nlayout.phtml: include "layout.phtml"

ログイン時
----------------

こちらは email とパスワードで認証が終わってからアンケートを答え終わるまで、となります。

.. sequencediagram::

   title php-survey: Logged in diagram
   index -> core\nApplication: $application->run()
   core\nApplication -> controllers\nSurveyController: $application->controller->run()
   controllers\nSurveyController -> controllers\nBaseController: parent::render()
   controllers\nBaseController -> views\nlayout.phtml: include "layout.phtml"

アンケート終了時
----------------

アンケートをすべて答え終わったときの処理です。

.. sequencediagram::

   title php-survey: Finish diagram
   index -> core\nApplication: $application->run()
   core\nApplication -> controllers\nSurveyController: $application->controller->run()
   controllers\nSurveyController -> controllers\nSurveyController: finish()
   alt database query
     controllers\nSurveyController -> models\nAccount: $application->session->getAccount()->save
   end
   controllers\nSurveyController -> controllers\nBaseController: parent::render()   
   controllers\nBaseController -> views\nlayout.phtml: include "layout.phtml"

MVC モデル
----------

PHP の場合、html ファイルと PHP プログラムを一緒くたに記述し、各画面を 1 つのファイルで終わらすことも可能ですが、
一緒くた方式は画面遷移が 4 画面程度でわけがわからなくなり、体力で作り切ったとしても
のちに仕様変更が生じたときの保守性は最悪です。だって、デザインも一緒くたになってますから。

このような点を踏まえて、現在ではアンケート調査アプリケーションのように models, views, controllers に役割を分離した
MVC モデルという考え方が Web アプリケーションの作成では普通に使われます。
少なくとも views で、html を全部じゃないにしろ PHP プログラムから分離すれば、
デザインの修正はかなり楽（例えばテンプレートの修正を PHP がわからないデザイナーに振ることが可能になる）になることは想像がつくかと思います。

アンケート調査アプリケーションでは、BaseController.php, Session.php などのように、違う Web アプリケーション
を作るとしても使いまわしができそうな共通プログラムも作られていますが、この、使い回しができそうなプログラム
はいちいち作成せず、これらを集大成したフレームワークを使うのが一般的です。

例えば PHP だと、Laravel がありますし、Ruby だと `Ruby on Rails <https://coedo-rails.doorkeeper.jp/events/37506>`_ が有名です。
