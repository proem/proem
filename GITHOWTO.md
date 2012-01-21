_Proem_ uses the excellent _Git Branching Model_ described by Vincent Driessen
in his blog post at http://nvie.com/posts/a-successful-git-branching-model.

This document contains excerpts taken from that blog post.

Interested parties should also take a look at his awesome git-flow tool located
at https://github.com/nvie/gitflow. *Creating a feature branch*

##Feature branches

```shell
> May branch off from: develop
> Must merge back into: develop
> Branch naming convention: anything except master, develop, release-*, or hotfix-*
```

When starting work on a new feature, branch off from the develop branch.

```shell
$ git checkout -b myfeature develop
```

##Incorporating a finished feature on develop

Finished features may be merged into the develop branch definitely add them to the upcoming release:

```shell
$ git checkout develop
$ git merge --no-ff myfeature
$ git branch -d myfeature
$ git push origin develop
```

##Release branches

> May branch off from: develop
> Must merge back into: develop and master
> Branch naming convention: release-*

##Creating a release branch

Release branches are created from the develop branch. For example, say version
1.1.5 is the current production release and we have a big release coming up. The
state of develop is ready for the “next release” and we have decided that
this will become version 1.2 (rather than 1.1.6 or 2.0). So we branch off and
give the release branch a name reflecting the new version number:

```shell
$ git checkout -b release-1.2 develop
$ ./bump-version.sh 1.2
$ git commit -a -m "Bumped version number to 1.2"
```

##Finishing a release branch

```shell
$ git checkout master
$ git merge --no-ff release-1.2
$ git tag -a 1.2
$ git checkout develop
$ git merge --no-ff release-1.2
$ git branch -d release-1.2
```

##Hotfix branches

> May branch off from: master
> Must merge back into: develop and master
> Branch naming convention: hotfix-*

##Creating the hotfix branch

```shell
$ git checkout -b hotfix-1.2.1 master
$ ./bump-version.sh 1.2.1
$ git commit -a -m "Bumped version number to 1.2.1"
$ git commit -m "Fixed severe production problem"
```

##Finishing a hotfix branch

```shell
$ git checkout master
$ git merge --no-ff hotfix-1.2.1
$ git tag -a 1.2.1
$ git checkout develop
$ git merge --no-ff hotfix-1.2.1
$ git branch -d hotfix-1.2.1
```
