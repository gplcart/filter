[![Build Status](https://scrutinizer-ci.com/g/gplcart/filter/badges/build.png?b=master)](https://scrutinizer-ci.com/g/gplcart/filter/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gplcart/filter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gplcart/filter/?branch=master)

Filter is a [GPL Cart](https://github.com/gplcart/gplcart) module that adds advanced HTML filters to protect your users against XSS vulnerabilities and automatically correct malformed HTML. Based on the [HTML Purifier](https://github.com/ezyang/htmlpurifier) library.

**Installation**

This module requires 3-d party library which should be downloaded separately. You have to use [Composer](https://getcomposer.org) to download all the dependencies.

1. From your web root directory: `composer require gplcart/filter`. If the module was downloaded and placed into `system/modules` manually, run `composer update` to make sure that all 3-d party files are presented in the `vendor` directory.
2. Go to `admin/module/list` end enable the module
3. Go to `admin/user/role` and grant permissions to edit filter settings
3. Assign user roles on `admin/module/settings/filter`

