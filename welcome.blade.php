<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIM発行依頼フォーム</title>

    <!-- ① Viteを介してCSSとJS（Vue）を読み込む -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- ② Vueを適用させる範囲を指定（app.jsで mount('#app') と書いた場所） -->
    <div id="app">
        <!-- ③ Vueで登録したコンポーネントを配置 -->
        <sim-form></sim-form>
    </div>
</body>
</html>
