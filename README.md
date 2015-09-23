# 腾讯云 - 云API For PHP SDK

## 概述

本PHP SDK适用于调用腾讯云的云API。[详细接口列表点此。](http://www.qcloud.com/wiki/v2/API)

本PHP SDK特点：

* 轻量级胶水层、紧凑型设计
* 有完整的单元测试代码
* 符合PSR-4载入方式
* 仅需修改代码部分地方，即能快速转换成PSR-0载入方式以供PHP 5.2使用（namespace相关更改成类名即可）
* 支持Composer接入（```composer require horseluke/qcloud-api-sdk```）


## 协议

按惯例，使用Apache License, Version 2.0协议。

由于时间问题，代码内还没写协议注释，后面再补。


```

Copyright 2015 Horse Luke

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

```

## 系统要求

* PHP 5.3或以上（如果要运行单元测试，需要PHP 5.4或以上，因为部分单元测试使用了trait）
* PHP启用Curl扩展

## 使用方法

以下目录有使用方法：

* demo目录：是最原始的使用方法，不依赖任何载入方式
* tests目录下的所有“Example2”开头的目录：根据host进行的测试

建议为不同的腾讯云api host注册一个\QcloudApi\Base\Request类实例，方法有：

* 使用工厂模式 + 单例模式。

  - 见demo/demoFactory.php。

* 或使用依赖注入（Dependency Injection）中的Service Locator + 单例模式。

  - 有关Service Locator介绍，可以看 [Github silexphp/Pimple](https://github.com/silexphp/Pimple ) README.md中“Defining Services“部分。
  
  - （2.0.0版本开始可用）如果自己的框架没有实现，可使用SDK已经实现的简单Service Locator：\QcloudApi\Integrate\ServiceLocator。
  
    详细用法见目录/demo/Integrate/ServiceLocatorBasicUsage.php


![微信打赏](http://7xlz3z.com1.z0.glb.clouddn.com/img/git/wx_pay.jpg)
