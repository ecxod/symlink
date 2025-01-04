# Ecxod\Symlink\symlink

```sh
php composer.phar require twbs/bootstrap
php composer.phar require twbs/bootstrap-icons
php composer.phar require vlucas/phpdotenv
npm i jquery
npm i mathjax
npm i prismjs
npm i popperjs
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
# if not empty or not empty : 
SYMLINK='{
    "twbs/bootstrap":"y",
    "twbs/bootstrap-icons":"",
    "jquery":"y",
    "prismjs":"",
    "mathjax":"",
    "popperjs":"",
    "tinymce":"",
    "chartjs":""
}'
```
