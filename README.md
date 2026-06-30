# GoodWorld WP Online Books

WordPress + SQLite + Free PDF to Flipbook を使い、PDF 資料・教材・マニュアル・カタログをオンライン本棚として公開するための軽量スターターです。

このリポジトリは MySQL / MariaDB を使いません。WordPress は SQLite Database Integration プラグインで SQLite 上に構築し、PDF のめくり表示は Free PDF to Flipbook が生成したショートコードを GoodWorld Online Books に貼り付けて表示します。

## 構成

```text
goodworld-wp-online-books/
  docker-compose.yml
  .env.example
  docker/wordpress/Dockerfile
  scripts/install-sqlite-wp.sh
  storage/sqlite/.gitkeep
  uploads/.gitkeep
  wordpress/wp-content/plugins/goodworld-online-books/
```

## 必要な WordPress プラグイン

- SQLite Database Integration
- Free PDF to Flipbook
- GoodWorld Online Books (このリポジトリ同梱)

GoodWorld Online Books は依存プラグインが無効でも WordPress 全体を fatal error にしません。不足がある場合は管理画面に警告を表示します。

## Docker 起動

```bash
cp .env.example .env
docker compose up -d --build
```

WordPress は `.env` の `WORDPRESS_PORT` で指定したポートで開きます。標準設定では http://localhost:8080 です。この構成には MariaDB / MySQL / phpMyAdmin コンテナは含まれていません。

WordPress 本体は Docker named volume `wordpress_data` に永続化し、開発対象の `goodworld-online-books` プラグイン、SQLite DB、`uploads/` はホスト側へ bind mount しています。Windows 環境では WordPress 本体全体を bind mount すると初回コピーが極端に遅くなる場合があるためです。

## SQLite Database Integration の導入

この Docker 構成では SQLite Database Integration をイメージ内に同梱し、`wp-content/db.php` drop-in と `docker/wordpress/wp-config.php` をあらかじめ配置しています。

手動で構成を変更する場合は、WordPress 初期インストール前に SQLite Database Integration を有効化してください。プラグインのバージョンにより drop-in の配置や wp-config.php の設定が異なるため、必ず公式 README を確認してください。

手動手順の目安:

1. WordPress 管理画面、または wp-cli で SQLite Database Integration をインストールします。
2. プラグインの説明に従い `db.php` drop-in を `wp-content/db.php` に配置します。
3. 必要に応じて `wp-config.php` に SQLite 用の設定を追加します。
4. SQLite DB ファイルが永続化領域 `storage/sqlite/` に作られることを確認します。

補助スクリプト:

```bash
docker compose exec wordpress bash /var/www/html/scripts/install-sqlite-wp.sh
```

このスクリプトは固定推測で危険な変更を行わず、wp-cli でプラグイン導入を試みます。drop-in 設定は実際の SQLite Database Integration の README に従って確認してください。

## Free PDF to Flipbook の導入

1. WordPress 管理画面にログインします。
2. プラグイン追加で `Free PDF to Flipbook` を検索します。
3. インストールして有効化します。
4. `PDF TO FLIPBOOK` メニューを開きます。
5. PDF をアップロード、またはメディアライブラリから選択します。
6. 生成されたショートコードをコピーします。
7. `オンライン本` 投稿の「Free PDF to Flipbook ショートコード」欄へ貼り付けます。

初期 MVP では、GoodWorld Online Books 側で PDF URL から自動生成するのではなく、Free PDF to Flipbook が生成したショートコードを表示します。

## ショートコード

- `[gw_bookshelf]`: 本棚をカード形式で表示します。
- `[gw_upload_guide]`: PDF 本の作成手順を表示します。
- `[gw_book_list]`: 簡易的な本一覧を表示します。
- `[gw_featured_books]`: おすすめ本のみを表示します。

例:

```text
[gw_bookshelf category="ai" columns="3" limit="12" show_search="1" show_category_filter="1"]
```

## 公開用ページ

GoodWorld Online Books は有効化時に、サービス公開に必要な固定ページを自動生成し、`mybooks` 専用デザインの独立テンプレートで表示します（テーマ非依存）。

| ページ | スラッグ | テンプレート |
| --- | --- | --- |
| トップ（サービス紹介） | `/`（フロントページ） | `templates/landing-page.php` |
| PDF本の作り方 | `/upload-book-guide/` | `templates/guide-page.php` |
| オンライン本棚 | `/books/` | `templates/archive-book.php` |
| 料金プラン | `/pricing/` | `templates/pricing-page.php` |
| よくある質問 | `/faq/` | `templates/faq-page.php` |
| お問い合わせ | `/contact/` | `templates/contact-page.php` |
| 運営会社 / プライバシーポリシー / 利用規約 | `/company/`, `/privacy-policy/`, `/terms/` | `templates/page-simple.php`（本文は管理画面で編集可） |

ヘッダー／フッターは `templates/partials/site-header.php`・`site-footer.php` で共通化しています。スマホではハンバーガーメニュー（`assets/js/landing.js`）で開閉します。

## お問い合わせ・ニュースレター

- お問い合わせフォーム（`/contact/`）とフッターのニュースレター登録は、`admin-post.php` 経由で `GWOB_Forms` が処理し、`gwob_inquiry` 投稿タイプに保存します。
- 受信内容は管理画面の「お問い合わせ」メニューで確認できます（種別: お問い合わせ / ニュースレター）。
- 送信時に nonce 検証・入力検証を行います。管理者メール通知は `wp_mail` による best-effort で、SMTP 未設定環境でも保存自体は成功します。
- 運用時はメール送信プラグイン（WP Mail SMTP 等）の設定を推奨します。

## アクセス制御

- `public`: 誰でも閲覧できます。
- `logged_in`: ログインユーザーのみ閲覧できます。
- `private`: 管理者または編集者のみ閲覧できます。

権限がない場合は「この本を閲覧する権限がありません。」と表示します。

## SQLite 互換性チェック

- WordPress インストールが SQLite で完了すること。
- 投稿、`gw_book`、メタデータが保存できること。
- メディアアップロードが動くこと。
- Free PDF to Flipbook の登録データが保存できること。
- 本棚一覧、検索、カテゴリフィルタが動くこと。

## バックアップ

最低限、以下をバックアップしてください。

- `storage/sqlite/`
- `uploads/`
- `wordpress/wp-content/plugins/goodworld-online-books/`
- 必要に応じて `wordpress/wp-content/themes/`

## よくあるエラー

- SQLite インストール画面に進めない: `wp-content/db.php` drop-in と SQLite Database Integration の README を確認してください。
- メディアアップロードに失敗する: `wp-content/uploads` の書き込み権限とボリュームマウントを確認してください。
- flipbook が表示されない: Free PDF to Flipbook が有効か、許可されたショートコード名か確認してください。
- flipbook の枠・表組みは出るが日本語など文字が表示されない: PDF が CID キー font（日本語フォントなど）を使用しており、pdf.js が CMap を取得できていない状態です。GoodWorld Online Books は同梱の `assets/pdfjs/cmaps/`・`assets/pdfjs/standard_fonts/` と `assets/js/pdfjs-cmap-shim.js` で Free PDF to Flipbook 本体を改造せずに CMap 取得先を注入します。これらのファイルが配信できているか（404 でないか）確認してください。
- 本が表示されない: 公開範囲、公開状態、カテゴリー、ショートコード属性を確認してください。

## 動作確認結果

このローカル環境では以下を確認済みです。

- SQLite Database Integration: 有効化済み。SQLite DB は `storage/sqlite/.ht.sqlite` に作成されます。
- Free PDF to Flipbook: 有効化済み。`[fptf-flipbook pdf="..."]` ショートコードでサンプル PDF を表示します。
- GoodWorld Online Books: 有効化済み。`gw_book` 投稿タイプ、メタデータ、カテゴリーが SQLite に保存されます。
- メディアアップロード: `wp media import` で PDF をメディア登録できることを確認済みです。`uploads/sample-goodworld-online-book.pdf` は `/wp-content/uploads/sample-goodworld-online-book.pdf` として配信できます。
- ショートコード表示: `/books/` と `/books/sample-goodworld-online-book/` で本棚・詳細表示を確認済みです。

## SQLite 利用時の互換性メモ

SQLite は小規模サイト向けの軽量構成です。同時書き込みには弱いため、複数の wp-cli コマンド、管理画面操作、プラグイン更新チェックを並行して走らせると `database is locked` が出る場合があります。運用時は同時編集や同時更新を避け、プラグイン更新・大量登録・バックアップはアクセスの少ない時間に実行してください。

Free PDF to Flipbook 本体は改造していません。GoodWorld Online Books は、Free PDF to Flipbook のショートコードを安全に保存・表示する補助プラグインです。Free PDF to Flipbook 側のショートコード仕様が変わった場合は、許可ショートコード名と保存形式を確認してください。

## 今後の拡張予定

会員プラグイン連携、WooCommerce 連携、Stripe 決済、有料本販売、購入者限定閲覧、AI 要約、AI チャット質問応答、読書進捗保存、お気に入り、閲覧履歴、ダウンロード制限、PDF 透かし、社内ユーザー権限管理、多言語対応、SQLite から MySQL / PostgreSQL への移行手順。
