Kimai Time Tracking
===================

This is the repository of Kimai, a open source time tracking software
that runs on (almost) every webserver with PHP and MySQL.

You can get more information about this time-tracking software:

* at our [website](http://www.kimai.org)
* at our [forum](http://forum.kimai.org)
* in the [documentation](http://www.kimai.org/documentation/)

## Contributing

We would love to get input from all developer out there:

1. Fork Kimai or one of its [other repos][1]
2. Clone repository to your local disc
3. Create a branch (`git checkout -b my_kimai`)
4. Commit your changes to your fork. In your commit message refer to the issue number if there is already one, e.g. (`git commit -am "[BUGFIX] short description of fix (resolves #4711)"`)
5. Push to the branch (`git push origin my_kimai`)
6. Submit a [Pull Request][2] using GitHub with a link to your branch (here are some hints on [How to write the perfect pull request](https://github.com/blog/1943-how-to-write-the-perfect-pull-request))

## Keep your fork in sync with original repository

1. git remote add upstream https://github.com/kimai/kimai.git
2. git fetch upstream
3. git checkout master
4. git merge upstream/master
5. git push origin master

Here are a few rules to follow in order to ease code reviews, and discussions before maintainers accept and merge your work:

* You SHOULD write documentation.
* Please, write commit messages that make sense, and rebase your branch before submitting your Pull Request.
* One may ask you to squash your commits too. This is used to "clean" your Pull Request before merging it (we don't want commits such as fix tests, fix 2, fix 3, etc.).
* When creating your Pull Request on GitHub, you MUST write a description which gives the context and/or explains why you are creating it.

[1]: https://github.com/kimai
[2]: https://github.com/kimai/kimai/pulls

## Code Status
Issue tracker metrics: [![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/kimai/kimai.svg)](http://isitmaintained.com/project/kimai/kimai "Average time to resolve an issue") - [![Percentage of issues still open](http://isitmaintained.com/badge/open/kimai/kimai.svg)](http://isitmaintained.com/project/kimai/kimai "Percentage of issues still open")
