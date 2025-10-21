window.__vueCommon = {
    filters: {
        floatNumber(value) {
            return parseFloat(value).toFixed(2)
        }
    }, methods: {
        objectToQueryParams(data) {
            const params = new URLSearchParams();

            // 遍历对象的每个属性
            for (const key in data) {
                // 确保只处理对象自身的属性
                if (data.hasOwnProperty(key)) {
                    const value = data[key];

                    // 处理数组类型的值（会转换为 key=value1&key=value2 形式）
                    if (Array.isArray(value)) {
                        value.forEach(item => params.append(key, item));
                    } else {
                        params.append(key, value);
                    }
                }
            }

            // 转换为 query 字符串（例如 "name=John&age=30"）
            return params.toString();
        },
        /**
         * 打开新的子窗口
         * @param title
         * @param url
         */
        openNewFrame(title, url) {
            if (typeof window.parent.__adminOpenNewFrame === 'function') {
                window.parent.__adminOpenNewFrame({title, url})
            }
        }, closeFrame(url) {
            if (typeof window.parent.__adminCloseFrame === 'function') {
                window.parent.__adminCloseFrame(url)
            }
        }, refreshFrame(url) {
            if (typeof window.parent.__adminRefreshFrame === 'function') {
                window.parent.__adminRefreshFrame(url)
            }
        }, /**
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
        }, /**
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
        }, encryptData: function (data) {
            if (API_ENCODE) {
                //开启了api 参数加密
                let key = CryptoJS.enc.Utf8.parse(KEY);
                // console.log('data', JSON.stringify(data))
                let encrypted_data = CryptoJS.AES.encrypt(JSON.stringify(data), key, {
                    mode: CryptoJS.mode.ECB, padding: CryptoJS.pad.Pkcs7
                }).toString()
                // console.log('encrypted_data', encrypted_data)
                return {data: encrypted_data, is_encrypt: true}
            }
            return data
        }, decryptRes: function (res) {
            if (API_ENCODE) {
                //开启了api加密，就需要解密处理
                let {data = '', is_encrypt = false} = res
                if (!is_encrypt) {
                    return res;
                }
                let key = CryptoJS.enc.Utf8.parse(KEY);
                let decrypted = CryptoJS.AES.decrypt(data, key, {
                    mode: CryptoJS.mode.ECB, padding: CryptoJS.pad.Pkcs7
                }).toString(CryptoJS.enc.Utf8);
                if (!decrypted) {
                    this.$message.error('数据获取异常！');
                    return res
                }
                res = JSON.parse(decrypted)
            }
            return res
        }, handleRes(loadingInstance, resolve, reject) {
            return {
                success: (res) => {
                    res = this.decryptRes(res)
                    if (!res.status) {
                        this.$message.error(res.msg)
                    }
                    if (res.code === 401) {
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
                }, complete: () => {
                    if (loadingInstance !== false) {
                        loadingInstance.close()
                    }
                }
            }
        }, async handleFetchRes(response) {
            if (response.status === 302) {
                //未登录跳转
                setTimeout(function () {
                    location.href = "/admin/index/index"
                }, 1500)
            }
            let res = await response.json()
            res = this.decryptRes(res)
            if (!res.status) {
                this.$message.error(res.msg)
            }
            if (res.code === 401) {
                setTimeout(function () {
                    location.href = "/admin/index/index"
                }, 1500)
            }
            return res
        }, httpGet: function (url, data, loading = true, loadingTarget = '.loading') {
            let loadingInstance = loading ? this.$loading({
                target: loadingTarget
            }) : false
            let query = this.objectToQueryParams(data)
            if (query) {
                url = url + "?" + query
            }
            return fetch(url, {
                headers: {
                    // 'Authorization': 'Bearer ' + token, // 示例：添加token
                    'Accept': 'application/json' // 告知服务器期望接收 JSON 响应
                }, // 可选：若需跨域携带 Cookie，添加 credentials（原 ajax 的 xhrFields: {withCredentials: true}）
                // credentials: 'include'
            }).then(response => {
                loadingInstance.close()
                return this.handleFetchRes(response)
            })
        }, httpPost: function (url, data, is_encrypt = true, loading = true, loadingTarget = '.loading') {
            if (is_encrypt) {
                data = this.encryptData(data)
            }
            let loadingInstance = loading ? this.$loading({
                target: loadingTarget
            }) : false
            return fetch(url, {
                method: 'POST', // GET 可省略（fetch 默认 method 是 GET）
                body: JSON.stringify(data),
                headers: {
                    // 'Authorization': 'Bearer ' + token, // 示例：添加token
                    'content-type': "application/json",
                    'Accept': 'application/json' // 告知服务器期望接收 JSON 响应
                }, // 可选：若需跨域携带 Cookie，添加 credentials（原 ajax 的 xhrFields: {withCredentials: true}）
                // credentials: 'include'
            }).then(response => {
                loadingInstance.close()
                return this.handleFetchRes(response)
            })
            // return new Promise((resolve, reject) => {
            //     let loadingInstance = loading ? this.$loading({
            //         target: loadingTarget
            //     }) : false
            //     $.ajax({
            //         url: url,
            //         type: 'POST',
            //         data: JSON.stringify(data),
            //         contentType: 'application/json',
            //         dataType: 'json', ...this.handleRes(loadingInstance, resolve, reject)
            //     });
            // })
        }, httpPut: function (url, data, is_encrypt = true, loading = true, loadingTarget = '.loading') {
            if (is_encrypt) {
                data = this.encryptData(data)
            }
            // console.log("is_encrypt", is_encrypt, data)
            let loadingInstance = loading ? this.$loading({
                target: loadingTarget
            }) : false
            return fetch(url, {
                method: 'PUT', // GET 可省略（fetch 默认 method 是 GET）
                body: JSON.stringify(data),
                headers: {
                    // 'Authorization': 'Bearer ' + token, // 示例：添加token
                    'content-type': "application/json",
                    'Accept': 'application/json' // 告知服务器期望接收 JSON 响应
                }, // 可选：若需跨域携带 Cookie，添加 credentials（原 ajax 的 xhrFields: {withCredentials: true}）
                // credentials: 'include'
            }).then(response => {
                loadingInstance.close()
                return this.handleFetchRes(response)
            })
            // return new Promise((resolve, reject) => {
            //     let loadingInstance = loading ? this.$loading({
            //         target: loadingTarget
            //     }) : false
            //     $.ajax({
            //         url: url,
            //         type: 'PUT',
            //         data: JSON.stringify(data),
            //         contentType: 'application/json',
            //         dataType: 'json', ...this.handleRes(loadingInstance, resolve, reject)
            //     });
            // })
        }, httpDelete: function (url, data, loading = true, loadingTarget = '.loading') {
            let loadingInstance = loading ? this.$loading({
                target: loadingTarget
            }) : false
            return fetch(url, {
                method: 'DELETE', // GET 可省略（fetch 默认 method 是 GET）
                body: JSON.stringify(data),
                headers: {
                    // 'Authorization': 'Bearer ' + token, // 示例：添加token
                    'content-type': "application/json",
                    'Accept': 'application/json' // 告知服务器期望接收 JSON 响应
                }, // 可选：若需跨域携带 Cookie，添加 credentials（原 ajax 的 xhrFields: {withCredentials: true}）
                // credentials: 'include'
            }).then(response => {
                loadingInstance.close()
                return this.handleFetchRes(response)
            })
            // return new Promise((resolve, reject) => {
            //     let loadingInstance = loading ? this.$loading({
            //         target: loadingTarget
            //     }) : false
            //     $.ajax({
            //         url: url,
            //         type: 'DELETE',
            //         data: JSON.stringify(data),
            //         contentType: 'application/json',
            //         dataType: 'json', ...this.handleRes(loadingInstance, resolve, reject)
            //     });
            // })
        }
    }
};

// (function (vue) {
//     //引入vue mixin
//     vue.mixin(window.__vueCommon);
// })(window.Vue);