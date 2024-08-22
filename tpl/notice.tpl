<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>通知邮件</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #dddddd;
        }
        .header img {
            max-width: 150px;
            margin-right: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333333;
        }
        .content {
            padding: 20px 0;
        }
        .highlight {
            background-color: #ffffcc;
            padding: 10px;
            border-radius: 4px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            border-top: 1px solid #dddddd;
            color: #777777;
            font-size: 12px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{$logo}" alt="网站Logo">
        <h1>{$site}</h1>
    </div>
    <div class="content">
        {$content}
    </div>
    <div class="footer">
        <p>© {date("Y")} {$site}. 保留所有权利。</p>
        <p>Ankio | 联系我们: <a href="mailto:ankio@ankio.net">ankio@ankio.net</a></p>
    </div>
</div>
</body>
</html>
