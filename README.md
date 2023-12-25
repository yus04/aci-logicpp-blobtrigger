# システム概要
Blob Storage にデータ (csv を想定) が保存されたタイミングで、Logic Apps がContainer Instance のコンテナーを起動し、Blob Storage に保存されている最新のデータを Database for MySQL に書き込むシステムです。コンテナーで実行される関数は PHP で書かれています。

# 構成
以下が本システムの構成になります。
![Architecture](image/architecture.png)

# デモ
以下に、実施したデモの Azure ポータルのスクリーンショットを載せます。

Blob Storage のコンテナーにファイルをアップロードします。
![Blob Storage](image/blobstorageupload.png)

Logic Apps の実行の履歴より、ワークロードの実行が成功したことを確認します。
![Logic Apps](image/logicappsresult.png)

MySQL に接続し、保存したテーブルにアップロードしたデータの中身が書き込みされていることが確認できました。
![Database for MySQL](image/mysqlresult.png)

# ローカル実行
```
docker build . -t php74
docker run php74
```

![Result](image/localresult.png)


# 留意事項
- 本ソースコードはサンプルになります。
- index.php の <接続文字列>、<コンテナ名>、<ホスト名>、<データベース名>、<ユーザー名>、<パスワード> の中身を環境にあわせて変更していただく必要があります。
- コンテナーのイメージファイルは Azure Container Registry に保存しています。