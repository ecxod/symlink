# Ecxod\Symlink\symlink

```sh
php composer.phar require twbs/bootstrap
php composer.phar require twbs/bootstrap-icons
php composer.phar require vlucas/phpdotenv
npm i jquery
npm i mathjax
npm i prismjs
npm i popperjs
npm i semantic-ui
```

cat .env

```sh
PROJECT_PATH="... /webroot/project"
PUBLIC=="... /webroot/project/public"
STATIC="static"
CSS="static/css/"
# CSS_PATH="... /webroot/project/public/static/css/"
# PHCSS="static/phcss"
# PHCSS_PATH="... /webroot/project/public/static/phcss"
# JS="static/js/"
# JS_PATH=".../webroot/project/public/static/js/"
# PIC="static/pic"
# IMG="static/img"
#
```
cat symlink.json

```json
{
    "node_modules": {
        "chartjs": false,
        "jquery": true,
        "mathjax": false,
        "popperjs": true,
        "prismjs": false,
        "stackblitz": true,
        "tinymce": false
    },
    "vendor": {
        "twbs/bootstrap": true,
        "twbs/bootstrap-icons": true
    }
}
```

