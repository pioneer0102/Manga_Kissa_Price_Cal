# マンガ喫茶料金計算システム

PHP実装による高精度料金計算エンジン。コース課金・延長料金・深夜割増・税計算を包括的に処理。

## 機能

DateTimeImmutableベースの時間計算、10分ブロック延長料金、深夜時間帯割増（22:00-05:00 +15%）、10%消費税対応の業務用料金計算システム。

## 開発環境・要件

| 項目 | 要件 | 備考 |
|------|------|------|
| **PHP** | 7.4+ | DateTimeImmutableサポート必須 |

## 開発環境セットアップ

### 1. 依存関係のインストール
```bash
composer install
```

### 2. プリコミットフックのセットアップ
プロジェクトには、コミット前に自動的にコード品質チェックを実行するプリコミットフックが含まれています。

`.git/hooks/pre-commit` ファイルを作成し、以下のコードをその中にコピーしてください。

#### Windows (Git Bash)
```bash
# プリコミットフックに実行権限を付与
touch .git/hooks/pre-commit
```

#### Linux/macOS
```bash
# プリコミットフックに実行権限を付与
chmod +x .git/hooks/pre-commit
```

```bash
#!/bin/sh

echo "Running pre-commit checks..."

# Run the composer script that includes all your selected tools
composer run fix-all

if [ $? -ne 0 ]; then
    echo "❌ Pre-commit checks failed!"
    echo "Please fix the issues and try committing again."
    exit 1
fi

echo "✅ All pre-commit checks passed!"
exit 0
```

### 3. プリコミットフックの動作確認
```bash
# テストコミットで動作確認
git add .
git commit -m "test commit"
```

プリコミットフックは以下の処理を自動実行します:
- 構文チェック
- コードスタイル自動修正
- PHPStan静的解析
- PHP CodeSnifferチェック

### 4. コード品質ツールの個別実行
```bash
# 全品質チェックの実行
composer run lint-all

# コードスタイルの自動修正
composer run fix-all

# 構文チェックのみ
composer run syntax-check
```

### 詳細なリンティング設定
コード品質ツールの詳細設定については、[README-LINTING.md](./README-LINTING.md) を参照してください。

## ビジネス要件

- 各種コースの基本料金計算
- 延長料金の自動計算（10分単位切り上げ）
- 深夜時間帯（22:00-5:00）の割増料金計算
- 税込・税抜両方の金額計算
- 詳細な利用内訳の表示

## コード構造

```
src/
├── MangaCafeCalculator.php          # メイン計算エンジン
│   ├── calculate()                  # 料金計算（メイン処理）
│   └── calculateExtensionFee()      # 延長料金計算（深夜割増対応）
│   ...
│
├── CourseType.php                   # コース定数・設定クラス
│   ├── REGULAR_1HOUR               # 通常1時間コース定数
│   ├── PACK_3HOUR                  # 3時間パック定数
│   ├── PACK_5HOUR                  # 5時間パック定数
│   └── PACK_8HOUR                  # 8時間パック定数
│   ...
│
└── example.php                      # 使用例・デモンストレーション

tests/
└── test.php                         # 総合テストスイート
```

## 料金体系

| コース | 料金（税抜） | 時間 |
|--------|-------------|------|
| 通常料金 | 500円 | 1時間 |
| 3時間パック | 800円 | 3時間 |
| 5時間パック | 1,500円 | 5時間 |
| 8時間パック | 1,900円 | 8時間 |
| 延長料金 | 100円 | 10分ごと |

### 延長・割増料金
- **延長料金**: ¥100/10分ブロック（切り上げ）
- **深夜割増**: 22:00〜05:00時間帯は15%割増
- **消費税**: 10%

## 実行方法

### 基本実行
```bash
# 使用例の実行
php src/example.php

# Composer スクリプトを実行
composer run dev

# Make コマンドを使用
make dev
```
```bash
# テストスイートの実行
php tests/test.php

# Composer スクリプトを実行
composer test

# Make コマンドを使用
make test
```

### 開発時のワークフロー
プロジェクトには自動化されたコード品質チェックが組み込まれています:

1. **コードを編集**
2. **自動チェック実行**: `composer run lint-all`
3. **問題があれば自動修正**: `composer run fix-all`
4. **コミット**: プリコミットフックが自動実行される
5. **テスト**: `make test`
