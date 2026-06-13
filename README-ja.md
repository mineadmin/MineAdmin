[中文](./README.md) | [English](./README-en.md) | 日本語

# MineAdmin

<p align="center">
    <img src="https://raw.githubusercontent.com/mineadmin/MineAdmin-Vue/53924b3f98733201d4a2492cf2c91e65a56421be/public/logo.svg" width="120" alt="MineAdmin Logo" />
</p>

<p align="center">
    <a href="https://www.mineadmin.com" target="_blank">公式サイト</a> |
    <a href="https://doc.mineadmin.com" target="_blank">ドキュメント</a> |
    <a href="https://demo.mineadmin.com" target="_blank">デモ</a>
</p>

MineAdmin は、Web サイトの管理画面、運営プラットフォーム、権限センター、社内管理システム、CMS、CRM、OA、ERP などの業務アプリケーションをすばやく構築するための、すぐに使えるバックエンド管理システムです。

組織、ユーザー、権限、メニュー、ログ、添付ファイル、アプリマーケットなど、企業向け管理画面でよく使われる機能をあらかじめ備えています。そのまま業務管理に利用することも、独自モジュールを拡張して、より多くの時間を業務そのものに集中することもできます。

MineAdmin を使用する前に、必ず[《免責事項》](https://www.mineadmin.com/about/declaration)をお読みいただき、同意してください。

## 何に使えるか

- 社内管理画面の構築。社員、部門、役職、ロール、権限などの基本管理に利用できます。
- 業務運営プラットフォームの構築。コンテンツ管理、顧客管理、注文管理、ワークフロー管理、レポートダッシュボードなどに利用できます。
- 新規プロジェクトの管理画面基盤として利用し、ログイン、権限、メニュー、ログ、添付ファイルアップロードなどの共通機能をそのまま再利用できます。
- プラグイン型アプリケーションプラットフォームとして利用し、アプリマーケットから業務機能をインストール、アンインストール、拡張できます。
- 多言語、テーマ設定、レイアウト切り替え、タブ、パンくず、ウォーターマークなど、チーム向けの統一された管理画面体験を提供できます。

## 内蔵機能

### アカウントと権限

- ユーザー管理：ユーザーの追加、編集、削除、無効化、パスワード初期化、ロール割り当てなどに対応します。
- ロール管理：ロールごとにメニュー権限とボタン権限を割り当て、ユーザーごとのアクセス範囲と操作範囲を制御できます。
- メニュー管理：管理画面メニュー、ルート、ボタン権限、外部リンク、iframe ページ、並び順、キャッシュ、非表示状態などを管理できます。
- データ権限：役職またはユーザーごとにデータの表示範囲を設定し、メンバーごとに権限内のデータだけを表示できます。
- ログイン認証：管理画面ログイン、トークン検証、権限検証、現在のユーザー情報取得機能を提供します。

### 組織構造

- 部門管理：会社、機関、チームなどの階層型組織構造を管理できます。
- 役職管理：部門配下の役職を管理し、ユーザーを対応する役職に割り当てられます。
- 責任者管理：部門責任者を設定し、組織関係と管理責任を追跡しやすくします。
- メンバー確認：部門に紐づくユーザーを確認し、組織内のメンバー分布をすばやく把握できます。

### ログ監査

- ログインログ：ユーザーのログイン時刻、ログイン IP、ブラウザ、ログイン状態、メッセージを記録します。
- 操作ログ：ユーザーの重要操作、リクエスト方式、アクセスルート、業務名、操作時刻を記録します。
- 権限監査：ロール、メニュー、ボタン、データ権限を組み合わせ、ユーザーがアクセス可能なリソースの由来を確認しやすくします。

### データとファイル

- 添付ファイル管理：システムにアップロードされたファイル、画像などのリソースを一元管理できます。
- ファイルアップロード：管理画面からのアップロード入口を提供し、アップロード者、ファイル情報などを記録します。
- データセンター：今後の業務データ、ファイルリソース、拡張モジュールのための統一管理入口を提供します。

### アプリ拡張

- アプリマーケット：アプリ、プラグイン、フロントエンドコンポーネントを確認し、必要に応じてダウンロード、インストール、アンインストールできます。
- ローカルアプリ：ローカルにインストール済みのアプリを確認でき、ローカルアプリパッケージのアップロードにも対応します。
- プラグイン拡張：独立した業務機能をプラグインとしてパッケージ化し、メインシステムへの変更を減らす用途に適しています。

### 管理画面体験

- ワークベンチとダッシュボード：ワークベンチ、分析ページ、レポートページなど、管理画面ホームの例を提供します。
- 個人センター：プロフィール管理、パスワード変更など、よく使うアカウント操作に対応します。
- 多言語：簡体字中国語、繁体字中国語、英語などの言語リソースを内蔵しています。
- テーマとレイアウト：ライトモード、ダークモード、システム連動、テーマカラー、クラシックレイアウト、カラムレイアウト、ミックスレイアウトなどに対応します。
- 共通インタラクション：タブ、パンくず、全画面表示、検索、通知、ショートカット、ウォーターマーク、トップへ戻るなど、管理画面でよく使う体験を備えています。

## クイックスタート

MineAdmin はマイグレーションファイルを使ってインストールと初期データ作成を行います。SQL ファイルを手動でインポートする必要はありません。

まず、ローカルに `Composer` がインストールされていることを確認し、次のコマンドを実行してください。

```shell
composer create-project mineadmin/mineadmin --keep-vcs
```

インストール、設定、デプロイ、開発に関する詳しい説明は、[公式ドキュメント](https://doc.mineadmin.com)をご覧ください。

## デモ

[オンラインデモ](https://demo.mineadmin.com)

- アカウント：`admin`
- パスワード：`123456`

> デモ環境は体験用です。無関係なデータを追加しないでください。

## 環境要件

- PHP >= 8.1
- Swoole >= 5.0、かつ `Short Name` を無効化
- Redis >= 4.0
- MySQL >= 5.7 / PostgreSQL >= 10 / SQL Server
- Git >= 2.x

## 公式コミュニティ

> QQ グループは交流と学習のためのものです。無関係な雑談はお控えください。

<a href="https://qm.qq.com/q/PJnEgr4D8C">
  <img src="https://svg.hamm.cn/badge.svg?key=QQグループ&value=150105478" />
</a>

## 戦略パートナー

[Jingcedun 高防御 CDN - DDoS/CC ネットワーク攻撃に対抗する信頼できるサービスプロバイダー](https://www.jcdun.com/guoneigaofangcdn)

## 免責事項

[《免責事項》](https://doc.mineadmin.com/guide/start/declaration.html)

本ソフトウェアを、国の関連政策に違反するソフトウェアまたはアプリケーションの開発に使用してはなりません。本ソフトウェアの使用によって生じたいかなる法的責任も、`MineAdmin` とは関係ありません。

## 謝辞

> 以下は順不同です。

[Hyperf](https://hyperf.io/)  
[Element Plus](https://element-plus.org/)  
[Swoole](https://www.swoole.com)  
[Vue](https://vuejs.org/)  
[Vite](https://vitejs.cn/)  
[JetBrains](https://www.jetbrains.com/)

## OSCS セキュリティ認証

[![OSCS Status](https://www.oscs1024.com/platform/badge/kanyxmo/MineAdmin.svg?size=large)](https://www.murphysec.com/dr/9ztZvuSN6OLFjCDGVo)

## Star 推移

[![Stargazers over time](https://starchart.cc/mineadmin/mineadmin.svg)](https://starchart.cc/mineadmin/mineadmin.svg)

## コントリビューター

> MineAdmin の開発に参加してくださったすべてのコードコントリビューターに感謝します。[[contributors](https://github.com/mineadmin/mineadmin/graphs/contributors)]

<a href="https://github.com/mineadmin/mineadmin/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=mineadmin/mineadmin" />
</a>

[![コントリビューター推移](https://contributor-overtime-api.apiseven.com/contributors-svg?chart=contributorOverTime&repo=mineadmin/mineadmin)](https://www.apiseven.com/en/contributor-graph?chart=contributorOverTime&repo=mineadmin/mineadmin)

## デモ画像

[![pAdQKPJ.png](https://s21.ax1x.com/2024/10/22/pAdQKPJ.png)](https://imgse.com/i/pAdQKPJ)
[![pAdQlx1.png](https://s21.ax1x.com/2024/10/22/pAdQlx1.png)](https://imgse.com/i/pAdQlx1)
[![pAdQQ2R.png](https://s21.ax1x.com/2024/10/22/pAdQQ2R.png)](https://imgse.com/i/pAdQQ2R)
[![pAdQGqK.png](https://s21.ax1x.com/2024/10/22/pAdQGqK.png)](https://imgse.com/i/pAdQGqK)
[![pAdQYVO.png](https://s21.ax1x.com/2024/10/22/pAdQYVO.png)](https://imgse.com/i/pAdQYVO)
[![pAdQNIe.png](https://s21.ax1x.com/2024/10/22/pAdQNIe.png)](https://imgse.com/i/pAdQNIe)
[![pAdQaPH.png](https://s21.ax1x.com/2024/10/22/pAdQaPH.png)](https://imgse.com/i/pAdQaPH)
[![pAdQdGd.png](https://s21.ax1x.com/2024/10/22/pAdQdGd.png)](https://imgse.com/i/pAdQdGd)
