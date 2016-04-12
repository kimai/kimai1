Contributing
===================

We would love to get input from all developers out there!

We develop new stuff utilizing the [Gitflow Workflow](https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow) with minor adjustments.
The decision about it was discussed [here](https://github.com/kimai/kimai/issues/584), if you have questions go ahead and post them there! 

## Quick start

1. Fork Kimai or one of its [other repos][1]
2. Clone repository to your local disc
3. Checkout develop (`git checkout -b develop origin/develop`)
4. Create a branch for your feature (`git checkout -b my_feature`)
5. Commit the changes to your fork. In your commit message refer to the issue number if there is already one, e.g. (`git commit -am "fixed this and that (resolves #0815)"`)
6. Push your branch (`git push origin my_feature`)
7. Submit a [Pull Request][2] using GitHub with a link to your branch (here are some hints on [How to write the perfect pull request](https://github.com/blog/1943-how-to-write-the-perfect-pull-request))

## Keep your fork in sync with original repository

1. git remote add upstream https://github.com/kimai/kimai.git
2. git fetch upstream
3. git checkout develop
4. git merge upstream/develop
5. git push origin develop

## Pull request notes

Here are a few rules to follow in order to ease code reviews and discussions before maintainers accept and merge your work:

* You SHOULD write documentation.
* Please, write commit messages that make sense, and rebase your branch before submitting your Pull Request.
* One may ask you to squash your commits too. This is used to "clean" your Pull Request before merging it (we don't want commits such as fix tests, fix 2, fix 3, etc.).
* When creating your Pull Request on GitHub, you MUST write a description which gives the context and/or explains why you are creating it.

## Important things to consider

* Follow PSR-2 code style
* No whitespace changes in a Pull request
* We know that a lot of the Kimai codebase is old and "funny" ... you can ignore that ;-)
* You can restructure the code, but that should be done in an independent branch
* Make sure the Pull request has a decent size, otherwise we won't be able to review and merge it

[1]: https://github.com/kimai
[2]: https://github.com/kimai/kimai/pulls
