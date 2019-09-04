.. -*- coding: utf-8; -*-
.. CySec documentation master file, created by
   sphinx-quickstart on Wed Jun 20 09:57:41 2018.
   You can adapt this file completely to your liking, but it should at least
   contain the root `toctree` directive.

インストール
============

github よりソースをとってきます
-------------------------------

.. code-block:: console
   :linenos:

   $ cd ~
   $ git clone https://github.com/piwikjapan/php-survey.git

配置
----

* 動作に関係のないファイルは配置しないようにします

.. code-block:: console
   :linenos:

   $ mkdir /var/www/html/survey
   $ cd php-survey/
   $ cp -rp controllers core css images index.php models views /var/www/html/survey/

配置確認
--------

* Web ブラウザで http ://ホストオンリーアダプターの IP アドレス
* データベースが設定されていないので、エラーが出ます

 Fatal error: Uncaught Error: Undefined class constant 'MYSQL_ATTR_INIT_COMMAND' in /var/www/html/survey/core/Application.php:31 Stack trace: #0 /var/www/html/survey/index.php(23): Application->run() #1 {main} thrown in /var/www/html/survey/core/Application.php on line 31

エラーメッセージより、MySQL (MariaDB [#]_ ) の設定が必要であることがわかります。

.. [#] MariaDBの開発は、MySQLのオリジナルコードの作者で MySQL AB の創設者でもあるミカエル・ウィデニウスにより、現在オラクルによって所有されているMySQLをフォークして立ち上げられたプロジェクトにより行われている。引用元: `Wikipedia <https://ja.wikipedia.org/wiki/MariaDB>`_

PHP の mysql モジュールをインストール
-------------------------------------

* apache のリスタートを忘れないでください

.. code-block:: console
   :emphasize-lines: 1,2,13,17
   :linenos:

   $ sudo apt install php-mysql
   [sudo] cysec のパスワード:
   パッケージリストを読み込んでいます... 完了
   依存関係ツリーを作成しています
   状態情報を読み取っています... 完了
   以下の追加パッケージがインストールされます:
     php7.2-mysql
   以下のパッケージが新たにインストールされます:
     php-mysql php7.2-mysql
   アップグレード: 0 個、新規インストール: 2 個、削除: 0 個、保留: 7 個。
   119 kB のアーカイブを取得する必要があります。
   この操作後に追加で 461 kB のディスク容量が消費されます。
   続行しますか? [Y/n] Y
   ～ 略 ～
   libapache2-mod-php7.2 (7.2.19-0ubuntu0.18.04.2) のトリガを処理しています ...
   php-mysql (1:7.2+60ubuntu1) を設定しています ...
   $ sudo service apache2 restart

MariaDB をインストール
----------------------

.. code-block:: console
   :emphasize-lines: 1,4
   :linenos:

   $ apt install  mariadb-server
   ～ 略 ～
   この操作後に追加で 185 MB のディスク容量が消費されます。
   続行しますか? [Y/n] Y
   ～ 略 ～

CentOS であればどの Ubuntu の一般ユーザーからでも MariaDB の管理者（root）としてログインできるのですが、Ubuntu のパッケージは Ubuntu の root でしか MariaDB の root 操作がゆるされません。
`ERROR 1698 (28000): Access denied for user 'root'@'localhost' <https://stackoverflow.com/questions/39281594/error-1698-28000-access-denied-for-user-rootlocalhost>`_

これを、どの Ubuntu 一般ユーザーからでも MariaDB の管理者（root）としてログインできる設定に変更します。

* root での作業です。

.. code-block:: console
   :emphasize-lines: 1-3,14
   :linenos:

   # service mysqld stop
   # rm -rf /var/lib/mysql/*
   # mysql_install_db
   Installing MariaDB/MySQL system tables in '/var/lib/mysql' ...
   2019-08-21 14:16:54 140405381696640 [Note] /usr/sbin/mysqld (mysqld 10.1.41-MariaDB-0ubuntu0.18.04.1) starting as process 8065 ...
   OK
   Filling help tables...
   2019-08-21 14:16:58 140067996961920 [Note] /usr/sbin/mysqld (mysqld 10.1.41-MariaDB-0ubuntu0.18.04.1) starting as process 8094 ...
   OK
   Creating OpenGIS required SP-s...
   2019-08-21 14:17:01 140542689062016 [Note] /usr/sbin/mysqld (mysqld 10.1.41-MariaDB-0ubuntu0.18.04.1) starting as process 8126 ...
   OK
   ～ 略 ～
   # service mysqld start
   

これで CentOS 相当になりました。

MariaDB の root パスワードの設定と余計な項目の削除
--------------------------------------------------

* root パスワードの設定、anonymous ユーザー [#]_ の削除、test データベースの削除

  * 全部 Y なのです

.. code-block:: console
   :emphasize-lines: 1,11,17-19,31,37,44,53,62
   :linenos:

   # mysql_secure_installation

   NOTE: RUNNING ALL PARTS OF THIS SCRIPT IS RECOMMENDED FOR ALL MariaDB
         SERVERS IN PRODUCTION USE!  PLEASE READ EACH STEP CAREFULLY!

   In order to log into MariaDB to secure it, we'll need the current
   password for the root user.  If you've just installed MariaDB, and
   you haven't set the root password yet, the password will be blank,
   so you should just press enter here.

   Enter current password for root (enter for none):パスワードは空なので [Enter]
   OK, successfully used password, moving on...

   Setting the root password ensures that nobody can log into the MariaDB
   root user without the proper authorisation.

   Set root password? [Y/n] Y
   New password:pentester
   Re-enter new password:pentester
   Password updated successfully!
   Reloading privilege tables..
    ... Success!


   By default, a MariaDB installation has an anonymous user, allowing anyone
   to log into MariaDB without having to have a user account created for
   them.  This is intended only for testing, and to make the installation
   go a bit smoother.  You should remove them before moving into a
   production environment.

   Remove anonymous users? [Y/n] Y
    ... Success!

   Normally, root should only be allowed to connect from 'localhost'.  This
   ensures that someone cannot guess at the root password from the network.

   Disallow root login remotely? [Y/n] Y
    ... Success!

   By default, MariaDB comes with a database named 'test' that anyone can
   access.  This is also intended only for testing, and should be removed
   before moving into a production environment.

   Remove test database and access to it? [Y/n] Y
    - Dropping test database...
    ... Success!
    - Removing privileges on test database...
    ... Success!

   Reloading the privilege tables will ensure that all changes made so far
   will take effect immediately.

   Reload privilege tables now? [Y/n] Y
    ... Success!

   Cleaning up...

   All done!  If you've completed all of the above steps, your MariaDB
   installation should now be secure.

   Thanks for using MariaDB!
   # exit

.. [#] ユーザー名はなんでもいい、パスワードなしの MariaDB アカウント

PHP-Survey 用のデータベースを作ります
-------------------------------------

php-survey/index.php にあらかじめ定義しておいたので、これに合わせます

index.php

.. code-block:: php-inline
   :linenos:

   $config = array (
       "db_dsn"      => "mysql:dbname=survey_db;host=127.0.0.1",
       "db_user"     => "survey",
       "db_pass"     => "survey_pass",
       "db_pconnect" => true,
       "db_charset"  => "utf8",
       "base_url"    => "/survey", # この行はデータベース接続の設定ではない
   );

アンケート調査アプリケーションで使用する survey_db というデータベースと、root 以外のアカウント、survey を作ります。こういったアプリケーションでは機能を限定したユーザーを作ります。root を使うこともできるのですが、root はなんでもできます。例えばアプリケーションと関係のないデータベースも変更ができてしまいます。
 
* 10 行: survey_db というデータベースを作ります。デフォルト文字コードは utf-8 です
* 13 行: survey_db に survey@'localhost' からの全面アクセスを許可します。survey@'localhost' のパスワードは survey_pass とします。
* 19 行: （survey@'localhost' の）資格情報を有効化します。

.. code-block:: console
   :emphasize-lines: 1,10,13,19
   :linenos:

   $ mysql -uroot -ppentester
   Welcome to the MariaDB monitor.  Commands end with ; or \g.
   Your MariaDB connection id is 49
   Server version: 10.1.41-MariaDB-0ubuntu0.18.04.1 Ubuntu 18.04

   Copyright (c) 2000, 2018, Oracle, MariaDB Corporation Ab and others.

   Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.

   MariaDB [(none)]> CREATE DATABASE survey_db DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
   Query OK, 1 row affected (0.00 sec)

   MariaDB [(none)]> grant all on survey_db.* to survey@'localhost' identified by 'survey_pass' with grant option;
   Query OK, 0 rows affected (0.00 sec)

   MariaDB [(none)]> flush privileges;
   Query OK, 0 rows affected (0.00 sec)

   MariaDB [(none)]> exit
   Bye

utf8_general_ci とは

 アルファベットの大文字・小文字は区別せずにマッチする。
 ただし、区別しない文字は、半角は半角の大文字・小文字、全角は全角の大文字・小文字のみ。
 半角小文字と全角小文字同士はマッチしない。

root が必要な作業は終わったので、アンケート調査アプリケーションの専用アカウント survey で接続しなおします。
 
.. code-block:: console
   :emphasize-lines: 1-3,12,14,16,43,54
   :linenos:

   $ cd ~
   $ cd php-survey/
   $ mysql -uservey -psurvey_pass survey_db
   Welcome to the MariaDB monitor.  Commands end with ; or \g.
   Your MariaDB connection id is 60
   Server version: 10.1.41-MariaDB-0ubuntu0.18.04.1 Ubuntu 18.04

   Copyright (c) 2000, 2018, Oracle, MariaDB Corporation Ab and others.

   Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.

   MariaDB [survey_db]> \! pwd
   /home/cysec/php-survey/assets
   MariaDB [survey_db]> \! ls
   install.sql
   MariaDB [survey_db]> source install.sql
   Query OK, 0 rows affected (0.00 sec)

   Query OK, 0 rows affected (0.00 sec)

   Query OK, 0 rows affected (0.00 sec)

   Query OK, 0 rows affected (0.00 sec)

   Query OK, 0 rows affected (0.00 sec)

   Query OK, 0 rows affected (0.16 sec)

   Query OK, 1 row affected (0.00 sec)

   Query OK, 0 rows affected (0.01 sec)

   Query OK, 0 rows affected (0.01 sec)

   Query OK, 6 rows affected (0.00 sec)
   Records: 6  Duplicates: 0  Warnings: 0

   Query OK, 0 rows affected (0.01 sec)

   Query OK, 2 rows affected (0.00 sec)
   Records: 2  Duplicates: 0  Warnings: 0

   MariaDB [survey_db]> show tables;
   +---------------------+
   | Tables_in_survey_db |
   +---------------------+
   | accounts            |
   | answers             |
   | options             |
   | surveys             |
   +---------------------+
   4 rows in set (0.00 sec)

   MariaDB [survey_db]> exit
   Bye

動作確認
--------

再び Web ブラウザで http ://ホストオンリーアダプターの IP アドレス を開きます。 :numref:`login` 画面が出れば成功です。

.. figure:: ./_static/login.png
   :name: login
   :align: center

   アンケート調査アプリケーションログイン画面
