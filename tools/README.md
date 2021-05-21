# 奈良市プレミアム付商品券取扱店情報関連ツール

## CSVをgeojsonに変換

cnv2geojson.py

### 環境構築

前提：python 3 インストール済

``` bash
$ python -m venv venv
$ source venv/bin/activate
$ pip install --upgrade pip
$ pip install -r requirements.txt
```

## 実行方法

```
$ cnv2geojson.py [-i inputCSVfile] [-o outputGeojson]
```

ファイル指定なしの場合は、../nara-premium-geocoded.csv を ../nara-premium.geojson に変換

