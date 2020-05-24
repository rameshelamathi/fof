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
  Joomla's even more outdated version. [It was a rather complicated affair](https://www.akeebabackup.com/news/1558-info-about-fof-and-f0f.html).
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

The following features present in earlier versions of FOF 3 are scheduled for removal around FOF 3.6 or 3.7:

* `FOF30\Utils\StringHelper`. Use the replacements advised in the docblocks.
* `FOF30\Utils\FEFHelper\Html`. Use `FEFHelper.browse` through Joomla's `HtmlHelper` class instead.
* `FOF30\Utils\InstallScript`. Use `FOF30\Utils\InstallScript\Component` instead.
* XML Forms. We are removing this feature without a replacement. It's impossible to maintain it for Joomla 4. See below.
* Scaffolding. Removed as a result of removing the XML Forms feature.
* LESS. The third party LESS compiler we're using is buggy and unmaintained. Use an external LESS compiler or use something else, like SCSS/Sass.
* `FOF30\Render\AkeebaStrapper`. We discontinued Akeeba Strapper in 2018. Please use the Joomla3 renderer or write your own.
* mcrypt support. The mcrypt PHP extension has been declared deprecated since PHP 7.1, the minimum PHP version we now support. Moreover, it's not been maintained since 2003, making it unsuitable for use in production.
* HAL support in JSON output. It's a dead format since circa 2013. You can manually implement JSON-LD with a suitable schema in your JSON output.

We are going to be maintaining an `UPGRADE NOTES.md` file as we're dropping the axe on features.

### A short note about the removal of XML Forms

The introduction of FOF XML forms as an extension to the Joomla core Form package (JForm) came at the insistence of the OSM and Joomla leadership back in 2012. The goal was to include FOF 2 in Joomla itself as the official Joomla RAD Framework, eventually dropping its branding and changing its name to JRAD. The FOF 2 XML Forms and JForm packages were going to be merged into a single package.

Shortly after the inclusion of FOF 2 in Joomla 3.2 a leadership change in Joomla caused this effort to fall through. The new leadership opposed the very existence of a Joomla RAD Framework, disbanded the Working Group and even declined bug fixes of FOF 2 to be included in the Joomla core throughout their tenure. Incidentally, that's how we ended up with the whole F-zero-F / F-oh-F mess.

Work in the FOF XML Forms package continued in vain with FOF 3. However, it became apparent that the core Joomla Form package was evolving without considering extensibility outside its narrow scope a plausible goal. Seeing this, we stopped developing the FOF XML Forms package in late 2017. The changes in Joomla 4 in early 2020 made it impossible to maintain the FOF XML Forms package at all. Hence its removal without a replacement.