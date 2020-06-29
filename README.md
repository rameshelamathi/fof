# Framework on Framework [![Build Status](https://travis-ci.org/akeeba/fof.png)](https://travis-ci.org/akeeba/fof)

## What is FOF? 

FOF (Framework on Framework) is a rapid application development framework for Joomla!. It's designed to abstract changes
between different Joomla versions and provide a better, faster, fluent coding interface based on the same MVC concepts 
that you learned with Joomla.

## Requirements

FOF 3.6 and later requires Joomla 3.9 or later and PHP 7.1 or later. It will not work on older Joomla and PHP versions.

## FOF 2.x, 3.x and Joomla 3

Joomla 3 includes a very, **VERY** old version of FOF we have stopped developing in 2015 and declared End of Life in 
2016. Please don't use that! That's what FOF looked liked in the early 2010's. This repository has a far better, much 
newer version. And yes; both versions can run side by side.

This warrants an explanation of the extensions you see in the Extensions, Manage page with FOF in their name:

* **FOF** (type: Library, version 2.4.3). This is the ancient version of FOF included in Joomla. It's installed in 
  `libraries/fof`. It cannot and MUST NOT be removed. If you delete it your site will break – this is still used by some
  core Joomla components, including Two Factor Authentication. 
* **F0F** (type: Library). Note that this is F-zero-F. That's an old version of FOF 2.x, renamed so it can run next to
  Joomla's even more outdated version. [It was a rather complicated affair](https://www.akeeba.com/news/1558-info-about-fof-and-f0f.html).
  It's installed in `libraries/f0f` (f-zero-f). It should no longer be necessary but please do check first if you have
  any very old extension still using it.
* **file_fof30** (type: File). This is the current version of FOF 3. It's installed in  `libraries/fof30`. Do NOT remove
  it manually. It will be uninstalled automatically when the last extension using it is removed. 
* **FOF** (type: Library, version 3.x.y). This was the old package type of FOF 3. We switched to a file package in 2018
  to address Joomla bricking your sites if it failed to fully update FOF. While we try to remove the leftover entry from
  Joomla's Extensions, Manage page it's not always possible. If you see this entry please DO NOT try to remove it, you 
  will break your site.
* **User - FOF Token Management** (type: Plugin). This will be shipped with our extensions in 2020 to manage token
  authentication for JSON API calls in Joomla 3. Please do not remove if you're using any Akeeba-branded extension.
  Also, cool fact: this code has already been contributed to Joomla 4 for its brand new API application.

## FOF and Joomla 4

Joomla 4, thankfully, no longer includes the ancient version of FOF Joomla 3 shipped with. You can use the latest 
version of FOF with Joomla 4.

**Important!** We only work towards full compatibility with _stable_ versions of Joomla. Using FOF with pre-release
versions of Joomla (alpha, beta, RC) may result in issues. If you have identified the issue to be coming from FOF and
not your extensions feel free to file a Pull Request or an issue in this repository. Please be as specific and detailed
as possible. 

## Using FOF for your extensions

If you want to use FOF to build your extensions and include it with them please read our Wiki for more information.

## Mind the deprecated / removed stuff!

While our original goal was to follow semantic versioning, a combination of reasons led to us deciding to remove features in a backwards incompatible manner without bumping the major version of FOF.

Backwards incompatible changes and major new features are detailed in the [`UPGRADE NOTES.md`](UPGRADE NOTES.md) in FOF's repository.

The following features present in earlier versions of FOF 3 are scheduled for removal:

* `FOF30\Utils\FEFHelper\Html`. Use `FEFHelper.browse` through Joomla's `HtmlHelper` class instead.
* `FOF30\Utils\StringHelper`. Use the replacements advised in the docblocks.
* `FOF30\Utils\InstallScript`. Use `FOF30\Utils\InstallScript\Component` instead.