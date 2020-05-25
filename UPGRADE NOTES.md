# FOF 3 Upgrade Notes

This document contains backwards incompatible changes in reverse chronological order. You can use it to maintain your FOF-powered extension.

# FOF 3.6.0

## Minimum PHP and Joomla version requirements

FOF 3.6.0 requires Joomla 3.9 or later, including 4.0. Moreover, it requires PHP 7.1 or later.

These changes were necessary for creating a framework that will be able to work with Joomla 3.10 and 4.0 when both stable versions are released in mid- to late-2020.

## Removal of XML forms

One of the major changes in FOF 3.6 is the removal of the Form package, a.k.a. XML forms. When we first introduced this
feature Joomla had a limited number of form fields and a very well defined HTML output, created in the PHP classes
implementing the form fields. In the following years, Joomla! went on to using JLayouts to try and abstract the output
of form fields, allowing templates to override them.

The problem with that approach is that we had two features not present in Joomla's forms: header fields and using XML
forms for browse views. Each of these features required us to provide our own HTML output. This was produced inside
the PHP classes implementing the fields because that's what Joomla! itself did at that point. Moreover, because of the
way class hierarchies in Joomla's JForm package worked we had to literally extend each and every field class and add the
extra functionality we needed. When Joomla transitioned to JLayouts our fields would no longer render correctly in some
templates. Even if we rewrote the entire feature to use JLayouts there was no incentive for template developers to
provide overrides for our JLayouts so we still had the same issue.

On top of that, using XML forms in real world software proved that they are limited and cumbersome. Very quickly you end
up writing hordes of custom fields which are very difficult (if not impossible) to override. This makes output customisation very painful.

PHP view templates, introduced in Joomla! 1.5 back in 2007, are a much more versatile approach but they have a
fundamental issue: they are very verbose and make it difficult for frontend developers to understand what is going on.

The best alternative is templates using the Blade syntax. They are far less verbose, make much more sense to frontend
developers and they are transpiled to PHP, meaning that you can still use PHP code where needed. The downside is that
displaying them requires the `tokenizer` PHP extension. However, we have a solution to that in the form of precompiled
templates (placed in your component's PrecompiledTemplates folder, following the same structure as the ViewTemplates
folder). If the tokenizer is not available you will fall back to PrecompiledTemplates. The downside is that template
overrides in this case must be pure PHP templates, not Blade. If you are on a bad host which doesn't allow you to use
the tokenizer just switch hosts. There is absolutely no security reason whatsoever to disable the tokenizer extension.
The only real reason is that your host doesn't understand how PHP works -- which in itself is a security threat!!!

## Removal of scaffolding

Scaffolding in FOF 3 let you create Controllers, Models, Views and XML forms based on your database schema. This was a quick way to start hashing out a component. However, most of that functionality has been superseded by other FOF features, making this feature obsolete.

Creating a Controller, Model or View class file is not actually necessary. If you want to quickly whip out a component you have two options. One, create empty class files extending the base Controller/DataController, Model/DataModel and View/Html/Json/Csv classes respectively. This is the recommended method. Two, use the Magic factory to have FOF fill in the gaps for you.

This leaves us with view templates. Scaffolding was only really useful in creating XML forms which could approximately represent your data but suffered from all the problems plaguing XML forms, outlined above. We consider using Blade and the built-in common Blade view templates the best way to create a quick interface for your component. Moreover, you get to choose the CSS framework you'd like to implement instead of being forced to use Bootstrap 2 as was the case with XML forms. You win something, you lose something. In our experience the end result is far more flexible without too much additional time spent designing the interface.

# FOF 3.5.4

## Common Blade view templates

FOF 3.5.4 and later ships with a ViewTemplates folder which contains common view templates. These are used as wrappers in browse and edit views and implement picking and displaying users. These can be included by using `any:lib_fof30/Common/templateName` and can be overridden in two ways:

* Globally, in the `templates/YOUR_TEMPLATE/html/lib_fof30/Common` folder
* Per component, in the `View/Common/tmpl`, `views/Common/tmpl` or `ViewTemplates/Common` folder per standard FOF
  convention.

If you are not using FEF you can simply skip these common FEF View Templates.

The common Blade templates are implemented as a last resort fallback in FOF's ViewTemplateFinder. This means that FOF
will look for view template files in the following order: template overrides for your component, your component, the 
other side of your component (only if you're using a magic Factory), common Blade view template's overrides in 
`<template Folder>/html/lib_fof30/Common` and finally FOF itself (`libraries/fof30/ViewTemplates/Common`).

Unlike regular view templates, FOF will only look for `.blade.php` overrides for common Blade view templates. This is on
purpose. You are meant to `@include` them in your own Blade templates and override their sections as you see fit.

# FOF 3.1.0

Tip: All deprecated practices are logged in Joomla's deprecated log file.

## Minimum PHP version

**PHP 5.3 no longer supported**. The minimum supported version of PHP is now 5.4.0. Some features may require PHP 5.5+.

## Introduction of `PlatformInterface`

If you use a custom Platform class which doesn't extend FOF30\Platform\Joomla\Platform you will need to **implement the new methods from the PlatformInterface**. This is a potential b/c break. We're willing to take the bet and not call this FOF 4.0 since we've not yet heard of anyone using their custom platform. This is not exactly SemVer but hey, it's not like Joomla! itself offers SemVer...

## Template routing

**Template::route will now only merge parameters if you either pass a second boolean argument OR if your URL does not have either option or one of view, task query string parameters in it**. Check your code in case you do something like `$container->template->route('index.php?option=com_foobar&task=baz')` as this bad practice is no longer going to work.

## Session access in the Container is deprecated

**Using the $container->session is deprecated**. You can get/set session variables through the getSessionVar/setSessionVar methods of the Platform object ($container->platform).

## Plain session tokens are deprecated

**Using plain session tokens is deprecated**. Use form tokens. You can get one with $container->platform->getToken(true).

**Tokens must be used in forms with the token as the variable name and a value of 1**. Using the variable name _token with the token as the value is deprecated.
