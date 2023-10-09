# ACSEO PageBuilder Bundle

This bundle provides a PageBuilder Solution built on top of [GrapesJS](https://grapesjs.com/).

When using this Bundle, you will have access to :
* a **Twig Component** that you can use in your template with `{{ component('PageBuilder' {'idField' : 'my_field'}) }}`. This component will create the PageBuilder area.
* a `Page` Entity and a `PageController` that will allow you to store and load the HTML, CSS, and JSON config of the generated Web page.

## Installation

### Install the bundle using composer

```bash
composer require acseo/pagebuilder-bundle
````

Enable the bundle in you Symfony project

```php

<?php
// config/bundles.php

return [
    ACSEO\PageBuilderBundle\PageBuilderBundle::class => ['all' => true],
```

### Enable PageController to Load / Save Pages

You can choose to use the default `PageController` provided in order to load / save the Page entities

To do so, you need to enable the route in your project : 

```yaml
# config/routes/acseo_page_builder.yaml
acseo_page_builder:
    resource: '@PageBuilderBundle/src/Controller/'
    type: attribute
```

### Update your database to create the Page Entity

With doctrine, according to your strategy :

```bash
php bin/console doctrine:schema:update
# OR
php bin/console doctrine:migrations:diff
```

# Configuration

The Bundle configuration allows you to manage how GrapesJS will be **loaded**, and what additionnal **plugins** or **blocks** will be added

Here is an example of a configuration file :

```yaml
# config/packages/acseo_page_builder.yaml
acseo_page_builder:
  #
  # GrapesJS Config
  #
  # Use this to have only default values
  #
  grapesjs: ~  
  #
  # Use this to set specific values
  #
  grapesjs:
    js:             # Optional : URL of the JS file for GrapeJS. 
                    # Default : https://cdnjs.cloudflare.com/ajax/libs/grapesjs/0.21.7/grapes.min.js
    css:            # Optional : URL of the CSS file for GrapeJS. 
                    # Default : https://cdnjs.cloudflare.com/ajax/libs/grapesjs/0.21.7/css/grapes.min.css
    urlLoad:        # Optional : Route name used to load Page JSON Content. 
                    # Default : acseo_page_builder_load
    urlStore:       # Optional : Route name used to store Page JSON Content. 
                    # Default : acseo_page_builder_save
    pageController: # Optional : Controller used to load / save Pages.       
                    # Default : PageController::class 
  #
  # Plugins Config
  # Array of name, url, options
  #
  plugins:
    ## Uncomment this example to load grapesjs-preset-webpage
    #- name: grapesjs-preset-webpage
    #  url: https://cdn.jsdelivr.net/npm/grapesjs-preset-webpage@1.0.3/dist/index.js                  
  #
  # Blocks Config
  # Declare your custom Blocks
  #
  blocks:
      my-custom-block:
        label: 'Bloc Custom'
        category: 'ACSEO'
        media: '<svg viewBox="0 0 24 24"></svg>'
        content: '<div class="ACSEO">Mon Bloc ACSEO</div>'
```

# Usage

In order to work, your Twig page **must** contain an input field with the *page identifier* (the URI).

```twig
{# templates/my/page.html.twig #}

<input type="hidden" id="page_uri" value="hello-world" />
{# OR #}
<input type="text" id="page_uri" />

{{ component('PageBuilder', {'idField' : 'page_uri'}) }}
```