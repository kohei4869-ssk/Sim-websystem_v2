# SIM FORM

広告出稿の**見積もり・シミュレーション作成**における、依頼・管理システムです。
元々は自身でJavaScript＋GAS（Google Apps Script）＋Notionを使って構築した仕組みでしたが、PHP（Laravel）・JavaScript（jQuery/Vue.js）・MySQLで作り直しました。

> 本リポジトリは学習・ポートフォリオ目的のクローン開発であり、実際の業務データ・顧客情報・Webhook URLなどは一切含まれていません。

---

## 目次

1. [背景・目的](#背景・目的)
2. [システム概要](#システム概要)
3. [技術スタック](#技術スタック)
4. [業務フロー](#業務フロー)
5. [ディレクトリ構成](#ディレクトリ構成)
6. [工夫した点・苦労した点](#工夫した点・苦労した点)
7. [今後の課題](#今後の課題)

---

## 背景・目的

広告出稿の見積もりシミュレーションを、依頼から作成・管理まで手作業（メールでのやり取り＋一覧表への手動転記）で回していたことで、以下の課題がありました。

**依頼する側**
- 媒体メニューに詳しくないと、正確な依頼内容を伝えるのが難しい
- 必要項目の抜け漏れ・入力ミスが起きる
- 「一部の条件だけ変えたパターンも見積もりたい」という追加依頼が多く、最初から作成してほしいパターンを伝えるのが難しい

**受理する側**
- メール文から作成パターンを読み解いて、一覧表へ手作業で転記する必要がある
- 必要項目の抜け漏れ・入力ミスについて確認工数がかかり、作業に取り掛かるまでに時間がかかる

**案件管理**
- 誰が何件担当しているか、どの案件が対応中・完了なのかが把握しづらい
- 情報がメールや添付ファイルに分散し、過去の類似案件を探しづらい
- 過去の見積もりを再利用しづらい

これらの課題は個別の問題ではなく、依頼から見積もり管理までを一貫して支える仕組みが無いことが根本原因だと考え、個人の注意力や媒体リテラシーに頼ることなく、仕組み自体を構築しようと考えました。

そこで、依頼内容を構造化Girdフォームで入力＆仕様に基づいて条件ロジックで制御することで、正確かつ直感的に依頼が可能に。
担当部署への自動振り分け・対応状況の一元管理・見積もりデータの蓄積までを行う仕組みとして、SIM FORMを企画/開発しました。

## システム概要

依頼者は、グリッド形式の入力フォーム（SimForm.vue）を使って、クライアント名・案件名・媒体・配信条件などを直感的に入力できます。

1. 入力内容（所属×媒体）に応じて**担当部署が自動判定**され、
2. 該当部署の**ダッシュボード画面**（グラフ・カレンダー・カード表示）に案件が振り分けられ、
3. 担当者はダッシュボード上で**対応状況の管理・見積もり指標のリアルタイム自動計算**を行います。

依頼の入力から見積もりの完成まで、一連の流れをシステム上で完結できる設計です。

## 技術スタック

<table>
<tr><th>言語</th><th>フレームワーク／ライブラリ</th><th>主な使用目的・役割</th></tr>
<tr><td>PHP</td><td>Laravel</td><td>ルーティング・コントローラー・DB操作・画面生成・Slack Webhook通知など、サーバーサイド全体の処理</td></tr>
<tr><td rowspan="3">JavaScript</td><td>Vue.js</td><td>依頼入力フォーム（SimForm.vue）のUI構築・入力状態の管理</td></tr>
<tr><td>jQuery</td><td>管理画面（department.blade.php）でのDOM操作・イベント処理・Ajax通信</td></tr>
<tr><td>Chart.js</td><td>管理画面のグラフ描画（月別推移・ステータス内訳など）</td></tr>
<tr><td>SQL</td><td>MySQL</td><td>依頼・案件・見積もりデータの蓄積・管理</td></tr>
</table>

## 業務フロー

依頼が入力されてから、部署担当者が対応するまでの流れと、それぞれの担当技術・関連ファイルの対応です。

| フロー | 担当技術 | 関連ファイル | 内容 |
|---|---|---|---|
| ①依頼入力 | JavaScript（Vue.js） | `resources/js/components/SimForm.vue` | ENTRY→PLATFORMS→FORMの3ステップ入力フォームを実装。入力内容をJSONにまとめ、Ajaxでサーバーへ送信 |
| ②URLと処理の対応付け | PHP（Laravel） | `routes/web.php` | 「このURLにアクセスされたらこの処理を呼ぶ」という対応表を用意。`/departments/qm-sem-1`などのURLで、部署ごとに同じ仕組みを使い回せるように設計 |
| ③担当部署の自動判定 | PHP（Laravel） | `app/Http/Controllers/SubmitController.php` | 「依頼者の所属」×「媒体」の組み合わせを設定テーブル（department_routing）と照合し、担当部署を自動判定。判定結果を案件データに保存 |
| ④データ処理・保存 | PHP（Laravel）＋MySQL | `app/Http/Controllers/SubmitController.php` | 依頼者情報・案件情報・媒体別配信パターンの3種のテーブルに正規化して保存。複数媒体の同時出稿にも対応 |
| ⑤送信・通知 | PHP | `app/Http/Controllers/SubmitController.php` | 保存完了後、担当部署に応じたSlack Webhookで自動通知。メール送信はUIまで実装済みだがバックエンド未接続（ステータス更新のみ連動） |
| ⑥部署別ダッシュボード表示 | PHP（Laravel／Blade） | `resources/views/requests/department.blade.php` | 各部署のURLアクセス時、その部署宛ての案件だけを絞り込んで表示。共通の1テンプレートを使い回す設計 |
| ⑦管理画面の集計・加工 | PHP | `resources/views/requests/department.blade.php` | 案件データを月別件数・ステータス内訳・担当者別件数・媒体別内訳などに集計するロジックを実装 |
| ⑧管理画面の描画・操作 | JavaScript（Chart.js／jQuery） | `resources/views/requests/department.blade.php` | 集計結果をグラフ描画。タブ切替・検索・Excel風の列フィルター・カード/カレンダー表示のリアルタイム更新を実装 |
| ⑨見積もり自動計算・データ蓄積 | JavaScript／PHP | `resources/views/requests/department.blade.php` | 課金形態ごとの計算式で見積もり指標をリアルタイム自動計算し、estimate_itemsテーブルへ保存。データを蓄積し、媒体横断のResearch一覧としても参照可能に |

## ディレクトリ構成

```
resources/
  views/
    welcome.blade.php
    index.blade.php
    requests/
      department.blade.php
  js/
    components/
      SimForm.vue

app/
  Http/
    Controllers/
      SubmitController.php

routes/
  web.php

public/
  index.php
```

<table>
<tr><th>フォルダ</th><th>ファイル</th><th>役割</th></tr>
<tr><td rowspan="3"><code>resources/views/</code></td><td><code>welcome.blade.php</code></td><td>Vue（SimForm.vue）をマウントする入り口</td></tr>
<tr><td><code>index.blade.php</code></td><td>全案件の簡易一覧（開発初期の名残、現在未使用）</td></tr>
<tr><td><code>requests/department.blade.php</code></td><td>部署別ダッシュボード（依頼一覧・集計・見積り入力）</td></tr>
<tr><td><code>resources/js/components/</code></td><td><code>SimForm.vue</code></td><td>依頼者用の申請フォーム本体</td></tr>
<tr><td><code>app/Http/Controllers/</code></td><td><code>SubmitController.php</code></td><td>申請フォームの受付・部署振り分け・ダッシュボード用データ組み立て</td></tr>
<tr><td><code>routes/</code></td><td><code>web.php</code></td><td>画面表示用ルーティング</td></tr>
<tr><td><code>public/</code></td><td><code>index.php</code></td><td>Laravelのフロントコントローラ</td></tr>
</table>

> コード中には、実装の意図や処理の流れを説明する日本語コメントを詳しく入れています。学習過程でAIと対話しながら理解を深めた内容を、そのままコメントとして残しています。

## 工夫した点・苦労した点

- **Gird型UIの採用**：「一部の条件だけ変えたパターンも見積もりたい」という依頼が多かったことを踏まえ、最初から表（グリッド）形式で行を複製し、差分の項目だけ編集できるUIとして設計。依頼者は入力し直す手間なく類似パターンを依頼でき、受理側も見積もり送付時と同じフォーマットのまま依頼を受け取れるため、双方にメリットのある設計とした。
- **課金形態ごとに異なる見積もり計算式の実装**：CPC課金／vCPM課金／CPV課金／CPM課金、さらにYG-Display&DGCは素材形態によって4パターンと、条件によって計算順序・逆算する指標が異なる。既存のExcelテンプレートの数式を、JavaScriptへ正確に移植した。
- **「1つの依頼に複数媒体」という入れ子構造の設計**：依頼→媒体→配信パターンという3階層のデータを、DBでは正規化して保存しつつ、画面表示では依頼単位のカードにまとめ直す、という行き来をコード上で設計した。
- **「所属×媒体→担当部署」の自動判定ロジック**：所属ごとの個別ルールを優先し、無ければ共通ルールにフォールバックする、優先順位付きの判定処理を実装した。
- **非表示要素に対するグラフ描画の不具合対応**：タブが非表示（display:none）の間はChart.jsがcanvasサイズを正しく取得できない問題に対し、表示タイミングでの再描画処理を実装した。

## 今後の課題

- メール実送信機能のバックエンド実装（現状はステータス更新のみ連動）
- Notion（親子構成DB）で運用していたデータの本格移行・検証
