[![Build Status](https://scrutinizer-ci.com/g/gplcart/filter/badges/build.png?b=master)](https://scrutinizer-ci.com/g/gplcart/filter/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gplcart/filter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gplcart/filter/?branch=master)

Filter is a [GPL Cart](https://github.com/gplcart/gplcart) module that adds advanced HTML filters to protect your users against XSS vulnerabilities and automatically correct malformed HTML. Based on the [HTML Purifier](https://github.com/ezyang/htmlpurifier) library.

**Installation**

1. Download and extract to `system/modules` manually or using composer `composer require gplcart/filter`. IMPORTANT: If you downloaded the module manually, be sure that the name of extracted module folder doesn't contain a branch/version suffix, e.g `-master`. Rename if needed.
2. Go to `admin/module/list` end enable the module
3. Go to `admin/user/role` and grant permissions to edit filter settings
3. Assign user roles on `admin/module/settings/filter`

