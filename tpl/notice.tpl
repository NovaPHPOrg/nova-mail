<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{$site} 通知邮件</title>
</head>
<body style="margin:0;padding:15px;background:#f4f4f4;font-family:'PingFang SC','Microsoft YaHei','Hiragino Sans GB','Helvetica Neue','Helvetica','Arial',sans-serif;line-height:1.75;font-size:16px;color:#2b2b2b;">
<!-- 外层卡片容器 -->
<div style="background:#fff;border-radius:4px;box-shadow:0 2px 8px rgba(0,0,0,.15);max-width:700px;margin:50px auto;overflow:hidden;">
    <!-- 顶部双色条 -->
    <div style="position:relative;height:3px;background:#2984ef;">
        <div style="position:absolute;right:0;top:0;width:80px;height:3px;background:#8bd5ff;"></div>
    </div>

    <!-- Header：Logo + 站点名 -->
    <div style="display:flex;align-items:center;padding:30px 50px 0;">
        <img src="{$logo}" alt="{$site} Logo" style="width:40px;height:40px;border-radius:4px;margin-right:12px;">
        <h1 style="margin:0;font-size:24px;color:#111827;">{$site}</h1>
    </div>

    <!-- 正文：仍使用占位变量 -->
    <div style="padding:20px 50px 0;word-break:break-all;">
        {$content nofilter}
    </div>

    <!-- Footer -->
    <div style="font-size:12px;color:#6b7280;text-align:center;padding:30px 0;margin-top:60px;border-top:1px solid #e5e7eb;">
        <p style="margin:0 0 4px;">&copy; {date("Y")} {$site}. 保留所有权利。</p>
        <p style="margin:0;">Ankio | 联系我们: <a href="mailto:ankio@ankio.net" style="color:#2563eb;text-decoration:none;">ankio@ankio.net</a></p>
    </div>
</div>
</body>
</html>
