---
title: Amaze UI dialog 使用演示
prev: ../
---

### 显示 Alert 窗口

`````html
<buttong class="am-btn am-btn-primary js-alert">点击显示 Alert</buttong>

<script>
$(function() {
  $('.js-alert').on('click', function() {
    AMUI.dialog.alert({
      title: 'Alert 窗口',
      content: 'Hello world.',
      onConfirm: function() {
        console.log('close');
      }
    });
  });
});
</script>
`````

```html
<buttong class="am-btn am-btn-primary js-alert">点击显示 Alert</buttong>

<script>
  $('.js-alert').on('click', function() {
    AMUI.dialog.alert({
      title: 'Alert 窗口',
      content: 'Hello world.',
      onConfirm: function() {
        console.log('close');
      }
    });
  });
</script>
```
<script src="../amazeui.dialog.js"></script>
