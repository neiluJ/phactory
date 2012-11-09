# Phactory

Phar archive maker for PHP projects.

## Download

Release is in the [bin](http://github.com/neiluJ/phactory/tree/master/bin) folder.

* [Download here](https://github.com/neiluJ/phactory/blob/master/bin/phactory.phar?raw=true) (direct download)

## Usage

```
$ php phactory.phar <project directory> <phar name>
```

More informations/details are availables in ```--help```:

```
$ php phactory.phar make --help
Usage:
 make [-f|--format="..."] [-c|--compression[="..."]] [--vendors] [-s|--stub[="..."]] directory pharName

Arguments:
 directory             Path to project directory
 pharName              Name for the phar archive (without extension)

Options:
 --format (-f)         Phar format. Could be phar, tar or zip (default: "phar")
 --compression (-c)    Phar compression. Could be none, gz or bz2
 --vendors             include the 'vendor' directory
 --stub (-s)           Phar stub file.
```

## Install via Composer

You can install Phactory via Composer by requiring ```neiluj/phactory```.

# License & Credits

Phactory is licensed under the 3-clauses BSD license. Please read LICENSE and CREDITS for full details.