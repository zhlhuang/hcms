window.__vueCommon = {
    filters: {
        floatNumber(value) {
            return parseFloat(value).toFixed(2)
        }
    },
    methods: {
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
                    success: (res) => {
                        if (!res.status) {
                            this.$message.error(res.msg)
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
                });
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
                    success: (res) => {
                        if (!res.status) {
                            this.$message.error(res.msg)
                        }
                        setTimeout(function () {
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
                });
            })
        }
    }
};

(function (vue) {
    //引入vue mixin
    vue.mixin(window.__vueCommon);
})(window.Vue);