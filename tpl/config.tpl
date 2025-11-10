<title id="title">邮件配置 - {$title}</title>
<style id="style">
    .action-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }
    mdui-card {
        width: 100%;
    }
    .test-button {
        margin-left: 8px;
    }
    .config-section {
        margin-bottom: 24px;
    }
    .config-title {
        font-size: 18px;
        font-weight: 500;
        margin-bottom: 16px;
        color: var(--mdui-color-primary);
    }
</style>

<div id="container" class="container">
    <div class="row col-space16 p-4">
        <div class="col-xs12 title-large center-vertical mb-4">
            <mdui-icon name="mail" class="mr-2"></mdui-icon>
            <span>邮件配置</span>
        </div>
        <div class="col-xs12">
            <div class="config-section">
                <div class="config-title">SMTP服务器配置</div>
                <form class="row col-space16" id="mailForm">
                    <div class="col-md12">
                        <mdui-text-field
                            label="SMTP服务器"
                            name="host"
                            variant="outlined"
                            required
                            helper="例如: smtp.qq.com"
                        ></mdui-text-field>
                    </div>
                    <div class="col-md6">
                        <mdui-text-field
                            label="端口"
                            name="port"
                            type="number"
                            variant="outlined"
                            required
                            helper="例如: 465"
                        ></mdui-text-field>
                    </div>
                    <div class="col-md6">
                        <mdui-text-field
                            label="邮箱用户名"
                            name="username"
                            type="email"
                            variant="outlined"
                            required
                            helper="完整的邮箱地址"
                        ></mdui-text-field>
                    </div>
                    <div class="col-md12">
                        <mdui-text-field
                            label="邮箱密码"
                            name="password"
                            type="password"
                            variant="outlined"
                            required
                            helper="邮箱密码或授权码"
                        ></mdui-text-field>
                    </div>
                    <div class="col-md12">
                        <mdui-text-field
                            label="发件人名称"
                            name="site"
                            variant="outlined"
                            helper="显示的发件人名称"
                        ></mdui-text-field>
                    </div>
                    <div class="col-md12">
                        <mdui-text-field
                            label="默认邮件接收人"
                            name="defaultRecipient"
                            type="email"
                            variant="outlined"
                            helper="默认的邮件接收人邮箱地址（可选）"
                        ></mdui-text-field>
                    </div>
                    <div class="col-md12 action-buttons">
                        <mdui-button id="saveMail" icon="save" type="submit">
                            保存配置
                        </mdui-button>
                        <mdui-button id="testMail" icon="send" class="test-button">
                            测试邮件
                        </mdui-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script id="script">
    window.pageLoadFiles = [
        'Form'
    ];

    window.pageOnLoad = function (loading) {

        $.form.manage("/mail/config","#mailForm");


        // 处理渠道状态开关变化
        $("#testMail").on("click", function() {
            $('#container').showLoading('测试中');
            $.request.postForm("/mail/test", {

            },function (data) {
                $('#container').closeLoading();
                if (data.code === 200){
                    $.toaster.success("测试成功")
                }else{
                    $.toaster.error(data.msg)
                }

            });
        });

        window.pageOnUnLoad = function () {
            // 页面卸载时的清理工作
        };

    };
</script>