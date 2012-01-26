# Open ER2 Client API

This is an open Contao extension repository 2 client api,
that can be used to manage Contao extensions in an Ccontao installation.

The api is designed to *not* require the Contao framework,
but it may be used within Contao extensions.

## Requirements

- PHP 5.3 (namespaces and anonymous functions)
- PDO
- Monolog (https://github.com/Seldaek/monolog)

## Repository data

This client work with a local copy of the er3 repository data.
The local repository data can be synchronised by calling Repositry::syncRepository().
A initial synchronisation takes a lot of time, after this every additional sync is only incremental.
To shorten the initial synchronisation, a daily static dump is available at:

- http://contao.infinitysoft.de/open_er2/repository.en.bin - english repository information
- http://contao.infinitysoft.de/open_er2/repository.de.bin - german repository information
- http://contao.infinitysoft.de/open_er2/dependencies.bin - dependency information

This files contains serialized arrays of the database rows.

If you want to create your own static dump, in your own languages, have a look on the bin/staticRepository shell script.
