window.__vueCommon = {
    filters: {
        floatNumber(value) {
            return parseFloat(value).toFixed(2)
        }
    },
    methods: {
        /**
         * 打开新的子窗口
         * @param title
         * @param url
         */
        openNewFrame(title, url) {
            if (typeof window.parent.__adminOpenNewFrame === 'function') {
                window.parent.__adminOpenNewFrame({title, url})
            }
        },
        /**
         * URL 解析
         * @param url
         * @returns {{protocol: string, hostname: string, search: ({}|{}), host: string, hash: string, pathname: string}}
         */
        parserUrl: function (url) {
            var a = document.createElement('a');
            a.href = url;
            var search = function (search) {
                if (!search) return {};
                var ret = {};
                search = search.slice(1).split('&');
                for (var i = 0, arr; i < search.length; i++) {
                    arr = search[i].split('=');
                    var key = arr[0], value = arr[1];
                    if (/\[\]$/.test(key)) {
                        ret[key] = ret[key] || [];
                        ret[key].push(value);
                    } else {
                        ret[key] = value;
                    }
                }
                return ret;
            };

            return {
                protocol: a.protocol,
                host: a.host,
                hostname: a.hostname,
                pathname: a.pathname,
                search: search(a.search),
                hash: a.hash
            }
        },
        /**
         * 获取url参数
         * @param variable
         * @param default_value
         * @returns {{}|*|string}
         */
        getUrlQuery: function (variable = '', default_value = '') {
            var urlObj = this.parserUrl(window.location.href);
            if (variable === '') {
                return urlObj && urlObj.search
            }
            return urlObj && urlObj.search && urlObj.search[variable] ? urlObj.search[variable] : default_value;
        },
        decryptRes: function (res) {
            if (API_ENCODE) {
                //开启了api加密，就需要解密处理
                let {data = '', is_encrypt = false} = res
                if (!is_encrypt) {
                    return res;
                }
                let key = CryptoJS.enc.Utf8.parse(KEY);
                let decrypted = CryptoJS.AES.decrypt(data, key, {
                    mode: CryptoJS.mode.ECB,
                    padding: CryptoJS.pad.Pkcs7
                }).toString(CryptoJS.enc.Utf8);
                if (!decrypted) {
                    this.$message.error('数据获取异常！');
                    return res
                }
                res = JSON.parse(decrypted)
            }
            return res
        },
        handleRes(loadingInstance, resolve, reject) {
            return {
                success: (res) => {
                    res = this.decryptRes(res)
                    if (!res.status) {
                        this.$message.error(res.msg)
                    }
                    if (res.code === 501) {
                        setTimeout(function () {
                            location.href = "/admin/index/index"
                        }, 1500)
                    }
                    setTimeout(() => {
                        resolve(res);
                    }, 300)
                }, error: (err) => {
                    this.$message.error('系统繁忙，请稍后再试。');
                    reject(err)
                },
                complete: () => {
                    if (loadingInstance !== false) {
                        loadingInstance.close()
                    }
                }
            }
        },
        httpGet: function (url, data, loading = true, loadingTarget = '.loading') {
            return new Promise((resolve, reject) => {
                let loadingInstance = loading ? this.$loading({
                    target: loadingTarget
                }) : false
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: data,
                    dataType: 'json',
                    ...this.handleRes(loadingInstance, resolve, reject)
                })
            })
        },
        httpPost: function (url, data, loading = true, loadingTarget = '.loading') {
            return new Promise((resolve, reject) => {
                let loadingInstance = loading ? this.$loading({
                    target: loadingTarget
                }) : false
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    ...this.handleRes(loadingInstance, resolve, reject)
                });
            })
        },
        httpPut: function (url, data, loading = true, loadingTarget = '.loading') {
            return new Promise((resolve, reject) => {
                let loadingInstance = loading ? this.$loading({
                    target: loadingTarget
                }) : false
                $.ajax({
                    url: url,
                    type: 'PUT',
                    data: data,
                    dataType: 'json',
                    ...this.handleRes(loadingInstance, resolve, reject)
                });
            })
        },
        httpDelete: function (url, data, loading = true, loadingTarget = '.loading') {
            return new Promise((resolve, reject) => {
                let loadingInstance = loading ? this.$loading({
                    target: loadingTarget
                }) : false
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: data,
                    dataType: 'json',
                    ...this.handleRes(loadingInstance, resolve, reject)
                });
            })
        }
    }
};

// (function (vue) {
//     //引入vue mixin
//     vue.mixin(window.__vueCommon);
// })(window.Vue);