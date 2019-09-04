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

テーブル accounts
-----------------

解答者管理テーブルです。

.. csv-table::
   :header: 名前, データ型, 説明
   :widths: 10, 5, 30

   id, int(11), ユニークキー
   email, varchar(255), メールアドレス（強要はしていない）
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

以下は作りかけです。関係のない資料もありますので参考にしないでください。
------------------------------------------------------------------------

.. sequencediagram::

   index -> core\nApplication: $application->run()

.. sequencediagram::
   title your smartphone -> burp -> webserver
   your smartphone\n192.168.1.C -> default GW\n(buffalo)\n192.168.1.1: 1. HTTP request
   default GW\n(buffalo)\n192.168.1.1 -> *nat\n192.168.1.1\n192.168.100.A: 2. request
   nat\n192.168.1.1\n192.168.100.A -> +your burp\n192.168.100.B: 3. request
   alt transparent proxy
    your burp\n192.168.100.B->default GW\n(CySecPro)\n192.168.100.1: 4. request
    default GW\n(CySecPro)\n192.168.100.1->*nat\n192.168.100.1\n133.20.178.130: 5. request
    nat\n192.168.100.1\n133.20.178.130->+webserver: 6. request
    webserver->-nat\n192.168.100.1\n133.20.178.130: 7. response
    nat\n192.168.100.1\n133.20.178.130->default GW\n(CySecPro)\n192.168.100.1: 8. response
    default GW\n(CySecPro)\n192.168.100.1->your burp\n192.168.100.B: 9. response
   end
   your burp\n192.168.100.B ->-nat\n192.168.1.1\n192.168.100.A: 10. response
   nat\n192.168.1.1\n192.168.100.A->default GW\n(buffalo)\n192.168.1.1: 11. response
   default GW\n(buffalo)\n192.168.1.1->your smartphone\n192.168.1.C: 12. HTTP respons
