<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>通知邮件</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">
<div class="container" style="    box-sizing: border-box;width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
    <div class="header" style="display: flex; align-items: center; padding: 10px 0; border-bottom: 1px solid #dddddd;">
        <img src="{$logo}" alt="网站Logo" style="width: 40px;height: 40px; margin-right: 10px;">
        <h1 style="margin: 0; font-size: 24px; color: #333333;">{$site}</h1>
    </div>
    <div class="content" style="padding: 20px 0;">
        {$content nofilter}
    </div>
    <div class="footer" style="text-align: center; padding: 20px 0; border-top: 1px solid #dddddd; color: #777777; font-size: 12px;">
        <p>&copy; {date("Y")} {$site}. 保留所有权利。</p>
        <p>Ankio | 联系我们: <a href="mailto:ankio@ankio.net">ankio@ankio.net</a></p>
    </div>
</div>
</body>
</html>
