window.pageLoadFiles = [
    'Form',
];

window.pageOnLoad = function () {
    $.form.manage('/mail/api/config', '#mailForm');

    $('#testMail').on('click', function () {
        $('#container').showLoading('测试中');
        $.request.postForm('/mail/api/test', {}, function (data) {
            $('#container').closeLoading();
            if (data.code === 200) {
                $.toaster.success('测试成功');
            } else {
                $.toaster.error(data.msg);
            }
        });
    });

    window.pageOnUnLoad = function () {
    };

    return false;
};
