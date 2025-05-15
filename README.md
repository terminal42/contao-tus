
# terminal42/contao-tus

`terminal42/contao-tus` is an extension for the [Contao CMS](https://contao.org).
Adds the tus resumable upload protocol to Contao Open Source CMS

> [!CAUTION]
> This package is experimental and not ready for use!

## Installation

Choose the installation method that matches your workflow!

### Installation via Contao Manager

Search for `terminal42/contao-tus` in the Contao Manager and add it 
to your installation. Apply changes to update the packages.

### Manual installation

Add a composer dependency for this bundle. Therefore, change in the project root and run the following:

```bash
composer require terminal42/contao-tus
```

Depending on your environment, the command can differ, i.e. starting with `php composer.phar …` if you do not have
composer installed globally.

Then, update the database via the `contao:migrate` command or the Contao install tool.


## License

This bundle is released under the [MIT license](LICENSE)
