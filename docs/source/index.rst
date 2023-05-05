.. Nutria Nutrition documentation master file, created by
   sphinx-quickstart on Tue Mar 21 09:22:42 2023.
   You can adapt this file completely to your liking, but it should at least
   contain the root `toctree` directive.

Welcome to Nutria Nutrition's documentation!
============================================

How it works in 30 seconds
--------------------------

#. You input a food list.
#. You get a nutrient profile summarizing the food list's nutrient content.

A food list looks something like this:

.. csv-table:: Example food list
   :header: "Food", "Amount", "Unit"

   Foo,225,g
   Bar,20,ml
   Baz,100,g

And the corresponding nutrient profile looks like this:

.. csv-table:: Example nutrient profile
   :header: "Nutrient", "Amount", "Unit", "PDV"

   Protein,1.64,g,3.3%
   Total lipid (fat),0.76,g,1.0%
   "Carbohydrate, by difference",29.96,g,10.9%
   Energy,120.00,kcal,6.0%
   Water,166.92,g,5.6%
   "Fiber, total dietary",3.20,g,11.4%
   "Calcium, Ca",22.00,mg,1.7%
   "Iron, Fe",0.32,mg,1.8%
   "Magnesium, Mg",20.00,mg,4.8%
   "Phosphorus, P",28.00,mg,2.2%
   "Potassium, K",336.00,mg,7.1%
   "Sodium, Na",2.00,mg,0.1%
   "Zinc, Zn",0.18,mg,1.6%
   "Copper, Cu",0.22,mg,24.7%
   "Manganese, Mn",0.13,mg,5.5%
   "Selenium, Se",1.20,ug,2.2%
   "Vitamin A, RAE",108.00,ug RAE,12.0%
   Vitamin E (alpha-tocopherol),1.80,mg,12.0%
   Vitamin D (D2 + D3),0.00,ug,0.0%
   "Vitamin C, total ascorbic acid",72.80,mg,80.9%
   Thiamin,0.06,mg,4.7%
   Riboflavin,0.08,mg,5.8%
   Niacin,1.34,mg NE,8.4%
   Pantothenic acid,0.39,mg,7.9%
   Vitamin B-6,0.24,mg,14.0%
   Vitamin B-12,0.00,ug,0.0%
   "Choline, total",15.20,mg,2.8%
   Vitamin K (phylloquinone),8.40,ug,7.0%
   "Folate, DFE",86.00,ug DFE,21.5%
   Cholesterol,0.00,mg,0.0%
   "Fatty acids, total saturated",0.18,g,0.9%
   "Fatty acids, total monounsaturated",0.28,g,0.5%
   "Fatty acids, total polyunsaturated",0.14,g,0.9%
   "Sugars, total including NLEA",27.32,g,54.6%

The data comes from the United States Department of Agriculture's FoodData Central Standard Reference database of food nutrient content.

Features
--------

- View the nutrient profile of over 7500 common foods from the USDA's FoodData Central database
- Create custom ingredients to represent foods not in the USDA's database
- Create meals by combining ingredients
- View a meal's nutrient profile
- Create custom food lists by arbitrarily combining ingredients and meals, and view the food list's nutrient profile
- Create custom Reference Daily Intake profiles to use when computing percent daily value in nutrient profiles

Nutrients
---------

The app supports the following nutrients


.. csv-table::
   :header: Macronutrients

   Energy
   Protein
   Carbohydrates
   Water
   Dietary fiber
   Total fat
   Saturated fat
   Monounsaturated fat
   Polyunsaturated fat
   Cholesterol
   Sugars

.. csv-table::
   :header: Minerals

   Calcium
   Iron
   Magnesium
   Phosphorus
   Potassium
   Sodium
   Zinc
   Copper
   Manganese
   Selenium

.. csv-table::
   :header: Vitamins

   Vitamin A
   Vitamin B1
   Vitamin B2
   Vitamin B3
   Vitamin B5
   Vitamin B6
   Folate
   Vitamin B12
   Vitamin C
   Vitamin D
   Vitamin E
   Vitamin K
   Choline

Data
----

Data comes from the USDA FoodData Central Data Standard Reference Legacy database.
You can download the dataset on the `FoodData Central website <https://fdc.nal.usda.gov/download-datasets.html>`_.

References
----------

- `API Guide <https://fdc.nal.usda.gov/api-guide.html>`_
- `API Documentation <https://fdc.nal.usda.gov/api-spec/fdc_api.html>`_
- `API key usage <https://api.data.gov/docs/api-key/>`_
- `Download data <https://fdc.nal.usda.gov/download-datasets.html>`_
- `Datatype documentation <https://fdc.nal.usda.gov/data-documentation.html>`_
- `Foundation foods documentation <https://fdc.nal.usda.gov/docs/Foundation_Foods_Documentation_Apr2022.pdf>`_
- `Wikipedia: Reference Daily Intake <https://en.wikipedia.org/wiki/Reference_Daily_Intake>`_

Documentation
-------------

.. toctree::
   :maxdepth: 2
   :caption: Contents:

   database
   ui
   validation
   nutrient-profiles
   auth

