<?php
require '../utils/init.php';
require '../links/secret_link.php';
$key = 'DEEPDARKFANTASY1';
if($decrypted = openssl_decrypt(base64_decode($_POST['encData']),'aes-128-cbc',$key,OPENSSL_RAW_DATA,base64_decode($_POST['param']))){
    $data = json_decode($decrypted,true);
    $account = $data['account'];
    $match = md5($data['psw']);
    $remember = intval($data['remember']);
    if($info = mysqli_fetch_assoc(maria($link,"select id as uid,name,avatar from User.me where (account='$account' or email='$account') and match_='$match' limit 1"))){
        $head = base64_encode(json_encode(['alg'=>'SHA256','typ'=>'JWT']));
        if ($remember)
            $payload = [
                'iss'=>'bersder3000.com',
                'uid'=>$info['uid'],
                'name'=>$info['name'],
                'exp'=>time()+1209600,
                'iat'=>time(),
            ];
        else
            $payload = [
                'iss'=>'bersder3000.com',
                'uid'=>$info['uid'],
                'name'=>$info['name'],
                'exp'=>time()+86400,
                'iat'=>time(),
            ];
        $payload = base64_encode(json_encode($payload));
        $signature = hash_hmac('sha256',$head.'.'.$payload,'MYNAMEISVAN');
        $token = $head.'.'.$payload.'.'.$signature;
        if ($remember)
            setcookie('utk',$token,time()+1209600,'/');
        else
            setcookie('utk',$token,0,'/');
        echo json_encode(['code'=>0,'data'=>['info'=>$info,'remember'=>$remember,'token'=>$token]]);
    }
    else{
        echo json_encode(['code'=>1]);
    }
}
else
    echo json_encode(['code'=>1]);
