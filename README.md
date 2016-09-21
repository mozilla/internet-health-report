# Internet Health Report

## Usage

Internet Health Report uses the [Gulp Starter](https://github.com/vigetlabs/gulp-starter) build system. A comprehensive explanation of the tasks and configuration settings can be found in that project's [Readme](https://github.com/vigetlabs/gulp-starter/blob/master/README.md).

### Install Dependencies

Make sure Node is installed. This has been tested on Node `6.6.0`.

```bash
npm install
```

### Run development tasks:
```
npm start
```

This runs the default gulp task, which starts compiling, watching, and live updating files as they are changed. BrowserSync will start a server on port 3000. You'll be able to see live changes in all connected browsers.

## Localisation

Localised text content is managed in individual `content.properties` files within a language specific directory:

```
src/html/_locales/en-us
```

The HTML task compiles these with `.html` markup files to build seperate language directories.