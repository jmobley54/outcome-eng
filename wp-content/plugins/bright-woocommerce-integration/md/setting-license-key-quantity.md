# Introduction

It is possible to define a scenario using bright where a single purchase of a product results in multiple license seats being created. For example, a user came to us where they wanted to bundle in a single product:

*   2 seats of one course
*   10 seats of another

For example, you could call this a "manager's special", get two manager trainings and 10 team members.

This can be accomplished using product level bright metadata and the

# Defining Bright Metadata

This is accomplished using the product custom field editor in WordPress:

[<img src="http://help.aura-software.com/wp-content/uploads/sites/3/2015/07/bm.png" alt="bm" width="775" height="371" class="alignnone size-full wp-image-135" />][1]

# Correct Usage

In order to define metadata for a Bright course, the course must already be linked via the bright_course_id:

[Linking a Product][2]

If the product is not linked, the defined metadata is **ignored.**

# Do I need a metadata entry for every product?

No. Metadata annotates the product linked. If no metadata is found, the quantity will just default to a '1', for example.

# Data Format

Bright Metadata will need to be a valid JSON document. For example:

    {
      "bright-courses": [
        {
          "course-id": "Intro539bd113-fa96-4977-8396-10fd0ef92f18",
          "quantity-multiplier": 6
        },
        {
          "course-id": "PRP01cd3391-bdde-4a90-a6be-1b2891109020",
          "quantity-multiplier": 6
        },
        {
          "course-id": "Auditorc6d58d43-0aa2-4f4a-88a5-6f11e5c0410b",
          "quantity-multiplier": 3
        }
      ]
    }
    

For historical reasons, "quantity_multiplier" with an underscore is allowed, yet is deprecated and this functionality may be removed at a later date.

# Format for Variable Products:

    {
      "schemaVersion": "0.3",
      "bright-courses": [],
      "variations": [
        {
          "variationName": "Small Variation",
          "courseGuids": [
            "SmallCourseGuid",
            "ASecondCourseGuidForTheSmallVarition"
          ]
        },
        {
          "variationName": "Medium Variation",
          "courseGuids": [
            "MediumCourseGuid"
          ]
        },
        {
          "variationName": "Large Variation",
          "courseGuids": [
            "LargeCourseGuid"
          ]
        }
      ]
    }
    

Use the v.3 Bright Metadata Editor for assistance constructing a JSON document of this format.

0\.3 Version: [Bright Metadata JSON Editor v 0.3][3]

# JSON Editor For Bright Metadata

Here's a small mockup editor for editing Bright Metadata JSON Documents:

This data becomes the "value" for the bright_metadata custom field on a product.

0\.2 Version: [Bright Metadata JSON Editor v 0.2][4]

0\.3 Version: [Bright Metadata JSON Editor v 0.3][3]

# See Also

*   [License Keys][5]

 [1]: http://help.aura-software.com/wp-content/uploads/sites/3/2015/07/bm.png
 [2]: http://help.aura-software.com/linking-a-product/
 [3]: http://jeremydorn.com/json-editor/?schema=N4IgJAzgxgFgpgWwIYgFwhgF0wB1QenwCsIB7AOwFpp5kA6UgJwHN8ATRpAM00oAYALPhqIkAYhAAaEJgCeOOGhCkARkThRMUkDkakFjTAEs4ENKBHIAanEYQjFczPmL0ETIyPlmIAL7SVT2YsSihSAFc7UydjTAAbVxAAISCsAAIAYQios2k5BSUkRk5ZbXDyIwBHcLgASUxEM3QPGu0jBoQm0FiEpRSjYMxM7IhFPJclVXVNMorquo6mlrhpXX1bY2jUUDDI0cojNhiJtw8vH38QaqRyWNlKBHC44xw4k0ZjgvQvBuZbbQQXiMjwQaAAjL5Low4NUjNCjqgANogXZRA5HAC6kOkADcikYkMYKF0ZO1eugAAp6NjhTRpKz4wkOchpAAihJQ4y+ICKJVmVRq9UaSmWbUWMTJiQZniZji5iSmGi00nKAoWwtQy1WegMmxJeJlRPIADkkAhXN0TiB3J5vH5pKjRgBxcKHEn5RK8pClFVzQXi5qMVrSdoa7qSpSOuAuw7aD1KG3nPzYkDQ2HwtDIg0Eo2m83aKMxthmLGQ3xAA=&value=N4IgzgxgFgpgtgQwGowE5gJYHsB2IBcIIANCAEaoYDmUALgLQRYCu6MYBA2gLqkBuCSglrYcHfDwC+QA&theme=bootstrap2&iconlib=fontawesome4&object_layout=normal&show_errors=interaction&no_additional_properties&disable_edit_json&disable_properties
 [4]: http://jeremydorn.com/json-editor/?schema=N4IgJAzgxgFgpgWwIYgFwhgF0wB1QenwCsIB7AOwFpp5kA6UgJwHN8ATRpAM00oAYALPhqIkAYhAAaEJgCeOOGhCkARkThRMUkDkakFjTAEs4ENKBWMjzLJSikArowinzMo5gA2i9ACErNpgABADCjs6u0nIKSkiMnLLaDuRGAI4OcACSmIhm6JiMGdoeuW7GXj4g/tZYoeEu2tGVquqaSSnpWTkIeQUZ0rr6cIYmeaD2Ti6URmxl8pUQBUbkzCAAvtLpSOTlspQIDp7GOJ4mjHMx6Ms5zMPaCMtGBwhoAIxrGyCMcOlG37OoADaIAmEWmswAuh8PkAAAA==&value=N4IgRgTglg5gFgFwLQGMD2BXCBnAptkALgG0BdAXyAA=&theme=bootstrap2&iconlib=fontawesome4&object_layout=normal&show_errors=interaction&no_additional_properties&disable_edit_json&disable_properties
 [5]: http://help.aura-software.com/category/woocommerce-integration/license-keys/
