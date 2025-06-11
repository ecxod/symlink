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


Let us assume we have the following `package.json` file, which installs the packages `jquery` and `prismjs` into the `node_modules` folder.

**cat package.json**
```json
{
  "dependencies": {
    "jquery": "^3.7.1",
    "prismjs": "^1.29.0"
  }
}

```

In addition, we have a `composer.json` file that contains the following `require` section, which installs the required packages `sentry/sentry`, `vlucas/phpdotenv`, `twbs/bootstrap`, and `twbs/bootstrap-icons` into the `vendor` folder.

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

We need to determine which folders inside the `vendor` and `node_modules` directories must be exposed to the internet. This can be done in two ways: by copying them to the `public` folder, which is suboptimal because files in the exposed folders sometimes depend on other files within their respective modules, or by creating a symbolic link.

### tree -d /vendor
```sh
vendor
    ├── composer
    [...]
    ├── sentry
    │   └── sentry
    ├── twbs
    │   ├── bootstrap
    │   │   └── dist    <== must be exposed
    │   └── bootstrap-icons
    │       ├── font    <== must be exposed
    │       └── icons   <== must be exposed
    └── vlucas
        └── phpdotenv
```

In the case of the `node_modules` folder, the situation is similar, and we need to identify which folders must be exposed to the internet. For `jquery`, the developer has excellently designed a `dist` folder that is meant to be exposed, but this is not always the case. `prismjs` is one of those chaotic projects where the files that need to be exposed are scattered throughout the module folder. In such cases, we recommend exposing the entire module folder to the internet.

### tree -d /node_modules
```sh
node_modules
    ├── jquery
    │   └── dist        <== must be exposed
    └── prismjs         <== must be exposed
        ├── components
        ├── plugins
        └── themes
```

### **IMPORTANT** 
1. Not all folders inside the `vendor` and `node_modules` directories need to be linked to the `public` folder. Please carefully read the README files of your required libraries. 
2. It would be ideal if library developers named the folders intended for public exposure something like `dist`, which would allow us to create links fully automatically. Until then, this decision is up to you.

In this context, we have developed a library that creates the necessary symlinks from the `vendor` and `node_modules` folders to the `public` folder as the web server user (e.g., `www-data`). This detail is important because the web server must be able to follow the symlinks to the library folders and read the files there.

When you first run the library, it creates and continuously updates a file called `symlink-example.json`, which serves as a template for creating a `symlink.json` file used by the library to generate the necessary links for your project.

### cat symlink-example.json
```json
{
    "require": {
        "php": false,
        "sentry/sentry": false,
        "vlucas/phpdotenv": false,
        "twbs/bootstrap": false,
        "twbs/bootstrap-icons": false
    },
    "dependencies": {
        "jquery": false,
        "prismjs": false
    }
}
```

If you are familiar with the JSON file structure, all you need to know is that the desired link is nested in the JSON as an JSON like: `{ "source" : "destination" }`, where the source is the library folder and the destination is the public folder. If you remove the array, empty it, or replace it with `false`, the link will be removed the next time the library runs.

### cat symlink.json
```json
{
    "require": {
        "php": false,
        "sentry/sentry": false,
        "vlucas/phpdotenv": false,
        "twbs/bootstrap": {
            "dist": "public/static/bs/dist"
        },
        "twbs/bootstrap-icons": {
            "font": "public/static/bs/font",
            "icons": "public/static/bs/icons"
        }
    },
    "dependencies": {
        "jquery": {
            "dist": "public/static/@jquery/dist"
        },
        "prismjs": {
            "": "public/static/prismjs"
        }
    }

}
```
