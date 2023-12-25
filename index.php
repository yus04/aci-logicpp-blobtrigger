<?php
// Composerを使ってオートローダを読み込む
require_once 'vendor/autoload.php';

// Blob Storageのライブラリを読み込む
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;

// Blobに保存されているファイルを読み込む
function read_latest_blob() {

    // 接続文字列を指定する
    $connectionString = "<接続文字列>";

    // Blob Storageのクライアントを作成する
    $blobClient = BlobRestProxy::createBlobService($connectionString);

    // コンテナ名を指定する
    $containerName = '<コンテナ名>';

    // ブロブ一覧を取得
    $blobs = list_blobs($blobClient, $containerName);

    // 最新のBlob名を取得
    $latestBlobName = get_latest_blob_name($blobs);

    // 最新のBlobの読み取り
    $latestBlob = $blobClient->getBlob($containerName, $latestBlobName);

    // 最新のBlobの内容を取得
    $latestBlobData = stream_get_contents($latestBlob->getContentStream());

    return $latestBlobData;
}

// ブロブ一覧を取得
function list_blobs($blobClient, $containerName) {

    $options = new ListBlobsOptions();
    $result = $blobClient->listBlobs($containerName, $options);
    $blobs = $result->getBlobs();

    return $blobs;
}

// ブロブ一覧から最新のものを取得
function get_latest_blob_name($blobs) {

    $latestBlob = null;
    $latestTimestamp = 0;

    foreach ($blobs as $blob) {
        $timestamp = strtotime($blob->getProperties()->getLastModified()->format('Y-m-d H:i:s'));

        if ($timestamp > $latestTimestamp) {
            $latestTimestamp = $timestamp;
            $latestBlob = $blob;
        }
    }
    return $latestBlob->getName();
}

// Azure Database for MySQLにデータを挿入
function write_mysql($latestBlobData){
    // Azure Database for MySQLの接続情報
    $host = "<ホスト名>";
    $dbname = "<データベース名>";
    $username = "<ユーザー名>";
    $password = "<パスワード>";

    try {
        // PDOを使ってデータベースに接続
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

        // データ挿入用のSQL文
        $sql = "INSERT INTO sample_table (column1) VALUES (:data1)";

        // プリペアドステートメントの作成
        $stmt = $pdo->prepare($sql);
    
        // パラメータのバインド
        $stmt->bindValue(':data1', $latestBlobData, PDO::PARAM_STR);
    
        // クエリの実行
        $stmt->execute();
    
        echo "データが正常に挿入されました。";
    } catch (PDOException $e){
        echo "エラー: " . $e->getMessage();
    }
}

$latestBlobData = read_latest_blob();
write_mysql($latestBlobData);
