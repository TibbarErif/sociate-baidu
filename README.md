# sociate-baidu
百度第三方登录

# 使用方法
首先需要在 [百度开放平台](https://developer.baidu.com/) 申请账号并且创建应用。

接着打开工程项目，配置 `config/services.php` 文件，添加如下配置信息：

```
'baidu' => [
    'client_id' => env('BAIDU_KEY'),
    'client_secret' => env('BAIDU_SECRET'),
    'redirect' => env('BAIDU_REDIRECT_URI'),
],
```

接着在 `.env` 文件添加相应配置：

```
BAIDU_KEY=
BAIDU_SECRET=
BAIDU_REDIRECT_URI=
```

`BAIDU_KEY` 是你在百度创建应用的 `API Key`,
`BAIDU_SECRET` 是你创建应用的 `Secret Key`,
`BAIDU_REDIRECT_URI` 是你创建应用的回调地址。

百度开放平台第三方登录的流程如下：
用户点开百度的验证链接 > 用户授权 > 百度将页面重定向到我们设置的回调地址，并且附带 `code` 参数 > 通过 `code` 参数调用百度 `api` 换取用户的 `access_token` > 通过 `access_token` 作为参数调用获取用户资料的 `api` > 将获取到的资料保存到我们的数据库

在 `routes.php` 中添加回调地址对应的路由：

```
// 你的登录页面
Route::get('/login', 'OauthController@login');
// 回调页面
Route::get('/auth/baidu', 'OauthController@baidu');
```

创建 `OauthController` 控制器，引入对应的类：

```
namespace Firerabbit\Baidu;
```

在登录的视图页面调用 `getAuthoriteCodeUrl` 方法生成百度登录链接,将链接传入 `<a>` 标签的 `href` 参数即可,下面是简单的例子:

```
<?php

namespace App\Http\Controllers\Page;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Firerabbit\Baidu\Oauth;

class OauthController extends Controller
{
    public function login()
    {
        $baiduUrl = Oauth::getAuthoriteCodeUrl();
        echo "<a href='{$baiduUrl}'>使用百度登录</a>";
    }
}
```

样式就需要自己添加 `css` 进行修改了。

点击上面的链接后，会跳转到百度授权页面，此时用户如果点击授权登录，那么百度会重定向到我们设置的回调地址，并且在后面添加 `code` 参数，重定向后的链接如下：

```
http://huotuyouxi.com/auth/baidu?code=xxxxx
```

接着在控制器中添加回调路由的方法接收这个参数,并换取用户的 `access_token`,再通过 `access_token` 换取用户资料,完整的代码如下:

```
public function baidu(Request $request)
{
    $code = $request->code;

    $token = Oauth::getAccessToken($code);
    $info = Oauth::getUserInfo($token['access_token']);

    // 需要将用户资料保存起来
    dd(info);
}
```

以上，在控制台输入 `php artisan serve` 访问 `127.0.0.1:8000/login` 点击链接即可实现百度第三方登录了。
