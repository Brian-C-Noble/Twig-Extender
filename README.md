# Twig Extender plugin for Craft CMS 3.x

Adds filter and function not native to Twig or Craft CMS

![Screenshot](resources/img/plugin-logo.png)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require briancnoble/twig-extender

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Twig Extender.

## Filters

### vartype

Returns the variable type
   
```php
{% set variable = "Whammy!" %}

{{ variable|vartype }}

#Output

string
```
### striphttp

Removes the protocol from a URL.

```php 
{{ "https://sunnybyte.com"|striphttp }}

#Output

"sunnybyte.com"
```

### phone

Formats US phone numbers

```php
{{ "15555555555"|phone }}
{{ "15555555555"|phone('.') }}
{{ "15555555555"|phone('-', 'parens') }}

#Output
'1-555-555-5555'
'1.555.555.5555'
'1 (555) 555-5555'
```
_Arguments_  
$separators `string` = What to separate the phone number with.  
$parens `string` = Parenthesize the area code, **'parens'** is the only value allowed


### truncate

Truncates a string to specified a character limit and appends an ending to the string

```php
{% set paragraph = "Hello, this is my paragraph, use me to test some of our twig functions." %}

{{ paragraph|truncate(27) }}
{{ paragraph|truncate(5, ' World') }}

#Output
"Hello, this is my paragraph..."
"Hello World" 
```

_Arguments_  
$limit `integer` = The characters the string will be limited to.

$end `string` = What will be appended to the string. _default_ ...

## Functions

### isVarType

Returns true if the argument matches the variable type
```php
{% set variable = "Whammy!" %}

{% if isVarType(variable, 'string' %}
    {{ variable }}
{% endif %}

{% if isVarType(variable, 'integer' %}
    {{ variable }}
{% else %}
    I'm not an integer!
{% if %}

#Output
'Whammy!'
'I'm not an integer!'
```

_Arguments_  
$type `string` = what type to check the variable against.

_Variable Types_
- array
- boolean
- double
- float
- integer
- NULL
- object
- resource
- string 

### relativeTime 
### formatVideoUrl
### truncate
### oembed
###ordinal

## Twig Extender Roadmap

Some things to do, and ideas for potential features:

* Release it

Brought to you by [Brian Noble](https://github.com/Brian-C-Noble/)
