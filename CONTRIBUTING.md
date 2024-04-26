# Participate in development

MineAdmin is an open source project and we welcome anyone to participate in its development. If you would like to participate in development, please read the following.

## Repository address

Currently the source code repository is hosted on Github, and the front-end and back-end source code in the gitee repository is used as a mirror repository. The code is automatically synchronized every day. We do not support any kind of code submission.
However, [documentation](https://doc.mineadmin.com/) is maintained on [code cloud repository](https://gitee.com/mineadmin/mineadmin-doc).

### Github

* [MineAdmin back-end source code](https://github.com/mineadmin/mineadmin)
* [MineAdmin front-end source code](https://github.com/mineadmin/mineadmin-vue)
* [MineAdmin kernel components](https://github.com/mineadmin/components)

### Gitee

* [MineAdmin Documentation](https://gitee.com/mineadmin/mineadmin-doc)
* [MineAdmin backend source code](https://gitee.com/mineadmin/mineadmin)
* [MineAdmin front-end source code](https://gitee.com/mineadmin/mineadmin-vue)


## What you can do

### Follow [issues](https://github.com/mineadmin/mineadmin/issues) dynamics

* We will release some pending features in issues, if you are interested, you can leave a comment in issues, we will reply as soon as possible.
* Comment replies help users who ask questions;
* According to the content of [issues](https://github.com/mineadmin/mineadmin/issues), propose a reasonable solution; go to fix the bug or realize the function, and take [pull request](https://github.com/mineadmin/ mineadmin/pulls) to the MineAdmin repository.
* Keep an eye on the progress and status of your own Pull Requests, in order to push your Pull Requests to be merged into the main repository as soon as possible;
* Conduct Code Review on other people's Pull Requests, and give your suggestions and opinions.
* Develop independent functional components based on others' or your own requirements;
* Improve the [documentation](https://gitee.com/mineadmin/mineadmin-doc) to provide better usage instructions.

### Pull Request Guidelines

Although we regularly release features for development, you are more than welcome to suggest features that you would like to implement. You can submit your ideas in [issues](https://github.com/mineadmin/mineadmin/issues) and we will reply as soon as possible whether we accept them or not.
Before submitting an issue, please check if a similar issue has already been posted.

* fork this repository to your Github account;
* The format of the commit message should be [File Name]: Info about commit. (e.g.) README.md: Fix xxx bugs
* Run `composer cs-fix` to format your code before committing it;
* Run `composer an` for static code checking before committing;
* Run `composer test` before submitting code.
* Make sure to create the PR as your functional branch, rather than committing changes directly on the master branch.
* If your PR fixes a bug, please provide a description of the bug.

> 以下是中文说明
---

# 参与开发

MineAdmin 是一个开源项目，我们欢迎任何人参与开发。如果你想参与开发，请阅读以下内容。

## 仓库地址

目前源代码仓库托管在 Github,码云仓库中的前后端源码作为镜像仓库。每天会自动同步代码。不支持任何形式的代码提交。
但是 [文档](https://doc.mineadmin.com/) 是在[码云仓库](https://gitee.com/mineadmin/mineadmin-doc)上维护的。

### Github

* [MineAdmin 后端源代码](https://github.com/mineadmin/mineadmin)
* [MineAdmin 前端源代码](https://github.com/mineadmin/mineadmin-vue)
* [MineAdmin 内核组件](https://github.com/mineadmin/components)

### Gitee

* [MineAdmin 文档](https://gitee.com/mineadmin/mineadmin-doc)
* [MineAdmin 后端源代码](https://gitee.com/mineadmin/mineadmin)
* [MineAdmin 前端源代码](https://gitee.com/mineadmin/mineadmin-vue)


## 你可以做什么

### 关注 [issues](https://github.com/mineadmin/mineadmin/issues) 动态

* 我们会在 issues 中发布一些待开发的功能，如果你感兴趣，可以在 issues 中留言，我们会尽快回复。
* 评论回复帮助提出疑问的用户；
* 根据[issues](https://github.com/mineadmin/mineadmin/issues)内容，提出合理的解决方案；去修复bug或者实现功能，并以 [pull request](https://github.com/mineadmin/mineadmin/pulls) 形式提交至 MineAdmin 仓库
* 关注自己提交 Pull Request 的进度和状态，以推动您的 Pull Request 尽快合入主仓库；
* 对其他人提交的 Pull Request 进行 Code Review，并给出您的建议和看法。
* 根据他人或自己的需求，研发独立的功能组件；
* 完善[文档](https://gitee.com/mineadmin/mineadmin-doc)，提供更好的使用说明。

###  Pull Request 指南

虽然我们会定期发布一些待开发的功能，但是我们更欢迎你自己提出你想要实现的功能。你可以在 [issues](https://github.com/mineadmin/mineadmin/issues) 中提出你的想法，我们会尽快回复是否接受。
在提交问题之前，请检查是否已经发布了类似的问题。

* fork 本仓库到你的 Github 账号下；
* 提交信息的格式应为 [File Name]: Info about commit. （例如） README.md: Fix xxx bug
* 提交代码前，请先执行 `composer cs-fix` 进行代码格式化；
* 提交代码前，请先执行 `composer an` 进行代码静态检查；
* 提交代码前，请先执行 `composer test` 进行单元测试；单元测试不要在您的任何生产环境上运行，因为它会删除添加数据；
* 确保将 PR 创建为你的功能分支， 而不是 master 分支上直接提交修改。
* 如果你的 PR 修复了 bug，请提供有关相关 bug 的描述。
