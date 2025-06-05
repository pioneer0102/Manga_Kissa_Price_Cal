# PHP リンティング設定 - マンガ喫茶計算システム

このプロジェクトには、業界標準ツールを使用した包括的なPHPリンティングとコード品質設定が含まれています。

## インストール

### 前提条件
- PHP 8.0.30以上
- Composer

### 依存関係のインストール
```bash
composer install
```

## 使用方法

### 個別コマンド

#### コードスタイルのチェック
```bash
# PHP CS Fixer (ドライラン)
composer run lint

# PHP CodeSniffer
composer run phpcs

# PHPStan 解析
composer run phpstan
```

#### コードスタイルの修正
```bash
# PHP CS Fixer で自動修正
composer run lint-fix

# PHP CodeSniffer で自動修正
composer run phpcs-fix
```

#### 全チェックの実行
```bash
# 全リンティングツールの実行
composer run lint-all

# 全問題の修正
composer run fix-all
```

### Make の使用 (代替方法)
```bash
# 利用可能なコマンドを表示
make help

# 全チェックの実行
make lint-all

# 全問題の修正
make fix-all

# 完全な品質チェック
make full-check
