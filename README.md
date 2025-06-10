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


Let us admit we have the following `package.json` file, which will install the packages `jquery` and `prismjs` into the folder `node_modules` folder.

**cat package.json**
```json
{
  "dependencies": {
    "jquery": "^3.7.1",
    "prismjs": "^1.29.0"
  }
}

```
In adition we have a `composer.json` file that contains the following `require` section, that installes the rquired packages `sentry/sentry`, `vlucas/phpdotenv`, `ecxod/funktionen`, `twbs/bootstrap`, `twbs/bootstrap-icons` into the `vendor` folder.   

**cat composer.json**
```json
    [...]
    "require": {
        "php": "^7.2|^7.4|^8.0|^8.2",
        "sentry/sentry": "^4.8",
        "vlucas/phpdotenv": "^5.6",
        "twbs/bootstrap": "^5.3",
        "twbs/bootstrap-icons": "^1.13"
    },
    [...]
```

What we need to know is which folders inside the `vendor` and `node_modules` must be exposed to the internet. This can be done in two ways, by copying them to the `public` folder, what is suboptimal, because these files inside the folders that have to be exposed, are sometimes depending on files inside the respective modules. The other posibility is to create a symbolic link. 

### tree -d /vendor
```sh
  vendor
    ├── composer
    [...]
    ├── sentry
    │   └── sentry
    ├── twbs
    │   ├── bootstrap
    │   │   └── dist 	<== must be exposed
    │   └── bootstrap-icons
    │       ├── font 	<== must be exposed
    │       └── icons 	<== must be exposed
    └── vlucas
        └── phpdotenv
```
In case of the `mode_modules` folder the situation is not much different, and we need to know what folders need to get exposed to the internet. In case of `jquery` the developer has excelently designed a `dist` folder, that is ment to be exposed, but this is not always the case. The `prismjs` is one of this chaotic projects that has the files that need to be exposed to the internet distributed in the whole moduke folder. In such cases we recommend to expose the whole module folder to the internet. 

### tree -d /node_modules
```sh
node_modules
    ├── jquery
    │   └── dist	    <== must be exposed
    └── prismjs	        <== must be exposed
        ├── components
        ├── plugins
        └── themes
```

### **IMPORTANT** 1. Not all folders inside `vendor` and `node_modules` folder must be linked to the public folder. Please carefully read the README Files insed your required libraries. 2. It would be nice if the library developer call the folders that are supposed to be linked to the public folder something like `dist`, what could allow us to create the links in a fiully automatic way. Until then it is your decision. 

In this context we have developed a library, that is able to create the necessary symlinks from the library folders `vendor` and `node_modules` to the `public` folder as webserver user (e.g. `www-data`). This small detail is important because the webserver must be able to follew the symlinks to the library folder and read the files there.

When you first run the library it will create and contiusly update a file called `symlink-example.json`, which should show serve you as a template or pattern to create a `symlink.json` that is used by the library to create the necessary links for your project. 

### cat symlink-example.json
```json
{
    "node_modules": {
        "chartjs": false,
        "jquery/dist": false,
        "mathjax": false,
        "popperjs": false,
        "prismjs":false,
        "stackblitz": false,
        "tinymce": false
    },
    "vendor": {
        "twbs/bootstrap": false,
        "twbs/bootstrap-icons":false
    }
}
```

If you are used to the `json` file structure, then all you need to know, is, that the desired link is in our case nested inside the json as a array like :  ["source"=>"destination"], where the source is the library folder, and the destination is the public folder, if you remove the array, empty it, or replace it with `false`, then the link will be removed, as soon as the library runns next time.

### cat symlink.json
```json
{
    "node_modules": {
        "chartjs": false,
        "jquery/dist": [ "dist" => "public/static/@jquery/dist" ],
        "mathjax": false,
        "popperjs": false,
        "prismjs": [ "" => "public/static/prismjs" ],
        "stackblitz": false,
        "tinymce": false
    },
    "vendor": {
        "twbs/bootstrap": [ "dist" => "public/static/bs/dist" ],
        "twbs/bootstrap-icons": [ 
            "fonts" =>"public/static/bs/font", 
            "icons"=> "public/static/bs/icons"
        ]
    }
}
```

