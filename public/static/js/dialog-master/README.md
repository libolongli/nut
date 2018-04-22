# Amaze UI dialog
---

[![NPM version](https://img.shields.io/npm/v/amazeui-dialog.svg?style=flat-square)](https://www.npmjs.com/package/amazeui-dialog)
[![Dependency Status](https://img.shields.io/david/amazeui/dialog.svg?style=flat-square)](https://david-dm.org/amazeui/dialog)
[![devDependency Status](https://img.shields.io/david/dev/amazeui/dialog.svg?style=flat-square)](https://david-dm.org/amazeui/dialog#info=devDependencies)

Amaze UI Modal 插件 HTML 模板封装。

- [使用示例](http://amazeui.github.io/dialog/docs/demo.html)

**使用说明：**

1. 获取 Amaze UI dialog

  - [直接下载](https://github.com/amazeui/dialog/archive/master.zip)
  - 使用 NPM: `npm install amazeui-dialog`

2. 在 Amaze UI 样式之后引入 dialog 样式（`dist` 目录下的 CSS）：

  Amaze UI dialog 依赖 Amaze UI 样式。

  ```html
  <link rel="stylesheet" href="path/to/amazeui.min.css"/>
  ```

3. 引入 dialog 插件（`dist` 目录下的 JS）：

  ```html
  <script src="path/to/jquery.min.js"></script>
  <script src="path/to/amazeui.min.js"></script>
  <script src="path/to/amazeui.dialog.min.js"></script>
  ```

4. 调用 dialog:

  ```js
  AMUI.dialog.alert({
      title: '错误提示',
      content: '你的家还好吧',
      onConfirm: function() {
          console.log('close');
        }
  });

  AMUI.dialog.confirm({
      title: '错误提示',
      content: '正文内容',
      onConfirm: function() {
        console.log('onConfirm');
      },
      onCancel: function() {
        console.log('onCancel')
      }
  });

  var $loading = AMUI.dialog.loading();
  setTimeout(function() {
      $loading.modal('close');
  }, 3000);

  var $actions = AMUI.dialog.actions({
      title: '标题啊',
      items: [
        {content: '<a href="#"><span class="am-icon-wechat"></span> 分享到微信</a>'},
        {content: '<a href="#"><i class="am-icon-mobile"></i> 短信分享</a>'},
        {content: '<a href="#"><i class="am-icon-twitter"></i> 分享到 XX 萎跛</a>'}
      ],
      onSelected: function(index, target) {
        console.log(index);
        $actions.close();
      }
  });

  $actions.show();

  AMUI.dialog.popup({title: '标题', content: '正文'});
  ```
