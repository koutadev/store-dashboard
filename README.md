# 📊 Store Dashboard - 店舗売上管理ダッシュボード

複数店舗の売上データを一元管理し、可視化するWebアプリケーション。

## 📸 スクリーンショット

### ダッシュボード
KPIカード4種 + Chart.jsによる売上推移・店舗比較・カテゴリ構成比グラフ
![Dashboard](docs/screenshots/dashboard.png)

### 売上一覧
検索・ソート・ページネーション・Excel/CSVエクスポート対応
![Sales Index](docs/screenshots/sales-index.png)

### 売上登録
店舗・商品を選択して売上を登録。単価は商品マスタから自動計算
![Sales Create](docs/screenshots/sales-create.png)

### 店舗登録
エリア紐付き・営業状態管理付きの店舗マスタ管理
![Store Create](docs/screenshots/store-create.png)

## ⚡ 技術スタック

| カテゴリ | 技術 |
|---------|------|
| Backend | PHP 8.3 / Laravel 11 |
| Database | PostgreSQL 16 |
| Frontend | Blade / Tailwind CSS / Chart.js |
| Infra | Docker / Docker Compose |
| CI/CD | GitHub Actions |
| Test | PHPUnit（43テスト全PASS） |

## 🚀 主な機能

- **ダッシュボード**: KPIカード（今月売上・先月売上・目標達成率・売上件数）、月別売上推移（折れ線）、店舗別売上比較（横棒）、カテゴリ別構成比（ドーナツ）
- **売上管理**: CRUD、店舗・期間・商品名での検索、売上日/数量/合計でのソート、ページネーション
- **データ入出力**: Excel/CSVエクスポート（検索条件を維持したまま出力）
- **店舗管理**: CRUD、エリア→店舗の階層構造、営業状態フラグ
- **認証・権限**: admin/manager/staff のロール制御

## 🔧 こだわったポイント

### パフォーマンス
- N+1問題の解消（Eager Loading）
- 売上集計クエリに複合インデックス（store_id, sale_date）を設計
- PhpSpreadsheetによるExcelエクスポート

### テスト
- 43件のFeature Test / Unit Test（全PASS）
- ダッシュボード表示・CRUD操作・バリデーション・エクスポートをカバー
- GitHub Actionsで自動テスト実行

### セキュリティ
- CSRF対策（Laravel標準）
- SQLインジェクション対策（Eloquent ORM）
- XSS対策（Bladeのエスケープ）
- バリデーションの徹底

## 🏗️ セットアップ

### 前提条件

- Docker Desktop
- Git

### 手順
```bash
git clone https://github.com/kt040403/store-dashboard.git
cd store-dashboard
chmod +x setup.sh
./setup.sh
```

ブラウザで http://localhost:8080 にアクセス

### デモアカウント

| Role | Email | Password |
|------|-------|----------|
| 管理者 | admin@example.com | password |
| マネージャー | tanaka@example.com | password |
| スタッフ | sato@example.com | password |

### 便利コマンド
```bash
make up       # コンテナ起動
make down     # コンテナ停止
make shell    # appコンテナに入る
make fresh    # DB初期化（migrate:fresh --seed）
make test     # テスト実行（43テスト）
make lint     # コードフォーマット（Laravel Pint）
```

## 📐 DB設計

7テーブル構成（areas, stores, users, categories, products, sales, monthly_targets）

主要なリレーション:
- Area → Store（1:N）
- Store → Sale（1:N）
- Product → Sale（1:N）
- Category → Product（1:N）
- Store → MonthlyTarget（1:N）
- Store → User（1:N）

## 👤 作者

**Kouta** - 元寿司職人 → PHP バックエンドエンジニア

- Portfolio: https://portfolio-chi-sage-eud0tx0pxw.vercel.app
- GitHub: [@kt040403](https://github.com/kt040403)