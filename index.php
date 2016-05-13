<?php
require 'func_image.php';

foreach ($_FILES as $f) {
    if (is_uploaded_file($f['tmp_name'])) {
        save_image(
            $f['tmp_name'],
            sprintf('%s/upload/%s.%s', dirname($_SERVER['SCRIPT_FILENAME']), $f['name'], get_image_ext($f['tmp_name']))
        );
    }
}

$pattern = sprintf('%s/upload/*{.gif,.jpg,.png}', dirname($_SERVER['SCRIPT_FILENAME']));
$uploaded_images = glob($pattern, GLOB_BRACE);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <script src="send_post.js"></script>
</head>
<body>

    <div>
        <a href="index.php">&laquo; back</a>
    </div>
    <div>
        <form action="index.php" method="post" enctype="multipart/form-data">
            <input type="text" name="my_text1" placeholder="my_text1 テキスト1" />
            <input type="text" name="my_text2" placeholder="my_text1 テキスト2" />
            <input type="file" name="my_file1" />
            <input type="file" name="my_file2" />
            <input type="submit" name="Send" />
        </form>
        <button onClick="sendPost1();">sendPost1()</button>
        <button onClick="sendPost2();">sendPost2()</button>
        <button onClick="sendPost3();">sendPost3()</button>
    </div>

    <div>
        <?php if (!empty($_POST)): ?>
        <hr />
        <h1>$_POST</h1>
        <pre>
            <?php var_dump($_POST);  ?>
        </pre>
        <?php endif; ?>

        <?php if (!empty($_FILES)): ?>
        <hr />
        <h1>$_FILES</h1>
        <pre>
            <?php var_dump($_FILES);  ?>
        </pre>
        <?php endif; ?>

        <?php foreach ($uploaded_images as $v): ?>
        <a href="upload/<?php echo basename($v); ?>" target="_blank"><img src="upload/<?php echo basename($v); ?>" width="100"></a>
        <?php endforeach ?>
    </div>

    <hr />
    <div>
    <?php phpinfo(); ?>
    </div>


    <script type="text/javascript">
        function sendPost4(url, files) {
          var formData = new FormData();
          var date = new Date() ;
          formData.append('my_text1', 'sendPost4');
          formData.append('my_text2', date.getTime());

          for (var i = 0, file; file = files[i]; ++i) {
            formData.append(file.name, file);
          }

          var xhr = new XMLHttpRequest();
          xhr.open('POST', url, true);
          // xhr.onload = function(e) { ... };

          xhr.send(formData);  // multipart/form-dataを自動で付加してくれる
          alert("send! see console log...");
          console.log(formData);
        }

        document.querySelector('input[type="file"]').addEventListener('change', function(e) {
          sendPost4(upload_url, this.files);
        }, false);
    </script>

</body>
</html>
