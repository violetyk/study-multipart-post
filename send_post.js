var upload_url = 'http://localhost:8000/index.php';

function sendPost1() {
    var request = new XMLHttpRequest();
    request.open("POST", upload_url, true);

    var boundary = createBoundary();
    request.setRequestHeader( "Content-Type", 'multipart/form-data; boundary=' + boundary );

    var body = '';
    body += '--' + boundary + '\r\n' + 'Content-Disposition: form-data; name="my_text1"\r\n\r\n';
    body += "this is a text1";
    body += '\r\n';
    body += '--' + boundary + '\r\n' + 'Content-Disposition: form-data; name="my_text2"\r\n\r\n';
    body += "this is a text2";
    body += '\r\n';
    body += '--' + boundary + '--';
    request.send(body);
    console.log(request)
    console.log(body)
    alert("send! see console log...");
}

function sendPost2() {

  var oReq = new XMLHttpRequest();
  oReq.open("GET","img_sample/sample1.png", true);
  oReq.responseType = "arraybuffer";
  oReq.onload = function(oEvent) {
    var arrayBuffer = oReq.response;
    console.log( "len = " +  arrayBuffer.byteLength );

    var request = new XMLHttpRequest();
    request.open("POST", upload_url, true);

    var boundary = createBoundary();
    request.setRequestHeader("Content-Type", 'multipart/form-data; boundary=' + boundary );

    var buffer = unicode2buffer('--' + boundary + '\r\n' + 'Content-Disposition: forname="my_file1"; filename="upload_sample1.png"\r\n' + 'Content-Type: image/png\r\n\r\n');

    var buffer = appendBuffer(buffer, arrayBuffer);

    var buffer = appendBuffer(buffer , unicode2buffer('\r\n' + '--' + boundary + '--'));
    request.send(buffer);
    console.log(request)
    console.log(buffer)
    alert("send! see console log...");
  }
  // oReq.send(null);
}
 

function sendPost3() {

  var formData = new FormData();
  formData.append('my_text1', 'hogehoge');
  formData.append('my_text2', 123456);

  var xhr = new XMLHttpRequest();
  xhr.open('POST', upload_url, true);
  // xhr.onload = function(e) { ... };

  xhr.send(formData);
  alert("send! see console log...");


}


function unicode2buffer(str){
 
  console.log(str);
    var n = str.length,
        idx = -1,
        byteLength = 512,
        bytes = new Uint8Array(byteLength),
        i, c, _bytes;

    for(i = 0; i < n; ++i){
        c = str.charCodeAt(i);
        if(c <= 0x7F){
            bytes[++idx] = c;
        } else if(c <= 0x7FF){
            bytes[++idx] = 0xC0 | (c >>> 6);
            bytes[++idx] = 0x80 | (c & 0x3F);
        } else if(c <= 0xFFFF){
            bytes[++idx] = 0xE0 | (c >>> 12);
            bytes[++idx] = 0x80 | ((c >>> 6) & 0x3F);
            bytes[++idx] = 0x80 | (c & 0x3F);
        } else {
            bytes[++idx] = 0xF0 | (c >>> 18);
            bytes[++idx] = 0x80 | ((c >>> 12) & 0x3F);
            bytes[++idx] = 0x80 | ((c >>> 6) & 0x3F);
            bytes[++idx] = 0x80 | (c & 0x3F);
        }
        if(byteLength - idx <= 4){
            _bytes = bytes;
            byteLength *= 2;
            bytes = new Uint8Array(byteLength);
            bytes.set(_bytes);
        }
    }
    idx++;

    var result = new Uint8Array(idx);
    result.set(bytes.subarray(0,idx),0);

    return result.buffer;
}

function appendBuffer(buf1,buf2) {
    var uint8array = new Uint8Array(buf1.byteLength + buf2.byteLength);
    uint8array.set(new Uint8Array(buf1),0);
    uint8array.set(new Uint8Array(buf2),buf1.byteLength);
    return uint8array.buffer;
}


function createBoundary() {
    var multipartChars = "-_1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    var length = 30 + Math.floor( Math.random() * 10 );
    var boundary = "---------------------------";
    for (var i=0;i < length; i++) {
        boundary += multipartChars.charAt( Math.floor( Math.random() * multipartChars.length ) );
    }
    return boundary;
}
